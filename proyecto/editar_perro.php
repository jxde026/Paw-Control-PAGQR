<?php 
session_start(); // Esto tiene que estar al comienzo sí o sí
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paw Control - Edición de Perros</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
// Conexión a la base de datos
$conn = mysqli_connect("localhost", "root", "", "proyecto") or die("Error en la conexión: " . mysqli_connect_error());

$mensaje = "";
$codigoperro = isset($_SESSION["idperro"]) ? $_SESSION["idperro"] : null;

if ($codigoperro) {
    // Cargar los datos existentes del perro
    $sql = "SELECT * FROM perros WHERE codigoperro = '$codigoperro'";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $perro = mysqli_fetch_assoc($result);
        $NomPer = $perro['nombreperro'];
        $NomUsu = $perro['dueño'];
        $CiuPer = $perro['ciudad'];
        $IDPer = $perro['codigoperro'];
    } else {
        $mensaje = "No se encontró el perfil del perro.";
        $NomPer = $NomUsu = $CiuPer = $IDPer = "";
    }
} else {
    $NomPer = $NomUsu = $CiuPer = $IDPer = "";
}

if (isset($_POST['listo'])) {
    $NomPer = $_POST['NomPer'];
    $NomUsu = $_POST['NomUsu'];
    $CiuPer = $_POST['CiuPer'];
    $newIDPer = $_POST['IDPer'];

    // Verificar si la nueva ID ya existe en la base de datos
    $checksql = "SELECT `codigoperro` FROM `perros` WHERE `codigoperro` = '$newIDPer' AND `codigoperro` != '$IDPer'";
    $result = mysqli_query($conn, $checksql);

    if (mysqli_num_rows($result) > 0) {
        $mensaje = "La nueva ID ya existe. No se pueden realizar los cambios.";
    } else {
        // Actualizar perfil existente con la nueva ID
        $textsql = "UPDATE `perros` SET `nombreperro`='$NomPer', `ciudad`='$CiuPer', `dueño`='$NomUsu', `codigoperro`='$newIDPer' WHERE `codigoperro`='$IDPer'";
        $consulta = mysqli_query($conn, $textsql);

        if ($consulta) {
            $mensaje = "Perfil actualizado exitosamente.";

            // Actualizar la imagen si se ha subido una nueva
            if (isset($_FILES['FotPer']) && $_FILES['FotPer']['error'] == UPLOAD_ERR_OK) {
                $fileType = mime_content_type($_FILES['FotPer']['tmp_name']);
                $allowedTypes = ['image/jpeg', 'image/png'];

                if (in_array($fileType, $allowedTypes)) {
                    $image = $_FILES['FotPer']['tmp_name'];
                    $imgContent = addslashes(file_get_contents($image));

                    $updateImageSql = "UPDATE `perros` SET `fotoperro` = '$imgContent' WHERE `codigoperro` = '$newIDPer'";

                    if ($conn->query($updateImageSql) === TRUE) {
                        $mensaje .= " Imagen subida y guardada exitosamente.";
                    } else {
                        $mensaje .= " Error al guardar la imagen: " . $conn->error;
                    }
                } else {
                    $mensaje .= " Por favor, sube un archivo de imagen válido (JPEG, PNG).";
                }
            }

            // Actualizar la sesión con la nueva ID
            $_SESSION["idperro"] = $newIDPer;
        } else {
            $mensaje = "Error al actualizar el perfil del perro: " . mysqli_error($conn);
        }
    }

    // Cerrar la conexión
    $conn->close();
}
?>
<button id="Boton-Volver"><a href="perfil_perro.php">Volver</a></button>
<center><h1>Paw Control</h1>
<h1>Edición de Perros</h1></center>

<!-- Formulario para editar datos del perro e imagen -->
<form action="" method="POST" enctype="multipart/form-data">
    <table border="2" align="center">
        <th colspan="2">Datos del Perro</th>
        <tr>
            <td>Nombre del perro</td>
            <td><input type="text" maxlength="50" name="NomPer" value="<?php echo htmlspecialchars($NomPer); ?>" required></td>
        </tr>
        <tr>
            <td>Nombre de usuario del dueño</td>
            <td><input type="text" maxlength="50" name="NomUsu" value="<?php echo htmlspecialchars($NomUsu); ?>" required></td>
        </tr>
        <tr>
            <td>ID</td>
            <td><input type="text" maxlength="4" name="IDPer" value="<?php echo htmlspecialchars($IDPer); ?>" required></td>
        </tr>
        <tr>
            <td>Ciudad</td>
            <td><input type="text" maxlength="50" name="CiuPer" value="<?php echo htmlspecialchars($CiuPer); ?>" required></td>
        </tr>
        <tr>
            <td>Selecciona una imagen para subir:</td>
            <td><input type="file" name="FotPer" id="FotPer" accept="image/*"></td>
        </tr>
        <tr>
            <td><input type="submit" class="boton" name="listo" value="Listo"></td>
            <td><?php echo $mensaje; ?></td>
        </tr>
    </table>
</form>
</body>
</html>