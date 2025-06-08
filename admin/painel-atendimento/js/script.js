// Sample data
let users = [];
let leads = [];
let contacts = [];
let currentUser = null;

$(function () {
  $.ajax({
    url: '/admin/api/leads/view',
    method: 'GET',
    success: function (data) {
      leads = data.leads || [];
      contacts = data.registros || [];
      doLogin();
    }
  });
});

function doLogin() {
  let name = localStorage.getItem('userName') || '';
  while (!name) {
    name = prompt('Digite seu nome para acessar o painel:');
    if (name === null) return; // usuário cancelou
    name = name.trim();
  }
  localStorage.setItem('userName', name);
  const initial = name.charAt(0).toUpperCase();
  currentUser = { id: 1, name, initial };
  users = [currentUser];
  updateUserInfo();
  setupEventListeners();
  renderLeads();
}

$('#logout-btn').on('click', function () {
  localStorage.removeItem('userName');
  location.reload();
});

function updateUserInfo() {
  $('#user-name').text(currentUser.name);
  $('#user-initial').text(currentUser.initial);
}

function setupEventListeners() {
  // Tab switching (só existe leads)
  $('.tab-btn').on('click', function () {
    $('.tab-btn').removeClass('text-indigo-600 border-b-2 border-indigo-600').addClass('text-gray-500');
    $(this).addClass('text-indigo-600 border-b-2 border-indigo-600').removeClass('text-gray-500');
    $('.tab-content').removeClass('active');
    $('#leads').addClass('active');
  });

  // Logout
  $('#logout-btn').on('click', function () {
    location.reload();
  });

  // Close modals
  $('.close-modal').on('click', function () {
    $('#contact-modal').addClass('hidden');
    $('#history-modal').addClass('hidden');
  });

  // Form submission
  $('#contact-form').on('submit', handleContactFormSubmit);

  // Search and filter for leads
  $('#search-leads').on('input', renderLeads);
  $('#filter-leads').on('change', renderLeads);

  // Toggle contact type sections
  $('#contact-type-value').on('change', toggleContactSections);

  // Responded radio buttons
  $('input[name="responded"]').on('change', toggleReasonSection);
  $('input[name="purchased"]').on('change', toggleReasonSection);
}

