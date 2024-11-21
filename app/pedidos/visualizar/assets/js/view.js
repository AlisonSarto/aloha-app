$(document).ready(function () {

  //? verifica se passou o id do pedido, se não tiver retorna
  if (location.href.indexOf('?id=') == -1) {
    $('main').html(`
      <br>
      <h1 class="title is-4">404 - Pedido não encontrado</h1>
      <p>Por favor, volte para a lista de pedidos e tente novamente.</p>
    `);
    return;
  }

  const id = location.href.split('?id=')[1];

  $.ajax({
    url: '/api/pedidos/view',
    method: 'GET',
    data: {
      id: id
    },
    success: function (data) {
      const pedido = data.pedidos[0];

      const situacao_id = pedido.situacao_id;
      const nome_situacao = pedido.nome_situacao;

      const situacoes = {
        '3395252': 'em-analize',
        '4629853': 'preparando-envio',
        '4737015': 'em-rota',
        '3395254': 'entregue',
      }
      
      var anormal = true;
      for (const key in situacoes) {
        if (key == situacao_id) {
          $(`#${situacoes[key]}`).addClass('is-active');
          anormal = false;
          break;
        }
      }

      if (anormal) {
        $('#step').html(`
          <li class="steps-segment" id="em-analize">
            <span class="steps-marker is-danger">
              <span class="icon">
                <i class="fa-solid fa-triangle-exclamation"></i>
              </span>
            </span>
            <div class="steps-content">
              <p class="heading">${nome_situacao}</p>
            </div>
          </li> 
        `);
      }

      $('#data').text(pedido.data.split('-').reverse().join('/'));
      $('#prazo').text(pedido.prazo_entrega.split('-').reverse().join('/'));

      const produtos = pedido.produtos;

      produtos.forEach(produto => {
        const pacote = produto.produto;

        pacote.valor_total = pacote.valor_total.replace('.', ',');
        pacote.quantidade = pacote.quantidade.replace('.00', '');

        //* Dividindo direto para incluir descontos
        var valor_venda = parseFloat(pacote.valor_total.replace(',', '.')) / parseInt(pacote.quantidade);
        valor_venda = valor_venda.toFixed(2).replace('.', ',');

        $('#produtos').append(`
          <tr>
            <td class="has-text-nowrap">${pacote.nome_produto}</td>
            <td class="has-text-nowrap">${pacote.quantidade}</td>
            <td class="has-text-nowrap">R$ ${valor_venda}</td>
            <td class="has-text-nowrap">R$ ${pacote.valor_total}</td>
          </tr>
        `);
      });

      $('#vlr-pedido').text(pedido.valor_produtos.replace('.', ','));
      $('#vlr-frete').text(pedido.valor_frete.replace('.', ','));
      $('#vlr-total').text(pedido.valor_total.replace('.', ','));

      const pagamentos = pedido.pagamentos;

      pagamentos.forEach(pagamento => {
        const pgmnt = pagamento.pagamento;

        pgmnt.data_vencimento = pgmnt.data_vencimento.split('-').reverse().join('/');
        pgmnt.valor = pgmnt.valor.replace('.', ',');

        $('#pagamentos').append(`
          <tr>
            <td class="has-text-nowrap">${pgmnt.data_vencimento}</td>
            <td class="has-text-nowrap">R$ ${pgmnt.valor}</td>
            <td class="has-text-nowrap">${pgmnt.nome_forma_pagamento}</td>
          </tr>
        `);
      });

      $('main').show();
    },
    error: function () {
      $('main').html(`
        <br>
        <h1 class="title is-4">404 - Pedido não encontrado</h1>
        <p>Por favor, volte para a lista de pedidos e tente novamente.</p>
      `);
    }
  });


});