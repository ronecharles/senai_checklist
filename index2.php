<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div>
  <div id="btn-carregar">TEste</div>
</div>


<div id="conteudo"></div>

  <script>

    function carregarConteudoFetch(url, targetId) {
      const container = document.getElementById(targetId);
      fetch(url)
        .then(response => {
          if (!response.ok) {
            throw new Error('HTTP error! status: ' + response.status);
          }
          return response.text();
        })
        .then(html => {
          container.innerHTML = html;
          inicializarChecklist(); // Chama a função para ligar os eventos
        })
        .catch(err => {
          container.innerHTML = 'Erro ao carregar: ' + err.message;
        });
    }

    document.getElementById('btn-carregar')
      .addEventListener('click', () => carregarConteudoFetch('conteudos/checklist.html', 'conteudo'));

    // Função para inicializar o checklist
    function inicializarChecklist() {
      const $list = $('#task-list');

      function loadTasks() {
        const timestamp = new Date().getTime();
        $.getJSON('tasks.json?_t=' + timestamp, function(tasks) {
          $list.empty();
          if (Array.isArray(tasks)) {
            tasks.forEach((t, i) => {
              const $li = $(
                `<li data-index="${i}">
                  <input type="checkbox" ${t.done ? 'checked' : ''}>
                  <label>${t.text}</label>
                  <button class="del-btn">✕</button>
                </li>`
              );
              if (t.done) $li.addClass('completed');
              $list.append($li);
            });
          }
        }).fail(function(jqXHR, textStatus, errorThrown) {
          console.error('Erro ao carregar tarefas:', textStatus, errorThrown);
          alert('Erro ao carregar tarefas. Verifique o console para mais detalhes.');
        });
      }

      function saveTasks(tasks, reloadAfterSave = false) {
        $.ajax({
          url: 'api/save.php',
          method: 'POST',
          contentType: 'application/json',
          data: JSON.stringify(tasks),
          success: function(response) {
            if (response.success) {
              if (reloadAfterSave) {
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

      function getCurrentTasks(cb) {
        const timestamp = new Date().getTime();
        $.getJSON('conteudos/tasks.json?_t=' + timestamp, function(tasks) {
          if (Array.isArray(tasks)) {
            cb(tasks);
          } else {
            cb([]);
          }
        }).fail(function() {
          cb([]);
        });
      }

      function updateListIndexes() {
        $list.children().each(function(index) {
          $(this).attr('data-index', index);
        });
      }

      loadTasks();

      $('#conteudo').on('click', '#add-btn', function() {
        const text = $('#new-task').val().trim();
        if (!text) return;

        const newTask = { text, done: false };
        const $li = $(
          `<li data-index="${$list.children().length}">
            <input type="checkbox" ${newTask.done ? 'checked' : ''}>
            <label>${newTask.text}</label>
            <button class="del-btn">✕</button>
          </li>`
        );
        $list.append($li);
        updateListIndexes();
        $('#new-task').val('');

        getCurrentTasks(tasks => {
          tasks.push(newTask);
          saveTasks(tasks);
        });
      });

      $list.on('change', 'input[type=checkbox]', function() {
        const $li = $(this).closest('li');
        const idx = +$li.data('index');

        if (this.checked) {
          $li.addClass('completed');
        } else {
          $li.removeClass('completed');
        }

        getCurrentTasks(tasks => {
          tasks[idx].done = this.checked;
          saveTasks(tasks);
        });
      });

      $list.on('click', '.del-btn', function() {
        const $li = $(this).closest('li');
        const idx = +$li.data('index');

        $li.remove();
        updateListIndexes();

        getCurrentTasks(tasks => {
          tasks.splice(idx, 1);
          saveTasks(tasks);
        });
      });
    }
  </script>


</body>
</html>