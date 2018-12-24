<?php

function conectar() {
    $conexion = mysqli_connect("localhost", "root", "", "transversal");
    if (!$conexion) {
        die("No se ha podido establecer la conexión con el servidor");
    }
    mysqli_set_charset($conexion, 'utf8');
    return $conexion;
}

function desconectar($conexion) {
    mysqli_close($conexion);
}

function insert_update_delete($orden) {
    $conexion = conectar();
    if (mysqli_query($conexion, $orden)) {
        $resultado = true;
    } else {
        $resultado = mysqli_error($conexion);
    }
    desconectar($conexion);
    return $resultado;
}

function select($orden) {
    $conexion = conectar();
    $resultado = mysqli_query($conexion, $orden);
    desconectar($conexion);
    return $resultado;
}

function select_ciudades_comunidades_provincias($tipo, $valor = "") {
    //1=comunidad,2=provincia
    switch ($tipo) {
        case 1:
            $consulta = "select CAST(CONVERT(comunidad USING utf8) AS binary) as comunidad from ciudad group by comunidad";
            break;
        case 2:
            $consulta = "select CAST(CONVERT(provincia USING utf8) AS binary) as provincia from ciudad where comunidad='$valor' group by provincia";
            break;
        case 3:
            $consulta = "select CAST(CONVERT(munucipio USING utf8) AS binary) as munucipio, idciudad from ciudad where provincia='$valor'";
            break;
    }
    return select($consulta);
}

function select_generico($tabla, $where, $campo_where, $campos = "*") {
    if ($where == "null") {
        $orden = "select $campos from $tabla";
    } else {
        $orden = "select $campos from $tabla where $campo_where='$where'";
    }
    $conexion = conectar();
    $resultado = mysqli_query($conexion, $orden);
    desconectar($conexion);
    return $resultado;
}

function select_datos_usuarios($tipo, $id) {
    switch ($tipo) {
        case "fan":
            $consulta = "select nombre_usuario as 'Nombre usuario: ',nombre as 'Nombre: ',apellidos as 'Apellidos: ',email as 'Correo electronico: ',telefono as 'Telefono: ',ciudad.munucipio as 'Ciudad :',imagen as 'Imagen: ',sexo as 'Sexo: ', nacimiento as 'Fecha nacimiento: '  from usuario inner join ciudad on usuario.ciudad=ciudad.idciudad inner join fan on usuario.idusuario=fan.idfan where idusuario=" . $id;
            break;
        case "musico":
            $consulta = "select nombre_usuario as 'Nombre usuario: ',usuario.nombre as 'Nombre: ',apellidos as 'Apellidos: ',email as 'Email: ',telefono as 'Telefono: ',ciudad.munucipio as 'Ciudad: ',imagen as 'Imagen: ',nombre_artistico as 'Nombre artistico: ',genero.nombre as 'Genero: ',componentes as 'Numero componentes: ' from usuario inner join ciudad on usuario.ciudad=ciudad.idciudad inner join musico on usuario.idusuario=musico.idmusico inner join genero on musico.genero=genero.idgenero where idusuario=" . $id;
            break;
        case "local":
            $consulta = "select nombre_usuario as 'Nombre usuario: ',nombre as 'Nombre: ',apellidos as 'Apellidos: ',email as 'Correo electronico: ',telefono as 'Telefono: ',ciudad.munucipio as 'Ciudad: ',imagen as 'Imagen: ',direccion as 'Direccion del local: ',aforo as 'Aforo maximo: '  from usuario inner join ciudad on usuario.ciudad=ciudad.idciudad inner join local on usuario.idusuario=local.idlocal where idusuario=" . $id;
            break;
    }
    return select($consulta);
}

