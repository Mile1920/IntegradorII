<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database {--incremental : Realizar backup incremental} {--full : Realizar backup completo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear backup de la base de datos (completo o incremental)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isIncremental = $this->option('incremental');
        $isFull = $this->option('full');

        if (!$isIncremental && !$isFull) {
            $this->error('Debe especificar --incremental o --full');
            return 1;
        }

        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupDir = 'backups/' . now()->format('Y/m/d');

        // Crear directorio si no existe
        Storage::makeDirectory($backupDir);

        if ($isFull) {
            $this->info('Iniciando backup completo de la base de datos...');
            return $this->createFullBackup($timestamp, $backupDir);
        }

        if ($isIncremental) {
            $this->info('Iniciando backup incremental de la base de datos...');
            return $this->createIncrementalBackup($timestamp, $backupDir);
        }

        return 0;
    }

    /**
     * Crear backup completo
     */
    private function createFullBackup($timestamp, $backupDir)
    {
        try {
            $filename = "full_backup_{$timestamp}.sql";
            $path = "{$backupDir}/{$filename}";

            // Obtener todas las tablas (PostgreSQL)
            $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
            $databaseName = config('database.connections.pgsql.database');

            $sql = "-- Mina Porco Database Full Backup\n";
            $sql .= "-- Generated: {$timestamp}\n";
            $sql .= "-- Database: {$databaseName}\n\n";

            $sql .= "BEGIN;\n\n";

            foreach ($tables as $table) {
                $tableName = $table->tablename;

                // Estructura de la tabla (PostgreSQL)
                $createTable = DB::select("SELECT * FROM information_schema.columns WHERE table_name = '{$tableName}' AND table_schema = 'public' ORDER BY ordinal_position");
                if (!empty($createTable)) {
                    $sql .= "-- Table structure for {$tableName}\n";
                    $sql .= "-- Note: This is a simplified structure export\n";
                    $sql .= "-- Full schema backup would require pg_dump\n\n";
                }

                // Datos de la tabla
                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    $sql .= "-- Data for {$tableName}\n";
                    foreach ($rows as $row) {
                        $columns = array_keys((array)$row);
                        $values = array_map(function($value) {
                            return $value === null ? 'NULL' : "'" . addslashes($value) . "'";
                        }, (array)$row);

                        $sql .= "INSERT INTO {$tableName} (" . implode(',', $columns) . ") VALUES (" . implode(',', $values) . ");\n";
                    }
                    $sql .= "\n";
                }
            }

            $sql .= "COMMIT;\n";

            // Guardar archivo
            Storage::put($path, $sql);

            $this->info("✅ Backup completo creado: {$path}");
            $this->info("Tamaño: " . Storage::size($path) . " bytes");

            // Limpiar backups antiguos (mantener últimos 10)
            $this->cleanupOldBackups('full_backup_', 10);

            return 0;

        } catch (\Exception $e) {
            $this->error('Error creando backup completo: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Crear backup incremental
     */
    private function createIncrementalBackup($timestamp, $backupDir)
    {
        try {
            $filename = "incremental_backup_{$timestamp}.sql";
            $path = "{$backupDir}/{$filename}";

            // Obtener último backup incremental o full
            $lastBackup = $this->getLastBackupTime();

            $sql = "-- Mina Porco Database Incremental Backup\n";
            $sql .= "-- Generated: {$timestamp}\n";
            $sql .= "-- Since: " . ($lastBackup ? $lastBackup->format('Y-m-d H:i:s') : 'Never') . "\n\n";

            $sql .= "BEGIN TRANSACTION;\n\n";

            // Obtener todas las tablas disponibles (PostgreSQL)
            $allTables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
            $tables = array_map(function($table) {
                return $table->tablename;
            }, $allTables);
            $changesFound = false;

            foreach ($tables as $tableName) {
                $query = DB::table($tableName);

                if ($lastBackup) {
                    $query->where('created_at', '>=', $lastBackup)
                          ->orWhere('updated_at', '>=', $lastBackup);
                }

                $rows = $query->get();

                if ($rows->count() > 0) {
                    $changesFound = true;
                    $sql .= "-- Changes in {$tableName} since last backup\n";

                    foreach ($rows as $row) {
                        // Para updates, necesitamos determinar si es INSERT o UPDATE
                        $existsInBackup = false; // Lógica simplificada

                        if ($existsInBackup) {
                            // UPDATE logic would go here
                        } else {
                            // INSERT
                            $columns = array_keys((array)$row);
                            $values = array_map(function($value) {
                                return $value === null ? 'NULL' : "'" . addslashes($value) . "'";
                            }, (array)$row);

                            $sql .= "INSERT OR REPLACE INTO {$tableName} (" . implode(',', $columns) . ") VALUES (" . implode(',', $values) . ");\n";
                        }
                    }
                    $sql .= "\n";
                }
            }

            if (!$changesFound) {
                $sql .= "-- No changes found since last backup\n";
            }

            $sql .= "COMMIT;\n";

            // Guardar archivo
            Storage::put($path, $sql);

            $this->info("✅ Backup incremental creado: {$path}");
            $this->info("Tamaño: " . Storage::size($path) . " bytes");

            // Limpiar backups antiguos (mantener últimos 20 incrementales)
            $this->cleanupOldBackups('incremental_backup_', 20);

            return 0;

        } catch (\Exception $e) {
            $this->error('Error creando backup incremental: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Obtener fecha del último backup
     */
    private function getLastBackupTime()
    {
        $backupFiles = Storage::files('backups');

        $timestamps = [];
        foreach ($backupFiles as $file) {
            if (preg_match('/(?:full|incremental)_backup_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})\.sql/', $file, $matches)) {
                $timestamps[] = Carbon::createFromFormat('Y-m-d_H-i-s', $matches[1]);
            }
        }

        return !empty($timestamps) ? max($timestamps) : null;
    }

    /**
     * Limpiar backups antiguos
     */
    private function cleanupOldBackups($prefix, $keepCount)
    {
        $backupFiles = Storage::files('backups');

        $matchingFiles = array_filter($backupFiles, function($file) use ($prefix) {
            return strpos(basename($file), $prefix) === 0;
        });

        if (count($matchingFiles) <= $keepCount) {
            return;
        }

        // Ordenar por fecha (más antiguos primero)
        usort($matchingFiles, function($a, $b) {
            return filemtime(storage_path('app/' . $a)) <=> filemtime(storage_path('app/' . $b));
        });

        // Eliminar archivos antiguos
        $filesToDelete = array_slice($matchingFiles, 0, count($matchingFiles) - $keepCount);

        foreach ($filesToDelete as $file) {
            Storage::delete($file);
            $this->info("🗑️ Eliminado backup antiguo: {$file}");
        }
    }
}