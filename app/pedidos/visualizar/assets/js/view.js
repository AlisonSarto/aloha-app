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
      console.log(pedido)
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