<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Log para debug
error_log("save.php chamado - Método: " . $_SERVER['REQUEST_METHOD']);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Permite apenas salvar em tasks.json ou clientes.json
$allowedFiles = ['tasks.json', 'clientes.json'];
$fileParam = isset($_GET['file']) ? $_GET['file'] : 'tasks.json';
if (!in_array($fileParam, $allowedFiles)) {
    http_response_code(400);
    echo json_encode(['error' => 'Arquivo não permitido']);
    exit;
}

// Caminho absoluto para o arquivo
$file = dirname(__DIR__) . DIRECTORY_SEPARATOR . $fileParam;

error_log("Arquivo de destino: " . $file);

// Verifica se o diretório existe e é gravável
$directory = dirname($file);
if (!is_dir($directory)) {
    error_log("Diretório não existe: " . $directory);
    http_response_code(500);
    echo json_encode(['error' => 'Diretório não existe']);
    exit;
}

if (!is_writable($directory)) {
    error_log("Diretório não é gravável: " . $directory);
    http_response_code(500);
    echo json_encode(['error' => 'Diretório não é gravável']);
    exit;
}

// Verifica se o arquivo existe
$fileExists = file_exists($file);
error_log("Arquivo existe: " . ($fileExists ? 'Sim' : 'Não'));

// Se o arquivo não existe, cria com array vazio
if (!$fileExists) {
    error_log("Arquivo não existe, criando: " . $file);
    $initialData = json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if (file_put_contents($file, $initialData) === false) {
        error_log("Erro ao criar arquivo: " . $file);
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao criar arquivo']);
        exit;
    }
    error_log("Arquivo criado com sucesso: " . $file);
}

// Verifica se o arquivo é gravável
if (!is_writable($file)) {
    error_log("Arquivo não é gravável: " . $file);
    http_response_code(500);
    echo json_encode(['error' => 'Arquivo não é gravável']);
    exit;
}

// Recebe o JSON bruto do POST
$raw = file_get_contents('php://input');

error_log("Dados recebidos: " . substr($raw, 0, 100) . "...");

if (empty($raw)) {
    http_response_code(400);
    echo json_encode(['error' => 'Dados não fornecidos']);
    exit;
}

// Valida JSON
$data = json_decode($raw, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("Erro JSON: " . json_last_error_msg());
    http_response_code(400);
    echo json_encode(['error' => 'JSON inválido: ' . json_last_error_msg()]);
    exit;
}

// Verifica se é um array
if (!is_array($data)) {
    error_log("Dados não são array: " . gettype($data));
    http_response_code(400);
    echo json_encode(['error' => 'Dados devem ser um array']);
    exit;
}

error_log("Dados válidos, salvando " . count($data) . " itens");

// Cria backup do arquivo atual antes de salvar
$backupFile = $file . '.backup';
if ($fileExists) {
    $currentContent = file_get_contents($file);
    if ($currentContent !== false) {
        file_put_contents($backupFile, $currentContent);
        error_log("Backup criado: " . $backupFile);
    }
}

// Salva formatado
$jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
if (file_put_contents($file, $jsonData) === false) {
    error_log("Erro ao gravar arquivo: " . $file);
    
    // Tenta restaurar backup se existir
    if (file_exists($backupFile)) {
        $backupContent = file_get_contents($backupFile);
        if ($backupContent !== false) {
            file_put_contents($file, $backupContent);
            error_log("Backup restaurado devido a erro de salvamento");
        }
    }
    
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao gravar arquivo']);
    exit;
}

// Verifica se o arquivo foi salvo corretamente
$savedContent = file_get_contents($file);
if ($savedContent === false || json_decode($savedContent) === null) {
    error_log("Arquivo salvo está corrompido, restaurando backup");
    
    // Restaura backup
    if (file_exists($backupFile)) {
        $backupContent = file_get_contents($backupFile);
        if ($backupContent !== false) {
            file_put_contents($file, $backupContent);
            error_log("Backup restaurado devido a arquivo corrompido");
        }
    }
    
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao salvar arquivo - dados corrompidos']);
    exit;
}

// Remove backup se tudo estiver ok
if (file_exists($backupFile)) {
    unlink($backupFile);
    error_log("Backup removido após salvamento bem-sucedido");
}

error_log("Arquivo salvo com sucesso: " . $file);
echo json_encode([
    'success' => true, 
    'message' => 'Dados salvos com sucesso', 
    'count' => count($data),
    'file_size' => strlen($jsonData),
    'file_path' => $file
]);
