$(document).ready(function() {

  var produtos = [];

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

              <div class="column is-6 is-align-content-center">
                <span class="is-size-7">${produto.nome}</span>
              </div>

              <div class="column">
                <div class="quantidade" data-id="${produto.id}">
                  <button class="decrementar">-</button>
                  <span class="qtd">0</span>
                  <button class="incrementar">+</button>
                </div>
              </div>
              
            </div>
          </div>
        `);
      });

      $('main').show();

      //? Adiciona eventos aos botões de incrementar e decrementar
      $('.incrementar').click(function() {
        const qtdElem = $(this).siblings('.qtd');
        let qtd = parseInt(qtdElem.text());
        qtdElem.text(qtd + 1);
      });

      $('.decrementar').click(function() {
        const qtdElem = $(this).siblings('.qtd');
        let qtd = parseInt(qtdElem.text());
        if (qtd > 0) {
          qtdElem.text(qtd - 1);
        }
      });
    }
  });

  var page = 1;
  $('#btn-continuar').click(function() {

    $('#btn-continuar').attr('disabled', true);

    if (page === 1) {
      //? Registra a qtd de cada produto que é > 0 em um novo array pedido
      var pedido = [];
      produtos.forEach(produto => {
        const qtd = parseInt($(`.quantidade[data-id="${produto.id}"] .qtd`).text());
        if (qtd > 0) {
          pedido.push({
            id: produto.id,
            nome: produto.nome,
            qtd: qtd
          });
        }
      });

      console.log(pedido);

      //? Se não tiver nenhum produto selecionado, retorna
      if (pedido.length === 0) {
        $('#btn-continuar').attr('disabled', false);
        return false;
      }

      //? Adiciona os produtos selecionados na próxima página
      $('#pedido').html('');
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

        $('#pedido').append(`
          <div class="box ${cor}">
            <div class="columns is-mobile is-gapless">

              <div class="column is-2 is-align-content-center">
                <span class="is-size-3" id="emoji">${emoji}</span> 
              </div>

              <div class="column is-7 is-align-content-center">
                <span class="is-size-7">${produto.nome}</span>
              </div>

              <div class="column has-text-centered is-align-content-center is-size-5">
                <b>${produto.qtd}</b>
              </div>
              
            </div>
          </div>
        `);
      });

      $('#btn-voltar').show();

    }

    if ()

    $(`#page-${page}`).fadeOut(500, function() {
      page++;
      $(`#page-${page}`).fadeIn(500);
      $('#btn-continuar').attr('disabled', false);
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
    });
  });

});