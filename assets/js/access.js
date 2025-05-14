$(document).ready(function() {

  $.ajax({
    url: '/api/login/profile',
    type: 'GET',
    complete: function(data) {
      if (data.status == 403) {
        location.href = '/login';
      }
      var response = data.responseJSON;
      if (response.profile_interno.length === 0) {
        location.href = '/app/registro';
      }
      if (response.boleto_atrasado == true) {
        location.href = '/app/err/boleto-atrasado/';
      }
      // espera 1 segundo para mostrar o body
      setTimeout(function() {
        $('body').show();
      }, 700);
    }
  });

});