function actualizar_datos_usuario($campo, $valor, $idusuario, $tipo_usuario) {
    if (substr($campo, strlen($campo) - 1) == "*") {
        if (insert_update_delete("update $tipo_usuario set " . substr($campo, 0, strlen($campo) - 1) . "='$valor' where id$tipo_usuario=$idusuario") === true) {
            return '{"success":"Se ha modificado correctamete!!"}';
        } else {
            return '{"success":"Error modificando el dato"}';
        }
    } else {
        if (insert_update_delete("update usuario set $campo='$valor' where idusuario=" . $idusuario) === true) {
            return '{"success":"Se ha modificado correctamete!!"}';
        } else {
            return '{"success":"Error modificando el dato"}';
        }
    }
}

function update_generico($tabla, $campo, $valor, $where, $valor_where) {
    if (insert_update_delete("update $tabla set $campo='" . $valor . "' where $where=" . $valor_where) === true) {
        return true;
    } else {
        return false;
    }
}

function escape($array) {
    $conexion = conectar();
    foreach ($array as $clave => $string) {
        $string = strip_tags($string);
        $string = stripslashes($string);
        $string = mysqli_real_escape_string($conexion, $string);
        $array[$clave] = $string;
    }
    desconectar($conexion);
    return $array;
}

function registrar_usuario($array, $pass_cifrada, $tipo_varchar, $tipo, $ruta_guardar) {
    extract($array);
    $orden = "insert into usuario (nombre_usuario,password,nombre,apellidos,email,telefono,ciudad,imagen,tipo) values ('$nombre_usuario','$pass_cifrada','$nombre','$apellido','$email','$telefono','$municipio','$ruta_guardar','$tipo_varchar')";
    $conexion = conectar();
    mysqli_autocommit($conexion, false);
    if (mysqli_query($conexion, $orden)) {
        $id = mysqli_insert_id($conexion);
        switch ($tipo) {
            case 1:
                $orden = "insert into musico values('$id','$nombre_art','$genero','$numero_componentes')";
                break;
            case 2:
                $orden = "insert into fan values('$id','$sexo','$fecha')";
                break;
            case 3:
                $orden = "insert into local values('$id','$direccion','$aforo_maximo')";
                break;
        }
        if (mysqli_query($conexion, $orden)) {
            mysqli_commit($conexion);
            $resultado = true;
        } else {
            $resultado = mysqli_error($conexion);
            mysqli_rollback($conexion);
        }
    } else {
        $resultado = mysqli_error($conexion);
        mysqli_rollback($conexion);
    }
    mysqli_autocommit($conexion, true);
    desconectar($conexion);
    return $resultado;
}

function contratarGrupo($idconcierto, $idmusico) {
    $conexion = conectar();
    mysqli_autocommit($conexion, false);
    $orden = "update propuesta set estado=1 where concierto=$idconcierto and grupo=$idmusico";
    if (mysqli_query($conexion, $orden)) {
        $orden2 = "update propuesta set estado=2 where concierto=$idconcierto and grupo!=$idmusico";
        if (mysqli_query($conexion, $orden2)) {
            $orden3 = "update concierto set estado=1,grupo=$idmusico where idconcierto=$idconcierto";
            if (mysqli_query($conexion, $orden3)) {
                mysqli_commit($conexion);
                $resultado = true;
            } else {
                $resultado = mysqli_error($conexion);
                mysqli_rollback($conexion);
            }
        } else {
            $resultado = mysqli_error($conexion);
            mysqli_rollback($conexion);
        }
    } else {
        $resultado = mysqli_error($conexion);
        mysqli_rollback($conexion);
    }
    mysqli_autocommit($conexion, true);
    desconectar($conexion);
    return $resultado;
}

