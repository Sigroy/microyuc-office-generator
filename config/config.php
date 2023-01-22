<?php
define('DEV', true);
define('ROOT_FOLDER', 'public');

//Get Heroku ClearDB connection information
//$cleardb_url = parse_url(getenv("CLEARDB_DATABASE_URL"));
//$cleardb_server = $cleardb_url["host"];
//$cleardb_username = $cleardb_url["user"];
//$cleardb_password = $cleardb_url["pass"];
//$cleardb_db = substr($cleardb_url["path"],1);
//$active_group = 'default';
//$query_builder = TRUE;

// Connect to DB
//$conn = mysqli_connect($cleardb_server, $cleardb_username, $cleardb_password, $cleardb_db);

$esta_carpeta = substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT'])); // Carpeta en la que está este archivo
$carpeta_padre = dirname($esta_carpeta);                      // Carpeta padre de esta carpeta
define("DOC_ROOT", $carpeta_padre . DIRECTORY_SEPARATOR . ROOT_FOLDER . DIRECTORY_SEPARATOR);             // Raíz documento

// Configuración de la base de datos
$type = 'mysql';
$server = 'localhost';
$db = 'microyuc_project';
$port = '3307';
$charset = 'utf8mb4';
$username = 'sig';
$password = '1234';

// Nombre de origen de datos
$dsn = "$type:host=$server;dbname=$db;port=$port;charset=$charset";

// Configuración para la subida de archivos
define('UPLOADS', dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . ROOT_FOLDER . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR);
define('MEDIA_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/tiff', 'image/webp',]);
define('FILE_EXTENSIONS', ['jpeg', 'jpg', 'jpe', 'jif', 'jfif', 'png', 'gif', 'bmp', 'tif', 'tiff', 'webp',]);
define('MAX_SIZE', '5242880');

//$conn = mysqli_connect('localhost', 'sig', '1234', 'microyuc_project');
//
//if (!$conn) {
//    echo 'Connection error: ' . mysqli_connect_error();
//}