function renderLeads() {
  const searchTerm = $('#search-leads').val() ? $('#search-leads').val().toLowerCase() : '';
  const filterValue = $('#filter-leads').val();

  let filteredLeads = leads.filter(lead => {
    if (searchTerm && !lead.nome.toLowerCase().includes(searchTerm)) {
      return false;
    }
    if (filterValue !== 'all' && lead.status !== filterValue) {
      return false;
    }
    return true;
  });

  const $leadsList = $('#leads-list');
  $leadsList.empty();

  if (filteredLeads.length === 0) {
    $leadsList.html(`
          <div class="col-span-full text-center py-8">
              <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
              <p class="text-gray-500">Nenhum lead encontrado</p>
          </div>
        `);
    return;
  }

  filteredLeads.forEach(lead => {
    const firstContactDate = lead.primeiro_contato.split('-').reverse().join('/');
    const hasContacts = contacts.some(contact =>
      contact.lead_id === lead.id
    );

    let statusBadge = '';
    switch (lead.status) {
      case 'novo':
        statusBadge = `<span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded whitespace-nowrap">Novo</span>`;
        break;
      case 'andamento':
        statusBadge = `<span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded whitespace-nowrap">Em andamento</span>`;
        break;
      case 'convertido':
        statusBadge = `<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded whitespace-nowrap">Convertido</span>`;
        break;
      case 'perdido':
        statusBadge = `<span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded whitespace-nowrap">Perdido</span>`;
        break;
    }

    const card = $(`
      <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-2xl transition-shadow duration-200">
        <div class="p-6">
          <div class="flex justify-between items-start mb-4">
            <div class="flex items-center space-x-3 w-full">
              <span class="lead-name text-lg font-bold text-gray-900 flex-1 transition-all duration-150 break-all" 
                    data-id="${lead.id}" 
                    contenteditable="false"
                    spellcheck="false"
                    style="outline: none; cursor: pointer;">
                ${lead.nome || 'Lead sem nome'}
              </span>
            </div>
            ${statusBadge}
          </div>
          <div class="space-y-2 mb-4">
            <div class="flex items-center text-gray-600">
              <div class="w-8 h-8 flex items-center justify-center rounded-full bg-indigo-50 mr-2">
                <i class="fas fa-phone-alt text-indigo-500"></i>
              </div>
              <span class="ml-1">${lead.telefone}</span>
              <a href="https://app.botconversa.com.br/16801/live-chat/all/${lead.telefone}" target="_blank" class="ml-3 text-green-500 hover:text-green-700 transition">
                <i class="fab fa-whatsapp text-xl"></i>
              </a>
            </div>
            <div class="flex items-center text-gray-600">
              <div class="w-8 h-8 flex items-center justify-center rounded-full bg-indigo-50 mr-2">
                <i class="fas fa-calendar-alt text-indigo-500"></i>
              </div>
              <span class="ml-1">Primeiro contato: <span class="font-medium text-gray-800">${firstContactDate}</span></span>
            </div>
          </div>
          <div class="flex space-x-2 mt-4">
            <button class="register-contact flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg font-semibold shadow transition" 
                    data-id="${lead.id}">
              <i class="fas fa-plus-circle mr-1"></i> Registrar Contato
            </button>
            ${hasContacts ?
              `<button class="view-history bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-3 rounded-lg shadow transition"
                data-id="${lead.id}">
                <i class="fas fa-history"></i>
              </button>` : ''
            }
          </div>
        </div>
      </div>
    `);

    card.find('.lead-name').on('dblclick', function () {
      const $this = $(this);
      $this.attr('contenteditable', 'true').focus();

      // Seleciona todo o texto ao entrar em edição
      document.execCommand('selectAll', false, null);

      // Ao pressionar Enter ou sair do foco, salva
      $this.on('keydown.editName', function (e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          $this.blur();
        }
        if (e.key === 'Escape') {
          e.preventDefault();
          $this.text(lead.nome); // volta ao valor anterior
          $this.blur();
        }
      });

      $this.on('blur.editName', function () {
        $this.attr('contenteditable', 'false');
        $this.off('.editName');
        const newName = $this.text().trim();
        if (newName && newName !== lead.nome) {
          lead.nome = newName;
      
          // AJAX para atualizar no servidor
          $.ajax({
            url: '/admin/api/leads/edit-nome',
            method: 'POST',
            data: { id: lead.id, nome: newName },
            success: function (data) {
              console.log('Nome atualizado com sucesso:', data);
              showToast('Nome do lead atualizado!');
              renderLeads();
            },
            error: function () {
              showToast('Erro ao atualizar nome no servidor!');
              $this.text(lead.nome); // restaura nome local se erro
            }
          });
      
        } else {
          $this.text(lead.nome); // restaura se vazio
        }
      });
    });

    card.find('.register-contact').on('click', function () {
      openContactModal(lead.id);
    });

    card.find('.view-history').on('click', function () {
      openHistoryModal(lead.id);
    });

    $leadsList.append(card);
  });
}

function openContactModal(clientId) {
  $('#contact-form')[0].reset();
  $('#client-id').val(clientId);
  $('#modal-title').text('Registrar Contato');
  toggleContactSections();
  $('#contact-modal').removeClass('hidden');
}

function toggleContactSections() {
  toggleReasonSection();
}
function toggleReasonSection() {
  const responded = $('input[name="responded"]:checked').val();
  const purchased = $('input[name="purchased"]:checked').val();
  const lost = $('input[name="lost"]:checked').val();

  // Se NÃO respondeu
  if (responded === 'no') {
    $('#purchased-section').addClass('hidden');
    $('#lost-section').addClass('hidden');
    // Sempre mostra "Observações"
    $('#observations-section label').text('Observações');
    $('#reason-section').addClass('hidden');
    $('#observations-section').removeClass('hidden');
    return;
  }

  // Se respondeu
  $('#purchased-section').removeClass('hidden');
  $('#observations-section').removeClass('hidden');

  // Se comprou
  if (purchased === 'yes') {
    $('#lost-section').addClass('hidden');
    $('#reason-section').addClass('hidden');
    $('#observations-section label').text('Observações');
    return;
  }

  // Se NÃO comprou
  $('#lost-section').removeClass('hidden');

  if (lost === 'yes') {
    // Muda o label para "Por que foi perdido?"
    $('#observations-section label').text('Por que foi perdido?');
    $('#reason-section').addClass('hidden');
  } else {
    // Volta para "Observações"
    $('#observations-section label').text('Observações');
    $('#reason-section').removeClass('hidden');
  }
}

