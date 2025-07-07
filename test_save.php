<?php
// Teste simples de salvamento
header('Content-Type: application/json');

// Dados de teste
$testData = [
    ['text' => 'Tarefa de teste 1', 'done' => false],
    ['text' => 'Tarefa de teste 2', 'done' => true],
    ['text' => 'Tarefa de teste 3', 'done' => false]
];

$file = 'tasks.json';

// Salva os dados
$jsonData = json_encode($testData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
$result = file_put_contents($file, $jsonData);

if ($result !== false) {
    echo json_encode([
        'success' => true,
        'message' => 'Dados de teste salvos com sucesso',
        'bytes_written' => $result,
        'file' => $file
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao salvar arquivo',
        'file' => $file
    ]);
}
?> 