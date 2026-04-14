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

// Consultamos todas las incidencias
$sql = "SELECT id, titulo, latitud, longitud, foto_url FROM incidencias WHERE estado = 'pendiente' OR estado IS NULL";
$resultado = mysqli_query($con, $sql);

$incidencias = array();
while($fila = mysqli_fetch_assoc($resultado)) {
    // Vamos metiendo cada fila en el array
    $incidencias[] = $fila;
}

// Devolvemos el array completo en formato JSON
header('Content-Type: application/json');
echo json_encode(array("exito" => true, "datos" => $incidencias));

mysqli_close($con);
?>