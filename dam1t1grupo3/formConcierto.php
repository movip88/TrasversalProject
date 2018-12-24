<form action="" method="POST" onsubmit="return comprovar()" id="form_conciertos" >
    <label>Nombre</label>
    <input type="text" class="form_info" name="nombre" maxlength="200" minlength="1" required="">
    <br />
    <label>Dia</label>
    <input type="date" class="form_info" name="dia" required="">
    <br />
    <label>Hora</label>
    <input type="time" class="form_info" name="hora" required="">
    <br />
    <label>Precio</label>
    <input type="number" class="form_info" name="precio" required="">
    <br />
    <label for="genero">Genero: </label>
    <select name="genero" class="form_info">
        <?php
        require_once './database.php';
        $resultado = select_generico("genero", "null", "null");
        while ($fila = mysqli_fetch_assoc($resultado)) {
            echo "<option value='" . $fila['idgenero'] . "'>" . $fila['nombre'] . "</option>";
        }
        ?>
    </select>
    <br />
    <input type="submit" id="enviar_concierto" value="enviar">
</form>

