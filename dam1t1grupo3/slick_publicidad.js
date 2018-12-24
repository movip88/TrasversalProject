$(document).ready(inicio);

function inicio() {
    configurarSlick1();
    configurarSlick2();
}

function configurarSlick1() {
    $("#publicaidad1").slick({
        dots: false,
        autoplay: true,
        autoplaySpeed: 3000,
        infinite: true,
        speed: 500,
        slidesToShow: 1,
        prevArrow: false,
        nextArrow: false
    });
}

function configurarSlick2() {
    $("#publicaidad2").slick({
        dots: false,
        autoplay: true,
        autoplaySpeed: 3000,
        infinite: true,
        speed: 500,
        slidesToShow: 1,
        prevArrow: false,
        nextArrow: false
    });
}
