<?php
require_once './database.php';
session_start();
$_POST = escape($_POST);

switch ($_GET['option']) {
    case 1:
        if (isset($_SESSION['type_user'])) {
            $respuesta['cabecera'] = 'opciones_pagina_principal.php?option=3';
            switch ($_SESSION['type_user']) {
                case "fan":
                    $respuesta['contenido_pagina'] = 'opciones_pagina_principal.php?option=4';
                    break;
                case "musico":
                    $respuesta['contenido_pagina'] = 'opciones_pagina_principal.php?option=5';
                    break;
                case "local":
                    $respuesta['contenido_pagina'] = 'opciones_pagina_principal.php?option=6';
                    break;
            }
        } else {
            $respuesta['cabecera'] = 'opciones_pagina_principal.php?option=1';
            $respuesta['contenido_pagina'] = 'opciones_pagina_principal.php?option=2';
        }
        $respuesta = json_encode($respuesta);
        echo $respuesta;
        break;
    case 2:
        $resultado = select_generico("usuario", $_POST['nombre'], "nombre_usuario", "idusuario,password,tipo");
        if (mysqli_num_rows($resultado) == 1) {
            $resultado = mysqli_fetch_assoc($resultado);
            if (password_verify($_POST['contra'], $resultado['password'])) {
                $_SESSION['type_user'] = $resultado['tipo'];
                $_SESSION['idusuario'] = $resultado['idusuario'];
                echo '{"success":"true"}';
            } else {
                echo '{"success":"La contraseña no es la correcta"}';
            }
        } else {
            echo '{"success":"El usuario no existe"}';
        }
        break;
    case 3:
        session_destroy();
        echo '{"success":"true"}';
        break;
    case 4:
        $resultado = json_encode(mysqli_fetch_assoc(select_datos_usuarios($_SESSION['type_user'], $_SESSION['idusuario'])));
        echo $resultado;
        break;
    case 5:
        switch ($_POST['option']) {
            case 0:
                $string = "Modificar nombre de usuario: <input campo_bbdd='nombre_usuario' onchange='checkUsername()' id='input_modificar' type='text' required='' maxlength='10' />";
                break;
            case 1:
                $string = "Modificar nombre: <input type='text' campo_bbdd='nombre' id='input_modificar' required='' maxlength='45' />";
                break;
            case 2:
                $string = "Modificar apellido: <input type='text' campo_bbdd='apellidos' id='input_modificar' required='' maxlength='45' />";
                break;
            case 3:
                $string = "Modificar email: <input type='email' campo_bbdd='email' id='input_modificar' required='' maxlength='80' />";
                break;
            case 4:
                $string = "Modificar telefono: <input type='number' campo_bbdd='telefono' id='input_modificar' required='' maxlength='9' />";
                break;
            case 5:
                $string = "Modificar ciudad: <select id='comunidad' onchange='cargarProvincia()'><option></option>";
                $resultado = select_ciudades_comunidades_provincias(1);
                while ($fila = mysqli_fetch_assoc($resultado)) {
                    $string .= "<option value='" . $fila['comunidad'] . "'>" . $fila['comunidad'] . "</option>";
                }
                $string .= "</select><br /><div id='provincias'></div><div id='municipios'>";
                break;
            case 6:
                $string = "Modificar imagen: <input type='file' campo_bbdd='imagen' id='input_modificar' required=''/>";
                break;
            case 10:
                $string = "Modificar Contraseña:<br />Contraseña antigua: <input type='password' onchange='checkPassword()' id='antigua' minlength='6' maxlength='10' required=''><br />";
                $string .= "Contraseña nueva: <input type='password' minlength='6' maxlength='10' id='contra' required=''><br />";
                $string .= "Repetir contraseña nueva: <input type='password' campo_bbdd='password' id='input_modificar' minlength='6' maxlength='10' required=''><br />";
                break;
        }
        switch ($_SESSION['type_user']) {
            case "fan":
                switch ($_POST['option']) {
                    case 7:
                        $string = "Modificar sexo: <select campo_bbdd='sexo*' id='input_modificar'><option></option><option value='hombre'>Hombre</option><option value='mujer'>Mujer</option><option value='mucho'>Mucho</option></select>";
                        break;
                    case 8:
                        $string = "Modificar fecha nacimiento: <input campo_bbdd='nacimiento*' id='input_modificar' type='date' required=''>";
                        break;
                }
                break;
            case "musico":
                switch ($_POST['option']) {
                    case 7:
                        $string = "Modificar nombre artistico: <input type='text' campo_bbdd='nombre_artistico*' id='input_modificar' required='' maxlength='60' />";
                        break;
                    case 8:
                        $string = "Modificar genero: <select campo_bbdd='genero*' id='input_modificar'><option></option>";
                        $resultado = select_generico("genero", "null", "null");
                        while ($fila = mysqli_fetch_assoc($resultado)) {
                            $string .= "<option value='" . $fila['idgenero'] . "'>" . $fila['nombre'] . "</option>";
                        }
                        $string .= "</select>";
                        break;
                    case 9:
                        $string = "Modificar numero de componentes: <input type='number' campo_bbdd='componentes*' id='input_modificar' max='11' required=''>";
                        break;
                }
                break;
            case "local":
                switch ($_POST['option']) {
                    case 7:
                        $string = "Modificar direccion: <input type='text' maxlength='60' campo_bbdd='direccion*' id='input_modificar' required=''>";
                        break;
                    case 8:
                        $string = "Modificar aforo maximo: <input type='number' campo_bbdd='aforo*' id='input_modificar' required=''>";
                        break;
                }
                break;
        }
        echo '{"success":"' . $string . "<button id='modificar'> Modificar </button>" . '"}';
        break;
    case 7:
        if (isset($_POST['valor'])) {
            echo actualizar_datos_usuario($_POST['campo'], $_POST['valor'], $_SESSION['idusuario'], $_SESSION['type_user']);
        } else {
            $resultado = mysqli_fetch_assoc(select_generico("usuario", $_SESSION['idusuario'], "idusuario", "imagen,nombre_usuario"));
            $nombre_foto = $_FILES['valor']['name'];
            $ruta_guardar = "fotos_usuarios/" . $resultado['nombre_usuario'] . "_" . rand(0, 200) . "_" . $nombre_foto;
            $extension_img = pathinfo($ruta_guardar, PATHINFO_EXTENSION);
            if ($extension_img == "jpg" || $extension_img == "png") {
                if (move_uploaded_file($_FILES['valor']['tmp_name'], $ruta_guardar)) {
                    unlink($resultado['imagen']);
                    if (update_generico("usuario", "imagen", $ruta_guardar, "idusuario", $_SESSION['idusuario'])) {
                        echo '{"success":"Se ha modificado correctamete!!"}';
                    } else {
                        echo '{"success":"La imagen no se ha podido guardar correctamente"}';
                    }
                } else {
                    echo '{"success":"La imagen no se ha podido guardar correctamente"}';
                }
            } else {
                echo '{"success":"El formato de la imagen no es el correcto solo jpg o png"}';
            }
        }
        break;
    case 8:
        $resultado = mysqli_fetch_assoc(select_generico("usuario", $_SESSION['idusuario'], "idusuario", "password"));
        if (password_verify($_POST['contra'], $resultado['password'])) {
            echo '{"success":"true"}';
        } else {
            echo '{"success":"false"}';
        }
        break;
    case 9:
        if (strpos($_POST['tabla'], "participantes") !== false) {
            list($_POST['tabla'], $idconcierto) = explode("_", $_POST['tabla']);
        } else if (strpos($_POST['tabla'], "votarconcie/*/") !== false || strpos($_POST['tabla'], "musicos/*/") !== false) {
            list($_POST['tabla'], $where) = explode("/*/", $_POST['tabla']);
        }
        $string = "<table>";
        switch ($_POST['tabla']) {
            case "locales":
                $string .= "<tr><th>Nombre</th><th>Email</th><th>Telefono</th><th>Ciudad</th><th>Dirreccion</th><th>Aforo maximo</th></tr>";
                break;
            case "musicos":
                $string .= "<tr><th>Nombre</th><th class='ocultar'>Email</th><th class='ocultar'>Telefono</th><th class='ocultar'>Ciudad</th><th>Nombre artistico</th><th>Genero</th><th>Voto</th></tr>";
                break;
            case "conciertos":
                $id = $_SESSION['idusuario'];
                $string .= "<tr><th>Nombre</th><th>Dia</th><th class='ocultar'>Hora</th><th class='ocultar'>Precio</th><th>Genero</th><th>Escoger grupo</th><th>Eliminar</th></tr>";
                break;
            case "conciertosok":
                $id = $_SESSION['idusuario'];
                $string .= "<tr><th>Nombre</th><th>Dia</th><th class='ocultar'>Hora</th><th class='ocultar'>Precio</th><th>Genero</th><th>Grupo</th></tr>";
                break;
            case "conciertos_genero":
                $id = mysqli_fetch_assoc(select_generico("musico", $_SESSION['idusuario'], "idmusico", "genero"))['genero'];
                $string .= "<tr><th>Nombre</th><th>Dia</th><th class='ocultar'>Hora</th><th class='ocultar'>Precio</th><th>Local</th><th class='ocultar'>Ciudad</th><th class='ocultar'>Dirección</th><th>Apuntarse / Desapuntarse</th></tr>";
                break;
            case "participantes":
                $string .= "<tr><th>Nombre</th><th>Componentes</th><th>Puntuacion</th><th>Aceptar</th></tr>";
                break;
            case "conciertoshacer":
                $id = $_SESSION['idusuario'];
                $string .= "<tr><th>Nombre</th><th>Dia</th><th class='ocultar'>Hora</th><th class='ocultar'>Precio</th><th>Local</th><th class='ocultar'>Ciudad</th><th class='ocultar'>Dirección</th></tr>";
                break;
            case "todosconcier":
                $string .= "<tr><th>Nombre</th><th>Artista</th><th>Dia</th><th>Hora</th><th>Precio</th><th>Local</th><th>Ciudad</th><th>Dirección</th></tr>";
                break;
            case "votarconcie":
                $string .= "<tr><th>Nombre</th><th>Artista</th><th>Dia</th><th class='ocultar'>Hora</th><th class='ocultar'>Precio</th><th>Local</th><th class='ocultar'>Ciudad</th><th class='ocultar'>Dirección</th><th>Voto</th></tr>";
                break;
        }
        if (strpos($_POST['tabla'], "concierto") !== false) {
            $resultado = listados($_POST['numero'] - 5, $_POST['tabla'], $id);
        } else if ($_POST['tabla'] == "participantes") {
            $resultado = listados($_POST['numero'] - 5, $_POST['tabla'], $idconcierto);
        } else {
            if (isset($where)) {
                $resultado = listados($_POST['numero'] - 5, $_POST['tabla'], 0, $where);
            } else {
                $resultado = listados($_POST['numero'] - 5, $_POST['tabla']);
            }
        }
        if (mysqli_num_rows($resultado) == 0) {
            $string .= "<tr><td colspan='3'>No hay valores aún...</td></tr>";
        }
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $string .= "<tr>";
            foreach ($fila as $clave => $nombre) {

                if ($clave == "Ver grupos") {
                    $string .= "<td class='sinVerTodos'><button class='boton_local' onclick='escogerGrupo(" . $fila['idconcierto'] . ",this)'>$nombre</button></td>";
                } else if ($clave == "Eliminar") {
                    $string .= "<td class='sinVerTodos'><button class='boton_local' onclick='eliminarConcierto(" . $fila['idconcierto'] . ")'>$nombre</button></td>";
                } else if ($clave == "Apuntarse") {
                    $resultado2 = ver_propuesta_determinada($fila['idconcierto'], $_SESSION['idusuario']);
                    if (mysqli_num_rows($resultado2) == 0) {
                        $string .= "<td class='sinVerTodos'><input type='checkbox' onchange='check(this)' idconcierto='" . $fila['idconcierto'] . "'></td>";
                    } else {
                        $string .= "<td class='sinVerTodos'><input type='checkbox' onchange='check(this)' idconcierto='" . $fila['idconcierto'] . "' checked=''></td>";
                    }
                } else if ($clave == "Aceptar") {
                    $string .= "<td><button class='boton_local' onclick='aceptarGrupo(" . $fila['idconcierto'] . "," . $fila['idgrupo'] . ")'>$nombre</button></td>";
                } else if ($clave == "voto") {
                    if (isset($fila['idusuario'])) {
                        $estado = mysqli_num_rows(buscar_voto_musico($_SESSION['idusuario'], $fila['idusuario']));
                        $string .= "<td class='sinVerTodos'><div id='heart-shape' onclick='votarMusico(" . $fila['idusuario'] . ",this)' estat='$estado'><img src='img/$estado.png' alt='''/></div></td>";
                    } else {
                        $estado = mysqli_num_rows(buscar_voto_concierto($_SESSION['idusuario'], $fila['idconcierto']));
                        $string .= "<td class='sinVerTodos'><div id='heart-shape' onclick='votarConcierto(" . $fila['idconcierto'] . ",this)' estat='$estado'><img src='img/$estado.png' alt='''/></div></td>";
                    }
                } else if (strpos($clave, "id") !== false) {
                    continue;
                } else if ($clave == "email" || $clave == "telefono" || $clave == "munucipio" || $clave == "pago" || $clave == "hora" || $clave == "direccion") {
                    if ($clave == "direccion") {
                        $ubicación=$fila['munucipio'].",".$nombre;
                        $string .= "<td class='ocultar sinVerTodos'> <label ubicacion='$ubicación' onclick='verUbicacion(this)' style='border-bottom:2px solid; cursor:pointer;'>$nombre</label></td>";
                    } else {
                        $string .= "<td class='ocultar'>$nombre</td>";
                    }
                } else {
                    $string .= "<td>$nombre</td>";
                }
            }
            $string .= "</tr>";
        }
        $string .= "</table>";
        if (strpos($_POST['tabla'], "concierto") !== false) {
            echo '{"success":"' . $string . '","numero":"' . numeroRegistros($_POST['tabla'], $id) . '","div":"' . $_POST['tabla'] . '"}';
        } else if ($_POST['tabla'] == "participantes") {
            echo '{"success":"' . $string . '","numero":"' . numeroRegistros($_POST['tabla'], $idconcierto) . '","div":"' . $_POST['tabla'] . "_" . $idconcierto . '"}';
        } else {
            echo '{"success":"' . $string . '","numero":"' . numeroRegistros($_POST['tabla']) . '","div":"' . $_POST['tabla'] . '"}';
        }
        break;
    case 10:
        $pass_cifrada = password_hash($_POST['contra'], PASSWORD_DEFAULT);
        if (update_generico("usuario", "password", $pass_cifrada, $_SESSION['idusuario'], "idusuario")) {
            echo '{"success":"Se ha modificado correctamete!!"}';
        } else {
            echo '{"success":"Error modificando el dato"}';
        }
        break;
    case 11:
        $resultado = registrar_concierto($_POST, $_SESSION['idusuario']);
        if ($resultado === true) {
            echo '{"success":"Concierto creado correctamente!!"}';
        } else {
            echo '{"success":"' . $resultado . '"}';
        }
        break;
    case 12:
        $resultado = delete_generico("concierto", "idconcierto", $_POST['idconcierto']);
        if ($resultado === true) {
            echo '{"success":"Concierto eliminado correctamente!!"}';
        } else {
            echo '{"success":"' . $resultado . '"}';
        }
        break;
    case 13:
        $result = anadir_sacarPorpuesta($_POST['tipo'], $_POST['idconcierto'], $_SESSION['idusuario']);
        if ($result === true) {
            echo '{"succes":"Actualizado tu tu peticion para el concierto"}';
        } else {
            echo '{"succes":"' . $result . '"}';
        }
        break;
    case 14:
        $resultado = contratarGrupo($_POST['idconcierto'], $_POST['idgrupo']);
        if ($resultado === true) {
            echo '{"success":"Aceptado el grupo correctamente, deseamos que sea un concierto exitoso"}';
        } else {
            echo '{"success":"' . $resultado . '"}';
        }
        break;
    case 15:
        if ($_POST['estado'] == 0) {
            $resultado = votar_musico($_SESSION['idusuario'], $_POST['idmusico']);
            $estado = 1;
        } else {
            $resultado = retirar_voto_musico($_SESSION['idusuario'], $_POST['idmusico']);
            $estado = 0;
        }
        if ($resultado === true) {
            echo '{"succes":"Se ha actualizado tu voto!!","estado":"' . $estado . '"}';
        } else {
            echo '{"succes":"' . $resultado . '"}';
        }
        break;
    case 16:
        if ($_POST['estado'] == 0) {
            $resultado = votar_concierto($_SESSION['idusuario'], $_POST['idconcierto']);
            $estado = 1;
        } else {
            $resultado = retirar_voto_concierto($_SESSION['idusuario'], $_POST['idconcierto']);
            $estado = 0;
        }
        if ($resultado === true) {
            echo '{"succes":"Se ha actualizado tu voto!!","estado":"' . $estado . '"}';
        } else {
            echo '{"succes":"' . $resultado . '"}';
        }
        break;
        break;
    case 17:
        ?>
        <br />
        Que quieres buscar:
        <select id="tipo" onchange="buscar_info()">
            <option></option>
            <option value="1">Musicos</option>
            <option value="2">Conciertos</option>
        </select>
        <br />
        <div id="buscador"></div>
        <br />
        <div id="resultados"></div>
        <?php
        break;
    case 18:
        switch ($_POST['tipo']) {
            case 1:
                echo '{"success":"Introduce el texto para realizar la busqueda: <input type=\'text\' id=\'info_buscar\' onkeyup=\'buscarMusico()\'>"}';
                break;
            case 2:
                echo '{"success":"Introduce el texto para realizar la busqueda: <input type=\'text\' id=\'info_buscar\' onkeyup=\'buscarConcierto()\'>"}';
                break;
        }
        break;
    case 19:

        break;
}
?>
