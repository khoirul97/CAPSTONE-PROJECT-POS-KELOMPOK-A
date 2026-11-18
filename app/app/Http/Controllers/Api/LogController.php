<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LogController extends BaseController
{
    public function index()
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (!File::exists($logPath)) {
            return response()->json([
                'exists' => false,
                'message' => 'Log file not found'
            ]);
        }

        $size = File::size($logPath);
        $lastModified = File::lastModified($logPath);
        
        // Hitung total lines
        $lines = File::lines($logPath);
        $totalLines = 0;
        foreach ($lines as $line) {
            if (trim($line) !== '') {
                $totalLines++;
            }
        }
        
        return response()->json([
            'exists' => true,
            'size' => $this->formatSize($size),
            'size_bytes' => $size,
            'last_modified' => date('Y-m-d H:i:s', $lastModified),
            'path' => $logPath,
            'total_lines' => $totalLines
        ]);
    }

    public function getLogContent(Request $request)
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (!File::exists($logPath)) {
            return response()->json([
                'content' => 'Log file not found',
                'lines' => []
            ]);
        }

        $lines = File::lines($logPath);
        $logLines = [];
        
        foreach ($lines as $line) {
            if (trim($line) !== '') {
                $logLines[] = [
                    'content' => $line,
                    'type' => $this->getLogType($line)
                ];
            }
        }

        // Sorting - default descending (terbaru di bawah)
        $sortOrder = $request->get('sort', 'desc');
        
        if ($sortOrder === 'asc') {
            // Ascending: oldest first (lama di atas, baru di bawah)
            // Tidak perlu reverse karena data sudah urut dari file
        } else {
            // Descending: newest first (terbaru di bawah)
            $logLines = array_reverse($logLines);
        }

        return response()->json([
            'content' => implode("\n", array_column($logLines, 'content')),
            'lines' => $logLines,
            'total' => count($logLines),
            'sort' => $sortOrder
        ]);
    }

    public function clearLog()
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (File::exists($logPath)) {
            File::put($logPath, '');
            return response()->json(['message' => 'Log cleared successfully']);
        }
        
        return response()->json(['message' => 'Log file not found'], 404);
    }

    public function downloadLog()
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (!File::exists($logPath)) {
            return response()->json(['message' => 'Log file not found'], 404);
        }

        return response()->download($logPath, 'laravel.log');
    }

    private function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    private function getLogType($line)
    {
        if (str_contains($line, 'ERROR')) {
            return 'error';
        } elseif (str_contains($line, 'WARNING')) {
            return 'warning';
        } elseif (str_contains($line, 'INFO')) {
            return 'info';
        } elseif (str_contains($line, 'DEBUG')) {
            return 'debug';
        } elseif (str_contains($line, 'NOTICE')) {
            return 'notice';
        } elseif (str_contains($line, 'ALERT')) {
            return 'alert';
        } elseif (str_contains($line, 'EMERGENCY')) {
            return 'emergency';
        } elseif (str_contains($line, 'CRITICAL')) {
            return 'critical';
        } else {
            return 'info';
        }
    }
}