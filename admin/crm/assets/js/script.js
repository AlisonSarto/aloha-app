let clientsData = [];
const $sidebar = $('#sidebar');
const $mainContent = $('#mainContent');
const $toggleSidebarBtn = $('#toggleSidebar');
const $mobileMenuBtn = $('#mobileMenuBtn');
const $clientPanel = $('#clientPanel');
const $closePanel = $('#closePanel');
const $kanbanCards = $('.kanban-card');

let sidebarExpanded = false;
let currentClientId = null;

function toggleSidebar() {
  sidebarExpanded = !sidebarExpanded;
  $sidebar.toggleClass('expanded', sidebarExpanded);
  $mainContent.css('margin-left', sidebarExpanded ? '240px' : '80px');
  $toggleSidebarBtn.html(
    sidebarExpanded
      ? '<i class="fas fa-chevron-left text-gray-500"></i>'
      : '<i class="fas fa-chevron-right text-gray-500"></i>'
  );
}

function toggleMobileMenu() {
  $sidebar.toggleClass('expanded');
}

function openClientPanel(clientId) {
  currentClientId = clientId;
  const client = clientsData.find(c => c.id === clientId);

  if (client) {
    $('#clientName').text(client.name);
    $('#clientFullName').val(client.name);
    $('#clientBusinessName').val(client.business);
    $('#clientDocument').val(client.document);
    $('#clientPhone').val('+' + client.phone);

    $('#conversationSummary').text(client.analysis.summary);

    $('#sentimentFill').css('width', `${client.analysis.sentiment}%`);
    $('#sentimentPercentage').text(`${client.analysis.sentiment}%`);

    const $recommendationsList = $('#recommendationsList').empty();
    client.analysis.recommendations.forEach(recommendation => {
      $('<li>').text(recommendation).appendTo($recommendationsList);
    });

    const $conversationHistory = $('#conversationHistory').empty();
    client.conversations.forEach(day => {
      const dateObj = new Date(day.date);
      const formattedDate = dateObj.toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      });
      $('<div>')
        .addClass('text-center my-4')
        .append(
          $('<span>')
            .addClass('text-xs bg-gray-100 text-gray-500 px-3 py-1 rounded-full')
            .text(formattedDate)
        )
        .appendTo($conversationHistory);

      const $messagesDiv = $('<div>').addClass('flex flex-col');
      day.messages.forEach(message => {
        const $messageDiv = $('<div>')
          .addClass(`message ${message.type} fade-in`)
          .append(
            $('<div>')
              .addClass('flex flex-col')
              .append(
                $('<p>').addClass('text-sm').text(message.text),
                $('<span>').addClass('text-xs text-gray-500 self-end mt-1').text(message.time)
              )
          );
        $messagesDiv.append($messageDiv);
      });
      $conversationHistory.append($messagesDiv);
      $conversationHistory.scrollTop($conversationHistory[0].scrollHeight);
    });

    const $tasksList = $('#tasksList').empty();
    client.tasks.forEach(task => {
      const $taskDiv = $('<div>')
        .addClass(`task-item p-3 rounded-lg flex items-start ${task.completed ? 'completed' : ''}`)
        .attr('data-id', task.id);

      const $checkbox = $('<div>')
        .addClass(`task-checkbox mr-3 mt-0.5 ${task.completed ? 'checked' : ''}`)
        .html(task.completed ? '<i class="fas fa-check text-white text-xs"></i>' : '');

      const $taskContent = $('<div>').addClass('flex-1');
      $taskContent.append(
        $('<p>').addClass('text-sm font-medium').text(task.text),
        $('<p>').addClass('text-xs text-gray-500 mt-1').text(`Vencimento: ${formatDate(task.dueDate)}`)
      );

      $taskDiv.append($checkbox, $taskContent).appendTo($tasksList);

      $checkbox.on('click', function () {
        const taskId = parseInt($taskDiv.data('id'));
        const clientIndex = clientsData.findIndex(c => c.id === currentClientId);
        const taskIndex = clientsData[clientIndex].tasks.findIndex(t => t.id === taskId);

        if (taskIndex !== -1) {
          const completed = !(clientsData[clientIndex].tasks[taskIndex].completed);
          clientsData[clientIndex].tasks[taskIndex].completed = completed;
          $checkbox.toggleClass('checked', completed);
          $taskDiv.toggleClass('completed', completed);
          $checkbox.html(completed ? '<i class="fas fa-check text-white text-xs"></i>' : '');
        }
      });
    });

    $clientPanel.addClass('open');
  }
}

