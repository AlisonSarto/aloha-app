$(document).ready(function () {
  var vlr_pacote;
  var vlr_frete;
  var pedido;
  var produtos = [];
  var n_pedido;
  var blackFriday = false;
  var vlr_pacote_uni;
  var qtd_total;
  var boleto_bloqueado = false;
  var prazo_boleto = 0;
  var dias_antecedencia = 0; // valor padrão
  var data_min_original; // armazena a data mínima original para entrega

  // Função para adicionar dias úteis a uma data
  function adicionarDiasUteis(data, diasUteis) {
    var dataResultado = new Date(data);
    var diasAdicionados = 0;
    
    while (diasAdicionados < diasUteis) {
      dataResultado.setDate(dataResultado.getDate() + 1);
      
      // Verifica se é dia útil (segunda a sábado: 1-6, exceto domingo: 0)
      var diaSemana = dataResultado.getDay();
      if (diaSemana >= 1 && diaSemana <= 6) {
        diasAdicionados++;
      }
    }
    
    return dataResultado;
  }

  // Função para configurar data de entrega baseada no tipo
  function configurarDataEntrega(tipo_entrega = 'entrega') {
    var data = new Date();
    var data_original = new Date(); // mantém a data original para comparação
    
    if (tipo_entrega === 'entrega' && dias_antecedencia > 0) {
      // Para entrega, adiciona os dias úteis de antecedência
      // Se hoje for sábado, não adiciona dias.
      if (data.getDay() !== 6) {
        data = adicionarDiasUteis(data, dias_antecedencia);
      }
      console.log(`Configurando entrega: bloqueando ${dias_antecedencia} dia(s) útil(is) a partir de hoje`);
    } else {
      console.log(`Configurando retirada: sem bloqueio de data`);
    }
    // Para retirada ou quando dias_antecedencia = 0, permite data atual
    
    var dia = data.getDate();
    var mes = data.getMonth() + 1;
    var ano = data.getFullYear();
    
    if (mes < 10) {
      mes = '0' + mes;
    }
    if (dia < 10) {
      dia = '0' + dia;
    }
    
    var data_formatada = `${ano}-${mes}-${dia}`;
    $('#data-entrega').attr('min', data_formatada);
    $('#data-entrega').val(data_formatada);
    
    console.log(`Data mínima definida: ${data_formatada}`);
    
    // Armazena a data mínima para entrega
    if (tipo_entrega === 'entrega') {
      data_min_original = data_formatada;
    }
  }

  // Função para exibir modal de aviso
  function exibirModalAviso() {
    // Se for sábado, não exibe o modal de aviso
    if (new Date().getDay() === 6) {
      return;
    }
    
    if (dias_antecedencia > 0) {
      var titulo_modal = "Importante: Prazo de Entrega";
      var mensagem_modal = "";
      
      if (dias_antecedencia === 1) {
        mensagem_modal = "Devido à alta demanda de fim de ano, todos os pedidos terão prazo de entrega de até 1 dia útil (incluindo sábados). Faremos o possível para entregar o quanto antes! Pedimos que se programem com antecedência para receber seus pedidos dentro desse período.";
      } else {
        mensagem_modal = `Devido à alta demanda de fim de ano, todos os pedidos terão prazo de entrega de até ${dias_antecedencia} dias úteis (incluindo sábados). Faremos o possível para entregar o quanto antes! Pedimos que se programem com antecedência para receber seus pedidos dentro desse período.`;
      }
      
      // Atualiza o título do modal
      $('#modal-aviso-entrega h2').text(titulo_modal);
        
      $('#modal-texto-aviso').html(mensagem_modal);
      
      setTimeout(function() {
        $('#modal-aviso-entrega').fadeIn(300);
      }, 500);
    }
  }

  // Configuração inicial da data (será reconfigurada após carregar as configurações)
  configurarDataEntrega();

  // Verifica se é o dia da black friday
  var data = new Date();
  var dia = data.getDate();
  var mes = data.getMonth() + 1;
  var ano = data.getFullYear();
  if ((dia == 22 && mes == 11 && ano == 2024) || (dia == 23 && mes == 11 && ano == 2024)) {
    blackFriday = true;
  }

  if (blackFriday) {
    $('#black-friday').show();
  }

  // Função para calcular e atualizar o total do pedido
  function atualizarTotalPedido() {
    let total = 0;
    let qtd_total = 0;

    // Primeiro, soma todas as quantidades
    $('.produto-card').each(function () {
      const qtd = parseInt($(this).find('.input-quantidade').val()) || 0;
      qtd_total += qtd;
    });

    // Define o valor unitário conforme a tabela de preços
    if (vlr_pacote != 0) {
      vlr_pacote_uni = parseFloat(vlr_pacote);
    } else if (qtd_total <= 30) {
      vlr_pacote_uni = 28.00;
    } else if (qtd_total <= 100) {
      vlr_pacote_uni = 25.20;
    } else if (qtd_total > 100) {
      vlr_pacote_uni = 22.40;
    }

    // Agora calcula o subtotal de cada produto
    $('.produto-card').each(function () {
      const qtd = parseInt($(this).find('.input-quantidade').val()) || 0;
      const subtotal = qtd * vlr_pacote_uni;
      total += subtotal;
      $(this).find('.subtotal').text(`Subtotal: R$ ${subtotal.toFixed(2).replace('.', ',')}`);
    });

    $('#total-pedido').text(`R$ ${total.toFixed(2).replace('.', ',')}`);
  }

  // Função para incrementar quantidade
  function incrementarQtd(id) {
    const input = $(`.input-quantidade[data-id="${id}"]`);
    let valor = parseInt(input.val()) || 0;
    input.val(valor + 1).trigger('input');
  }

  // Função para decrementar quantidade
  function decrementarQtd(id) {
    const input = $(`.input-quantidade[data-id="${id}"]`);
    let valor = parseInt(input.val()) || 0;
    if (valor > 0) {
      input.val(valor - 1).trigger('input');
    }
  }

  $.ajax({
    url: '/api/login/profile',
    method: 'GET',
    success: function (data) {
      // Verifica se existem dados do profile interno
      if (data.profile_interno && data.profile_interno.length > 0) {
        vlr_pacote = data.profile_interno[0].vlr_pacote;
        vlr_frete = data.profile_interno[0].vlr_frete;
        n_pedido = data.profile_interno[0].n_pedidos;
        boleto_bloqueado = data.profile_interno[0].boleto_bloqueado;
        prazo_boleto = data.profile_interno[0].prazo_boleto;
      } else {
        // Valores padrão caso não tenha profile interno
        vlr_pacote = 0;
        vlr_frete = '5.00';
        n_pedido = 0;
        boleto_bloqueado = false;
        prazo_boleto = 0;
      }

      // Carrega configurações de entrega
      if (data.config_entrega && data.config_entrega.dias_antecedencia !== undefined) {
        dias_antecedencia = data.config_entrega.dias_antecedencia;
      } else {
        dias_antecedencia = 1; // valor padrão
      }
      
      console.log(`Configuração carregada: ${dias_antecedencia} dias de antecedência`);
      
      // Reconfigura a data com as configurações corretas
      configurarDataEntrega('entrega');

      // Agora que a configuração foi carregada, exibe o modal de aviso
      exibirModalAviso();

      if (prazo_boleto > 0 && boleto_bloqueado == 'false') {
        $('#tipo-pagamento').append(`
              <option value="boleto">Boleto</option>
            `);
        boleto = true;
      }

      if (n_pedido == 0) {
        $('#frete-gratis').show();
        vlr_frete = '0.00';
      }

      $('#vlr-frete').text(`R$ ${vlr_frete.replace('.', ',')}`);

      // Define o valor unitário do pacote para cálculos iniciais
      vlr_pacote_uni = parseFloat(vlr_pacote);
    },
    error: function () {
      window.location.href = '/login';
    }
  });

  $.ajax({
    url: '/api/produtos/view',
    method: 'GET',
    success: function (data) {
      produtos = data.produtos;

      produtos.forEach((produto, index) => {
        var emoji = '⚠️';
        var cor = '';

        var nomeProduto = produto.nome.replace(/^ALOHA\s+/i, '');

        if (produto.nome.toLowerCase().includes('coco')) {
          emoji = '🥥';
          cor = 'coco';
        } else if (produto.nome.toLowerCase().includes('pessego')) {
          emoji = '🍑';
          cor = 'pessego';
        } else if (produto.nome.toLowerCase().includes('maracujá')) {
          emoji = '🥭';
          cor = 'maracuja';
        } else if (produto.nome.toLowerCase().includes('melancia')) {
          emoji = '🍉';
          cor = 'melancia';
        } else if (produto.nome.toLowerCase().includes('maça')) {
          emoji = '🍏';
          cor = 'maca';
        } else if (produto.nome.toLowerCase().includes('morango')) {
          emoji = '🍓';
          cor = 'morango';
        } else if (produto.nome.toLowerCase().includes('laranja')) {
          emoji = '🍊';
          cor = 'laranja';
          if (blackFriday) {
            produto.nome = produto.nome + ' (10% off)';
          }
        } else if (produto.nome.toLowerCase().includes('pitaya')) {
          emoji = '🐉';
          cor = 'pitaya';
          if (blackFriday) {
            produto.nome = produto.nome + ' (10% off)';
          }
        } else if (produto.nome.toLowerCase().includes('limão')) {
          emoji = '🍋';
          cor = 'limao';
          if (blackFriday) {
            produto.nome = produto.nome + ' (10% off)';
          }
        }

        const delay = index * 100;
        const animationClass = `animate-fade-in delay-${index % 3 + 1}00`;

        $('#produtos').append(`
              <div class="produto-card ${cor} p-3 flex flex-col sm:flex-row items-center ${animationClass}">
                <div class="emoji-container flex-shrink-0">
                  ${emoji}
                </div>
                <div class="ml-0 sm:ml-4 flex-grow min-w-0 w-full">
                  <div class="font-medium text-sm">${nomeProduto}</div>
                  <div class="subtotal text-gray-500">Subtotal: R$ 0,00</div>
                </div>
                <div class="mt-2 sm:mt-0 sm:ml-4 w-full sm:w-auto">
                  <div class="quantidade-controls flex items-center justify-center" data-id="${produto.id}">
                    <button type="button" class="btn-qty btn-qty-minus" onclick="decrementarQtd(${produto.id})">-</button>
                    <div class="mx-2 w-16 input-quantidade-wrapper">
                      <input class="input-quantidade text-center" type="number" min="0" placeholder="0" data-id="${produto.id}">
                    </div>
                    <button type="button" class="btn-qty btn-qty-plus" onclick="incrementarQtd(${produto.id})">+</button>
                  </div>
                </div>
              </div>
            `);

      });

      // Adiciona evento para atualizar o subtotal quando a quantidade muda
      $(document).on('input', '.input-quantidade', function () {
        atualizarTotalPedido();
      });

      // Expõe as funções para o escopo global
      window.incrementarQtd = incrementarQtd;
      window.decrementarQtd = decrementarQtd;

      $('#main-container').fadeIn(300);
      
      // NÃO exibe o modal aqui - será exibido após carregar a configuração
    }
  });

  // Funcionalidade para fechar o modal de aviso
  $('#fechar-modal-aviso').click(function() {
    $('#modal-aviso-entrega').fadeOut(300);
  });

  // Fechar modal clicando no fundo
  $('#modal-aviso-entrega').click(function(e) {
    if (e.target === this) {
      $('#modal-aviso-entrega').fadeOut(300);
    }
  });

  // Função para atualizar dica do prazo de entrega
  function atualizarDicaPrazo(tipo_entrega) {
    if (new Date().getDay() === 6 && tipo_entrega === 'entrega') {
      $('#dica-prazo-entrega').hide();
      return;
    }
    if (tipo_entrega === 'entrega' && dias_antecedencia > 0) {
      var texto_dica = "";
      if (dias_antecedencia === 1) {
        texto_dica = "Entrega disponível a partir de amanhã";
      } else {
        texto_dica = `Entrega disponível a partir de ${dias_antecedencia} dias`;
      }
      $('#texto-dica-prazo').text(texto_dica);
      $('#dica-prazo-entrega').show();
    } else if (tipo_entrega === 'retirada') {
      $('#texto-dica-prazo').text("Retirada disponível a partir de hoje");
      $('#dica-prazo-entrega').show();
    } else {
      $('#dica-prazo-entrega').hide();
    }
  }

  // Evento para mudança no tipo de entrega
  $('#tipo-entrega').change(function () {
    var tipo_entrega = $(this).val();
    
    if (tipo_entrega === 'entrega') {
      $('#notif-frete').show();
      // Restaura o bloqueio de data para entrega
      configurarDataEntrega('entrega');
      atualizarDicaPrazo('entrega');
    } else {
      $('#notif-frete').hide();
      // Remove o bloqueio de data para retirada
      configurarDataEntrega('retirada');
      atualizarDicaPrazo('retirada');
    }
  });

  var page = 1;
  $('#btn-continuar').click(function () {
    $('#btn-continuar').attr('disabled', true);

    if (page === 1) {
      // Registra a qtd de cada produto que é > 0 em um novo array pedido
      pedido = [];
      produtos.forEach(produto => {
        const qtd = parseInt($(`.input-quantidade[data-id="${produto.id}"]`).val());
        if (qtd > 0) {
          pedido.push({
            id: produto.id,
            nome: produto.nome,
            qtd: qtd
          });
        }
      });

      // Se não tiver nenhum produto selecionado, retorna
      if (pedido.length === 0) {
        $('#btn-continuar').attr('disabled', false);
        alert('Selecione pelo menos um produto para continuar.');
        return false;
      }

      // Adiciona os produtos selecionados na próxima página
      $('#resumo-pacotes').html('');
      var valor_pedido = 0;

      // Verifica a quantidade total de pacotes
      qtd_total = 0;
      pedido.forEach(pacote => {
        qtd_total += pacote.qtd;
      });

      // Tabela de preços
      pedido.forEach(pacote => {
        if (vlr_pacote != 0) {
          vlr_pacote_uni = parseFloat(vlr_pacote);
        } else if (qtd_total <= 30) {
          vlr_pacote_uni = parseFloat(28.00);
        } else if (qtd_total <= 100) {
          vlr_pacote_uni = parseFloat(25.20);
        } else if (qtd_total > 100) {
          vlr_pacote_uni = parseFloat(22.40);
        }

        var subtotal = pacote.qtd * vlr_pacote_uni;

        $('#resumo-pacotes').append(`
              <tr>
                <td class="has-text-nowrap">${pacote.nome}</td>
                <td class="has-text-nowrap">${pacote.qtd}</td>
                <td class="has-text-nowrap">R$ ${(subtotal).toFixed(2).replace('.', ',')}</td>
              </tr>
            `);

        valor_pedido += subtotal;
      });

      $('#resumo-vlr-pedido').text(`R$ ${valor_pedido.toFixed(2).replace('.', ',')}`);
      $('#btn-voltar').show();

      // Atualiza a barra de progresso
      $('#progress-fill').css('width', '50%');
      $('#step-1').removeClass('active').addClass('completed');
      $('#step-2').addClass('active');
      $('#label-1').removeClass('active').addClass('completed');
      $('#label-2').addClass('active');
    }

    if (page === 2) {
      // Verifica se os selects tipo-pagamento e tipo-entrega estão preenchidos
      const tipo_pagamento = $('#tipo-pagamento').val();
      const tipo_entrega = $('#tipo-entrega').val();
      const data_entrega = $('#data-entrega').val();
      const obs = $('#obs').val();

      if (tipo_pagamento === null) {
        $('#btn-continuar').attr('disabled', false);
        alert('Selecione a forma de pagamento');
        return false;
      }

      if (tipo_entrega === null) {
        $('#btn-continuar').attr('disabled', false);
        alert('Selecione a forma de entrega');
        return false;
      }

      if (data_entrega === '') {
        $('#btn-continuar').attr('disabled', false);
        alert('Selecione a data de entrega');
        return false;
      }

      if (obs === '') {
        $('#btn-continuar').attr('disabled', false);
        alert('Informe o horário de funcionamento do local de entrega');
        return false;
      }

      // Atualiza a barra de progresso
      $('#progress-fill').css('width', '100%');
      $('#step-2').removeClass('active').addClass('completed');
      $('#step-3').addClass('active');
      $('#label-2').removeClass('active').addClass('completed');
      $('#label-3').addClass('active');
    }

    // resumo geral
    if (page === 2) {
      // Retrieve the selected value from the #tipo-entrega dropdown
      var tipoEntrega = $('#tipo-entrega option:selected').val();

      // Check if the selected value is "retirada"
      if (tipoEntrega === 'retirada') {
        $('#vlr-frete').text('R$ 0,00');
      } else {
        $('.if-frete').show();
      }

      $('#resumo-total-pacote').html(qtd_total + ' pacotes');
      $('#resumo-vlr-pacote').html('R$ ' + parseFloat(vlr_pacote_uni).toFixed(2).replace('.', ','));

      $('#resumo-vlr-pedido').html($('#total-pedido').html());
      $('#resumo-frete').text($('#vlr-frete').text());

      // Calculate the total
      var total = parseFloat($('#resumo-vlr-pedido').text().replace('R$ ', '').replace(',', '.')) + parseFloat($('#vlr-frete').text().replace('R$ ', '').replace(',', '.'));
      $('#resumo-total').text(`R$ ${total.toFixed(2).replace('.', ',')}`);

      // Update the remaining HTML elements
      $('#resumo-entrega').text($('#tipo-entrega option:selected').text());
      $('#resumo-pagamento').text($('#tipo-pagamento option:selected').text());
      $('#resumo-data').text($('#data-entrega').val().split('-').reverse().join('/'));
    }

    // finalizar pedido
    if (page === 3) {
      // Pagina de loading
      $('#page-3').fadeOut(200, function () {
        $('#btns').css('display', 'none');
        $(`#page-4`).fadeIn(200);
        $('html, body').animate({ scrollTop: 0 }, 200);
      });

      dados = {
        pedido: pedido,
        tipo_entrega: $('#tipo-entrega').val(),
        tipo_pagamento: $('#tipo-pagamento').val(),
        data_entrega: $('#data-entrega').val(),
        obs: $('#obs').val(),
      }

      $.ajax({
        url: '/api/pedidos/create',
        method: 'POST',
        data: dados,
        success: function (ress) {
          console.log(ress);
          $('#loading-icon').html('<i class="fas fa-check-circle loading-success"></i>');
          $('#loading-title').text('Pedido realizado com sucesso!');
          $('#loading-subtitle').html(`
                <div class="text-center">
                  <p class="mb-3">Aguarde que em breve nossa equipe analisará e confirmará o pedido.</p>
                  <p class="mb-3">Assim que estiver tudo certo, avisaremos você.</p>
                  <p class="font-medium">Equipe Aloha agradece a preferência! 🥂</p>
                </div>
              `);
        },
        error: function (data) {
          console.log(data);
          $('#loading-icon').html('<i class="fas fa-exclamation-circle text-red-500"></i>');
          $('#loading-title').text('Erro ao processar o pedido');
          $('#loading-subtitle').text('Por favor, tente novamente mais tarde.');
        }
      });

      return;
    }

    if (page === 2) {
      $('#btn-continuar').text('Finalizar pedido');
      $('#btn-continuar').addClass('btn-success');
    } else {
      $('#btn-continuar').text('Continuar');
      $('#btn-continuar').removeClass('btn-success');
    }

    $(`#page-${page}`).fadeOut(500, function () {
      page++;
      $(`#page-${page}`).fadeIn(500);
      $('#btn-continuar').attr('disabled', false);
      $('html, body').animate({ scrollTop: 0 }, 500);
    });
  });

  $('#btn-voltar').click(function () {
    if (page === 2) {
      $('#btn-voltar').hide();

      // Atualiza a barra de progresso
      $('#progress-fill').css('width', '0%');
      $('#step-1').addClass('active').removeClass('completed');
      $('#step-2').removeClass('active');
      $('#label-1').addClass('active').removeClass('completed');
      $('#label-2').removeClass('active');
    }

    if (page === 3) {
      // Atualiza a barra de progresso
      $('#progress-fill').css('width', '50%');
      $('#step-2').addClass('active').removeClass('completed');
      $('#step-3').removeClass('active');
      $('#label-2').addClass('active').removeClass('completed');
      $('#label-3').removeClass('active');
    }

    $(`#page-${page}`).fadeOut(500, function () {
      page--;
      $(`#page-${page}`).fadeIn(500);
      $('#btn-continuar').attr('disabled', false);
      $('#btn-continuar').text('Continuar');
      $('#btn-continuar').removeClass('btn-success');
    });
  });
});