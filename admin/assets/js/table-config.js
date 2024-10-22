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
  $('#loading').attr('style', 'display: block');
  $('#search').attr('disabled', true);

  $.ajax({
    url: './api/clientes/view',
    type: 'GET',
    success: function(response) {
      const clientes = response.clientes;

      clientes.forEach(cliente => {

        var registro;
        var btns;
        if (cliente.registrado == true) {
          registro = '<i class="fas fa-check fa-lg" style="color: #51f73b;"></i>';
          btns = `
            <button class="btn btn-primary btn-sm view" data-id="${cliente.id}">
              <i class="fa-solid fa-eye"></i>
            </button>
            <button class="btn btn-warning btn-sm mx-2 edit" data-id="${cliente.id}">
              <i class="fa-solid fa-pencil"></i>
            </button>
            <button class="btn btn-danger btn-sm delete" data-id="${cliente.id}" data-nome="${cliente.nome}">
              <i class="fa-solid fa-trash"></i>
            </button>
          `;
        }else {
          registro = '<i class="fas fa-times fa-lg" style="color: #f75151;"></i>';
          btns = `
            <button class="btn btn-success btn-sm magic" data-magic="${cliente.magic}" data-nome="${cliente.nome}">
              <i class="fa-solid fa-wand-magic-sparkles"></i>
            </button>
          `;
        }

        $('#clientes').append(`
          <tr>
            <td>${cliente.nome}</td>
            <td class="text-center">${registro}</td>
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