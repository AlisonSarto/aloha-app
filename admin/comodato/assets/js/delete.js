$(document).on('click', '.delete', function() {

  const id = $(this).data('id');
  const cliente = $(this).data('nome');

  //? Tempo de espera em s
  contador = 2;

  //* Mostra modal de confirmação
  const modal = $('#modal');
  modal.find('.modal-title').html('Deletar Cliente');
  modal.find('.modal-footer').html(`
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
    <button type="button" class="btn btn-danger" id="delete" disabled>Deletar ${contador}s</button>
  `);
  modal.find('.modal-body').html(`
    <p>
      Deseja realmente deletar o acesso do cliente <b>${cliente}</b>? <br>
      <b>Obs:</b> O cliente será deletado do Aloha App, mas continuará no Gestão Click, sem perder seus dados e vendas.
    </p>
  `);
  modal.modal('show');
  
  //* Libera o deletar depois do tempo
  var interval = setInterval(function() {
    
    contador--;
    $('#delete').html(`Deletar ${contador}s`);
    if (contador == 0) {
      $('#delete').prop('disabled', false);
      $('#delete').html(`Deletar`);
      clearInterval(interval);
    }

    //? Se fechar o modal para a contagem
    $('#modal').on('hidden.bs.modal', function() {
      clearInterval(interval);
    });

  }, 1000);

  //? Deleta o usuário
  $('#delete').click(function() {

    $.ajax({
      url: `./api/clientes/delete?id=${id}`,
      type: 'DELETE',
      beforeSend: function() {
        $('#delete').html('<i class="fas fa-spinner fa-spin"></i> Deletando...');
        $('#delete').attr('disabled', true);
      },
      success: function(data) {
        message = data.message;
        $('#modal').modal('hide');
        toast(message, 'success');
        newTable();
      },
      error: function(error) {
        console.log(error);
        var message = error.responseJSON.message;
        $('#delete').html('Deletar');
        $('#delete').attr('disabled', false);
        toast(message, 'danger');
      }
    });

  });

});