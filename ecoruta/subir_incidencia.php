<?php
$DB_SERVER="localhost";
$DB_USER="root";
$DB_PASS="";
$DB_DATABASE="ecoruta";

$con = mysqli_connect($DB_SERVER, $DB_USER, $DB_PASS, $DB_DATABASE);

if (mysqli_connect_errno()) {
    echo json_encode(array("exito" => false, "mensaje" => "Error BD"));
    exit();
}

// Por si la foto es grande
ini_set('memory_limit', '256M');

$parametros = json_decode(file_get_contents('php://input'), true);

if(isset($parametros["id_usuario"]) && isset($parametros["foto_base64"]) && isset($parametros["latitud"])) {
    
    $id_usuario = $parametros["id_usuario"];
    $titulo = $parametros["titulo"];
    $latitud = $parametros["latitud"];
    $longitud = $parametros["longitud"];
    $foto_base64 = $parametros["foto_base64"];

    //  Generar un nombre único para la foto y guardarla en la carpeta uploads
    $nombre_archivo = "incidencia_" . $id_usuario . "_" . time() . ".jpg";
    $ruta_guardado = "uploads/" . $nombre_archivo;
    
    // Decodificar el texto Base64 y guardarlo como archivo de imagen
    file_put_contents($ruta_guardado, base64_decode($foto_base64));

    //  Guardar los datos en la base de datos
    $sql = "INSERT INTO incidencias (id_usuario, titulo, latitud, longitud, foto_url) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    
    // Guardamos la URL relativa para poder cargarla luego en la app
    $url_publica = "http://10.0.2.2/ecoruta/" . $ruta_guardado; 
    
    mysqli_stmt_bind_param($stmt, "isdds", $id_usuario, $titulo, $latitud, $longitud, $url_publica);

    if (mysqli_stmt_execute($stmt)) {
        $respuesta = array("exito" => true, "mensaje" => "Incidencia reportada con éxito");
    } else {
        $respuesta = array("exito" => false, "mensaje" => "Error al guardar en BD: " . mysqli_error($con));
    }
    mysqli_stmt_close($stmt);

} else {
    $respuesta = array("exito" => false, "mensaje" => "Faltan parámetros");
}

header('Content-Type: application/json');
echo json_encode($respuesta);
mysqli_close($con);
?>