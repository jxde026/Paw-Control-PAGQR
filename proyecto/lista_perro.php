<?php 
session_start(); // Esto tiene que estar al comienzo sí o sí

if (!isset($_SESSION['usuario'])) {
    session_destroy(); // Termina la sesión
    header("Location: index.php"); // Redirige al login
    exit();
}
$nombre = $_SESSION["usuario"];

$conn = mysqli_connect("sql311.infinityfree.com", "if0_37488786", "XpzOBiDgIP", "if0_37488786_proyecto") or die("Error en la conexión");
mysqli_set_charset($conn, "utf8mb4");
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paw Control Lista de perros</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="texto">
    <button id="Boton-Volver"><a href="index.php">Volver</a></button>
    <br>
    <div class="fondo-lista">
        <div class="titulos">
            <?php
            echo '<h1 id="bienvenido">Bienvenido ' . $_SESSION["usuario"] . '!</h1>';
            ?>

            <h1>Paw Control</h1>
            <h2>Buscar mascota por ID</h2>
            <!-- Formulario para buscar por ID -->
            <form method="POST" action="">
                <label for="codigoperro">ID:</label>
                <input id="buscar" type="text" id="codigoperro" name="codigoperro">
                <button type="submit" id="Boton-Buscar" name="buscar">Buscar</button>
            </form>

            <?php
            // Procesar el formulario de búsqueda por ID
            if (isset($_POST['buscar']) && !empty($_POST['codigoperro'])) {
                $codigoperro = $_POST['codigoperro'];

                // Consulta preparada para buscar por código único
                $sql = "SELECT * FROM perros WHERE codigoperro = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $codigoperro);
                $stmt->execute();
                $resultado = $stmt->get_result();

                if ($resultado->num_rows > 0) {
                    echo "<h3>Resultado de la búsqueda:</h3>";
                    echo "<div style='overflow-x:auto;'>";
                    echo "<table border='1' align='center'>";
                    echo "<tr><th>Foto</th><th>ID</th><th>Nombre</th><th>Edad</th><th>Dueño</th><th>Metodo de contacto</th>";
                    while ($perro = $resultado->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><img src='data:image/jpeg;base64," . base64_encode($perro['fotoperro']) . "' alt='Foto de " . $perro['nombreperro'] . "' class='foto-perro'></td>";
                        echo "<td>{$perro['codigoperro']}</td>";
                        echo "<td>{$perro['nombreperro']}</td>";
                        echo "<td>{$perro['Edad']}</td>";
                        echo "<td>{$perro['dueño']}</td>";
                        echo "<td>{$perro['Metodo_contacto']}</td>";
                        if($nombre == $perro['dueño']){
                            $_SESSION["idperro"] = $perro['codigoperro'];  
                            echo "<td><a class='boton-ver-perfil' href='perfil_perro.php?codigoperro={$fila['codigoperro']}'>Ver perfil</a></td>";
                        }
                    }
                    echo "</table>";
                    echo "</div>";
                } else {
                    echo "<p>No se encontró ninguna mascota con ese código único.</p>";
                }

                $stmt->close();
            }
            ?>
        </div>

        <div class="lista">
            <h1>Lista de mascotas</h1> 
            <?php
            // Solo mostrar los botones si no es un usuario invitado
            if ($_SESSION["usuario"] != "invitado") {
                echo "<form method='POST' action=''>";
                echo "<button id='Boton-Añadir'><a href='registro_perro.php'>Añadir una mascota</a></button> &nbsp &nbsp &nbsp";
                echo "<button type='submit' id='Boton-Añadir' name='tus_perros'>Tus mascotas</button>";
                echo "</form>";
            }

            // Mostrar las mascotas del usuario
            if (isset($_POST['tus_perros'])) {
                $nombre = $_SESSION["usuario"];
                $sql = "SELECT * FROM perros WHERE dueño = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $nombre);
                $stmt->execute();
                $resultado = $stmt->get_result();

                if ($resultado->num_rows > 0) {
                    echo "<h3>Tus mascotas:</h3>";
                    echo "<div style='overflow-x:auto;'>";
                    echo "<table border='1'align='center'>";
                    echo "<tr><th>Foto</th><th>ID</th><th>Nombre</th><th>Edad</th><th>Dueño</th><th>Metodo de contacto</th>";
                    while ($perro = $resultado->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><img src='data:image/jpeg;base64," . base64_encode($perro['fotoperro']) . "' alt='Foto de " . $perro['nombreperro'] . "' class='foto-perro'></td>";
                        echo "<td>{$perro['codigoperro']}</td>";
                        echo "<td>{$perro['nombreperro']}</td>";
                        echo "<td>{$perro['Edad']}</td>";
                        echo "<td>{$perro['dueño']}</td>";
                        echo "<td>{$perro['Metodo_contacto']}</td>";
                        echo "<td><a class='boton-ver-perfil' href='perfil_perro.php?codigoperro={$perro['codigoperro']}'>Ver perfil</a></td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    echo "</div>";
                } else {
                    echo "<p>No se encontró ninguna mascota.</p>";
                }

                $stmt->close();
            }
            ?>

            <!-- Mostrar todas las mascotas -->
            <div style='overflow-x:auto;'>
            <table align="center" border=1>
                <tr>
                    <th>Foto</th>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Edad</th>
                    <th>Dueño</th>
                    <th>Metodo de contacto</th>
                    <?php
                    if ($_SESSION["usuario"] != "invitado") {
                        echo "<th>Perfil</th>";
                    }
                    ?>
                </tr>
                <?php
                // Consulta para obtener todas las mascotas
                $sql = "SELECT * FROM perros";
                $resultado = $conn->query($sql);
                

                if ($resultado->num_rows > 0) {
                    // Mostrar cada perro en una fila de la tabla
                    while ($fila = $resultado->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><img src='data:image/jpeg;base64," . base64_encode($fila['fotoperro']) . "' alt='Foto de " . $fila['nombreperro'] . "' class='foto-perro'></td>";
                        echo "<td>{$fila['codigoperro']}</td>";
                        echo "<td>{$fila['nombreperro']}</td>";
                        echo "<td>{$fila['Edad']}</td>";
                        echo "<td>{$fila['dueño']}</td>";
                        echo "<td>{$fila['Metodo_contacto']}</td>";
                        if($nombre == $fila['dueño']){
                            $_SESSION["idperro"] = $fila['codigoperro'];  
                            echo "<td><a class='boton-ver-perfil' href='perfil_perro.php?codigoperro={$fila['codigoperro']}'>Ver perfil</a></td>";
                        }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No se encontraron perros registrados.</td></tr>";
                }
                ?>
            </table>
            </div>
        </div>    
    </div>
</div>
</body>
</html>