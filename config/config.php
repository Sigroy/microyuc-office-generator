<?php
define('DEV', true);
define('ROOT_FOLDER', 'public');

$esta_carpeta = substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT'])); // Carpeta en la que está este archivo
$carpeta_padre = dirname($esta_carpeta);                      // Carpeta padre de esta carpeta
define("DOC_ROOT", $carpeta_padre . DIRECTORY_SEPARATOR . ROOT_FOLDER . DIRECTORY_SEPARATOR);             // Raíz documento

// Configuración de la base de datos
$type = 'mysql';
$server = 'microyuc-project.cldoq9azsv2y.us-east-1.rds.amazonaws.com';
$db = 'microyuc_project';
$port = '3306';
$charset = 'utf8mb4';
$username = 'microyuc';
$password = 'NYyQaw1Mdrn1[wVt';

// Nombre de origen de datos
$dsn = "$type:host=$server;dbname=$db;port=$port;charset=$charset";

// Configuración para la subida de archivos
define('UPLOADS', dirname(__DIR__) . DIRECTORY_SEPARATOR . ROOT_FOLDER . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR);
define('TIPOS_COMPROBACION', ['capital_de_trabajo', 'activo_fijo', 'adecuaciones', 'insumos', 'certificaciones',]);
define('VIAS_GESTION', ['Correo electrónico', 'Llamada telefónica', 'Visita', 'Pago', 'Reestructura', 'Otro',]);
define('MODALIDADES', ['MYE', 'MYV',]);
define('TIPOS_CREDITO', ['GP', 'Aval', 'Hipotecario',]);
define('MEDIA_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/tiff', 'image/webp',]);
define('FILE_EXTENSIONS', ['jpeg', 'jpg', 'jpe', 'jif', 'jfif', 'png', 'gif', 'bmp', 'tif', 'tiff', 'webp',]);
define('MAX_SIZE', '5242880');