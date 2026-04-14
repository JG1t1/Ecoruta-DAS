<?php
// Configuración de la base de datos
$DB_SERVER="localhost"; 
$DB_USER="root";        // Usuario por defecto en XAMPP
$DB_PASS="";            // Contraseña vacía por defecto en XAMPP
$DB_DATABASE="ecoruta"; // Nuestra base de datos

// Se establece la conexión
$con = mysqli_connect($DB_SERVER, $DB_USER, $DB_PASS, $DB_DATABASE);

// Comprobamos la conexión
if (mysqli_connect_errno()) {
    $respuesta = array("exito" => false, "mensaje" => "Error de conexion: " . mysqli_connect_error());
    echo json_encode($respuesta);
    exit();
}

// Recogemos el JSON que nos envía la app de Android
$parametros = json_decode(file_get_contents('php://input'), true);

if(isset($parametros["nombre"]) && isset($parametros["email"]) && isset($parametros["password"])) {
    $nombre = $parametros["nombre"];
    $email = $parametros["email"];
    // Por seguridad, SIEMPRE encriptamos la contraseña en el servidor
    $password_encriptada = password_hash($parametros["password"], PASSWORD_DEFAULT);

    // Sentencia SQL preparada (evita inyección SQL y hackeos)
    $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $nombre, $email, $password_encriptada);

    if (mysqli_stmt_execute($stmt)) {
        $respuesta = array("exito" => true, "mensaje" => "Usuario registrado correctamente");
    } else {
        $respuesta = array("exito" => false, "mensaje" => "Error al registrar: Puede que el email ya exista.");
    }
    mysqli_stmt_close($stmt);

} else {
    $respuesta = array("exito" => false, "mensaje" => "Faltan parámetros");
}

// Devolver el resultado en formato JSON para que Android lo entienda
header('Content-Type: application/json');
echo json_encode($respuesta);

mysqli_close($con);
?>