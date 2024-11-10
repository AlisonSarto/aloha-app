$(document).ready(function() {

  var vlr_pacote;
  var vlr_frete;
  var pedido;
  var produtos = [];
  var n_pedido;

  $.ajax({
    url: '/api/login/profile',
    method: 'GET',
    success: function(data) {
      vlr_pacote = data.profile_interno[0].vlr_pacote;
      vlr_frete = data.profile_interno[0].vlr_frete;
      n_pedido = data.profile_interno[0].n_pedidos;

      if (n_pedido == 0) {
        $('#frete-gratis').show();
        vlr_frete = '0.00';
      }

      $('#vlr-cada-pacote').text(`R$ ${vlr_pacote.replace('.', ',')}`);	
      $('#vlr-frete').text(`R$ ${vlr_frete.replace('.', ',')}`);
    },
    error: function() {
      window.location.href = '/login';
    }
  })

  $.ajax({
    url: '/api/produtos/view',
    method: 'GET',
    success: function(data) {
      produtos = data.produtos;
      
      produtos.forEach(produto => {

        var emoji = '⚠️';
        var cor = '';
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
        }

        $('#produtos').append(`
          <div class="box ${cor}">
            <div class="columns is-mobile is-gapless">

              <div class="column is-2 is-align-content-center">
                <span class="is-size-3" id="emoji">${emoji}</span> 
              </div>

              <div class="column is-6 is-align-content-center" style="margin-left: 7px">
                <span class="is-size-7">${produto.nome}</span>
              </div>

              <div class="column">
                <div class="quantidade" data-id="${produto.id}">
                  <input class="input qtd" type="number" value="0">
                </div>
              </div>
              
            </div>
          </div>
        `);
      });

      $('main').show();
    }
  });

  var page = 1;
  $('#btn-continuar').click(function() {

    $('#btn-continuar').attr('disabled', true);

    if (page === 1) {
      //? Registra a qtd de cada produto que é > 0 em um novo array pedido
      pedido = [];
      produtos.forEach(produto => {
        const qtd = parseInt($(`.quantidade[data-id="${produto.id}"] .qtd`).val());
        if (qtd > 0) {
          pedido.push({
            id: produto.id,
            nome: produto.nome,
            qtd: qtd
          });
        }
      });

      //? Se não tiver nenhum produto selecionado, retorna
      if (pedido.length === 0) {
        $('#btn-continuar').attr('disabled', false);
        return false;
      }

      //? Adiciona os produtos selecionados na próxima página
      $('#resumo-pacotes').html('');
      var valor_pedido = 0;
      pedido.forEach(produto => {

        var emoji = '⚠️';
        var cor = '';
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
        }

        $('#resumo-pacotes').append(`
          <div class="box ${cor}">
            <div class="columns is-mobile is-gapless">

              <div class="column is-2 is-align-content-center">
                <span class="is-size-3" id="emoji">${emoji}</span> 
              </div>

              <div class="column is-7 is-align-content-center" style="margin-left: 7px">
                <span class="is-size-7">${produto.nome}</span>
              </div>

              <div class="column has-text-centered is-align-content-center is-size-5">
                <b>${produto.qtd}</b>
              </div>
              
            </div>
            
            <div class="columns is-mobile is-gapless">
              <div class="column is-12 is-align-content-center">
                <b>Subtotal:</b> R$ ${(produto.qtd * vlr_pacote).toFixed(2).replace('.', ',')}
              </div>
            </div>
          </div>
        `);

        valor_pedido += produto.qtd * vlr_pacote;
      });

      $('#resumo-vlr-pedido').text(`R$ ${valor_pedido.toFixed(2).replace('.', ',')}`);

      $('#btn-voltar').show();

    }

    if (page === 2) {
      //? Verifica se os selects tipo-pagamento e tipo-entrega estão preenchidos
      const tipo_pagamento = $('#tipo-pagamento').val();
      const tipo_entrega = $('#tipo-entrega').val();

      if (tipo_pagamento === null || tipo_entrega === null) {
        $('#btn-continuar').attr('disabled', false);
        return false;
      }
    }

    // resumo geral
    if (page === 2) {
      // Retrieve the selected value from the #tipo-entrega dropdown
      var tipoEntrega = $('#tipo-entrega option:selected').val();
      
      // Check if the selected value is "retirada"
      if (tipoEntrega === 'retirada') {
        $('#vlr-frete').text('R$ 0,00');
      }else {
        $('.if-frete').show();
      }
      
      // Update the HTML elements
      $('#resumo-vlr-pedido').html($('#vlr-pedido').html());
      $('#resumo-pacotes').html($('#pedido').html());
      $('#resumo-frete').text($('#vlr-frete').text());
      
      // Calculate the total
      var total = parseFloat($('#resumo-vlr-pedido').text().replace('R$ ', '').replace(',', '.')) + parseFloat($('#vlr-frete').text().replace('R$ ', '').replace(',', '.'));
      $('#resumo-total').text(`R$ ${total.toFixed(2).replace('.', ',')}`);
      
      // Update the remaining HTML elements
      $('#resumo-entrega').text($('#tipo-entrega option:selected').text());
      $('#resumo-pagamento').text($('#tipo-pagamento option:selected').text());
    }

    // finalizar pedido
    if (page === 3) {
      
      //? Pagina de loading
      $('#page-3').fadeOut(200, function() {
        $('#btns').css('display', 'none');
        $(`#page-4`).fadeIn(200);
        $('html, body').animate({ scrollTop: 0 }, 200);
      });

      dados = {
        pedido: pedido,
        tipo_entrega: $('#tipo-entrega').val(),
        tipo_pagamento: $('#tipo-pagamento').val()
      }

      $.ajax({
        url: '/api/pedidos/create',
        method: 'POST',
        data: dados,
        success: function(data) {
          console.log(data);

          $('#loading-icon').html('<i class="fas fa-check fa-bounce" style="color: #63d84b;"></i>');
          $('#loading-title').text('Pedido realizado com sucesso!');
          $('#loading-subtitle').text('Aguarde o contato da nossa equipe para finalizar o pagamento e agendar a entrega.');
        },
        error: function(data) {
          console.log(data);
        }
      });

      return;

    }
    
    if (page === 2) {
      $('#btn-continuar').text('Finalizar pedido');
      $('#btn-continuar').addClass('is-success');
    }else {
      $('#btn-continuar').text('Continuar');
      $('#btn-continuar').removeClass('is-success');
    }

    $(`#page-${page}`).fadeOut(500, function() {
      page++;
      $(`#page-${page}`).fadeIn(500);
      $('#btn-continuar').attr('disabled', false);
      $('html, body').animate({ scrollTop: 0 }, 500);
    });

  });

  $('#btn-voltar').click(function() {
    
    if (page === 2) {
      $('#btn-voltar').hide();
      $('#pedido').empty();
    }

    $(`#page-${page}`).fadeOut(500, function() {
      page--;
      $(`#page-${page}`).fadeIn(500);
      $('#btn-continuar').attr('disabled', false);
      $('#btn-continuar').text('Continuar');
      $('#btn-continuar').removeClass('is-success');
    });

  });

  $('#tipo-entrega').change(function() {
    if ($(this).val() === 'entrega') {
      $('#notif-frete').show();
    } else {
      $('#notif-frete').hide();
    }
  });

});