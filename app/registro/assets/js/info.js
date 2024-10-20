$(document).ready(function() {

  $.ajax({
    url: '/api/login/profile',
    method: 'GET',
    success: function(data) {

      if (data.profile_interno.length > 0) {
        window.location.href = '/';
      }
      
      const user = data.profile[0];

      $('main').show();

      $('#nome-user').text(user.nome);
    },
    error: function() {
      window.location.href = '/';
    }
  });

  var page = 1;
  $('#btn-continuar').click(function() {

    //? Email
    if (page == 4) {
      if ($('#email').val() == '') {
        $('#email').addClass('is-danger');
        return;
      }
    }

    //? Senha
    if (page == 5) {
      if ($('#senha').val() == '') {
        $('#senha').addClass('is-danger');
        return;
      }
    }

    $(this).attr('disabled', true);

    //? Fim
    if (page == 6) {
      
      $(this).addClass('is-loading');

      $.ajax({
        url: '/api/registro/add',
        method: 'POST',
        data: {
          email: $('#email').val(),
          senha: $('#senha').val(),
        },
        success: function() {
          window.location.href = '/';
        },
        error: function() {
          // reinicia a pagina
          window.location.href = '/registro';
        }
      });

      return;

    }

    if (page == 5) {
      $(this).text('Acessar Aloha App');
      $(this).addClass('is-link');
      $(this).removeClass('is-success');
    }

    $(`#box-${page}`).fadeOut(500, function() {
      page++;
      $(`#box-${page}`).fadeIn(500);
      $('#btn-continuar').attr('disabled', false);
    });

  });

});