<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Checklist em JSON</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: #f0f2f5;
      height: 100vh;
      overflow: hidden;
    }

    .app-container {
      display: flex;
      height: 100vh;
      background: #fff;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    /* Menu Lateral Esquerdo */
    .sidebar {
      width: 350px;
      background: #fff;
      border-right: 1px solid #e9edef;
      display: flex;
      flex-direction: column;
      position: relative;
    }

    .sidebar-header {
      background: #f0f2f5;
      padding: 16px;
      border-bottom: 1px solid #e9edef;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .sidebar-title {
      font-size: 18px;
      font-weight: 600;
      color: #111b21;
    }

    .menu-toggle {
      display: none;
      background: none;
      border: none;
      font-size: 20px;
      cursor: pointer;
      color: #54656f;
    }

    .menu-items {
      flex: 1;
      overflow-y: auto;
    }

    .menu-item {
      display: flex;
      align-items: center;
      padding: 16px;
      cursor: pointer;
      transition: background-color 0.2s;
      border-bottom: 1px solid #f0f2f5;
    }

    .menu-item:hover {
      background: #f5f6f6;
    }

    .menu-item.active {
      background: #e9edef;
      border-left: 3px solid #00a884;
    }

    .menu-item-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #00a884;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 12px;
      color: white;
      font-size: 18px;
    }

    .menu-item-content {
      flex: 1;
    }

    .menu-item-title {
      font-weight: 500;
      color: #111b21;
      margin-bottom: 2px;
    }

    .menu-item-subtitle {
      font-size: 13px;
      color: #667781;
    }

    /* Área de Conteúdo Principal */
    .main-content {
      flex: 1;
      display: flex;
      flex-direction: column;
      background: #fff;
    }

    .content-header {
      background: #f0f2f5;
      padding: 16px;
      border-bottom: 1px solid #e9edef;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .content-title {
      font-size: 18px;
      font-weight: 600;
      color: #111b21;
    }

    .content-area {
      flex: 1;
      padding: 20px;
      overflow-y: auto;
      background: #fff;
    }

    /* Loading */
    .loading {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 200px;
      color: #667781;
    }

    .loading::after {
      content: '';
      width: 20px;
      height: 20px;
      border: 2px solid #e9edef;
      border-top: 2px solid #00a884;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-left: 10px;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* Responsividade */
    @media (max-width: 768px) {
      .sidebar {
        position: fixed;
        left: -350px;
        top: 0;
        height: 100vh;
        z-index: 1000;
        transition: left 0.3s ease;
      }

      .sidebar.open {
        left: 0;
      }

      .menu-toggle {
        display: block;
      }

      .main-content {
        margin-left: 0;
      }
    }

    /* Animações */
    .fade-in {
      animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="app-container">
    <!-- Menu Lateral Esquerdo -->
    <div class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <div class="sidebar-title">Menu Principal</div>
        <button class="menu-toggle" id="menuToggle">☰</button>
      </div>
      
      <div class="menu-items">
        <div class="menu-item active" data-content="checklist">
          <div class="menu-item-icon">✓</div>
          <div class="menu-item-content">
            <div class="menu-item-title">Checklist</div>
            <div class="menu-item-subtitle">Gerenciar tarefas</div>
          </div>
        </div>
        
        <div class="menu-item" data-content="debug">
          <div class="menu-item-icon">🔧</div>
          <div class="menu-item-content">
            <div class="menu-item-title">Debug</div>
            <div class="menu-item-subtitle">Informações do sistema</div>
          </div>
        </div>
        
        <div class="menu-item" data-content="about">
          <div class="menu-item-icon">ℹ️</div>
          <div class="menu-item-content">
            <div class="menu-item-title">Sobre</div>
            <div class="menu-item-subtitle">Informações do projeto</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Área de Conteúdo Principal -->
    <div class="main-content">
      <div class="content-header">
        <div class="content-title" id="contentTitle">Checklist</div>
      </div>
      
      <div class="content-area">
        <div id="content-container">
          <div class="loading">Carregando...</div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Estado global da aplicação
    window.AppState = {
      currentContent: 'checklist',
      isLoading: false,
      checklistData: {
        tasks: [],
        isLoading: false,
        isSaving: false,
        lastLoaded: null
      },
      contentCache: {} // Cache para conteúdo carregado
    };

    // Função para carregar conteúdo externo
    function loadContent(contentId) {
      if (AppState.isLoading) return;
      
      AppState.isLoading = true;
      AppState.currentContent = contentId;
      
      // Mostrar loading
      $('#content-container').html('<div class="loading">Carregando...</div>');
      
      // Atualizar título
      const title = $(`.menu-item[data-content="${contentId}"] .menu-item-title`).text();
      $('#contentTitle').text(title);
      
      // Verificar se já temos o conteúdo em cache
      if (AppState.contentCache[contentId]) {
        $('#content-container').html(AppState.contentCache[contentId]).addClass('fade-in');
        AppState.isLoading = false;
        
        // Se for checklist, restaurar estado
        if (contentId === 'checklist') {
          restoreChecklistState();
        }
        return;
      }
      
      // Carregar conteúdo via AJAX
      $.ajax({
        url: `conteudos/${contentId}.php`,
        method: 'GET',
        success: function(response) {
          // Armazenar no cache
          AppState.contentCache[contentId] = response;
          
          $('#content-container').html(response).addClass('fade-in');
          AppState.isLoading = false;
          
          // Se for checklist, inicializar
          if (contentId === 'checklist') {
            initializeChecklist();
          }
        },
        error: function(xhr, status, error) {
          $('#content-container').html(`
            <div style="text-align: center; padding: 50px; color: #667781;">
              <h3>Erro ao carregar conteúdo</h3>
              <p>Não foi possível carregar a página solicitada.</p>
              <p><small>Erro: ${status} - ${error}</small></p>
            </div>
          `);
          AppState.isLoading = false;
        }
      });
    }

    // Função para inicializar o checklist
    function initializeChecklist() {
      if (typeof window.ChecklistManager !== 'undefined') {
        window.ChecklistManager.initialize();
      }
    }

    // Função para restaurar estado do checklist
    function restoreChecklistState() {
      if (typeof window.ChecklistManager !== 'undefined') {
        window.ChecklistManager.restoreState();
      }
    }

    // Navegação do menu
    function showContent(contentId) {
      $('.menu-item').removeClass('active');
      $(`.menu-item[data-content="${contentId}"]`).addClass('active');
      
      loadContent(contentId);
      
      // Fecha menu no mobile
      if ($(window).width() <= 768) {
        $('#sidebar').removeClass('open');
      }
    }

    // Toggle do menu mobile
    function toggleMenu() {
      $('#sidebar').toggleClass('open');
    }

    // Ao carregar página
    $(function() {
      console.log('Aplicação carregada, inicializando...');
      
      // Navegação do menu
      $('.menu-item').click(function() {
        const contentId = $(this).data('content');
        showContent(contentId);
      });

      // Toggle do menu mobile
      $('#menuToggle').click(toggleMenu);

      // Carregar conteúdo inicial
      loadContent('checklist');
      
      console.log('Aplicação inicializada com sucesso');
    });
  </script>
</body>
</html>
