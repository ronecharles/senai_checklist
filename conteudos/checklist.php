<link rel="stylesheet" href="conteudos/styles.css">

<div class="checklist-container">
  <div class="add-task-section">
    <input id="new-task" type="text" class="add-task-input" placeholder="Nova tarefa…">
    <button id="add-btn" class="add-task-btn">Adicionar</button>
  </div>

  <div id="status-message" class="status-message" style="display: none;"></div>
  <div id="debug-info" class="debug-info" style="display: none;"></div>
  
  <div class="task-list" id="task-list"></div>
</div>

<script>
// Script específico do checklist
$(document).ready(function() {
  const $list = $('#task-list');
  const $statusMessage = $('#status-message');
  const $debugInfo = $('#debug-info');
  let isLoading = false;
  let isSaving = false;
  let currentTasks = [];

  // Função para debug
  function debugLog(message) {
    const timestamp = new Date().toLocaleTimeString();
    const logMessage = `[${timestamp}] ${message}`;
    $debugInfo.append(`${logMessage}<br>`);
    console.log(logMessage);
  }

  // Função para mostrar mensagens de status
  function showStatus(message, type = 'info') {
    $statusMessage.removeClass('error success').addClass(type).text(message).show();
    debugLog(`Status: ${message} (${type})`);
    if (type !== 'error') {
      setTimeout(() => $statusMessage.hide(), 3000);
    }
  }

  // Carrega JSON e renderiza lista
  function loadTasks() {
    if (isLoading) {
      debugLog('Tentativa de carregamento ignorada - já carregando');
      return;
    }
    isLoading = true;
    debugLog('Iniciando carregamento de tarefas...');
    
    const timestamp = new Date().getTime();
    const url = `tasks.json?_t=${timestamp}`;
    debugLog(`Fazendo requisição para: ${url}`);
    
    $.getJSON(url, function(tasks) {
      debugLog(`Resposta recebida: ${JSON.stringify(tasks).substring(0, 100)}...`);
      
      if (Array.isArray(tasks)) {
        currentTasks = tasks;
        renderTasks(tasks);
        showStatus(`Carregadas ${tasks.length} tarefas`, 'success');
        debugLog(`Lista renderizada com ${tasks.length} tarefas`);
      } else {
        currentTasks = [];
        renderTasks([]);
        showStatus('Formato de dados inválido', 'error');
        debugLog('Dados recebidos não são um array');
      }
      isLoading = false;
    }).fail(function(jqXHR, textStatus, errorThrown) {
      debugLog(`Erro no carregamento: ${textStatus} - ${errorThrown}`);
      console.error('Erro ao carregar tarefas:', textStatus, errorThrown);
      showStatus('Erro ao carregar tarefas. Verifique o console para mais detalhes.', 'error');
      currentTasks = [];
      renderTasks([]);
      isLoading = false;
    });
  }

  // Renderiza as tarefas na interface
  function renderTasks(tasks) {
    $list.empty();
    tasks.forEach((t, i) => {
      const $li = $(`
        <div class="task-item" data-index="${i}">
          <input type="checkbox" class="task-checkbox" ${t.done ? 'checked' : ''}>
          <label class="task-label">${t.text}</label>
          <button class="task-delete-btn">✕</button>
        </div>
      `);
      if (t.done) $li.addClass('completed');
      $list.append($li);
    });
    updateListIndexes();
  }

  // Salva array no servidor
  function saveTasks(tasks, reloadAfterSave = false) {
    if (isSaving) {
      debugLog('Tentativa de salvamento ignorada - já salvando');
      return;
    }
    isSaving = true;
    debugLog(`Salvando ${tasks.length} tarefas...`);
    const data = JSON.stringify(tasks);
    debugLog(`Dados a serem enviados: ${data.substring(0, 100)}...`);
    
    $.ajax({
      url: 'api/save.php',
      method: 'POST',
      contentType: 'application/json',
      data: data,
      success: function(response) {
        debugLog(`Resposta do servidor: ${JSON.stringify(response)}`);
        if (response.success) {
          currentTasks = tasks;
          showStatus('Tarefas salvas com sucesso', 'success');
          if (reloadAfterSave) {
            debugLog('Recarregando lista após salvamento...');
            setTimeout(function() {
              loadTasks();
            }, 200);
          }
        } else {
          showStatus('Erro ao salvar: ' + (response.error || 'Erro desconhecido'), 'error');
        }
        isSaving = false;
      },
      error: function(xhr, status, error) {
        debugLog(`Erro na requisição: ${status} - ${error}`);
        console.error('Erro na requisição:', status, error);
        try {
          const response = JSON.parse(xhr.responseText);
          showStatus('Erro ao salvar: ' + (response.error || 'Erro desconhecido'), 'error');
        } catch (e) {
          showStatus('Erro ao salvar. Verifique o console para mais detalhes.', 'error');
        }
        isSaving = false;
      }
    });
  }

  // Atualiza os índices dos elementos da lista
  function updateListIndexes() {
    $list.children().each(function(index) {
      $(this).attr('data-index', index);
    });
    debugLog(`Índices atualizados: ${$list.children().length} itens`);
  }

  // Adiciona nova tarefa
  function addNewTask() {
    const text = $('#new-task').val().trim();
    if (!text) {
      showStatus('Digite uma tarefa válida', 'error');
      return;
    }
    
    debugLog(`Adicionando nova tarefa: "${text}"`);
    
    const newTask = { text, done: false };
    const updatedTasks = [...currentTasks, newTask];
    
    renderTasks(updatedTasks);
    $('#new-task').val('');
    
    saveTasks(updatedTasks);
  }

  // Inicialização
  debugLog('Checklist carregado, inicializando...');
  loadTasks();

  // Adicionar nova tarefa
  $('#add-btn').click(function() {
    debugLog('Botão adicionar clicado');
    addNewTask();
  });
  
  // Permitir adicionar com Enter
  $('#new-task').keypress(function(e) {
    if (e.which === 13) {
      debugLog('Tecla Enter pressionada');
      addNewTask();
    }
  });

  // Delegação de eventos: toggle e delete
  $list.on('change', '.task-checkbox', function() {
    const $taskItem = $(this).closest('.task-item');
    const idx = +$taskItem.data('index');
    debugLog(`Checkbox alterado no índice ${idx}, valor: ${this.checked}`);
    
    if (this.checked) {
      $taskItem.addClass('completed');
    } else {
      $taskItem.removeClass('completed');
    }
    
    if (currentTasks[idx]) {
      currentTasks[idx].done = this.checked;
      saveTasks(currentTasks);
    } else {
      debugLog(`Erro: tarefa no índice ${idx} não encontrada`);
    }
  });

  $list.on('click', '.task-delete-btn', function() {
    const $taskItem = $(this).closest('.task-item');
    const idx = +$taskItem.data('index');
    debugLog(`Botão deletar clicado no índice ${idx}`);
    
    if (currentTasks[idx]) {
      currentTasks.splice(idx, 1);
      renderTasks(currentTasks);
      saveTasks(currentTasks);
    } else {
      debugLog(`Erro: tarefa no índice ${idx} não encontrada para deletar`);
    }
  });
  
  debugLog('Checklist inicializado com sucesso');
});
</script> 