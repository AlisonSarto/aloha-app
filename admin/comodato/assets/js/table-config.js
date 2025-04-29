$(document).ready(function() {
  
  newTable();

  $('#search').on('keyup', function() {
    var value = $(this).val();
    $('#clientes tr').filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
  });


});

function newTable() {

  $('#clientes').empty();
  $('#table-clientes').hide();
  $('#accordion-cadastros').hide();
  $('#loading').attr('style', 'display: block');
  $('#search').attr('disabled', true);

  $.ajax({
    url: '/admin/api/clientes/view',
    type: 'GET',
    data: {
      comodato: true
    },
    success: function(response) {

      const clientes = response.clientes;

      clientes.forEach(cliente => {

        var btns;
        if (cliente.registrado == true) {
          btns = `
            <button class="btn btn-primary btn-sm view" data-id="${cliente.id}">
              <i class="fa-solid fa-eye"></i>
            </button>
          `;
        }

        $('#clientes').append(`
          <tr>
            <td>${cliente.nome}</td>
            <td class="d-flex">
              ${btns}
            </td>
          </tr>
        `);
               
      });

      //? Se tiver algo no search, filtra
      if ($('#search').val() != '') {
        var value = $('#search').val();
        $('#clientes tr').filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
      }

      $('#loading').attr('style', 'display: none !important');
      $('#table-clientes').show();
      $('#accordion-cadastros').show();

      $('#search').attr('disabled', false);
      $('#search').focus();

    }
  });
}

function toast(text,color) {
  $('.toast-header').removeClass('text-bg-success text-bg-danger text-bg-primary');
  $('.toast-header').addClass('text-bg-'+color);
  $('.toast-body').html(text);
  $('#toast').toast('show');
};