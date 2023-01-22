<?php
require './config/config.php';
require './includes/functions.php';

check_login();

$sidebar_active = 'carta';
$header_title = 'Panel de cartas';

// Consulta para recibir el número de cartas
$sql = "SELECT COUNT(*) FROM carta";
$statement = $pdo->query($sql);
$num_cartas = $statement->fetchColumn();

// Consulta para generar el panel de cartas
$sql = "SELECT id, nombre_cliente, numero_expediente, fecha_creacion, fecha_visita, monto_inicial,
       mensualidades_vencidas, adeudo_total, nombre_archivo FROM carta ORDER BY id DESC;";
$statement = $pdo->query($sql);
$cartas = $statement->fetchAll();

// Revisa si la string de consulta tiene un id para eliminar la carta de la base de datos y del directorio
// Recibir y validar id
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    try {
        // Consulta para recibir el nombre del archivo a eliminar
        $sql = "SELECT nombre_archivo FROM carta WHERE id = :id";
        $statement = $pdo->prepare($sql);
        $statement->execute([$id]);
        $nombre_archivo = $statement->fetchColumn();

        // Sentencia para borrar la carta en la base de datos
        $sql = "DELETE FROM carta WHERE id = :id";
        $statement = $pdo->prepare($sql);
        $statement->execute([$id]);

        // Eliminar el archivo del directorio de archivos generados
        unlink('./files/cartas/' . $nombre_archivo);
        header('Location: cartas.php', response_code: 302);
        exit;
    } catch (Exception $e) {
        throw $e;
    }
}

require_once './includes/header.php';
?>
<div class="main__app">
    <div class="main__header">
        <div>
            <h1 class="main__title">Cartas</h1>
            <span class="main__subtitle"><?= $num_cartas ?> <?= $num_cartas > 1 || $num_cartas == 0 ? 'cartas' : 'carta' ?></span>
        </div>
        <div class="main__btnContainer">
            <a href="excel-cartas.php" class="main__btn main__btn--excel">
                <svg xmlns="http://www.w3.org/2000/svg" class="main__icon" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5"/>
                </svg>
                Exportar a Excel</a>
            <a href="generador-carta.php" class="main__btn main__btn--main">
                <svg xmlns="http://www.w3.org/2000/svg" class="main__icon" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Nueva carta
            </a>
        </div>
    </div>
    <table class="table">
        <thead class="table__head">
        <tr class="table__row--head">
            <th scope="col" class="table__head">
                Acreditado
            </th>
            <th scope="col" class="table__head table__data--left">
                Folio
            </th>
            <th scope="col" class="table__head table__data--left">
                Monto inicial
            </th>
            <th scope="col" class="table__head table__data--left">
                Adeudo total
            </th>
            <th scope="col" class="table__head table__head--width">
                Mensualidades vencidas
            </th>
            <th scope="col" class="table__head">
                Fecha de creación
            </th>
            <th scope="col" class="table__head">
                Fecha de visita
            </th>
            <th scope="col" colspan="3" class="table__head">
                Acciones
            </th>
        </tr>
        </thead>
        <tbody class="table__body">
        <?php foreach ($cartas as $carta): ?>
            <tr class="table__row--body">
                <td class="table__data table__data--bold"><?= $carta['nombre_cliente'] ?></td>
                <td class="table__data table__data--left"><?= $carta['numero_expediente'] ?></td>
                <td class="table__data table__data--left"><?= '$' . number_format($carta['monto_inicial'], 2); ?></td>
                <td class="table__data table__data--left"><?= '$' . number_format($carta['adeudo_total'], 2); ?></td>
                <td class="table__data"><?= $carta['mensualidades_vencidas']; ?></td>
                <td class="table__data"><?= date("d-m-Y", strtotime($carta['fecha_creacion'])); ?></td>
                <td class="table__data"><?= $carta['fecha_visita'] ? date("d-m-Y", strtotime($carta['fecha_visita'])) : ''; ?></td>
                <?php if (file_exists('./files/cartas/' . $carta['nombre_archivo'])): ?>
                    <td class="table__data"><a class="table__data--link"
                                               href="./files/cartas/<?= $carta['nombre_archivo'] ?>">Descargar</a>
                    </td>
                <?php else: ?>
                    <td class="table__data"><a class="table__data--nolink">Descargar</a>
                    </td>
                <?php endif; ?>
                <td class="table__data"><a class="table__data--green"
                                           href="agregar-fecha-carta.php?id=<?= $carta['id'] ?>">Agregar fecha</a>
                </td>
                <td class="table__data"><a class="table__data--red"
                                           href="cartas.php?id=<?= $carta['id'] ?>">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</main>
</div>
</body>
</html>