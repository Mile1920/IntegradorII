<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $logFile = storage_path('logs/laravel.log');
        $logContent = [];

        if (file_exists($logFile)) {
            $lines = file($logFile);
            $lines = array_reverse($lines);
            $lines = array_slice($lines, 0, 5000);
            $lines = array_reverse($lines);

            $level = $request->input('level', '');
            $search = $request->input('search', '');
            $date = $request->input('date', '');

            foreach ($lines as $line) {
                $parsed = $this->parseLogLine($line);

                if ($level && $parsed['level'] !== $level) continue;
                if ($search && !str_contains(strtolower($parsed['text']), strtolower($search))) continue;
                if ($date && !str_contains($parsed['date'], $date)) continue;

                $logContent[] = $parsed;
            }

            $logContent = array_reverse($logContent);
        }

        $levels = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];
        $stats = $this->getLogStats($logFile);

        return view('system.logs', compact('logContent', 'levels', 'stats'));
    }

    public function clear()
    {
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
            Log::info('Logs del sistema limpiados');
        }

        return redirect()->route('system.logs')->with('success', 'Logs limpiados correctamente');
    }

    public function download()
    {
        $logFile = storage_path('logs/laravel.log');
        if (!file_exists($logFile)) {
            return redirect()->route('system.logs')->with('error', 'No hay archivos de log disponibles');
        }

        return response()->download($logFile, 'laravel_' . date('Y-m-d_H-i-s') . '.log');
    }

    private function parseLogLine($line)
    {
        $parsed = [
            'raw' => $line,
            'date' => '',
            'time' => '',
            'level' => 'info',
            'text' => $line,
            'has_stack' => false,
        ];

        if (preg_match('/^\[(\d{4}-\d{2}-\d{2}) (\d{2}:\d{2}:\d{2})\].*?\.(\w+):/', $line, $matches)) {
            $parsed['date'] = $matches[1];
            $parsed['time'] = $matches[2];
            $parsed['level'] = strtolower($matches[3]);
            $parsed['text'] = $line;
        }

        if (preg_match('/stack trace:/i', $line)) {
            $parsed['has_stack'] = true;
        }

        return $parsed;
    }

    private function getLogStats($logFile)
    {
        $stats = [
            'total' => 0,
            'error' => 0,
            'warning' => 0,
            'info' => 0,
            'debug' => 0,
            'critical' => 0,
            'emergency' => 0,
            'size' => 0,
        ];

        if (file_exists($logFile)) {
            $stats['size'] = filesize($logFile);
            $content = file_get_contents($logFile);

            foreach (['error', 'warning', 'info', 'debug', 'critical', 'emergency'] as $level) {
                preg_match_all('/\.' . $level . ':/i', $content, $matches);
                $stats[$level] = count($matches[0]);
                $stats['total'] += $stats[$level];
            }
        }

        return $stats;
    }
}
