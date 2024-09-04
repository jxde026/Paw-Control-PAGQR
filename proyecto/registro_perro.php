<?php 
    session_start(); // esto tiene que estar al comienzo si o si
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paw Control registro de perros</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
    // Conexión a la base de datos
    $conn = mysqli_connect("localhost", "root", "", "proyecto") or die("Error en la conexión: " . mysqli_connect_error());

    $mensaje = "";

    if (isset($_POST['listo'])) {
        $NomPer = $_POST['NomPer'];
        $NomUsu = $_POST['NomUsu'];
        $CiuPer = $_POST['CiuPer'];
        $IDPer = $_POST['IDPer'];

        // Verificar si el ID ya existe
        $checksql = "SELECT `codigoperro` FROM `perros` WHERE `codigoperro` = '$IDPer'";
        $result = mysqli_query($conn, $checksql);

        if (mysqli_num_rows($result) < 1) {
            // Insertar nuevo perro
            $textsql = "INSERT INTO `perros` (`nombreperro`, `codigoperro`, `ciudad`, `dueño`) VALUES ('$NomPer', '$IDPer', '$CiuPer', '$NomUsu')";
            $consulta = mysqli_query($conn, $textsql);

            if ($consulta) {
                // Verificar si se ha enviado un archivo
                if (isset($_FILES['FotPer']) && $_FILES['FotPer']['error'] == UPLOAD_ERR_OK) {
                    // Obtener la información del archivo
                    $fileType = mime_content_type($_FILES['FotPer']['tmp_name']);

                    // Verificar si el archivo es una imagen
                    $allowedTypes = ['image/jpeg', 'image/png'];
                    
                    if (in_array($fileType, $allowedTypes)) {
                        // Obtener el archivo subido y convertirlo a formato binario
                        $image = $_FILES['FotPer']['tmp_name'];
                        $imgContent = addslashes(file_get_contents($image));

                        // Actualizar la imagen en la base de datos
                        $updateImageSql = "UPDATE `perros` SET `fotoperro` = '$imgContent' WHERE `codigoperro` = '$IDPer'";

                        if ($conn->query($updateImageSql) === TRUE) {
                            $mensaje .= " Imagen subida y guardada exitosamente.";
                        } else {
                            $mensaje .= " Error al guardar la imagen: " . $conn->error;
                        }
                    } else {
                        $mensaje .= " Por favor, sube un archivo de imagen válido (JPEG, PNG).";
                    }
                }
                $mensaje = "Perro registrado exitosamente." . $mensaje;
            } else {
                $mensaje = "Error al agregar el perfil del perro: " . mysqli_error($conn);
            }
        } else {
            $mensaje = "ID de perro ya existe";
        }

        // Cerrar la conexión
        $conn->close();
    }
?>
<button id="Boton-Volver"><a href="lista_perro.php">Volver</a></button>
<center><h1>Paw Control</h1>
<h1>Registro de Perros</h1></center>

<!-- Formulario combinado para datos del perro e imagen -->
<form action="" method="POST" enctype="multipart/form-data">
    <table border="2" align="center">
        <th colspan="2">Datos del Perro</th>
        <tr>
            <td>Nombre del perro</td>
            <td><input type="text" maxlength="50" name="NomPer" required></td>
        </tr>
        <tr>
            <td>Nombre de usuario del dueño</td>
            <td><input type="text" maxlength="50" name="NomUsu" required></td>
        </tr>
        <tr>
            <td>ID</td>
            <td><input type="text" maxlength="4" name="IDPer" required></td>
        </tr>
        <tr>
            <td>Ciudad</td>
            <td><input type="text" maxlength="50" name="CiuPer" required></td>
        </tr>
        <tr>
            <td>Selecciona una imagen para subir:</td>
            <td><input type="file" name="FotPer" id="FotPer" accept="image/*" required></td>
        </tr>
        <tr>
            <td><input type="submit" class="boton" name="listo" value="Listo"></td>
            <td><?php echo $mensaje; ?></td>
        </tr>
    </table>
</form>
</body>
</html>