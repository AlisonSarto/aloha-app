$(document).ready(function() {

  $.ajax({
    url: "/api/pagamentos/atrasados",
    type: "GET",
    dataType: "json",
    complete: function(data) {
      data = data.responseJSON;

      if (data.status == 404) {
        // Não há boletos atrasados
        window.location.href = "/";
      } else if (data.status == 200) {
        // Há boletos atrasados
        var boletos = data.boletos;
        boletos.forEach(boleto => {
          addBoleto(boleto.venda_codigo, boleto.data_vencimento, boleto.valor, boleto.link_cobranca);
        });
      }

      $('body').show()

    }

  });

  function addBoleto(n_pedido, vencimento, valor, link) {

    valor = valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    valor = valor.replace(".", ",");

    var vencimento_formatado = vencimento.split("-").reverse().join("/");

    var dias_em_atraso = Math.floor((new Date() - new Date(vencimento)) / (1000 * 60 * 60 * 24));

    $("#boletos").append(`
      <div class="boleto-item">
        <div class="d-flex justify-content-between">
          <div>
            <h5 class="mb-1">Pedido #${n_pedido}</h5>
            <p class="mb-1">Vencimento: ${vencimento_formatado}</p>
            <p class="mb-0"><strong>Valor: ${valor}</strong></p>
          </div>
          <div>
            <span class="badge bg-danger">${dias_em_atraso} dias em atraso</span>
          </div>
        </div>
        <button class="btn btn-primary aloha-primary boleto-button" onclick="window.location.href='${link}'">Acessar Boleto</button>
      </div>
    `);
  }

});