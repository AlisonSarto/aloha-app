$(document).on('click', '.magic', function() {

  const magic = $(this).data('magic');
  const nome = $(this).data('nome');

  //? Copia o magic para a área de transferência
  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val(magic).select();
  document.execCommand("copy");
  $temp.remove();

  toast(`Link de acesso do cliente <b>${nome}</b> copiado`,'success');
  
});