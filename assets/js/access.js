$(document).ready(function() {

  $.ajax({
    url: '/api/login/profile',
    type: 'GET',
    complete: function(data) {
      if (data.status == 403) {
        location.href = '/login';
      }
      var response = data.responseJSON;
      
      // Verifica se response e profile_interno existem antes de acessar
      if (response && response.profile_interno && response.profile_interno.length === 0) {
        location.href = '/app/registro';
      }
      if (response && response.boleto_atrasado == true) {
        location.href = '/app/err/boleto-atrasado/';
      }
      // espera 1 segundo para mostrar o body
      setTimeout(function() {
        $('body').show();
      }, 700);
    }
  });

});