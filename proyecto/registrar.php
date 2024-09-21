<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Paw Control Registro</title>
</head>
<body>
<?php
// Conexión a la base de datos
$conn = mysqli_connect("localhost", "root", "", "proyecto") or die("Error en la conexión: " . mysqli_connect_error());

$mensaje = "";

if (isset($_POST['listo'])) {
    $NomUsu = $_POST['NomUsu'];
    $ConUsu = $_POST['ConUsu'];
    $CorUsu = $_POST['CorUsu'];

    // Verificar si el nombre de usuario ya existe
    $checksql = "SELECT `nombreusu` FROM `usuarios` WHERE `nombreusu` = '$NomUsu'";
    $result = mysqli_query($conn, $checksql);

    if (mysqli_num_rows($result) > 0) {
        $mensaje = "Nombre de usuario ya existe";
    } else {
        // Encriptar la contraseña
        $hashContraseña = password_hash($ConUsu, PASSWORD_DEFAULT);

        // Insertar nuevo usuario
        $textsql = "INSERT INTO `usuarios` (`nombreusu`, `contraseña`, `correo_electronico`) VALUES ('$NomUsu', '$hashContraseña', '$CorUsu')";
        $consulta = mysqli_query($conn, $textsql);

        if ($consulta) {
            $mensaje = "Usuario agregado correctamente";
        } else {
            $mensaje = "Error al agregar usuario: " . mysqli_error($conn);
        }
    }
}
?>
<button id="Boton-Volver"><a href="index.php">Volver</a></button>
<div class="fondo">
<center><h1>Paw Control</h1></center>
<form action="" method="POST">
    <table border="2" align="center">
        <thead>
            <tr>
                <th colspan="2">Ingrese sus datos</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Nombre de Usuario</td>
                <td><input type="text" maxlength="50" name="NomUsu" required></td>
            </tr>
            <tr>
                <td>Contraseña</td>
                <td><input type="password" maxlength="30" name="ConUsu" required></td>
            </tr>
            <tr>
                <td>Correo Electrónico</td>
                <td><input type="email" maxlength="50" name="CorUsu" required></td>
            </tr>
            <tr>
                <td><input type="submit" class="boton" name="listo" value="Listo"></td>
                <td><?php echo $mensaje; ?></td>
            </tr>
        </tbody>
    </table>
    <br>
</form>
</div>
</body>
</html>