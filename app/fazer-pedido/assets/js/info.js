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

  // Data de entrega, input #data-entrega coloca a data de hoje
  var data = new Date();
  var dia = data.getDate();
  var mes = data.getMonth() + 1;
  var ano = data.getFullYear();
  if (mes < 10) {
    mes = '0' + mes;
  }
  if (dia < 10) {
    dia = '0' + dia;
  }
  $('#data-entrega').attr('min', `${ano}-${mes}-${dia}`);
  $('#data-entrega').val(`${ano}-${mes}-${dia}`);

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
      vlr_pacote = data.profile_interno[0].vlr_pacote;
      vlr_frete = data.profile_interno[0].vlr_frete;
      n_pedido = data.profile_interno[0].n_pedidos;

      boleto_bloqueado = data.profile_interno[0].boleto_bloqueado;
      prazo_boleto = data.profile_interno[0].prazo_boleto;

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
      $('#resumo-data').text($('#data-entrega').val());
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
          $('#loading-icon').html('<i class="fas fa-check-circle loading-success fa-bounce"></i>');
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

  $('#tipo-entrega').change(function () {
    if ($(this).val() === 'entrega') {
      $('#notif-frete').show();
    } else {
      $('#notif-frete').hide();
    }
  });
});