$(document).ready(function() {

  $.ajax({
    url: '/api/login/profile',
    type: 'GET',
    success: function(data) {
      $('#nome').text(data.profile[0].nome);
      $('main').show();
    }
  })

});