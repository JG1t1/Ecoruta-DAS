<?php
$con = mysqli_connect("localhost", "root", "", "ecoruta");
$parametros = json_decode(file_get_contents('php://input'), true);

if(isset($parametros["id_incidencia"])) {
    $id = $parametros["id_incidencia"];
    $sql = "UPDATE incidencias SET estado = 'resuelta' WHERE id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(array("exito" => true, "mensaje" => "¡Incidencia resuelta!"));
    } else {
        echo json_encode(array("exito" => false, "mensaje" => "Error al resolver"));
    }
}
?>