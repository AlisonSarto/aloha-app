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
        <label for="email">Email:</label>
        <input type="text" id="email" class="form-control" value="${cliente.dados_internos.email}">

        <hr class="my-3">

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

        var email = $('#email').val();
        var vlr_pacote = $('#vlr_pacote').val().replace(',', '.');
        var vlr_frete = $('#vlr_frete').val().replace(',', '.');
    
        if (email == '' || vlr_pacote == '' || vlr_frete == '') {
          toast('Preencha todos os campos!', 'danger');
          return;
        }

        //? Verifica se mudou algo
        if (cliente.dados_internos.email == email && cliente.dados_internos.vlr_pacote == vlr_pacote && cliente.dados_internos.vlr_frete == vlr_frete) {
          toast('Nada foi alterado!', 'danger');
          return;
        }

        $.ajax({
          url: './api/clientes/edit',
          type: 'POST',
          data: {
            id: id,
            email: email,
            vlr_pacote: vlr_pacote,
            vlr_frete: vlr_frete
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