function listados($inicio, $tipo, $id = null ,$where="") {
    insert_update_delete("delete from concierto where estado=0 and dia<now()");
    switch ($tipo) {
        case "locales":
            return select("select usuario.nombre,email,telefono,ciudad.munucipio,direccion,aforo from usuario inner join ciudad on usuario.ciudad=ciudad.idciudad inner join local on usuario.idusuario=local.idlocal order by munucipio limit $inicio,5");
            break;
        case "musicos":
            return select("select usuario.nombre as 'aa',email,telefono,ciudad.munucipio,nombre_artistico,genero.nombre,'voto',usuario.idusuario from usuario inner join ciudad on usuario.ciudad=ciudad.idciudad inner join musico on usuario.idusuario=musico.idmusico inner join genero on musico.genero=genero.idgenero where concat(usuario.nombre,email,telefono,ciudad.munucipio,nombre_artistico,genero.nombre,usuario.idusuario) like '%$where%' order by genero.nombre limit $inicio,5");
            break;
        case "conciertos":
            return select("select concierto.nombre as 'Nombre Concierto', dia,hora,pago,genero.nombre as 'nombre genero','Ver grupos','Eliminar',idconcierto from concierto inner join genero on concierto.genero=genero.idgenero where concierto.estado=0 and sala=$id limit $inicio,5");
            break;
        case "conciertosok":
            return select("select concierto.nombre as 'Nombre Concierto', dia,hora,pago,genero.nombre as 'nombre genero', musico.nombre_artistico as 'Nombre musico' from concierto inner join genero on concierto.genero=genero.idgenero inner join musico on musico.idmusico=concierto.grupo where concierto.estado=1 and sala=$id and dia>=now() order by dia,hora desc limit $inicio,5");
            break;
        case "conciertos_genero":
            return select("select concierto.nombre as 'Nombre Concierto', dia,hora,pago,usuario.nombre,ciudad.munucipio,direccion, 'Apuntarse',idconcierto from concierto inner join usuario on usuario.idusuario=concierto.sala inner join ciudad on usuario.ciudad=ciudad.idciudad inner join local on usuario.idusuario=local.idlocal where concierto.estado=0 and genero=$id limit $inicio,5");
            break;
        case "participantes":
            return select("select musico.nombre_artistico, musico.componentes,count(voto_usuario.usuario) as puntuacion,'Aceptar',concierto as 'idconcierto',grupo as 'idgrupo' from propuesta inner join musico on musico.idmusico=propuesta.grupo left join voto_usuario on musico.idmusico=voto_usuario.usuario where concierto=$id group by musico.idmusico limit $inicio,5");
            break;
        case "conciertoshacer":
            return select("select concierto.nombre as 'Nombre Concierto', dia,hora,pago,usuario.nombre,ciudad.munucipio,direccion from concierto inner join usuario on usuario.idusuario=concierto.sala inner join ciudad on usuario.ciudad=ciudad.idciudad inner join local on usuario.idusuario=local.idlocal where grupo=$id and dia>=now() order by dia,hora desc limit $inicio,5");
            break;
        case "todosconcier":
            return select("select concierto.nombre,musico.nombre_artistico,dia,hora,pago,usuario.nombre as 'aa',ciudad.munucipio,local.direccion from concierto inner join usuario on concierto.sala=usuario.idusuario inner join local on concierto.sala=local.idlocal inner join musico on concierto.grupo=musico.idmusico inner join ciudad on usuario.ciudad=ciudad.idciudad where estado=1 and dia>=now() order by dia,hora desc limit $inicio,5");
            break;
        case "votarconcie":
            return select("select concierto.nombre,musico.nombre_artistico,dia,hora,pago,usuario.nombre as 'aa',ciudad.munucipio,local.direccion,'voto',concierto.idconcierto from concierto inner join usuario on concierto.sala=usuario.idusuario inner join local on concierto.sala=local.idlocal inner join musico on concierto.grupo=musico.idmusico inner join ciudad on usuario.ciudad=ciudad.idciudad where estado=1 and dia<now() and concat(concierto.nombre,musico.nombre_artistico,dia,hora,pago,usuario.nombre,ciudad.munucipio,local.direccion,concierto.idconcierto) like '%$where%' order by dia,hora desc limit $inicio,5");
            break;
        case "top10musicos":
            return select("select usuario.nombre as 'aa',nombre_artistico,genero.nombre,count(*) as puntos from usuario inner join musico on usuario.idusuario=musico.idmusico inner join genero on musico.genero=genero.idgenero inner join voto_usuario on musico.idmusico=voto_usuario.usuario group by musico.idmusico order by puntos desc limit $inicio,10");
            break;
    }
}

