<?php 
    session_start(); // esto tiene que estar al comienzo si o si
    
    if (!isset($_SESSION['usuario'])) {
        // si no inicio sesion se lo redirecciona al login
        session_destroy();
        header("Location: index.php");
        exit();
    }
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
    $conn = mysqli_connect("sql311.infinityfree.com", "if0_37488786", "XpzOBiDgIP", "if0_37488786_proyecto") or die("Error en la conexión");

    $mensaje = "";

    if (isset($_POST['listo'])) {
        $NomPer = $_POST['NomPer'];
        $EdaPer = $_POST['EdaPer'];
        $NomUsu = $_POST['NomUsu'];
        $IDPer = $_POST['IDPer'];
        $Contacto = $_POST['Contacto'];
        $PriPol = $_POST['PriPol'];
        $SegPol = $_POST['SegPol'];
        $RefPol = $_POST['RefPol'];
        $AnuPol = $_POST['AnuPol'];
        $Rab = $_POST['Rab'];
        $AnuRab = $_POST['AnuRab'];
        $TriFel = $_POST['TriFel'];
        $RefTri = $_POST['RefTri'];

        // Verificar si el ID ya existe
        $checksql = "SELECT `codigoperro` FROM `perros` WHERE `codigoperro` = '$IDPer'";
        $result = mysqli_query($conn, $checksql);

        if (mysqli_num_rows($result) < 1) {
            // Insertar nuevo perro
            $textsql = "INSERT INTO `perros` 
            (`nombreperro`, `Edad`, `codigoperro`, `Metodo_contacto`, `dueño`, `Primera_Polivalente`, `Segunda_Polivalente`, `Refuerzo_Polivalente`, `Cant_anual_poli`, `Rabia`, `Cant_anual_rabia`, `Triple_Felina`, `Refuerzo_triple`) 
            VALUES ('$NomPer', '$IDPer', '$Contacto', '$NomUsu', '$PriPol', '$SegPol', '$RefPol', '$AnuPol', '$Rab', '$AnuRab', '$TriFel', '$RefTri')";
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
<div class="fondo-editar">
<center><h1>Paw Control</h1>
<h1>Registro de Perros</h1></center>

<!-- Formulario combinado para datos del perro e imagen -->
<form action="" method="POST" enctype="multipart/form-data">
    <table border="2" align="center">
        <th colspan="2">Datos del Perro</th>
        <tr>
            <td>Nombre de la mascota</td>
            <td><input type="text" maxlength="50" name="NomPer" required></td>
        </tr>
        <tr>
            <td>Edad de la mascota</td>
            <td><input type="text" maxlength="50" name="EdaPer" required></td>
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
            <td>Metodo de contacto</td>
            <td><input type="text" maxlength="50" name="Contacto" required></td>
        </tr>
        <td colspan="2">
            Rellenar con una x las vacunas no anuales
        </td>
        <tr>
            <td>1ra Dosis Polivalente</td>
            <td><input type="text" maxlength="50" name="PriPol" pattern="[xX]" title="Solo puedes ingresar la letra x"></td>
        </tr>
        <tr>
            <td>2da Dosis Polivalente</td>
            <td><input type="text" maxlength="50" name="SegPol" pattern="[xX]" title="Solo puedes ingresar la letra x"></td>
        </tr>
        <tr>
            <td>Refuerzo de Polivalente</td>
            <td><input type="text" maxlength="50" name="RefPol" pattern="[xX]" title="Solo puedes ingresar la letra x"></td>
        </tr>
        <tr>
            <td>Cantidad de refuerzos anuales de Polivalente</td>
            <td><input type="int" maxlength="50" name="AnuPol"></td>
        </tr>
        <tr>
            <td>Rabia</td>
            <td><input type="text" maxlength="50" name="Rab" pattern="[xX]" title="Solo puedes ingresar la letra x"></td>
        </tr>
        <tr>
            <td>Cantidad de refuerzos anuales de Rabia</td>
            <td><input type="text" maxlength="50" name="AnuRab"></td>
        </tr>
        <tr>
            <td>Triple Felina</td>
            <td><input type="text" maxlength="50" name="TriFel" pattern="[xX]" title="Solo puedes ingresar la letra x"></td>
        </tr>
        <tr>
            <td>Refuerzo Triple Felina</td>
            <td><input type="text" maxlength="50" name="RefTri" pattern="[xX]" title="Solo puedes ingresar la letra x"></td>
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
<br>
</div>
</body>
</html>