<?php 
    session_start(); // esto tiene que estar al comienzo si o si
?> 
<link rel="stylesheet" href="style.css">
<?php
    if ($_SESSION['usuario']=="") {
        session_destroy(); // termina la sesion
        header("Location: index.php"); // redirige al login
        exit(); // el codigo deja de correr luego del redirect
    }


    $conn = mysqli_connect("sql311.infinityfree.com", "if0_37488786", "XpzOBiDgIP", "if0_37488786_proyecto") or die("Error en la conexión");
    mysqli_set_charset($conn, "utf8mb4");
    $usuario = $_SESSION["usuario"];
    $mascota_id = intval($_GET['codigoperro']); //convertir a integer para evitar inyeccion de sql

    // Consulta para obtener la información del perro por su ID
    $sql = "SELECT * FROM perros WHERE codigoperro = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $mascota_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $perro = $resultado->fetch_assoc();
        if ($perro['dueño'] != $usuario){
            header("Location: lista_perro.php");
            exit();
        }
        // Mostrar el perfil completo del perro
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo $perro['nombreperro']; ?></title>
</head>
<body>
    <button id="Boton-Volver"><a href="lista_perro.php">Volver</a></button>
    <button id='Boton-Añadir'><a href="editar_perro.php?codigoperro=<?php echo $perro['codigoperro']; ?>">Editar perfil</a></button>
    <div class="fondo-editar">
    <h1>Perfil de <?php echo $perro['nombreperro']; ?></h1>
    <div style='overflow-x:auto;'>
    <table align="center" border=1>
        <tr>
            <th>Foto</th>
            <td><img src="data:image/jpeg;base64,<?php echo base64_encode($perro['fotoperro']); ?>" alt="Foto de <?php echo $perro['nombreperro']; ?>"></td>
        </tr>
        <tr>
            <th>Nombre</th>
            <td><?php echo $perro['nombreperro']; ?></td>
        </tr>
        <tr>
            <th>Edad</th>
            <td><?php echo $perro['Edad']; ?></td>
        </tr>
        <tr>
            <th>ID</th>
            <td><?php echo $perro['codigoperro']; ?></td>
        </tr>
         <tr>
            <th>Dueño</th>
            <td><?php echo $perro['dueño']; ?></td>
        </tr>
        <tr>
            <th>Metodo de contacto</th>
            <td><?php echo $perro['Metodo_contacto']; ?></td>
        </tr>
        <tr>
            <td>1ra Dosis Polivalente</td>
            <td><?php echo $perro['Primera_Polivalente']; ?></td>
        </tr>
        <tr>
            <td>2da Dosis Polivalente</td>
            <td><?php echo $perro['Segunda_Polivalente']; ?></td>
        </tr>
        <tr>
            <td>Refuerzo de Polivalente</td>
            <td><?php echo $perro['Refuerzo_Polivalente']; ?></td>
        </tr>
        <tr>
            <td>Cantidad de refuerzos anuales de Polivalente</td>
            <td><?php echo $perro['Cant_anual_poli']; ?></td>
        </tr>
        <tr>
            <td>Rabia</td>
            <td><?php echo $perro['Rabia']; ?></td>
        </tr>
        <tr>
            <td>Cantidad de refuerzos anuales de Rabia</td>
            <td><?php echo $perro['Cant_anual_rabia']; ?></td>
        </tr>
        <tr>
            <td>Triple Felina</td>
            <td><?php echo $perro['Triple_Felina']; ?></td>
        </tr>
        <tr>
            <td>Refuerzo Triple Felina</td>
            <td><?php echo $perro['Refuerzo_triple']; ?></td>
        </tr>
    </table>
    </div>
    <br>
    </div>
</body>
</html>
<?php
    } else {
        echo "<p>No se encontró ningún perro con ese ID.</p>";
    }

    $stmt->close();
?>
