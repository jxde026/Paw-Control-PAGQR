<?php
session_start();
unset($_SESSION['usuario']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Paw Control Inicio de Sesión</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    // Conexión a la base de datos
    $conn = mysqli_connect("localhost", "root", "", "proyecto") or die("Error en la conexión");

    $mensaje = "";

    if (isset($_POST['boton'])) {
        $boton = $_POST['boton'];
        $nombre = $_POST['nombre'];
        $contraseña = $_POST['contraseña'];

        if ($boton == "Confirmar") {
            // Consulta SQL para buscar el usuario
            $sql = "SELECT contraseña FROM usuarios WHERE nombreusu = '$nombre'";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $hashContraseña = $row['contraseña'];
                // Verificar la contraseña
                if (password_verify($contraseña, $hashContraseña)) {
                    $_SESSION["usuario"] = $nombre;
                    header("Location: lista_perro.php");
                    exit();
                } else {
                    $mensaje = "¡Nombre de usuario o contraseña incorrectos!";
                }
            } else {
                $mensaje = "¡Nombre de usuario o contraseña incorrectos!";
            }
        }

        if ($boton == "Ingresar como invitado") {
            $_SESSION["usuario"] = "invitado";
            header("Location: lista_perro.php");
            exit();
        }
    }
    ?>
    
        <center><img class="logo" src="logo.png" alt="Logo"></center>
    <div class="fondo-sesion">
    <div class="titulos">
        <center><h1>Inicio de Sesión</h1></center>
    </div>
    <div class="formulario">
        <form action="" method="post">
            <font size="2" face="Bahnschrift">
                <table border="2" align="center">
                    <tr>
                        <td>INGRESE EL NOMBRE DE USUARIO</td>
                        <td><input type="text" name="nombre"></td>
                    </tr>
                    <tr>
                        <td>INGRESE LA CONTRASEÑA</td>
                        <td><input type="password" name="contraseña"></td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <input type="submit" name="boton" value="Confirmar">
                            <input type="submit" name="boton" value="Ingresar como invitado">
                            <button id="Boton-registrar"><a href="registrar.php">Registrarse</a></button>
                        </td>
                    </tr>
                </table>
            </font>
        </form>
        <p><?php echo $mensaje; ?></p>
    </div>
</div>
</body>
</html>