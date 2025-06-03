<?php

if (!function_exists('humanFileSize')) {
    /**
     * Format bytes to a human-readable format
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    function humanFileSize($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}