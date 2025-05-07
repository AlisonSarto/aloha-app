$(document).ready(function() {
  
  newTable();

  $('#dias_inativo').on('change', function() {
    var dias_inativo = $(this).val();

    if (dias_inativo == '') {
      toast('Selecione um período', 'danger');
      return;
    }

    newTable();
  });

});

function newTable() {

  console.log('newTable');

  $('#clientes').empty();
  $('#table-clientes').hide();
  $('#loading').attr('style', 'display: block');

  var dias_inativo = $('#dias_inativo').val();

  $.ajax({
    url: '/admin/api/clientes/inativos',
    type: 'GET',
    data: {
      dias_inativo: dias_inativo
    },
    success: function(response) {

      const clientes = response.clientes;

      clientes.forEach(cliente => {

        if (cliente.dias_inativo != 'Nunca comprou') {
          cliente.dias_inativo = cliente.dias_inativo + ' dias';
        }

        $('#clientes').append(`
          <tr>
            <td>${cliente.nome}</td>
            <td>${cliente.celular}</td>
            <td>${cliente.dias_inativo}</td>
          </tr>
        `);
               
      });

      $('#loading').attr('style', 'display: none !important');
      $('#table-clientes').show();

    }
  });
}

function toast(text,color) {
  $('.toast-header').removeClass('text-bg-success text-bg-danger text-bg-primary');
  $('.toast-header').addClass('text-bg-'+color);
  $('.toast-body').html(text);
  $('#toast').toast('show');
};