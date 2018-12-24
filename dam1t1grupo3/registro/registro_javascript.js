$(document).ready(inicio);

function inicio() {
    $(".musico").click(musico);
    $(".fan").click(fan);
    $(".local").click(local);
}

function musico() {
    animar("musico");
    $("#formulario").load('forms.php?option=1');
}

function fan() {
    animar("fan");
    $("#formulario").load('forms.php?option=2');
}

function local() {
    animar("local");
    $("#formulario").load('forms.php?option=3');
}

function checkUsername() {
    $.ajax({
        type: "POST",
        url: "ajax_registro.php?option=4",
        dataType: "json",
        data: {nombre: $("#nombre_usuario").val()},
        success: function (data, textStatus, jqXHR) {
            if (data['succsess'] == "true") {
                $("#user_bad").css({"display": "none"});
                $("#user_corect").css({"display": "inline-block"});
                $("#user_corect").attr({correcto: "true"});
            } else {
                $("#user_corect").css({"display": "none"});
                $("#user_bad").css({"display": "inline-block"});
                $("#user_corect").attr({correcto: "false"});
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown + "EROOOOR!!!");
        }
    });
}

function cargarProvincia() {
    $.ajax({
        type: "POST",
        url: "ajax_registro.php?option=1",
        dataType: "json",
        data: {comunidad: $("#comunidad").val()},
        success: function (data, textStatus, jqXHR) {
            var text = "<select id='provincia' onchange='cargarMunicipios()'><option></option>";
            for (var i = 0; i < data.length; i++) {
                text += "<option value='" + data[i] + "'>" + data[i] + "</option>";
            }
            text += "</select>";
            $("#provincias").html("<label for='provincia'>Provincia: </label>");
            $("#provincias").append(text);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown + "EROOOOR!!!!!!!!!");
        }
    });
}

function cargarMunicipios() {
    $.ajax({
        type: "POST",
        url: "ajax_registro.php?option=2",
        dataType: "json",
        data: {provincia: $("#provincia").val()},
        success: function (data, textStatus, jqXHR) {
            var text = "<select name='municipio' class='form_info' id='municipio'><option></option>";
            for (var i = 0; i < data.length; i++) {
                text += "<option value='" + data[i][0] + "'>" + data[i][1] + "</option>";
            }
            text += "</select>";
            $("#municipios").html("<label for='ciudad'>Municipio: </label>");
            $("#municipios").append(text);
        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    });
}

function enviar() {
    var dato = "{";
    if ($("#contra").val() != $("#contra2").val()) {
        $("#contra").css({"border-color": "red"});
        $("#contra2").css({"border-color": "red"});
        $("#error").css({"display": "inline-block", "color": "red"});
    } else {
        if ($("#user_corect").attr("correcto") == "true") {
            $("#contra").css({"border-color": "green"});
            $("#contra2").css({"border-color": "green"});
            $("#error").css({"display": "none", "color": "red"});
            var fd = new FormData();
            var files = $('#imagen')[0].files[0];
            fd.append('foto', files);
            $(".form_info").each(function (k) {
                if ($(this).attr("evitar") === undefined) {
                    fd.append($(this).attr("name"), $(this).val());
                }
            });
            if (document.forms["formulariAlta"]["sexo"] !== undefined) {
                fd.append('sexo', document.forms["formulariAlta"]["sexo"].value);
            }
            fd.append('tipo', $(".tipo").attr("tipo"));
            $.ajax({
                type: "POST",
                url: "ajax_registro.php?option=3",
                dataType: 'json',
                data: fd,
                contentType: false,
                processData: false,
                success: function (data, textStatus, jqXHR) {
                    if (data['succsess'] == "true") {
                        swal({
                            title: "Felicidades!",
                            text: "Dado de alta correctamente, se te redigira a la pagina inicial donde podras loggearte...",
                            icon: "success"});
                        setTimeout(function () {
                            window.location.href = "../";
                        }, 1000);
                    } else {
                        swal({
                            title: "Error en el registro!",
                            text: data,
                            icon: "error"});
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert(errorThrown + "ERROOOOOOR!!!");
                }
            });
        } else {
            swal({
                title: "Error en el registro!",
                text: "Nombre de usuario ya en uso prueba con otro...",
                icon: "error"});
        }
    }
    return false;
}

function animar(clase) {
    $("#seleccionar img").stop();
    $("#seleccionar button").stop();
    $("#seleccionar img").animate({"width": "25px", "height": "25px"});
    $("#seleccionar button").css({"height": "auto", "width": "auto"});
    $("." + clase).animate({"width": "50px", "height": "50px"}, {duration: 1000});
}