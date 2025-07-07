<div class="about-container">
  <div class="about-header">
    <h2>ℹ️ Sobre o Projeto</h2>
    <p class="about-subtitle">Sistema de Checklist com Interface WhatsApp</p>
  </div>

  <div class="about-content">
    <div class="about-section">
      <h3>📋 Descrição</h3>
      <p>Este é um sistema de checklist desenvolvido com PHP e JavaScript, inspirado na interface do WhatsApp. O projeto permite gerenciar tarefas de forma simples e intuitiva, com persistência de dados em arquivo JSON.</p>
    </div>

    <div class="about-section">
      <h3>🚀 Funcionalidades</h3>
      <div class="features-grid">
        <div class="feature-item">
          <div class="feature-icon">➕</div>
          <div class="feature-text">
            <strong>Adicionar Tarefas</strong>
            <p>Crie novas tarefas facilmente</p>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon">✅</div>
          <div class="feature-text">
            <strong>Marcar Concluída</strong>
            <p>Marque tarefas como finalizadas</p>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon">🗑️</div>
          <div class="feature-text">
            <strong>Excluir Tarefas</strong>
            <p>Remova tarefas desnecessárias</p>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon">💾</div>
          <div class="feature-text">
            <strong>Persistência JSON</strong>
            <p>Dados salvos automaticamente</p>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon">📱</div>
          <div class="feature-text">
            <strong>Interface Responsiva</strong>
            <p>Funciona em qualquer dispositivo</p>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon">🔧</div>
          <div class="feature-text">
            <strong>Debug Integrado</strong>
            <p>Ferramentas de diagnóstico</p>
          </div>
        </div>
      </div>
    </div>

    <div class="about-section">
      <h3>🛠️ Tecnologias Utilizadas</h3>
      <div class="tech-stack">
        <div class="tech-item">
          <span class="tech-name">PHP</span>
          <span class="tech-desc">Backend e API</span>
        </div>
        <div class="tech-item">
          <span class="tech-name">JavaScript</span>
          <span class="tech-desc">Frontend e Interatividade</span>
        </div>
        <div class="tech-item">
          <span class="tech-name">jQuery</span>
          <span class="tech-desc">Manipulação DOM e AJAX</span>
        </div>
        <div class="tech-item">
          <span class="tech-name">JSON</span>
          <span class="tech-desc">Armazenamento de Dados</span>
        </div>
        <div class="tech-item">
          <span class="tech-name">CSS3</span>
          <span class="tech-desc">Estilização e Layout</span>
        </div>
        <div class="tech-item">
          <span class="tech-name">HTML5</span>
          <span class="tech-desc">Estrutura da Interface</span>
        </div>
      </div>
    </div>

    <div class="about-section">
      <h3>📁 Estrutura do Projeto</h3>
      <div class="file-structure">
        <div class="file-item">
          <span class="file-icon">📄</span>
          <span class="file-name">index.php</span>
          <span class="file-desc">Página principal com layout WhatsApp</span>
        </div>
        <div class="file-item">
          <span class="file-icon">📁</span>
          <span class="file-name">conteudos/</span>
          <span class="file-desc">Páginas carregadas dinamicamente</span>
        </div>
        <div class="file-item">
          <span class="file-icon">🔧</span>
          <span class="file-name">api/save.php</span>
          <span class="file-desc">API para salvar dados</span>
        </div>
        <div class="file-item">
          <span class="file-icon">📊</span>
          <span class="file-name">tasks.json</span>
          <span class="file-desc">Arquivo de dados das tarefas</span>
        </div>
      </div>
    </div>

    <div class="about-section">
      <h3>🎯 Como Usar</h3>
      <div class="usage-steps">
        <div class="step">
          <div class="step-number">1</div>
          <div class="step-content">
            <strong>Adicionar Tarefa</strong>
            <p>Digite o texto da tarefa e pressione Enter ou clique em "Adicionar"</p>
          </div>
        </div>
        <div class="step">
          <div class="step-number">2</div>
          <div class="step-content">
            <strong>Marcar Concluída</strong>
            <p>Clique na caixa de seleção ao lado da tarefa</p>
          </div>
        </div>
        <div class="step">
          <div class="step-number">3</div>
          <div class="step-content">
            <strong>Excluir Tarefa</strong>
            <p>Clique no botão "✕" ao lado da tarefa</p>
          </div>
        </div>
        <div class="step">
          <div class="step-number">4</div>
          <div class="step-content">
            <strong>Navegar</strong>
            <p>Use o menu lateral para acessar diferentes seções</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.about-container {
  max-width: 800px;
  margin: 0 auto;
}

.about-header {
  text-align: center;
  margin-bottom: 30px;
}

.about-header h2 {
  color: #111b21;
  margin-bottom: 10px;
}

.about-subtitle {
  color: #667781;
  font-size: 16px;
}

.about-content {
  display: grid;
  gap: 30px;
}

.about-section {
  background: #fff;
  border-radius: 8px;
  padding: 25px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.about-section h3 {
  color: #111b21;
  margin-bottom: 20px;
  font-size: 18px;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.feature-item {
  display: flex;
  align-items: center;
  padding: 15px;
  background: #f8f9fa;
  border-radius: 8px;
  border-left: 4px solid #00a884;
}

.feature-icon {
  font-size: 24px;
  margin-right: 15px;
}

.feature-text strong {
  display: block;
  color: #111b21;
  margin-bottom: 5px;
}

.feature-text p {
  color: #667781;
  font-size: 14px;
  margin: 0;
}

.tech-stack {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
}

.tech-item {
  display: flex;
  flex-direction: column;
  padding: 15px;
  background: #f8f9fa;
  border-radius: 8px;
  text-align: center;
}

.tech-name {
  font-weight: 600;
  color: #00a884;
  font-size: 16px;
  margin-bottom: 5px;
}

.tech-desc {
  color: #667781;
  font-size: 14px;
}

.file-structure {
  display: grid;
  gap: 15px;
}

.file-item {
  display: flex;
  align-items: center;
  padding: 12px;
  background: #f8f9fa;
  border-radius: 6px;
}

.file-icon {
  margin-right: 12px;
  font-size: 18px;
}

.file-name {
  font-weight: 600;
  color: #111b21;
  margin-right: 15px;
  min-width: 120px;
}

.file-desc {
  color: #667781;
  font-size: 14px;
}

.usage-steps {
  display: grid;
  gap: 20px;
}

.step {
  display: flex;
  align-items: flex-start;
  padding: 15px;
  background: #f8f9fa;
  border-radius: 8px;
}

.step-number {
  background: #00a884;
  color: white;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  margin-right: 15px;
  flex-shrink: 0;
}

.step-content strong {
  display: block;
  color: #111b21;
  margin-bottom: 5px;
}

.step-content p {
  color: #667781;
  margin: 0;
  font-size: 14px;
}

@media (max-width: 768px) {
  .features-grid {
    grid-template-columns: 1fr;
  }
  
  .tech-stack {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .file-item {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .file-name {
    margin-bottom: 5px;
  }
}
</style> 