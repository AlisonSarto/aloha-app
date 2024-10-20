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
    }
  });

});