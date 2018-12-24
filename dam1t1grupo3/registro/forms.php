<?php
require_once '../database.php';

switch ($_GET['option']) {
    case 1:
        echo "<h3 class='tipo' tipo='1'>Esta pagina esta diseñada para ti nuevo musico</h3>";
        break;
    case 2:
        echo "<h3 class='tipo' tipo='2'>Esta pagina esta diseñada para ti nuevo fan</h3>";
        break;
    case 3:
        echo "<h3 class='tipo' tipo='3'>Esta pagina esta diseñada para ti nuevo local</h3>";
        break;
}
?>
<form action="" method="POST" onsubmit="return enviar()" name="formulariAlta">
    <label for="nombre">Nombre: </label>
    <input type="text" name="nombre" class="form_info" id="nombre" maxlength="45" required="">
    <br />
    <label for="apellido">Apellido: </label>
    <input type="text" name="apellido" class="form_info" id="apellido" maxlength="45" required="">
    <br />
    <label for="comunidad">Comunidad: </label>
    <select id="comunidad" onchange="cargarProvincia()">
        <option></option>
        <?php
        $resultado = select_ciudades_comunidades_provincias(1);
        while ($fila = mysqli_fetch_assoc($resultado)) {
            echo "<option value='" . $fila['comunidad'] . "'>" . $fila['comunidad'] . "</option>";
        }
        ?>
    </select>
    <br />
    <div id="provincias">
        
    </div>
    <div id="municipios">
        
    </div>
    <label for="telefono">Telefono: </label>
    <input type="number" name="telefono" class="form_info" id="telefono" maxlength="9" required="">
    <br />
    <label for="email">Email: </label>
    <input type="email" name="email" class="form_info" id="email" maxlength="80" required="">
    <br />
    <label for="nombre_usuario">Nombre usuario: </label>
    <input type="text" id="nombre_usuario" onchange="checkUsername()" name="nombre_usuario" class="form_info" id="nombre_usuario" maxlength="10" required="">
    <label id="user_corect" evitar="true" style="display: none;color:green">El nombre de usuario es correcto</label>
    <label id="user_bad" evitar="true" style="display: none;color:red">El nombre de usuario ya esta en uso</label>
    <br />
    <label for="contra">Contraseña: </label>
    <input type="password" name="contra" class="form_info" id="contra" minlength="6" maxlength="10" required="">
    <br />
    <label evitar="true" for="contra2">Repetir Contraseña: </label>
    <input type="password" evitar="true" name="contra2" class="form_info" id="contra2" minlength="6" maxlength="10" required="">
    <label id="error" evitar="true" style="display: none">Las contraseñas no coinciden</label>
    <br />
    <br />
    <label for="web">Web: </label>
    <input type="url" name="web" class="form_info" id="web" required="">
    <br />
    <label for="web">Imagen: </label>
    <input type="file" name="imagen" id="imagen" required="">
    <br />
    <?php
    switch ($_GET['option']) {
        case 1:
            ?>
            <label for="genero">Genero: </label>
            <select name="genero" class="form_info">
                <?php
                $resultado = select_generico("genero","null","null");
                while ($fila = mysqli_fetch_assoc($resultado)) {
                    echo "<option value='" . $fila['idgenero'] . "'>" . $fila['nombre'] . "</option>";
                }
                ?>
            </select>
            <br />
            <label for="nombre_art">Nombre artistico: </label>
            <input type="text" name="nombre_art" class="form_info" id="nombre_art" maxlength="60" required="">
            <br />
            <label for="componentes">Numero de componentes: </label>
            <input type="number" name="numero_componentes" class="form_info" id="componentes" max="11" required="">
            <?php
            break;
        case 2:
            ?>
            <label for="sexo" evitar="true" >Sexo: </label>
            <input type="radio" class="eleccion" name="sexo" value="hombre" checked="">Hombre
            <input type="radio" class="eleccion" name="sexo" value="mujer">Mujer
            <br />
            <label for="fecha">Fecha nacimiento: </label>
            <input type="date" name="fecha" class="form_info" id="fecha" required="">
            <?php
            break;
        case 3:
            ?>
            <form action="" method="POST" onsubmit="return enviar()">
                <label for="direccion">Direccion: </label>
                <input type="text" name="direccion" class="form_info" id="direccion" maxlength="60" required="">
                <br />
                <label for="aforo_maximo">Aforno maximo: </label>
                <input type="number" name="aforo_maximo" class="form_info" id="aforo_maximo" required="">
                <?php
                break;
        }
        ?>
        <br />
        <input id="submit" type="submit" />
    </form>
