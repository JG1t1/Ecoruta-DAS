<?php
$DB_SERVER="localhost"; 
$DB_USER="root";        
$DB_PASS="";            
$DB_DATABASE="ecoruta"; 

$con = mysqli_connect($DB_SERVER, $DB_USER, $DB_PASS, $DB_DATABASE);

if (mysqli_connect_errno()) {
    echo json_encode(array("exito" => false, "mensaje" => "Error de conexion BD"));
    exit();
}

$parametros = json_decode(file_get_contents('php://input'), true);

if(isset($parametros["email"]) && isset($parametros["password"])) {
    $email = $parametros["email"];
    $password = $parametros["password"];

    $sql = "SELECT id, nombre, password FROM usuarios WHERE email = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($fila = mysqli_fetch_assoc($resultado)) {
        // Comparamos la contraseña enviada con el hash guardado
        if (password_verify($password, $fila['password'])) {
            $respuesta = array(
                "exito" => true, 
                "mensaje" => "Login correcto",
                "id_usuario" => $fila['id'],
                "nombre" => $fila['nombre']
            );
        } else {
            $respuesta = array("exito" => false, "mensaje" => "Contraseña incorrecta");
        }
    } else {
        $respuesta = array("exito" => false, "mensaje" => "El usuario no existe");
    }
    mysqli_stmt_close($stmt);
} else {
    $respuesta = array("exito" => false, "mensaje" => "Faltan parámetros");
}

header('Content-Type: application/json');
echo json_encode($respuesta);
mysqli_close($con);
?>
