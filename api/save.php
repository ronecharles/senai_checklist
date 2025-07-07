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
$file = '../' . $fileParam;

error_log("Arquivo de destino: " . $file);

// Verifica se o arquivo existe, se não, cria com array vazio
if (!file_exists($file)) {
    error_log("Arquivo não existe, criando: " . $file);
    file_put_contents($file, '[]');
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

// Salva formatado
$jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
if (file_put_contents($file, $jsonData) === false) {
    error_log("Erro ao gravar arquivo: " . $file);
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao gravar arquivo']);
    exit;
}

error_log("Arquivo salvo com sucesso: " . $file);
echo json_encode(['success' => true, 'message' => 'Dados salvos com sucesso', 'count' => count($data)]);
