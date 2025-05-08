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

      var boleto_bloqueado = cliente.dados_internos.boleto_bloqueado;
      if (cliente.dados_internos.boleto_bloqueado == 'true') {
        boleto_bloqueado = 'checked';
      } else {
        boleto_bloqueado = '';
      }

      const modal = $('#modal');

      modal.find('.modal-title').text('Editar cliente ' + cliente.nome);
      modal.find('.modal-body').html(`
        <label>Valor do Pacote:</label>
        <div class="input-group mb-3">
          <span class="input-group-text">R$</span>
          <input type="text" id="vlr_pacote" class="form-control" value="${cliente.dados_internos.vlr_pacote.replace('.', ',')}">
        </div>

        <label>Valor do Frete:</label>
        <div class="input-group mb-3">
          <span class="input-group-text">R$</span>
          <input type="text" id="vlr_frete" class="form-control" value="${cliente.dados_internos.vlr_frete.replace('.', ',')}">
        </div>
        
        <label>Qtd semanal comodato (0 = não tem comodato):</label>
        <input type="text" id="qtd_semanal" class="form-control mb-3" value="${cliente.dados_internos.qtd_semanal_comodato.replace('.', ',')}">

        <label>Bloquear boleto</label>
        <div class="form-check form-switch mb-3">
          <input class="form-check-input" type="checkbox" role="switch" id="boleto_bloqueado_${id}" ${boleto_bloqueado}>
        </div>
        
        <label>Prazo boleto (0 = boleto desativado):</label>
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

$(document).on('change', `.form-check-input[type="checkbox"]`, function() {
  const id = $(this).attr('id').split('_').pop(); // Extrai o ID do cliente
  const boletoBloqueado = $(this).is(':checked'); // Verifica se está marcado


  $.ajax({
    url: './api/clientes/switch_boleto',
    type: 'POST',
    data: {
      id: id,
      boleto_bloqueado: boletoBloqueado
    },
    error: function(response) {
      console.log(response);
      var message = response.responseJSON.message;
      toast(message, 'danger');
      $(`#boleto_bloqueado_${id}`).prop('checked', !boletoBloqueado);
    }
  });


});