$(document).ready(function() {

  $.ajax({
    url: '/api/login/profile',
    type: 'GET',
    success: function(data) {
      // Verifica se profile existe antes de acessar
      if (data.profile && data.profile.length > 0) {
        $('#nome').text(data.profile[0].nome);
        $('main').show();
      } else {
        // Se não tem dados do profile, redireciona para login
        window.location.href = '/login';
      }
    },
    error: function() {
      window.location.href = '/login';
    }
  })

});