<?php
header('Content-Type: application/json');

$file = 'tasks.json';
$directory = dirname($file);

echo json_encode([
    'diagnostic' => [
        'file_path' => realpath($file),
        'directory_path' => realpath($directory),
        'file_exists' => file_exists($file),
        'directory_exists' => is_dir($directory),
        'file_readable' => is_readable($file),
        'file_writable' => is_writable($file),
        'directory_writable' => is_writable($directory),
        'file_size' => file_exists($file) ? filesize($file) : 0,
        'file_permissions' => file_exists($file) ? substr(sprintf('%o', fileperms($file)), -4) : 'N/A',
        'directory_permissions' => substr(sprintf('%o', fileperms($directory)), -4),
        'current_user' => get_current_user(),
        'php_user' => posix_getpwuid(posix_geteuid())['name'] ?? 'Unknown',
        'file_owner' => file_exists($file) ? posix_getpwuid(fileowner($file))['name'] ?? 'Unknown' : 'N/A',
        'directory_owner' => posix_getpwuid(fileowner($directory))['name'] ?? 'Unknown'
    ],
    'file_content' => file_exists($file) ? json_decode(file_get_contents($file), true) : null,
    'timestamp' => date('Y-m-d H:i:s')
], JSON_PRETTY_PRINT);
?> 