function numeroRegistros($tipo, $id = null) {
    switch ($tipo) {
        case "locales":
            $resultado = mysqli_fetch_assoc(select("select count(*) as 'num' from usuario where tipo='local'"));
            break;
        case "musicos":
            $resultado = mysqli_fetch_assoc(select("select count(*) as 'num' from usuario where tipo='musico'"));
            break;
        case "conciertos":
            $resultado = mysqli_fetch_assoc(select("select count(*) as 'num' from concierto where concierto.estado=0 and sala=$id"));
            break;
        case "conciertosok":
            $resultado = mysqli_fetch_assoc(select("select count(*) as 'num' from concierto where concierto.estado=1 and sala=$id"));
            break;
        case "conciertos_genero":
            $resultado = mysqli_fetch_assoc(select("select count(*) as 'num' from concierto where concierto.estado=0 and genero=$id"));
            break;
        case "participantes":
            $resultado = mysqli_fetch_assoc(select("select count(*) as 'num' from propuesta where concierto=$id"));
            break;
        case "conciertoshacer":
            $resultado = mysqli_fetch_assoc(select("select count(*) as 'num' from concierto where grupo=$id and dia>=now()"));
            break;
        case "todosconcier":
            $resultado = mysqli_fetch_assoc(select("select count(*) as 'num' from concierto where estado=1 and dia>=now()"));
            break;
        case "votarconcie":
            $resultado = mysqli_fetch_assoc(select("select count(*) as 'num' from concierto where estado=1 and dia<now()"));
            break;
    }
    return $resultado['num'];
}

function registrar_concierto($array, $idlocal) {
    extract($array);
    $insert = insert_update_delete("insert into concierto (nombre,estado,dia,hora,pago,sala,genero) values ('$nombre',0,'$dia','$hora','$precio','$idlocal','$genero')");
    if ($insert === true) {
        return true;
    } else {
        return $insert;
    }
}

function delete_generico($tabla, $campo_where, $condicon_where) {
    $result = insert_update_delete("delete from $tabla where $campo_where=$condicon_where");
    if ($result === true) {
        return true;
    } else {
        return $result;
    }
}

function ver_propuesta_determinada($idconcierto, $idgrupo) {
    return select("select estado from propuesta where concierto=$idconcierto and grupo=$idgrupo");
}

function anadir_sacarPorpuesta($tipo, $idconcierto, $idmusico) {
    switch ($tipo) {
        case 0:
            $result = insert_update_delete("delete from propuesta where concierto=$idconcierto and grupo=$idmusico");
            break;
        case 1:
            $result = insert_update_delete("insert into propuesta values($idconcierto,$idmusico,0)");
            break;
    }
    if ($result === true) {
        return true;
    } else {
        return $result;
    }
}

function buscar_voto_musico($idfan, $idmusico) {
    return select("select * from voto_usuario where fan=$idfan and usuario=$idmusico");
}

function votar_musico($idfan, $idmusico) {
    return insert_update_delete("insert into voto_usuario values('$idfan', '$idmusico')");
}

function retirar_voto_musico($idfan, $idmusico) {
    return insert_update_delete("delete from voto_usuario where fan=$idfan and usuario=$idmusico");
}

function buscar_voto_concierto($idfan, $idconcierto) {
    return select("select * from voto_concierto where fan=$idfan and concierto=$idconcierto");
}

function votar_concierto($idfan, $idconcierto) {
    return insert_update_delete("insert into voto_concierto values('$idfan', '$idconcierto')");
}

function retirar_voto_concierto($idfan, $idconcierto) {
    return insert_update_delete("delete from voto_concierto where fan=$idfan and concierto=$idconcierto");
}
?>