<?php

require_once '../database.php';

$_POST= escape($_POST);

switch ($_GET['option']) {
    case 1:
        $comunidad=$_POST['comunidad'];
        
        $resultado = select_ciudades_comunidades_provincias(2, $comunidad);
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $provincias[] = $fila['provincia'];
        }
        echo json_encode($provincias);
        break;
    case 2:
        $resultado = select_ciudades_comunidades_provincias(3, $_POST['provincia']);
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $municipios[] = array($fila['idciudad'], $fila['munucipio']);
        }
        echo json_encode($municipios);
        break;
    case 3:
        $nombre_foto = $_FILES['foto']['name'];
        $ruta_guardar = "../fotos_usuarios/" . $_POST['nombre_usuario'] . "_" . rand(0, 200) . "_" . $nombre_foto;
        $extension_img = pathinfo($ruta_guardar, PATHINFO_EXTENSION);
        if ($extension_img == "jpg" || $extension_img == "png") {
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_guardar)) {
                $pass_cifrada = password_hash($_POST['contra'], PASSWORD_DEFAULT);
                switch ($_POST['tipo']) {
                    case 1:
                        $tipo_varchar = "musico";
                        break;
                    case 2:
                        $tipo_varchar = "fan";
                        break;
                    case 3:
                        $tipo_varchar = "local";
                        break;
                }
                
                $insert= registrar_usuario($_POST, $pass_cifrada, $tipo_varchar, $_POST['tipo'],substr($ruta_guardar, 3));
                if($insert===true){
                    echo '{"succsess":"true"}';
                } else {
                    echo '{"succsess":"' . $insert . '"}';
                }
            } else {
                echo '{"succsess":"La imagen no se ha podido guardar correctamente"}';
            }
        } else {
            echo '{"succsess":"El formato de la imagen no es el correcto solo jpg o png"}';
        }
        break;
    case 4:
        $resultado= select_generico("usuario",$_POST['nombre'],"nombre_usuario");
        if(mysqli_num_rows($resultado)==0){
            echo '{"succsess":"true"}';
        }else{
            echo '{"succsess":"false"}';
        }
        break;
}
?>

