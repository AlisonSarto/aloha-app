$(document).on('click', '.view', function() {

  const id = $(this).data('id');

  $.ajax({
    url: './api/clientes/view',
    type: 'GET',
    data: {
      id: id
    },
    success: function(response) {
      const cliente = response.clientes[0];

      const modal = $('#modal');

      modal.find('.modal-title').text('Visualizar cliente ' + cliente.nome);
      modal.find('.modal-body').html(`
        <p><strong>Valor pacote:</strong> R$ ${cliente.dados_internos.vlr_pacote.replace('.', ',')}</p>
        <p><strong>Valor frete:</strong> R$ ${cliente.dados_internos.vlr_frete.replace('.', ',')}</p>
        <p><strong>Qtd semanal comodato:</strong> ${cliente.dados_internos.qtd_semanal_comodato.replace('.', ',')} pacotes</p>
        <p><strong>Prazo boleto:</strong> ${cliente.dados_internos.prazo_boleto} dias</p>
      `);
      modal.find('.modal-footer').html(`
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      `);
      modal.modal('show');
      
    }
  });
  
});