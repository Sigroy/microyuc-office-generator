<?php
declare(strict_types=1);
require '../src/bootstrap.php';

check_login();

$sidebar_active = 'inicio';
$header_title = 'Inicio';

// Consulta para recibir el número de cartas
$sql = "SELECT COUNT(*) FROM carta";
$statement = $pdo->query($sql);
$num_cartas = $statement->fetchColumn();

// Consulta para recibir el número de bitácoras
$sql = "SELECT COUNT(*) AS 'num' FROM bitacora";
$statement = $pdo->query($sql);
$num_bitacoras = $statement->fetchColumn();

require_once './includes/header.php';
?>
<div class="dashboard__home">
    <div class="dashboard__card">
        <h2 class="card__title">
            <svg xmlns="http://www.w3.org/2000/svg" class="card__icon" fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Cartas generadas
        </h2>
        <a href="cartas.php" class="card__number"><?= $num_cartas ?></a>
    </div>
    <div class="dashboard__card">
        <h2 class="card__title">
            <svg xmlns="http://www.w3.org/2000/svg" class="card__icon" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Bitácoras generadas
        </h2>
        <a href="bitacoras.php" class="card__number"><?= $num_bitacoras ?></a>
    </div>
</div>
</main>
</div>
</body>
</html>