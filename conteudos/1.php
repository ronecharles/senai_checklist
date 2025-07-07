<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Checklist em JSON</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body { font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; }
    h1 { text-align: center; }
    ul { list-style: none; padding: 0; }
    li { display: flex; align-items: center; margin: 8px 0; }
    li input[type="text"] { flex: 1; margin-right: 8px; padding: 4px; }
    li.completed label { text-decoration: line-through; color: #888; }
    button { padding: 6px 12px; margin-left: 4px; }
    #new-task { width: calc(100% - 100px); padding: 6px; }
  </style>
</head>
<body>
  <h1>Checklist</h1>

  <div>
    <input id="new-task" type="text" placeholder="Nova tarefa…">
    <button id="add-btn">Adicionar</button>
  </div>

  <ul id="task-list"></ul>

  <script>
    const $list = $('#task-list');

    // Carrega JSON e renderiza lista
    function loadTasks() {
      // Adiciona timestamp para evitar cache
      const timestamp = new Date().getTime();
      $.getJSON('tasks.json?_t=' + timestamp, function(tasks) {
        $list.empty();
        if (Array.isArray(tasks)) {
          tasks.forEach((t, i) => {
            const $li = $(`
              <li data-index="${i}">
                <input type="checkbox" ${t.done ? 'checked' : ''}>
                <label>${t.text}</label>
                <button class="del-btn">✕</button>
              </li>
            `);
            if (t.done) $li.addClass('completed');
            $list.append($li);
          });
        }
      }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error('Erro ao carregar tarefas:', textStatus, errorThrown);
        alert('Erro ao carregar tarefas. Verifique o console para mais detalhes.');
      });
    }

    // Salva array no servidor
    function saveTasks(tasks, reloadAfterSave = false) {
      $.ajax({
        url: 'api/save.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(tasks),
        success: function(response) {
          if (response.success) {
            if (reloadAfterSave) {
              // Pequeno delay para garantir que o arquivo foi escrito completamente
              setTimeout(function() {
                loadTasks();
              }, 100);
            }
          } else {
            alert('Erro ao salvar: ' + (response.error || 'Erro desconhecido'));
          }
        },
        error: function(xhr, status, error) {
          console.error('Erro na requisição:', status, error);
          try {
            const response = JSON.parse(xhr.responseText);
            alert('Erro ao salvar: ' + (response.error || 'Erro desconhecido'));
          } catch (e) {
            alert('Erro ao salvar. Verifique o console para mais detalhes.');
          }
        }
      });
    }

    // Recupera dados atuais, faz callback com array
    function getCurrentTasks(cb) {
      // Adiciona timestamp para evitar cache
      const timestamp = new Date().getTime();
      $.getJSON('tasks.json?_t=' + timestamp, function(tasks) {
        if (Array.isArray(tasks)) {
          cb(tasks);
        } else {
          cb([]);
        }
      }).fail(function() {
        cb([]);
      });
    }

    // Atualiza os índices dos elementos da lista
    function updateListIndexes() {
      $list.children().each(function(index) {
        $(this).attr('data-index', index);
      });
    }

    // Ao carregar página
    $(function() {
      loadTasks();

      // Adicionar nova tarefa
      $('#add-btn').click(function() {
        const text = $('#new-task').val().trim();
        if (!text) return;
        
        // Adiciona a tarefa imediatamente na interface
        const newTask = { text, done: false };
        const $li = $(`
          <li data-index="${$list.children().length}">
            <input type="checkbox" ${newTask.done ? 'checked' : ''}>
            <label>${newTask.text}</label>
            <button class="del-btn">✕</button>
          </li>
        `);
        $list.append($li);
        updateListIndexes(); // Atualiza os índices
        $('#new-task').val('');
        
        // Salva no servidor
        getCurrentTasks(tasks => {
          tasks.push(newTask);
          saveTasks(tasks);
        });
      });

      // Delegação de eventos: toggle e delete
      $list.on('change', 'input[type=checkbox]', function() {
        const $li = $(this).closest('li');
        const idx = +$li.data('index');
        
        // Atualiza imediatamente a interface
        if (this.checked) {
          $li.addClass('completed');
        } else {
          $li.removeClass('completed');
        }
        
        // Salva no servidor
        getCurrentTasks(tasks => {
          tasks[idx].done = this.checked;
          saveTasks(tasks);
        });
      });

      $list.on('click', '.del-btn', function() {
        const $li = $(this).closest('li');
        const idx = +$li.data('index');
        
        // Remove imediatamente da interface
        $li.remove();
        updateListIndexes();
        
        // Salva no servidor
        getCurrentTasks(tasks => {
          tasks.splice(idx, 1);
          saveTasks(tasks);
        });
      });
    });
  </script>
</body>
</html>