function handleContactFormSubmit(e) {
  e.preventDefault();

  const lead_id = parseInt($('#client-id').val());
  const contactType = $('#contact-type').val();
  const respondeu = $('input[name="responded"]:checked').val() === 'yes';
  const comprou = $('input[name="purchased"]:checked').val() === 'yes';
  const perdemos = $('input[name="lost"]:checked').val() === 'yes';
  const observacao = $('#observations').val().trim();

  const newContact = {
    id: contacts.length + 1,
    lead_id,
    tipo: contactType,
    respondeu,
    comprou,
    perdemos,
    observacao,
    atendente: currentUser.name
  };

  $.ajax({
    url: '/admin/api/leads/add-registro',
    method: 'POST',
    data: newContact,
    success: function(response) {
      console.log('Contato registrado com sucesso:', response);

      $('#contact-modal').addClass('hidden');
      showToast('Contato registrado com sucesso!');

      // Puxa novamente os dados do servidor
      $.ajax({
        url: '/admin/api/leads/view',
        method: 'GET',
        success: function (data) {
          leads = data.leads || [];
          contacts = data.registros || [];
          renderLeads();

          // Se o histórico desse lead estiver aberto, atualize o histórico
          if (!$('#history-modal').hasClass('hidden')) {
            const abertoId = $('#contact-history').data('lead-id');
            if (abertoId == lead_id) {
              openHistoryModal(lead_id);
            }
          }
        },
        error: function() {
          showToast('Erro ao atualizar dados do servidor!');
        }
      });
    },
    error: function() {
      showToast('Erro ao registrar contato no servidor!');
    }
  });
}

function openHistoryModal(clientId) {
  const client = leads.find(l => l.id === clientId);

  const clientContacts = contacts.filter(c => c.lead_id === clientId)
    .sort((a, b) => new Date(b.date) - new Date(a.date));

  const $historyContainer = $('#contact-history');
  $historyContainer.empty();
  $historyContainer.data('lead-id', clientId);

  // Adiciona o contador de contatos no topo
  $historyContainer.append(`
    <div class="mb-4 text-center">
      <span class="font-semibold text-indigo-600">${clientContacts.length}</span> contato${clientContacts.length === 1 ? '' : 's'} foram feitos
    </div>
  `);

  if (clientContacts.length === 0) {
    $historyContainer.append(`
      <div class="text-center py-4">
        <p class="text-gray-500">Nenhum contato registrado</p>
      </div>
    `);
  } else {
    clientContacts.forEach(contact => {
      const contactDate = contact.data
        ? new Date(contact.data).toLocaleString('pt-BR')
        : '';

      let contactTypeIcon = '';
      switch (contact.tipo) {
        case 'mensagem':
          contactTypeIcon = '<i class="fab fa-whatsapp text-green-500"></i>';
          break;
        case 'ligacao':
          contactTypeIcon = '<i class="fas fa-phone-alt text-blue-500"></i>';
          break;
        default:
          contactTypeIcon = '<i class="fas fa-comment text-gray-500"></i>';
      }

      let statusInfo = '';
      if (contact.respondeu == 'false') {
        statusInfo = '<span class="text-red-500">Cliente não respondeu</span>';
      } else if (contact.perdemos == 'true') {
        statusInfo = '<span class="text-red-500">Perdido</span>';
      } else {
        statusInfo = '<span class="text-green-500">Cliente respondeu</span>';
        if (contact.comprou == 'true') {
          statusInfo += ' • <span class="text-green-500">Comprou</span>';
        } else {
          statusInfo += ' • <span class="text-red-500">Não comprou</span>';
        }
      }

      const $historyItem = $(`
        <div class="bg-gray-50 rounded-lg p-4">
          <div class="flex justify-between items-start">
              <div class="flex items-center">
                  ${contactTypeIcon}
                  <span class="ml-2 font-medium">${getContactTypeText(contact.tipo)}</span>
              </div>
              <span class="text-sm text-gray-500">
                <i class="fas fa-clock mr-1"></i>${contactDate}
              </span>
          </div>
          <div class="mt-2">
              ${statusInfo}
          </div>
          ${contact.observacao ? `
              <div class="mt-2 text-sm">
                  <span class="font-medium">Observações:</span> ${contact.observacao}
              </div>
          ` : ''}
          <div class="mt-2 text-xs text-gray-500">
              Registrado por ${contact.atendente}
          </div>
        </div>
      `);

      $historyContainer.append($historyItem);
    });
  }

  $('#history-modal').removeClass('hidden');
}

function showToast(message) {
  $('#toast-message').text(message);
  $('#toast').removeClass('translate-y-20 opacity-0');
  setTimeout(() => {
    $('#toast').addClass('translate-y-20 opacity-0');
  }, 3000);
}

function getContactTypeText(type) {
  switch (type) {
    case 'mensagem': return 'Mensagem';
    case 'ligacao': return 'Ligação';
    default: return 'Outro';
  }
}