function closeClientPanel() {
  $clientPanel.removeClass('open');
  currentClientId = null;
}

function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString('pt-BR');
}

function initDragAndDrop() {

  //!
  return;

  const $cards = $('.kanban-card');
  const $columns = $('.kanban-column');

  $cards.attr('draggable', true);

  $cards.on('dragstart', function (e) {
    $(this).addClass('opacity-50');
    e.originalEvent.dataTransfer.setData('text/plain', $(this).data('id'));
  });

  $cards.on('dragend', function () {
    $(this).removeClass('opacity-50');
  });

  $columns.on('dragover', function (e) {
    e.preventDefault();
    $(this).addClass('bg-gray-100');
  });

  $columns.on('dragleave', function () {
    $(this).removeClass('bg-gray-100');
  });

  $columns.on('drop', function (e) {
    e.preventDefault();
    $(this).removeClass('bg-gray-100');
    const cardId = parseInt(e.originalEvent.dataTransfer.getData('text/plain'));
    const $card = $(`.kanban-card[data-id="${cardId}"]`);
    const targetColumnId = this.id;

    if ($card.length) {
      $card.removeClass('novo interessado pedido-realizado cliente-ativo inativo desqualificado').addClass(targetColumnId);
      const clientIndex = clientsData.findIndex(c => c.id === cardId);
      if (clientIndex !== -1) clientsData[clientIndex].status = targetColumnId;
      $(this).append($card);
      updateColumnCounters();
    }
  });
}

function updateColumnCounters() {
  $('.kanban-column').each(function () {
    const $counter = $(this).find('span').first();
    $counter.text($(this).find('.kanban-card').length);
  });
}

// Event Listeners
$toggleSidebarBtn.on('click', toggleSidebar);
$mobileMenuBtn.on('click', toggleMobileMenu);
$closePanel.on('click', closeClientPanel);

$kanbanCards.on('click', function () {
  openClientPanel(parseInt($(this).data('id')));
});

$('#whatsappBtn').on('click', function () {
  const phone = $('#clientPhone').val().replace(/\D/g, '');
  if (phone) window.open(`https://app.botconversa.com.br/16801/live-chat/all/+${phone}`, '_blank');
});

initDragAndDrop();

function handleResize() {
  if (window.innerWidth < 768) {
    $sidebar.removeClass('expanded');
    sidebarExpanded = false;
    $mainContent.css('margin-left', '0');
  } else {
    $mainContent.css('margin-left', sidebarExpanded ? '240px' : '80px');
  }
}

function renderKanbanCards() {
  // Limpa todas as colunas
  $('.kanban-column').each(function () {
    $(this).find('.kanban-card').remove();
  });

  clientsData.forEach(client => {
    const $card = $('<div>')
      .addClass(`kanban-card ${client.status}`)
      .attr('data-id', client.id)
      .append(
        $('<div>').addClass('flex justify-between items-start mb-2').append(
          $('<h4>').addClass('font-medium').text(client.name)
        ),
        $('<p>').addClass('text-sm text-gray-500 mb-2').text(client.business),
        $('<div>').addClass('flex justify-between items-center').append(
          $('<span>').addClass('text-xs text-gray-500').text(
            client.phone ? `+${client.phone}` : ''
          ),
          $('<button>')
            .addClass('text-green-500 hover:bg-green-50 p-1 rounded-full')
            .append($('<i>').addClass('fab fa-whatsapp'))
        )
      );

    // Evento de abrir painel do cliente
    $card.on('click', function () {
      openClientPanel(client.id);
    });

    // Adiciona o card na coluna correspondente
    $(`#${client.status}`).append($card);
  });

  // Atualiza contadores
  updateColumnCounters();

  // Reaplica drag and drop
  initDragAndDrop();
}

// Chame a função ao carregar a página
$(document).ready(function () {
  $.ajax({
    url: '/admin/api/crm/view',
    method: 'GET',
    success: function (data) {
      clientsData = data.crm;
      renderKanbanCards();
    },
    error: function () {
      alert('Erro ao carregar dados do CRM.');
    }
  });
});

$(window).on('resize', handleResize);
handleResize();