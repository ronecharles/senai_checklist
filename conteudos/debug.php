<div class="debug-container">
  <h2>🔧 Debug do Sistema</h2>
  <p>Esta seção mostra informações de debug do sistema em tempo real.</p>
  
  <div class="debug-sections">
    <div class="debug-section">
      <h3>📊 Status do Sistema</h3>
      <div class="status-grid">
        <div class="status-item">
          <span class="status-label">Servidor:</span>
          <span class="status-value" id="server-status">Verificando...</span>
        </div>
        <div class="status-item">
          <span class="status-label">Arquivo JSON:</span>
          <span class="status-value" id="json-status">Verificando...</span>
        </div>
        <div class="status-item">
          <span class="status-label">API Save:</span>
          <span class="status-value" id="api-status">Verificando...</span>
        </div>
      </div>
    </div>

    <div class="debug-section">
      <h3>📝 Log de Atividades</h3>
      <div id="debug-log" class="debug-log"></div>
      <button id="clear-log" class="debug-btn">Limpar Log</button>
    </div>

    <div class="debug-section">
      <h3>🧪 Testes Rápidos</h3>
      <div class="test-buttons">
        <button class="test-btn" onclick="testLoad()">Testar Carregamento</button>
        <button class="test-btn" onclick="testSave()">Testar Salvamento</button>
        <button class="test-btn" onclick="testAPI()">Testar API</button>
      </div>
    </div>

    <div class="debug-section">
      <h3>📄 Conteúdo do tasks.json</h3>
      <div id="json-content" class="json-content"></div>
      <button id="refresh-json" class="debug-btn">Atualizar</button>
    </div>
  </div>
</div>

<style>
.debug-container {
  max-width: 800px;
  margin: 0 auto;
}

.debug-sections {
  display: grid;
  gap: 20px;
  margin-top: 20px;
}

.debug-section {
  background: #fff;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.debug-section h3 {
  margin-bottom: 15px;
  color: #111b21;
  font-size: 16px;
}

.status-grid {
  display: grid;
  gap: 10px;
}

.status-item {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  border-bottom: 1px solid #f0f2f5;
}

.status-item:last-child {
  border-bottom: none;
}

.status-label {
  font-weight: 500;
  color: #54656f;
}

.status-value {
  font-weight: 600;
}

.status-value.online {
  color: #00a884;
}

.status-value.offline {
  color: #ff6b6b;
}

.debug-log {
  background: #f8f9fa;
  border: 1px solid #e9edef;
  border-radius: 6px;
  padding: 12px;
  height: 200px;
  overflow-y: auto;
  font-family: 'Courier New', monospace;
  font-size: 12px;
  margin-bottom: 10px;
}

.json-content {
  background: #f8f9fa;
  border: 1px solid #e9edef;
  border-radius: 6px;
  padding: 12px;
  height: 150px;
  overflow-y: auto;
  font-family: 'Courier New', monospace;
  font-size: 12px;
  margin-bottom: 10px;
}

.test-buttons {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.test-btn, .debug-btn {
  padding: 8px 16px;
  background: #00a884;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 12px;
  transition: background-color 0.2s;
}

.test-btn:hover, .debug-btn:hover {
  background: #008f72;
}

#clear-log {
  background: #ff6b6b;
}

#clear-log:hover {
  background: #ff5252;
}
</style>

<script>
$(document).ready(function() {
  let debugLogElement = $('#debug-log');
  
  function addLog(message) {
    const timestamp = new Date().toLocaleTimeString();
    debugLogElement.append(`[${timestamp}] ${message}<br>`);
    debugLogElement.scrollTop(debugLogElement[0].scrollHeight);
  }

  function checkSystemStatus() {
    // Verificar servidor
    addLog('Verificando status do servidor...');
    
    // Verificar arquivo JSON
    $.getJSON('tasks.json?_t=' + new Date().getTime(), function(data) {
      $('#json-status').text('Online').addClass('online');
      addLog('Arquivo tasks.json carregado com sucesso');
      updateJsonContent(data);
    }).fail(function() {
      $('#json-status').text('Offline').addClass('offline');
      addLog('Erro ao carregar tasks.json');
    });

    // Verificar API
    $.ajax({
      url: 'api/save.php',
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify([]),
      success: function(response) {
        $('#api-status').text('Online').addClass('online');
        addLog('API save.php funcionando');
      },
      error: function() {
        $('#api-status').text('Offline').addClass('offline');
        addLog('Erro na API save.php');
      }
    });

    $('#server-status').text('Online').addClass('online');
    addLog('Sistema inicializado');
  }

  function updateJsonContent(data) {
    $('#json-content').html(JSON.stringify(data, null, 2));
  }

  // Testes
  window.testLoad = function() {
    addLog('Iniciando teste de carregamento...');
    $.getJSON('tasks.json?_t=' + new Date().getTime(), function(tasks) {
      addLog(`✅ Carregamento bem-sucedido: ${tasks.length} tarefas`);
    }).fail(function(xhr, status, error) {
      addLog(`❌ Erro no carregamento: ${status} - ${error}`);
    });
  };

  window.testSave = function() {
    addLog('Iniciando teste de salvamento...');
    const testData = [
      { text: 'Tarefa de teste ' + new Date().toLocaleTimeString(), done: false }
    ];
    
    $.ajax({
      url: 'api/save.php',
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify(testData),
      success: function(response) {
        addLog('✅ Salvamento bem-sucedido');
        setTimeout(testLoad, 500);
      },
      error: function(xhr, status, error) {
        addLog(`❌ Erro no salvamento: ${status} - ${error}`);
      }
    });
  };

  window.testAPI = function() {
    addLog('Testando API...');
    $.ajax({
      url: 'api/save.php',
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify([]),
      success: function(response) {
        addLog('✅ API respondendo corretamente');
      },
      error: function(xhr, status, error) {
        addLog(`❌ API com erro: ${status} - ${error}`);
      }
    });
  };

  // Eventos
  $('#clear-log').click(function() {
    debugLogElement.empty();
    addLog('Log limpo');
  });

  $('#refresh-json').click(function() {
    addLog('Atualizando conteúdo do JSON...');
    $.getJSON('tasks.json?_t=' + new Date().getTime(), function(data) {
      updateJsonContent(data);
      addLog('Conteúdo do JSON atualizado');
    });
  });

  // Inicialização
  addLog('Debug carregado');
  checkSystemStatus();
  
  // Atualizar status a cada 30 segundos
  setInterval(checkSystemStatus, 30000);
});
</script> 