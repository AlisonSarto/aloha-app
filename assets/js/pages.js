$(document).ready(function () {

  if (location.href.indexOf('/?') > -1) {

    url = location.href.split('/?')[1];
    url = '/app/' + url;
    
    iframe(url);

  }else {
    //? Se não existir, leva o user para o inicio
    iframe('/app/pedidos');
  }

});

//* Offline
window.addEventListener('offline', function () {
  $('iframe').hide();
  $('body').html(``);
});

//* Online
window.addEventListener('online', function () {
  location.reload();
});

$('a').click(function (e) {

  e.preventDefault();
  url = $(this).attr('href');
  url = url.split('/?')[1];
  url = '/app/' + url;

  iframe(url);

});

$('iframe').on('load', function() {
  var iframeDocument = $(this).contents();
  
  iframeDocument.on('click', 'a', function(e) {
    e.preventDefault();

    if ($(this).attr('href') == '/sair') {
      window.location.href = '/sair';
    }
    
    var url = $(this).attr('href');
    url = url.split('/?')[1];
    url = '/app/' + url;
    
    iframe(url);
  });
});

function iframe(url) {

  $('iframe').hide();

  //? Verifica se a url existe no menu
  link = url.replace('/app/', './?');
  link = './' + link.split('/')[1];
  if ($(`a[href='${link}']`).length == 0) {

    //? Se não existir, volta para o inicio
    iframe('/app/pedidos');

    return;
  }
  
  $('iframe').attr('src', url);

  url = url.replace('/app/', './?');

  history.pushState(null, null, url);

  $('iframe').on('load', function () {

    $('iframe').show();

    var title = $('iframe').contents().find('title').text();
    $('title').text(title);

  });

}