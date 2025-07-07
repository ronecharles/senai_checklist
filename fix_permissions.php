<?php
header('Content-Type: application/json');

$file = 'tasks.json';
$directory = dirname($file);

$results = [
    'actions' => [],
    'errors' => [],
    'success' => true
];

function addAction($message) {
    global $results;
    $results['actions'][] = $message;
}

function addError($message) {
    global $results;
    $results['errors'][] = $message;
    $results['success'] = false;
}

// Verifica se o diretório existe
if (!is_dir($directory)) {
    addError("Diretório não existe: $directory");
} else {
    addAction("Diretório existe: $directory");
    
    // Verifica permissões do diretório
    if (!is_writable($directory)) {
        addAction("Tentando corrigir permissões do diretório...");
        if (chmod($directory, 0755)) {
            addAction("Permissões do diretório corrigidas para 755");
        } else {
            addError("Não foi possível corrigir permissões do diretório");
        }
    } else {
        addAction("Diretório já é gravável");
    }
}

// Verifica se o arquivo existe
if (!file_exists($file)) {
    addAction("Arquivo não existe, criando...");
    $initialData = json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if (file_put_contents($file, $initialData) !== false) {
        addAction("Arquivo criado com sucesso");
    } else {
        addError("Não foi possível criar o arquivo");
    }
} else {
    addAction("Arquivo existe: $file");
    
    // Verifica permissões do arquivo
    if (!is_writable($file)) {
        addAction("Tentando corrigir permissões do arquivo...");
        if (chmod($file, 0644)) {
            addAction("Permissões do arquivo corrigidas para 644");
        } else {
            addError("Não foi possível corrigir permissões do arquivo");
        }
    } else {
        addAction("Arquivo já é gravável");
    }
    
    // Verifica se o arquivo é válido
    $content = file_get_contents($file);
    if ($content === false) {
        addError("Não foi possível ler o arquivo");
    } else {
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            addAction("Arquivo JSON inválido, corrigindo...");
            $fixedData = json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if (file_put_contents($file, $fixedData) !== false) {
                addAction("Arquivo JSON corrigido");
            } else {
                addError("Não foi possível corrigir o arquivo JSON");
            }
        } else {
            addAction("Arquivo JSON válido");
        }
    }
}

// Verifica se o arquivo está vazio
if (file_exists($file) && filesize($file) === 0) {
    addAction("Arquivo está vazio, inicializando...");
    $initialData = json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if (file_put_contents($file, $initialData) !== false) {
        addAction("Arquivo inicializado com dados vazios");
    } else {
        addError("Não foi possível inicializar o arquivo");
    }
}

// Verificação final
if (file_exists($file) && is_readable($file) && is_writable($file)) {
    addAction("✅ Verificação final: arquivo está OK");
} else {
    addError("❌ Verificação final: arquivo ainda tem problemas");
}

$results['timestamp'] = date('Y-m-d H:i:s');
$results['file_path'] = realpath($file) ?: $file;
$results['file_size'] = file_exists($file) ? filesize($file) : 0;
$results['file_permissions'] = file_exists($file) ? substr(sprintf('%o', fileperms($file)), -4) : 'N/A';

echo json_encode($results, JSON_PRETTY_PRINT);
?> 