<?php 
    session_start(); // esto tiene que estar al comienzo si o si
?> 
<link rel="stylesheet" href="style.css">
<?php
    $conn = mysqli_connect("localhost","root","","proyecto") or die ("Error en la conexión");
    $codigoperro = $_SESSION["idperro"];

    // Consulta para obtener la información del perro por su ID
    $sql = "SELECT * FROM perros WHERE codigoperro = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $codigoperro);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $perro = $resultado->fetch_assoc();
        // Mostrar el perfil completo del perro
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo $perro['nombreperro']; ?></title>
    <style>
        table {
            width: 50%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .perfil-img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <button id="Boton-Volver"><a href="lista_perro.php">Volver</a></button>
    <button id='Boton-Añadir' method=POST name="editar"><a href='editar_perro.php'>Editar perfil</a></button>
    <h1>Perfil de <?php echo $perro['nombreperro']; ?></h1>
    <table>
        <tr>
            <th>Foto</th>
            <td><img src="data:image/jpeg;base64,<?php echo base64_encode($perro['fotoperro']); ?>" alt="Foto de <?php echo $perro['nombreperro']; ?>" class="perfil-img"></td>
        </tr>
        <tr>
            <th>Nombre</th>
            <td><?php echo $perro['nombreperro']; ?></td>
        </tr>
        <tr>
            <th>Ciudad</th>
            <td><?php echo $perro['ciudad']; ?></td>
        </tr>
        <tr>
            <th>ID</th>
            <td><?php echo $perro['codigoperro']; ?></td>
        </tr>
         <tr>
            <th>Dueño</th>
            <td><?php echo $perro['dueño']; ?></td>
        </tr>
    </table>
</body>
</html>
<?php
    } else {
        echo "<p>No se encontró ningún perro con ese ID.</p>";
    }

    $stmt->close();
?>
