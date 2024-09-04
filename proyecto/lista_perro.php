<?php 
    session_start(); // esto tiene que estar al comienzo si o si
?>  
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paw Control Lista de perros</title>
    <link rel="stylesheet" href="style.css">
</head>
<?php
	$conn = mysqli_connect("localhost","root","","proyecto") or die ("Error en la conexión");
?>
<body>
 
 
    <div class="texto">
    <button id="Boton-Volver"><a href="index.php">Volver</a></button>
    <br>
    <div class="fondo">
    <div class="titulos">
    <?php
    echo '<h1 id="bienvenido">Bienvenido ' . $_SESSION["usuario"] . '!<h1>';
    ?>
    
	<h1>Paw Control</h1>
	<h2>Buscar mascota por ID</h2>
    <form method="POST">
        <label for="codigoperro">ID:</label>
        <input id="buscar" type="text" id="codigoperro" name="codigoperro">
        <button type="submit" id="Boton-Buscar" name='buscar'>Buscar</button>
    </form>

    <?php
    // Procesar el formulario de búsqueda
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['buscar'])){
            $codigoperro = $_POST['codigoperro'];

            // Consulta preparada para buscar por código único
            $sql = "SELECT * FROM perros WHERE codigoperro = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $codigoperro);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows > 0) {
                echo "<h3>Resultado de la búsqueda:</h3>";
                echo "<table border='1'align='center'>";
                echo "<tr><th>ID</th><th>Nombre</th><th>Ciudad</th><th>Dueño</th><th>Perfil</th></tr>";
                while ($perro = $resultado->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$perro['codigoperro']}</td>";
                    echo "<td>{$perro['nombreperro']}</td>";
                    echo "<td>{$perro['ciudad']}</td>";
                    echo "<td>{$perro['dueño']}</td>";
                    echo "<td><a class='boton-ver-perfil' href='perfil_perro.php?codigoperro={$perro['codigoperro']}'>Ver perfil</a></td>"; // Botón para ver perfil del perro
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No se encontró ninguna mascota con ese código único.</p>";
            }

            $stmt->close();
        }
    }
    ?>
    </div>
    
    <div class="lista">
        <h1>Lista de mascotas</h1> 
        <?php
            if ($_SESSION["usuario"]!="invitado") {
                echo "<form method='POST'>";
                echo "<button id='Boton-Añadir'><a href='registro_perro.php'>Añadir una mascota</a></button> &nbsp &nbsp &nbsp"; //&nbsp es para hacer espacios entre los botones, añadir varios hace el espacio mas grande.
                echo "<button type='submit' id='Boton-Añadir' method='POST' name='tus_perros'>Tus mascotas</button>";
                echo "</form>";
            }
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST['tus_perros'])){
                    $nombre=$_SESSION["usuario"];
                    $sql = "SELECT * FROM perros WHERE dueño = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $nombre);
                    $stmt->execute();
                    $resultado = $stmt->get_result();
                    if ($resultado->num_rows > 0) {
                        echo "<h3>Resultado de la búsqueda:</h3>";
                        echo "<table border='1'align='center'>";
                        echo "<tr><th>ID</th><th>Nombre</th><th>Ciudad</th><th>Dueño</th><th>Perfil</th></tr>";
                        while ($perro = $resultado->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$perro['codigoperro']}</td>";
                            echo "<td>{$perro['nombreperro']}</td>";
                            echo "<td>{$perro['ciudad']}</td>";
                            echo "<td>{$perro['dueño']}</td>";
                            echo "<td><a class='boton-ver-perfil' href='perfil_perro.php?codigoperro={$perro['codigoperro']}'>Ver perfil</a></td>"; // Botón para ver perfil del perro
                            echo "</tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<p>No se encontró ninguna mascota.</p>";
                    }
            
                    $stmt->close();
                }
            }
        ?>
        <br><br>
        <table  align="center" border=1>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Ciudad</th>
                <th>Dueño</th>
                <?php
                if ($_SESSION["usuario"]!="invitado") {
                    echo "<th>Perfil</th>";
                }
                ?>
            </tr>
            <?php
            // Consulta para obtener todos los perros
            $sql = "SELECT * FROM perros";
            $resultado = $conn->query($sql);
            $nombre=$_SESSION["usuario"];

            if ($resultado->num_rows > 0) {
                // Mostrar cada perro en una fila de la tabla
                while ($fila = $resultado->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$fila['codigoperro']}</td>";
                    echo "<td>{$fila['nombreperro']}</td>";
                    echo "<td>{$fila['ciudad']}</td>";
                    echo "<td>{$fila['dueño']}</td>";
                    if($nombre==$fila['dueño']){
                        $_SESSION["idperro"]=$fila['codigoperro']  ;
                        echo "<td><a class='boton-ver-perfil' href='perfil_perro.php'>Ver perfil</a></td>"; // Botón para ver perfil del perro
                        echo "</tr>";
                    }
                }
            } else {
                echo "<tr><td colspan='4'>No se encontraron perros registrados.</td></tr>";
            }
            ?>
        </table>
    </div>    
</div>
</div>
</body>
</html>
