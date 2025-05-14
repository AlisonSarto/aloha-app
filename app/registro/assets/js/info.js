$(document).ready(function() {

  setTimeout(function() {
    $('body').show();
  }, 1000);

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

    //? Fim
    if (page == 3) {
      
      $(this).addClass('is-loading');

      $.ajax({
        url: '/api/registro/add',
        method: 'POST',
        success: function() {
          window.location.href = '/';
        }
      });

      return;

    }

    if (page == 2) {
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