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
// Gerenciador global do checklist
window.ChecklistManager = {
  $list: null,
  $statusMessage: null,
  $debugInfo: null,
  currentTasks: [],
  isLoading: false,
  isSaving: false,
  isInitialized: false,

  // Função para debug
  debugLog: function(message) {
    const timestamp = new Date().toLocaleTimeString();
    const logMessage = `[${timestamp}] ${message}`;
    if (this.$debugInfo) {
      this.$debugInfo.append(`${logMessage}<br>`);
    }
    console.log(logMessage);
  },

  // Função para mostrar mensagens de status
  showStatus: function(message, type = 'info') {
    if (this.$statusMessage) {
      this.$statusMessage.removeClass('error success').addClass(type).text(message).show();
      this.debugLog(`Status: ${message} (${type})`);
      if (type !== 'error') {
        setTimeout(() => this.$statusMessage.hide(), 3000);
      }
    }
  },

  // Carrega JSON e renderiza lista
  loadTasks: function() {
    if (this.isLoading) {
      this.debugLog('Tentativa de carregamento ignorada - já carregando');
      return;
    }
    this.isLoading = true;
    this.debugLog('Iniciando carregamento de tarefas...');
    
    const timestamp = new Date().getTime();
    const url = `tasks.json?_t=${timestamp}`;
    this.debugLog(`Fazendo requisição para: ${url}`);
    
    $.getJSON(url, function(tasks) {
      this.debugLog(`Resposta recebida: ${JSON.stringify(tasks).substring(0, 100)}...`);
      
      if (Array.isArray(tasks)) {
        this.currentTasks = tasks;
        this.renderTasks(tasks);
        this.showStatus(`Carregadas ${tasks.length} tarefas`, 'success');
        this.debugLog(`Lista renderizada com ${tasks.length} tarefas`);
        
        // Atualizar estado global
        if (window.AppState) {
          window.AppState.checklistData.tasks = tasks;
          window.AppState.checklistData.lastLoaded = new Date().getTime();
        }
      } else {
        this.currentTasks = [];
        this.renderTasks([]);
        this.showStatus('Formato de dados inválido', 'error');
        this.debugLog('Dados recebidos não são um array');
      }
      this.isLoading = false;
    }.bind(this)).fail(function(jqXHR, textStatus, errorThrown) {
      this.debugLog(`Erro no carregamento: ${textStatus} - ${errorThrown}`);
      console.error('Erro ao carregar tarefas:', textStatus, errorThrown);
      this.showStatus('Erro ao carregar tarefas. Verifique o console para mais detalhes.', 'error');
      this.currentTasks = [];
      this.renderTasks([]);
      this.isLoading = false;
    }.bind(this));
  },

  // Renderiza as tarefas na interface
  renderTasks: function(tasks) {
    if (!this.$list) return;
    
    this.$list.empty();
    tasks.forEach((t, i) => {
      const $li = $(`
        <div class="task-item" data-index="${i}">
          <input type="checkbox" class="task-checkbox" ${t.done ? 'checked' : ''}>
          <label class="task-label">${t.text}</label>
          <button class="task-delete-btn">✕</button>
        </div>
      `);
      if (t.done) $li.addClass('completed');
      this.$list.append($li);
    });
    this.updateListIndexes();
  },

  // Salva array no servidor
  saveTasks: function(tasks, reloadAfterSave = false) {
    if (this.isSaving) {
      this.debugLog('Tentativa de salvamento ignorada - já salvando');
      return;
    }
    this.isSaving = true;
    this.debugLog(`Salvando ${tasks.length} tarefas...`);
    const data = JSON.stringify(tasks);
    this.debugLog(`Dados a serem enviados: ${data.substring(0, 100)}...`);
    
    $.ajax({
      url: 'api/save.php',
      method: 'POST',
      contentType: 'application/json',
      data: data,
      success: function(response) {
        this.debugLog(`Resposta do servidor: ${JSON.stringify(response)}`);
        if (response.success) {
          this.currentTasks = tasks;
          this.showStatus('Tarefas salvas com sucesso', 'success');
          
          // Atualizar estado global
          if (window.AppState) {
            window.AppState.checklistData.tasks = tasks;
          }
          
          if (reloadAfterSave) {
            this.debugLog('Recarregando lista após salvamento...');
            setTimeout(function() {
              this.loadTasks();
            }.bind(this), 200);
          }
        } else {
          this.showStatus('Erro ao salvar: ' + (response.error || 'Erro desconhecido'), 'error');
        }
        this.isSaving = false;
      }.bind(this),
      error: function(xhr, status, error) {
        this.debugLog(`Erro na requisição: ${status} - ${error}`);
        console.error('Erro na requisição:', status, error);
        try {
          const response = JSON.parse(xhr.responseText);
          this.showStatus('Erro ao salvar: ' + (response.error || 'Erro desconhecido'), 'error');
        } catch (e) {
          this.showStatus('Erro ao salvar. Verifique o console para mais detalhes.', 'error');
        }
        this.isSaving = false;
      }.bind(this)
    });
  },

  // Atualiza os índices dos elementos da lista
  updateListIndexes: function() {
    if (!this.$list) return;
    
    this.$list.children().each(function(index) {
      $(this).attr('data-index', index);
    });
    this.debugLog(`Índices atualizados: ${this.$list.children().length} itens`);
  },

  // Adiciona nova tarefa
  addNewTask: function() {
    const text = $('#new-task').val().trim();
    if (!text) {
      this.showStatus('Digite uma tarefa válida', 'error');
      return;
    }
    
    this.debugLog(`Adicionando nova tarefa: "${text}"`);
    
    const newTask = { text, done: false };
    const updatedTasks = [...this.currentTasks, newTask];
    
    this.renderTasks(updatedTasks);
    $('#new-task').val('');
    
    this.saveTasks(updatedTasks);
  },

  // Inicializa o checklist
  initialize: function() {
    if (this.isInitialized) {
      this.debugLog('Checklist já inicializado, restaurando estado...');
      this.restoreState();
      return;
    }

    this.debugLog('Inicializando checklist...');
    
    // Referências aos elementos
    this.$list = $('#task-list');
    this.$statusMessage = $('#status-message');
    this.$debugInfo = $('#debug-info');
    
    // Verificar se temos dados em cache
    if (window.AppState && window.AppState.checklistData.tasks.length > 0) {
      this.debugLog('Restaurando tarefas do cache...');
      this.currentTasks = window.AppState.checklistData.tasks;
      this.renderTasks(this.currentTasks);
      this.showStatus(`Restauradas ${this.currentTasks.length} tarefas do cache`, 'success');
    } else {
      this.debugLog('Carregando tarefas do servidor...');
      this.loadTasks();
    }

    // Eventos
    this.bindEvents();
    
    this.isInitialized = true;
    this.debugLog('Checklist inicializado com sucesso');
  },

  // Restaura o estado do checklist
  restoreState: function() {
    this.debugLog('Restaurando estado do checklist...');
    
    // Restaurar referências aos elementos
    this.$list = $('#task-list');
    this.$statusMessage = $('#status-message');
    this.$debugInfo = $('#debug-info');
    
    // Restaurar tarefas se existirem
    if (this.currentTasks.length > 0) {
      this.renderTasks(this.currentTasks);
      this.debugLog(`Estado restaurado: ${this.currentTasks.length} tarefas`);
    } else if (window.AppState && window.AppState.checklistData.tasks.length > 0) {
      this.currentTasks = window.AppState.checklistData.tasks;
      this.renderTasks(this.currentTasks);
      this.debugLog(`Estado restaurado do cache: ${this.currentTasks.length} tarefas`);
    } else {
      this.debugLog('Nenhum estado para restaurar, carregando do servidor...');
      this.loadTasks();
    }
    
    // Rebind eventos
    this.bindEvents();
  },

  // Vincula eventos
  bindEvents: function() {
    // Remover eventos existentes para evitar duplicação
    $('#add-btn').off('click');
    $('#new-task').off('keypress');
    if (this.$list) {
      this.$list.off('change', '.task-checkbox');
      this.$list.off('click', '.task-delete-btn');
    }

    // Adicionar nova tarefa
    $('#add-btn').on('click', function() {
      this.debugLog('Botão adicionar clicado');
      this.addNewTask();
    }.bind(this));
    
    // Permitir adicionar com Enter
    $('#new-task').on('keypress', function(e) {
      if (e.which === 13) {
        this.debugLog('Tecla Enter pressionada');
        this.addNewTask();
      }
    }.bind(this));

    // Delegação de eventos: toggle e delete
    if (this.$list) {
      this.$list.on('change', '.task-checkbox', function() {
        const $taskItem = $(this).closest('.task-item');
        const idx = +$taskItem.data('index');
        this.debugLog(`Checkbox alterado no índice ${idx}, valor: ${this.checked}`);
        
        if (this.checked) {
          $taskItem.addClass('completed');
        } else {
          $taskItem.removeClass('completed');
        }
        
        if (this.currentTasks[idx]) {
          this.currentTasks[idx].done = this.checked;
          this.saveTasks(this.currentTasks);
        } else {
          this.debugLog(`Erro: tarefa no índice ${idx} não encontrada`);
        }
      }.bind(this));

      this.$list.on('click', '.task-delete-btn', function() {
        const $taskItem = $(this).closest('.task-item');
        const idx = +$taskItem.data('index');
        this.debugLog(`Botão deletar clicado no índice ${idx}`);
        
        if (this.currentTasks[idx]) {
          this.currentTasks.splice(idx, 1);
          this.renderTasks(this.currentTasks);
          this.saveTasks(this.currentTasks);
        } else {
          this.debugLog(`Erro: tarefa no índice ${idx} não encontrada para deletar`);
        }
      }.bind(this));
    }
  }
};

// Inicializar quando o documento estiver pronto
$(document).ready(function() {
  // Se já temos o AppState, inicializar imediatamente
  if (window.AppState && window.AppState.currentContent === 'checklist') {
    window.ChecklistManager.initialize();
  }
});
</script> 