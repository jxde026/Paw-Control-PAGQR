<?php 
session_start(); // Esto tiene que estar al comienzo sí o sí

if (!isset($_SESSION['usuario'])) {
    // si no inicio sesion se lo redirecciona al login
    session_destroy();
    header("Location: index.php");
    exit();
}

// Capturar el ID del perro desde la URL
$codigoperro = isset($_GET['codigoperro']) ? intval($_GET['codigoperro']) : null;

if (!$codigoperro) {
    header("Location: lista_perro.php");
    exit();
}

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
    $conn = mysqli_connect("sql311.infinityfree.com", "if0_37488786", "XpzOBiDgIP", "if0_37488786_proyecto") or die("Error en la conexión");
    mysqli_set_charset($conn, "utf8mb4");

    $mensaje = "";

    if ($codigoperro) {
        // Cargar los datos existentes del perro
        $sql = "SELECT * FROM perros WHERE codigoperro = '$codigoperro'";
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            $perro = mysqli_fetch_assoc($result);
                $NomPer = $perro['nombreperro'];
                $EdaPer = $perro['Edad'];
                $IDPer = $perro['codigoperro'];
                $NomUsu = $perro['dueño'];
                $Contacto = $perro['Metodo_contacto'];
                $PriPol = $perro['Primera_Polivalente'];
                $SegPol = $perro['Segunda_Polivalente'];
                $RefPol = $perro['Refuerzo_Polivalente'];
                $AnuPol = $perro['Cant_anual_poli'];
                $Rab = $perro['Rabia'];
                $AnuRab = $perro['Cant_anual_rabia'];  
                $TriFel = $perro['Triple_Felina'];
                $RefTri = $perro['Refuerzo_triple'];
        } else {
            $mensaje = "No se encontró el perfil del perro.";
            $NomPer = $EdaPer = $NomUsu = $Contacto = $IDPer = $PriPol = $SegPol = $RefPol = $AnuPol = $Rab = $AnuRab = $TriFel = $RefTri = "";
        }
    } else {
        $NomPer = $EdaPer = $NomUsu = $Contacto = $PriPol = $SegPol = $RefPol = $AnuPol = $Rab = $AnuRab = $TriFel = $RefTri = "";
    }

    if (isset($_POST['listo'])) {
        $NomPer = $_POST['NomPer'];
        $EdaPer = $_POST['EdaPer'];
        $NomUsu = $_POST['NomUsu'];
        $newIDPer = !empty($_POST['IDPer']) ? $_POST['IDPer'] : $IDPer;  // Use the new ID only if provided
        $Contacto = $_POST['Contacto'];
        $PriPol = $_POST['PriPol'];
        $SegPol = $_POST['SegPol'];
        $RefPol = $_POST['RefPol'];
        $AnuPol = $_POST['AnuPol'];
        $Rab = $_POST['Rab'];
        $AnuRab = $_POST['AnuRab'];
        $TriFel = $_POST['TriFel'];
        $RefTri = $_POST['RefTri'];
    
        // Verificar si la nueva ID ya existe en la base de datos
        $checksql = "SELECT `codigoperro` FROM `perros` WHERE `codigoperro` = '$newIDPer' AND `codigoperro` != '$IDPer'";
        $result = mysqli_query($conn, $checksql);
    
        if (mysqli_num_rows($result) > 0) {
            $mensaje = "La nueva ID ya existe. No se pueden realizar los cambios.";
        } else {
            // Actualizar perfil existente con la nueva ID
            $textsql = "UPDATE `perros` SET `nombreperro`='$NomPer', `Edad`='$EdaPer', `dueño`='$NomUsu', `Metodo_contacto`='$Contacto',
                `Primera_Polivalente`='$PriPol', `Segunda_Polivalente`='$SegPol', `Refuerzo_Polivalente`='$RefPol', `Cant_anual_poli`='$AnuPol', `Rabia`='$Rab', 
                `Cant_anual_rabia`='$AnuRab', `Triple_Felina`='$TriFel', `Refuerzo_triple`='$RefTri', `codigoperro`='$newIDPer' WHERE `codigoperro`='$IDPer'";
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
    <button id="Boton-Añadir"><a href="perfil_perro.php?codigoperro=<?php echo $codigoperro; ?>">Volver</a></button>
    <div class="fondo-editar">
    <h1>Edición de Perros</h1></center>

    <!-- Formulario para editar datos del perro e imagen -->
    <form action="" method="POST" enctype="multipart/form-data">
        <div style='overflow-x:auto;'>
        <table border="2" align="center">
            <th colspan="2">Datos del Perro</th>
            <tr>
                <td>Nombre de la mascota</td>
                <td><input type="text" maxlength="50" name="NomPer" value="<?php echo htmlspecialchars($NomPer); ?>" required></td>
            </tr>
            <tr>
                <td>Edad de la mascota</td>
                <td><input type="text" maxlength="50" name="EdaPer" value="<?php echo htmlspecialchars($EdaPer); ?>" required></td>
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
                <td>Metodo de contacto</td>
                <td><input type="text" maxlength="50" name="Contacto" value="<?php echo htmlspecialchars($Contacto); ?>" required></td>
            </tr>
            <td colspan="2">
                Rellenar con una x las vacunas no anuales
            </td>
            <tr>
                <td>1ra Dosis Polivalente</td>
                <td><input type="text" maxlength="50" name="PriPol" pattern="[xX]" title="Solo puedes ingresar la letra x" value="<?php echo htmlspecialchars($PriPol); ?>"></td>
            </tr>
            <tr>
                <td>2da Dosis Polivalente</td>
                <td><input type="text" maxlength="50" name="SegPol" pattern="[xX]" title="Solo puedes ingresar la letra x" value="<?php echo htmlspecialchars($SegPol); ?>"></td>
            </tr>
            <tr>
                <td>Refuerzo de Polivalente</td>
                <td><input type="text" maxlength="50" name="RefPol" pattern="[xX]" title="Solo puedes ingresar la letra x" value="<?php echo htmlspecialchars($RefPol); ?>"></td>
            </tr>
            <tr>
                <td>Cantidad de refuerzos anuales de Polivalente</td>
                <td><input type="int" maxlength="50" name="AnuPol" value="<?php echo htmlspecialchars($AnuPol); ?>"></td>
            </tr>
            <tr>
                <td>Rabia</td>
                <td><input type="text" maxlength="50" name="Rab" pattern="[xX]" title="Solo puedes ingresar la letra x" value="<?php echo htmlspecialchars($Rab); ?>"></td>
            </tr>
            <tr>
                <td>Cantidad de refuerzos anuales de Rabia</td>
                <td><input type="text" maxlength="50" name="AnuRab" value="<?php echo htmlspecialchars($AnuRab); ?>"></td>
            </tr>
            <tr>
                <td>Triple Felina</td>
                <td><input type="text" maxlength="50" name="TriFel" pattern="[xX]" title="Solo puedes ingresar la letra x" value="<?php echo htmlspecialchars($TriFel); ?>"></td>
            </tr>
            <tr>
                <td>Refuerzo Triple Felina</td>
                <td><input type="text" maxlength="50" name="RefTri" pattern="[xX]" title="Solo puedes ingresar la letra x" value="<?php echo htmlspecialchars($RefTri); ?>"></td>
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
        </div>
    </form>
    <br>
    </div>
</body>
</html>