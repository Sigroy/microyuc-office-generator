<?php
declare(strict_types=1);
session_start();
require '../src/bootstrap.php';
if (isset($_SESSION['login'])) {
    header("Location: inicio.php");
}

$sql = 'SELECT nombre, password FROM usuario';

$result = mysqli_query($conn, $sql);

$usuarios = mysqli_fetch_all($result, MYSQLI_ASSOC);

if ($_POST) {
    if ($_POST['user'] == "Admin" && $_POST['password'] == "123456789@MY") {
        $_SESSION['login'] = true;
        header("Location: inicio.php");
    } else {
        echo "<h1 style='text-align: center'>Usuario o contraseña incorrectos</h1>";
    }

}

?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../dist/css/styles.css">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <title>Microyuc | Inicio de sesión</title>
</head>
<body>
<div class="login">
    <img src="img/microyucfondo.png" alt="Logo de Microyuc" class="login__img">
    <div class="login__container">
        <h1 class="login__title">Iniciar sesión</h1>
        <p class="login__subtitle">Introduce tus credenciales para iniciar sesión.</p>
        <form action="index.php" method="post" class="login__form">
            <label for="user">
                <input type="text" id="user" name="user" placeholder="Usuario" class="login__input" required>
            </label>
            <label for="password">
                <input type="password" id="password" name="password" placeholder="Contraseña" class="login__input"
                       required>
            </label>
            <input type="submit" value="Iniciar sesión" class="login__btn">
        </form>
    </div>
</div>
</body>
</html>
