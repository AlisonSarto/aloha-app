$(document).ready(function() {

  var vlr_pacote;
  var vlr_frete;
  var pedido;
  var produtos = [];
  var n_pedido;
  var blackFriday = false;
  var vlr_pacote_uni;
  var qtd_total;

  //? Data de entrega, input #data-entrega coloca a data de hoje, sabendo que é um input de data html e o minimo é a data de hoje
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
  
  //? verifica se é o dia da black friday
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

  $.ajax({
    url: '/api/login/profile',
    method: 'GET',
    success: function(data) {
      vlr_pacote = data.profile_interno[0].vlr_pacote;
      vlr_frete = data.profile_interno[0].vlr_frete;
      n_pedido = data.profile_interno[0].n_pedidos;

      if (data.profile_interno[0].prazo_boleto > 0) {
        $('#tipo-pagamento').append(`
          <option value="boleto">Boleto</option>
        `);
        boleto = true;
      }

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
          emoji = '🍋‍🟩';
          cor = 'limao';
          if (blackFriday) {
            produto.nome = produto.nome + ' (10% off)';
          }
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
                  <input class="input qtd" type="number" placeholder="0">
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

      //? Verifica a quantidade total de pacotes
      qtd_total = 0;
      pedido.forEach(pacote => {
        qtd_total += pacote.qtd;
      });

      //! Tabela de preços
      pedido.forEach(pacote => {

        if (vlr_pacote != 0) {
          vlr_pacote_uni = parseFloat(vlr_pacote);

        }else if (qtd_total <= 30) {
          vlr_pacote_uni = parseFloat(28.00);

        }else if (qtd_total <= 100) {
          vlr_pacote_uni = parseFloat(25.20);

        }else if (qtd_total > 100) {
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

    }

    if (page === 2) {
      //? Verifica se os selects tipo-pagamento e tipo-entrega estão preenchidos
      const tipo_pagamento = $('#tipo-pagamento').val();
      const tipo_entrega = $('#tipo-entrega').val();
      const data_entrega = $('#data-entrega').val();

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
      
      $('#resumo-total-pacote').html(qtd_total + ' pacotes');
      $('#resumo-vlr-pacote').html('R$ ' + parseFloat(vlr_pacote_uni).toFixed(2).replace('.', ','));

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
        tipo_pagamento: $('#tipo-pagamento').val(),
        data_entrega: $('#data-entrega').val(),
        obs: $('#obs').val(),
      }

      $.ajax({
        url: '/api/pedidos/create',
        method: 'POST',
        data: dados,
        success: function(ress) {
          console.log(ress);
          $('#loading-icon').html('<i class="fas fa-check fa-bounce" style="color: #63d84b;"></i>');
          $('#loading-title').text('Pedido realizado com sucesso!');
          $('#loading-subtitle').html(`
            Aguarde que em breve nossa equipe analisará e confirmará o pedido.
            <br>
            <br>
            Assim que estiver tudo certo, avisaremos você.
            <br> 
            <br>  
            Equipe Aloha agradece a preferência! 🥂
          `);
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