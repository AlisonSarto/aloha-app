$(document).ready(function () {

  $.ajax({
    url: '/api/pendencias/view',
    method: 'GET',
    success: function (data) {
      const pedidos = data.pedidos;
      
      $('#pedidos').html('');
      pedidos.forEach(pedido => {
        $('#pedidos').append(`
          <a class="box" href="./?pedidos/visualizar?id=${pedido.id}">
            <div class="fixed-grid has-3-cols">

              <div class="grid">
                <div class="cell has-text-centered is-align-content-center">
                  <div class="is-size-7 has-text-weight-bold">${pedido.data}</div>
                </div>
                <div class="cell has-text-centered is-align-content-center">
                  <div class="is-size-6 has-text-weight-bold">Nº ${pedido.codigo}</div>
                </div>
                <div class="cell has-text-centered is-align-content-center">
                  <span class="tag is-${pedido.cor}">ㅤ</span>
                </div>
              </div>

              <p>
                <b>Prazo de entrega:</b> ${pedido.prazo_entrega}
              </p>
              <p>
                <b>Valor total:</b> R$ ${pedido.valor_total}
              </p>
            </div>
          </a>
        `);
      });
    },
    error: function () {
      $('#pedidos').html(`
        <br>
        <p>Nenhum pedido encontrado nos últimos 30 dias...</p>
      `);
    }
  });

});