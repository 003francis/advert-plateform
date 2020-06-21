$('#editAnnonce').click(function () {
    $('#editNotice').modal('show');
});
$('#editNotice').modal({
    keyboard: false, //quand l'utilisateur va cliquer sur la touche 'esc' du clavier, rien ne se passera
    show: false
});