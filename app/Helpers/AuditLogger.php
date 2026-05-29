<?php

namespace App\Helpers;

use App\Models\Auditoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    public static function log(string $accion, string $tabla, ?int $idRegistro = null, ?string $detalle = null): ?Auditoria
    {
        try {
            return Auditoria::create([
                'Id_Usuario' => Auth::id(),
                'Accion' => $accion,
                'Tabla_Afectada' => $tabla,
                'Id_Registro' => $idRegistro,
                'Detalle' => $detalle,
                'IP_Origen' => Request::ip(),
                'Fecha' => now(),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AuditLogger error: ' . $e->getMessage());
            return null;
        }
    }

    public static function logLogin($userId): void
    {
        self::log('INICIO_SESION', 'users', $userId, 'Inicio de sesión');
    }

    public static function logLogout($userId): void
    {
        self::log('CIERRE_SESION', 'users', $userId, 'Cierre de sesión');
    }

    public static function logCreate(string $tabla, int $id, string $descripcion): void
    {
        self::log('CREAR', $tabla, $id, $descripcion);
    }

    public static function logUpdate(string $tabla, int $id, string $descripcion): void
    {
        self::log('ACTUALIZAR', $tabla, $id, $descripcion);
    }

    public static function logDelete(string $tabla, int $id, string $descripcion): void
    {
        self::log('ELIMINAR', $tabla, $id, $descripcion);
    }
}