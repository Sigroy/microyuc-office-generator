<?php

declare(strict_types=1);

function check_login(): void
{
    session_start();
    if (!isset($_SESSION['login'])) {
        header('Location: index.php');
    }
}

//Set date format to replace in the docx
function set_date_format_letter(): IntlDateFormatter
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

function set_date_format_logbook(): IntlDateFormatter
{
    return datefmt_create(
        'es-MX',
        IntlDateFormatter::FULL,
        IntlDateFormatter::FULL,
        'America/Mexico_City',
        IntlDateFormatter::GREGORIAN,
        "EEEE d 'de' MMMM 'de' yyyy"
    );
}

function create_filename($filename, $upload_path)              // Function to make filename
{
    $basename = pathinfo($filename, PATHINFO_FILENAME);      // Get basename
    $extension = pathinfo($filename, PATHINFO_EXTENSION);     // Get extension
    $basename = preg_replace('/[^A-zÀ-ÿ0-9]/', '-', $basename); // Clean basename
    $new_filename = $basename;
    $i = 0;                                           // Counter
    while (file_exists($upload_path . $new_filename . '.' . $extension)) {            // If file exists
        $i = $i + 1;                                    // Update counter
        $new_filename = $basename . $i;         // New filepath
    }

    return $new_filename . '.' . $extension;                                 // Return filename
}

function str_lreplace($search, $replace, $subject)
{
    $pos = strrpos($subject, $search);

    if ($pos !== false) {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
}

function pdo(PDO $pdo, string $sql, array $arguments = null): PDOStatement
{
    if (!$arguments) {
        return $pdo->query($sql);
    }
    $statement = $pdo->prepare($sql);
    $statement->execute($arguments);
    return $statement;
}