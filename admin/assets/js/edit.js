$(document).on('click', '.edit', function() {

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

      modal.find('.modal-title').text('Editar cliente ' + cliente.nome);
      modal.find('.modal-body').html(`
        <label for="vlr_pacote">Valor do Pacote:</label>
        <div class="input-group mb-3">
          <span class="input-group-text">R$</span>
          <input type="text" id="vlr_pacote" class="form-control" value="${cliente.dados_internos.vlr_pacote.replace('.', ',')}">
        </div>

        <label for="vlr_frete">Valor do Frete:</label>
        <div class="input-group mb-3">
          <span class="input-group-text">R$</span>
          <input type="text" id="vlr_frete" class="form-control" value="${cliente.dados_internos.vlr_frete.replace('.', ',')}">
        </div>
        
        <label for="vlr_frete">Qtd semanal comodato (0 = não tem comodato):</label>
        <input type="text" id="qtd_semanal" class="form-control mb-3" value="${cliente.dados_internos.qtd_semanal_comodato.replace('.', ',')}">
        
        <label for="vlr_frete">Prazo boleto (0 = boleto bloquedo):</label>
        <input type="text" id="prazo_boleto" class="form-control" value="${cliente.dados_internos.prazo_boleto.replace('.', ',')}">
      `);
      modal.find('.modal-footer').html(`
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        <button type="button" class="btn btn-primary" id="salvar">Salvar</button>
      `);
      modal.modal('show');

      $('#vlr_pacote').on('blur', function() {
        var vlr_pacote = $(this).val().replace(',', '.');
        $(this).val(parseFloat(vlr_pacote).toFixed(2).replace('.', ','));
      });
      $('#vlr_frete').on('blur', function() {
        var vlr_pacote = $(this).val().replace(',', '.');
        $(this).val(parseFloat(vlr_pacote).toFixed(2).replace('.', ','));
      });

      $('#salvar').click(function() {

        var vlr_pacote = $('#vlr_pacote').val().replace(',', '.');
        var vlr_frete = $('#vlr_frete').val().replace(',', '.');
        var qtd_semanal = $('#qtd_semanal').val();
        var prazo_boleto = $('#prazo_boleto').val();
    
        if (vlr_pacote == '' || vlr_frete == '' || qtd_semanal == '' || prazo_boleto == '') {
          toast('Preencha todos os campos!', 'danger');
          return;
        }

        $.ajax({
          url: './api/clientes/edit',
          type: 'POST',
          data: {
            id: id,
            vlr_pacote: vlr_pacote,
            vlr_frete: vlr_frete,
            qtd_semanal: qtd_semanal,
            prazo_boleto: prazo_boleto
          },
          success: function(response) {
            var message = response.message;
            toast(message, 'success');
            modal.modal('hide');
          },
          error: function(response) {
            console.log(response);
            var message = response.responseJSON.message;
            toast(message, 'danger');
          }
        });
    
      });
      
    }
  });
  
});