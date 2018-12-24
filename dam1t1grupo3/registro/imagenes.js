function rotarImagenes() {
    var index = Math.floor((Math.random() * 7) + 1);;
    $("body").css({"background-image": "url(../img/imagenes-fondo/"+index+".jpg)"});
}
onload = function () {
    rotarImagenes();
    setInterval(rotarImagenes, 5000);
}