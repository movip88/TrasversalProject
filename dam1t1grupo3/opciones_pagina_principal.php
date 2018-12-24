<?php
require_once './database.php';
session_start();

switch ($_GET['option']) {
    case 1:
        ?>
        <form method="POST" onsubmit="return validarEntrada()">
            <label for="usuario"> Usuario </label>
            <input type="text" id="usuario" name="user" placeholder="Introduce user name" required="" maxlength="10" />
            <label for="contraseña"> Contraseña </label>
            <input type="password" id="contraseña" name="pass" placeholder="Contraseña" required="" maxlength="10" />
            <br />
            <input type="submit" value="Entrar" class="botenos_inicio"/>
            <button onclick="window.location.href = './registro/registar.html'" class="botenos_inicio"> Registrar </button>
        </form>
        <?php
        break;
    case 2:
        ?>
        <div id="listados">
            <h3>Listado Proximos conciertos</h3>
            <div id="listadotodosconcier" numero="5">
                <table>
                    <tr>
                    <tr><th>Nombre</th><th>Artista</th><th>Dia</th><th class='ocultar'>Hora</th><th class='ocultar'>Precio</th><th>Local</th><th class='ocultar'>Ciudad</th><th class='ocultar'>Dirección</th></tr>
                    </tr>
                    <?php
                    $resultado = listados(0, "todosconcier");
                    while ($fila = mysqli_fetch_assoc($resultado)) {
                        echo "<tr>";
                        foreach ($fila as $clave => $nombre) {
                            if ($clave == "munucipio" || $clave == "pago" || $clave == "hora" || $clave == "direccion") {
                                if ($clave == "direccion") {
                                    ?>
                                    <td class='ocultar sinVerTodos'> <label ubicacion='<?php echo $fila['munucipio'].",".$nombre; ?>' onclick='verUbicacion(this)' style='border-bottom:2px solid; cursor:pointer;'><?php echo $nombre;?></label> </td>
                               <?php
                                    } else {
                                    echo "<td class='ocultar'>$nombre</td>";
                                }
                            } else {
                                echo "<td>$nombre</td>";
                            }
                        }
                        echo "</tr>";
                    }
                    ?>
                </table>
                <?php
                if (numeroRegistros("todosconcier") >= 5) {
                    ?>
                    <button class="botones_listados" onclick = "actualizarListados(1, 'todosconcier')">Siguiente</button>
                    <?php
                }
                ?>
            </div>
            <h3>Top 10 mejores musicos</h3>
            <div id="listadotop10musicos">
                <table>
                    <tr>
                    <tr><th>Nombre</th><th>Nombre artistico</th><th>Genero</th><th>Votos</th></tr>
                    </tr>
                    <?php
                    $resultado = listados(0, "top10musicos");
                    while ($fila = mysqli_fetch_assoc($resultado)) {
                        echo "<tr>";
                        foreach ($fila as $nombre) {
                            echo "<td>$nombre</td>";
                        }
                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>
            <script>
                $("td").click(verTodosDatosTablaSwal);
                $(".sinVerTodos").off();
            </script>
            <?php
            break;
        case 3:
            $resultado = mysqli_fetch_assoc(select_generico("usuario", $_SESSION['idusuario'], "idusuario"));
            ?>
            <div id="datos_usuario">
                <div class="user" id="text">
                    <label>Bienvenido a tu pagina de inicio <?php echo $_SESSION['type_user']; ?>:</label>
                    <h4><?php echo $resultado['nombre'] . " " . $resultado['apellidos']; ?></h4>
                </div>
                <div class="user" id="foto">
                    <img id="imagen_usuario" src="<?php echo $resultado['imagen']; ?>"/>
                </div>
                <button onclick="cerrarSession()" id="cerrar_session">Cerrar sesión</button>
            </div>
            <?php
            break;
        case 4:
//fan
            datosUsuarios();
            ?>
            <div class="opcion">
                <label onclick="votarMusicoListado()">Votar musicos</label>
            </div>
            <br />
            <div class="opcion">
                <label onclick="votarConciertoListado()">Votar conciertos</label>
            </div>
            <br />
            <div class="opcion">
                <label onclick="buscador()">Buscador</label>
            </div>
        </div>
        <div id="contenido_datos">

        </div>
        <?php
        break;
    case 5:
//musico
        datosUsuarios();
        ?>
        <div class="opcion">
            <label onclick="verConciertosMusico()">Ver conciertos</label>
        </div>
        <br />
        </div>
        <div id="contenido_datos">

        </div>
        <?php
        break;
    case 6:
//local
        datosUsuarios();
        ?>
        <div class="opcion">
            <label onclick="cargarCrearConcierto()">Creación de conciertos</label>
        </div>
        <br />
        <div class="opcion">
            <label onclick="verConciertosLocal()">Ver mis conciertos</label>
        </div>
        <br />
        </div>
        <div id="contenido_datos">

        </div>
        <?php
        break;
}

function datosUsuarios() {
    ?>
    <h3 id="opcines">Opciones</h3>
    <div id="menus">
        <div class="opcion">
            <label id="ver_modificar" onclick="verDatos()">Ver / modificar datos del perfil</label>
        </div>
        <br />
        <?php
    }
    ?>

