<?php

function check_login(): void
{
    session_start();
    if (!isset($_SESSION['login'])) {
        header('Location: index.php');
    }
}

//Set date format to replace in the docx
function set_date_format(): IntlDateFormatter
{
    return datefmt_create(
        'es-MX',
        IntlDateFormatter::FULL,
        IntlDateFormatter::FULL,
        'America/Mexico_City',
        IntlDateFormatter::GREGORIAN,
        "MMMM 'de' yyyy"
    );
}