<?php
require './config/db_connect.php';
require './includes/functions.php';

check_login();

$sidebar_active = 'carta';
$header_title = 'Agregar fecha de visita';

// Recibir y validar id
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Si el id no es válido, redireccionar al panel de cartas
if (!$id) {
    header('Location: cartas.php', 404);
}

// Consulta para recibir el id y el nombre del cliente de la carta
$sql = "SELECT id, nombre_cliente FROM carta WHERE id = :id;";
$statement = $pdo->prepare($sql);
$statement->execute([$id]);
$carta = $statement->fetch();

// Si no se encontró la carta, redireccionar al panel de cartas
if (!$carta) {
    header('Location: cartas.php', 302);
}

// Se inicializa la variable para poder mostrarla en el HTML
$fecha_visita = '';

// Se crea un arreglo para almacenar los errores
$error = [
    'fecha_visita' => '',
];

// Se ejecuta solo si ha habido una petición POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Filtro regexp para validar fechas
    $filtro['options']['regexp'] = '/^[\d\-]+$/';

    // Se valida la fecha introducida por el usuario
    $fecha_visita = filter_input(INPUT_POST, 'fecha_visita', FILTER_VALIDATE_REGEXP, $filtro);

    // Si la fecha introducida es inválida, se agrega un mensaje al arreglo de errores
    $error['fecha_visita'] = $fecha_visita ? '' : 'Introduzca un formato de fecha válido.';

    // Se genera una string con los errores
    $invalido = implode($error);

    // Se ejecuta si no ha habido errores de validación
    if (!$invalido) {
        try {
            // Sentencia para actualizar la fecha de visita de la carta
            $sql = "UPDATE carta SET fecha_visita = :fecha_visita WHERE id = :id;";
            $statement = $pdo->prepare($sql);
            $statement->execute(['fecha_visita' => $fecha_visita, 'id' => $id]);

            header('Location: ./cartas.php', response_code: 302);
            exit;
        } catch (Exception $e) {
            throw $e;
        }

    }
}

require_once './includes/header.php';
?>
<div class="main__app">
    <div class="main__header">
        <h1 class="main__title">Agregar fecha de visita a <?= $carta['nombre_cliente']; ?></h1>
        <a href="cartas.php" class="main__btn main__btn--main">
            <svg xmlns="http://www.w3.org/2000/svg" class="main__icon" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            Gestionar cartas
        </a>
    </div>
    <div>
        <form class="form" action="agregar-fecha-carta.php?id=<?= $carta['id'] ?>" method="post">
            <fieldset class="form__fieldset form__fieldset--verification">
                <legend class="form__legend">Fecha de visita<span class="asterisk">*</span></legend>
                <div class="form__division">
                    <label class="form__label" for="fecha_visita"></label>
                    <input class="form__input" type="date" id="fecha_visita"
                           name="fecha_visita"
                           value="<?= htmlspecialchars($fecha_visita) ?>" required>
                    <p class="form__error"><?= $error['fecha_visita'] ?></p>
                </div>
            </fieldset>
            <div class="form__container--btn">
                <input class="container__btn--submit" type="submit" value="Agregar fecha">
            </div>
        </form>
    </div>
</div>
</main>
</div>
</body>
</html>
