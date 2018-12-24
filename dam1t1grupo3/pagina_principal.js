$(document).ready(cargar);

function cargar() {
    $.ajax({
        type: "POST",
        url: "ajax.php?option=1",
        dataType: "json",
        data: {},
        success: function (respostaJSON) {
            for (var valor in respostaJSON) {
                $("#" + valor).load(respostaJSON[valor]);
            }
            if (respostaJSON["cabecera"] == "opciones_pagina_principal.php?option=3") {
                verDatos();
            }
        }
    });
}

function cargarCrearConcierto() {
    $("#contenido_datos").load('formConcierto.php');
}

function comprovar() {
    var fd = new FormData();
    $(".form_info").each(function (k) {
        if ($(this).attr("evitar") === undefined) {
            fd.append($(this).attr("name"), $(this).val());
        }
    });
    $.ajax({
        type: "POST",
        url: "ajax.php?option=11",
        dataType: "json",
        data: fd,
        contentType: false,
        processData: false,
        success: function (data, textStatus, jqXHR) {
            swal({
                title: "¡Atención!",
                text: data['success'],
                icon: "info"});
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
    return false;
}

function verDatos(tipo) {
    $.ajax({
        type: "POST",
        url: "ajax.php?option=4",
        dataType: "json",
        data: {},
        success: function (data, textStatus, jqXHR) {
            $("#contenido_datos").html("<br />");
            var i = 0;
            for (var valor in data) {
                var label = $("<label style='color:#000000; border-bottom:2px solid; cursor:pointer;' campo='" + i + "'>" + valor.substring(0, (valor.length - 2)) + ": </label>");
                $("#contenido_datos").append(label);
                label.click(modificarDato);
                i++;

                if (valor == "Imagen: ") {
                    $("#contenido_datos").append("<br /><img src='" + data[valor] + "'/><br /> <br />");
                } else {
                    $("#contenido_datos").append(data[valor] + "<br /> <br />");
                }

            }
            var label = $("<label style='color:#000000; border-bottom:2px solid; cursor:pointer;' campo='10'>Contraseña</label><br />");
            $("#contenido_datos").append(label);
            label.click(modificarDato);
            $("#contenido_datos").append("<div id='contenido_datos_2'></div>")

        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}

function modificarDato() {
    $.ajax({
        type: "POST",
        url: "ajax.php?option=5",
        dataType: "json",
        data: {option: $(this).attr("campo")},
        success: function (data, textStatus, jqXHR) {
            $("#contenido_datos_2").html(data['success']);
            $("#modificar").click(updateDatos);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}

function updateDatos() {
    if ($("#input_modificar").attr("campo_bbdd") == "password") {
        if ($("#contra").val() != $("#input_modificar").val()) {
            swal({
                title: "¡Error!",
                text: "Las contraseñas no coinciden...",
                icon: "error"});
            return;
        } else {
            updatePassword($("#input_modificar").val());
            return;
        }
    }
    if ($("#input_modificar").attr("campo_bbdd") == "nombre_usuario") {
        if ($("#input_modificar").attr("correcto") == "false") {
            swal({
                title: "¡Error!",
                text: "Nombre de usuario ya en uso prueba con otro",
                icon: "error"});
            return;
        }
    }
    var fd = new FormData();
    if ($("#input_modificar").attr("type") != "file") {
        fd.append('valor', $("#input_modificar").val());
    } else {
        fd.append('valor', $('#input_modificar')[0].files[0]);
    }
    fd.append('campo', $("#input_modificar").attr("campo_bbdd"));
    $.ajax({
        type: "POST",
        url: "ajax.php?option=7",
        dataType: "json",
        data: fd,
        contentType: false,
        processData: false,
        success: function (data, textStatus, jqXHR) {
            swal({
                title: "¡Atención!",
                text: data['success'],
                icon: "info"});
            verDatos();
            $("#cabecera").load('opciones_pagina_principal.php?option=3');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}

function updatePassword(pass) {
    $.ajax({
        type: "POST",
        url: "ajax.php?option=10",
        dataType: "json",
        data: {contra: pass},
        success: function (data, textStatus, jqXHR) {
            swal({
                title: "¡Atención!",
                text: data['success'],
                icon: "info"});
            verDatos();

        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}

function validarEntrada() {
    $.ajax({
        type: "POST",
        url: "ajax.php?option=2",
        dataType: "json",
        data: {nombre: $("#usuario").val(), contra: $("#contraseña").val()},
        success: function (data, textStatus, jqXHR) {
            if (data['success'] == "true") {
                location.reload(true);
            } else {
                swal({
                    title: "¡Error de sesión!",
                    text: data['success'],
                    icon: "error"});
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
    return false;
}

function cerrarSession() {
    $.ajax({
        type: "POST",
        url: "ajax.php?option=3",
        dataType: "json",
        data: {},
        success: function (data, textStatus, jqXHR) {
            if (data['success'] == "true") {
                location.reload(true);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}

function cargarProvincia() {
    $.ajax({
        type: "POST",
        url: "./registro/ajax_registro.php?option=1",
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
        url: "./registro/ajax_registro.php?option=2",
        dataType: "json",
        data: {provincia: $("#provincia").val()},
        success: function (data, textStatus, jqXHR) {
            var text = "<select campo_bbdd='ciudad' id='input_modificar'><option></option>";
            for (var i = 0; i < data.length; i++) {
                text += "<option value='" + data[i][0] + "'>" + data[i][1] + "</option>";
            }
            text += "</select><button id='modificar'> Modificar </button>";
            $("#municipios").html("<label for='ciudad'>Municipio: </label>");
            $("#municipios").append(text);
            $("#modificar").click(updateDatos);
        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    });
}

function checkUsername() {
    $.ajax({
        type: "POST",
        url: "http://localhost/dam1t1grupo3/registro/ajax_registro.php?option=4",
        dataType: "json",
        data: {nombre: $("#input_modificar").val()},
        success: function (data, textStatus, jqXHR) {
            if (data['succsess'] == "true") {
                $("#input_modificar").attr({correcto: "true"});
            } else {
                $("#input_modificar").attr({correcto: "false"});
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown + "EROOOOR!!!");
        }
    });
}

function checkPassword() {
    $.ajax({
        type: "POST",
        url: "ajax.php?option=8",
        dataType: "json",
        data: {contra: $("#antigua").val()},
        success: function (data, textStatus, jqXHR) {
            if (data['success'] == "false") {
                swal({
                    title: "¡Error!",
                    text: "La contraseña antigua no es la correcta...",
                    icon: "error"});
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}

function actualizarListados(tipo, div) {
    var info = parseInt($("#listado" + div).attr("numero"));
    switch (tipo) {
        case 1:
            info += 5;
            $("#listado" + div).attr("numero", info);
            break;
        case 2:
            info -= 5;
            $("#listado" + div).attr("numero", info);
            break;
    }
    var fd = new FormData();
    fd.append('numero', info);
    fd.append('tabla', div);
    $.ajax({
        type: "POST",
        url: "ajax.php?option=9",
        dataType: "json",
        data: {numero: info, tabla: div},
        success: function (data, textStatus, jqXHR) {
            $("#listado" + data['div']).html(data['success']);
            if (info > 5) {
                var boton = "<button class='botones_listados' onclick=\"actualizarListados(2, '" + div + "')\">Atras</button>";
                $("#listado" + data['div']).append(boton);
            }
            if (data['numero'] > info) {
                var boton = "<button class='botones_listados' onclick=\"actualizarListados(1, '" + div + "')\">Siguiente</button>";
                $("#listado" + data['div']).append(boton);
            }
            $("td").click(verTodosDatosTablaSwal);
            $(".sinVerTodos").off();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}

function verTodosDatosTablaSwal() {
    var cabeceras = [];
    var datos = []
    $(this).parents('tr').find("td").each(function () {
        if (!($(this).html().includes("div") || $(this).html().includes("input") || $(this).html().includes("button"))) {
            datos.push($(this).html());
        }
    });
    $(this).parents('tr').parent('tbody:first-child').find('th').each(function () {
        cabeceras.push($(this).html());
    });
    var resultado = "";
    for (var i = 0; i < datos.length; i++) {
        resultado += cabeceras[i] + ": " + datos[i] + "<br />";
    }
    swal({
        title: "¡Toda la información!",
        html: resultado,
        icon: "info",
    });
}

function verConciertosLocal() {
    $("#contenido_datos").html("<h3>Concietos sin assignar</h3><div id='listadoconciertos' numero='0'></div><h3>Conciertos asignados</h3><div id='listadoconciertosok' numero='0'></div>");
    actualizarListados(1, "conciertos");
    actualizarListados(1, "conciertosok");
}

function eliminarConcierto(id) {
    $.ajax({
        type: "POST",
        url: "ajax.php?option=12",
        dataType: "json",
        data: {idconcierto: id},
        success: function (data, textStatus, jqXHR) {
            swal({
                title: "¡Atención!",
                text: data['success'],
                icon: "info"});
            $("#listadoconciertos").attr("numero", 0);
            actualizarListados(1, "conciertos");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}

function escogerGrupo(id, e) {
    var div = $("#concierto" + id);
    $(e).parent().html("<h5>Participantes...</h5><div id='listadoparticipantes_" + id + "' numero='0'></div>");
    actualizarListados(1, "participantes_" + id);

}

function aceptarGrupo(idconcierto, idgrupo) {
    $.ajax({
        type: "POST",
        url: "ajax.php?option=14",
        dataType: "json",
        data: {idconcierto: idconcierto, idgrupo: idgrupo},
        success: function (data, textStatus, jqXHR) {
            swal({
                title: "¡Atención!",
                text: data['success'],
                icon: "info"});
            verConciertosLocal();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}

function verConciertosMusico() {
    $("#contenido_datos").html("<h3>Conciertos de mi genero</h3><div id='listadoconciertos_genero' numero='0'></div><h3>Mis proximo conciertos</h3><div id='listadoconciertoshacer' numero='0'></div>");
    actualizarListados(1, "conciertos_genero");
    actualizarListados(1, "conciertoshacer");
}

function check(e) {
    if ($(e).prop('checked')) {
        var apuntado = 1;
    } else {
        var apuntado = 0;
    }
    $.ajax({
        type: "POST",
        url: "ajax.php?option=13",
        dataType: "json",
        data: {tipo: apuntado, idconcierto: $(e).attr("idconcierto")},
        success: function (data, textStatus, jqXHR) {
            swal({
                title: "¡Atención!",
                text: data['succes'],
                icon: "info"});
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}

function votarMusicoListado() {
    $("#contenido_datos").html("<h3>Listado Musicos</h3><div id='listadomusicos' numero='0'></div>");
    actualizarListados(1, "musicos");
}

function votarConciertoListado() {
    $("#contenido_datos").html("<h3>Listado Conciertos</h3><div id='listadovotarconcie' numero='0'></div>");
    actualizarListados(1, "votarconcie");
}

function votarMusico(id, e) {
    $.ajax({
        type: "POST",
        url: "ajax.php?option=15",
        dataType: "json",
        data: {idmusico: id, estado: $(e).attr("estat")},
        success: function (data, textStatus, jqXHR) {
            swal({
                title: "Atención",
                text: data['succes'],
                icon: "info"
            });
            $(e).attr("estat", data['estado']);
            $(e).children('img').attr("src", "img/" + data['estado'] + ".png");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}

function votarConcierto(id, e) {
    $.ajax({
        type: "POST",
        url: "ajax.php?option=16",
        dataType: "json",
        data: {idconcierto: id, estado: $(e).attr("estat")},
        success: function (data, textStatus, jqXHR) {
            swal({
                title: "Atención",
                text: data['succes'],
                icon: "info"
            });
            $(e).attr("estat", data['estado']);
            $(e).children('img').attr("src", "img/" + data['estado'] + ".png");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}

function buscador() {
    $("#contenido_datos").load('ajax.php?option=17');
}

function buscar_info() {
    $.ajax({
        type: 'POST',
        url: "ajax.php?option=18",
        dataType: 'json',
        data: {tipo: $("#tipo").val()},
        success: function (data, textStatus, jqXHR) {
            $("#buscador").html(data["success"]);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });

}

function buscarMusico() {
    $("#resultados").html("<h3>Listado Musicos</h3><div id='listadomusicos' numero='0'></div>");
    actualizarListados(1, "musicos/*/" + $("#info_buscar").val());
}

function buscarConcierto() {
    $("#resultados").html("<h3>Listado Conciertos</h3><div id='listadovotarconcie' numero='0'></div>");
    actualizarListados(1, "votarconcie/*/" + $("#info_buscar").val());
}

function verUbicacion(e) {
    var ubicacion=$(e).attr("ubicacion");
    var datos = "<div id='mapa2'></div>";
    swal({
        title: "¡Aqui tienes tu mapa!",
        html: datos});
    init(ubicacion);
}

function init(ubicacion) {
    $('#mapa2')
            .gmap3({
                zoom: 4
            })
            .infowindow({content: "contentString"})
            .marker([
                {address: ubicacion, data: "<h3>La ubicacion del local...</h3><div>"+ubicacion+"</div>", icon: "http://maps.google.com/mapfiles/marker_red.png"},
            ])
            .on('click', function (marker) {
                marker.setIcon('http://maps.google.com/mapfiles/marker_green.png');
                var map = this.get(0);
                var infowindow = this.get(1);
                infowindow.setContent(marker.data);
                infowindow.open(map, marker);
            })
            .then(function (markers) {
                markers[0].setIcon('http://maps.google.com/mapfiles/marker_orange.png');
            })
            .fit();
}