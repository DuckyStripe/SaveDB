<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST");
header("Allow: GET, POST");


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $token = $_GET['token'] ?? false;
    $tel = $_GET['tel'] ?? false;
    $msg = $_GET['msg'] ?? false;
    $status = $_GET['status'] ?? false;
    $respuesta=[];
    if (!$tel && !$msg && !$token) {
        http_response_code(400);
        $respuesta['estatus']=0;
        $respuesta['mensaje'] = 'Los campos telefono, mensaje y token son obligatorios';
        echo json_encode($respuesta);
    } else if (!$tel || (is_numeric($tel)!=1)) {
        http_response_code(400);
        $respuesta['estatus']=0;
        $respuesta['mensaje'] = 'El campo telefono es obligatorio o debe de ser numerico';
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    } else if (!$msg || strlen($msg) > 100) {
        http_response_code(400);
        $respuesta['estatus']=0;
        $respuesta['mensaje'] = 'El campo mensaje es obligatorio debe ser menor a 100 caracteres';
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    } else if (!$token || is_numeric($token)!=1) {
        http_response_code(400);
        $respuesta['estatus']=0;
        $respuesta['mensaje'] = 'El campo token es obligatorio o debe de ser numerico';
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    } else if (!$status) {
        http_response_code(400);
        $respuesta['estatus']=0;
        $respuesta['mensaje'] = 'El campo status es obligatorio';
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    } else {
        
        $serverName = "127.0.0.1";
        $uid = "sa";
        $pwd = "Password123.";
        $databaseName = "BI_ENVIOS_MASIVOS";

        $connectionInfo = array(
            "UID" => $uid,
            "PWD" => $pwd,
            "Database" => $databaseName
        );
        $conn = sqlsrv_connect($serverName, $connectionInfo);

        $tsql =  "EXEC [dbo].[SP_GUARDARRESPUESTAS] '$token','$tel','$msg','$status' ";


        $stmt = sqlsrv_query($conn, $tsql);

        if ($stmt) {
            http_response_code(200);
            $respuesta['estatus']=1;
            $respuesta['mensaje'] = 'Se ha guardado en base.';
            echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(200);
            $respuesta['estatus']=1;
            $respuesta['mensaje'] = 'Eror al guardar en base.';
            $respuesta['error'] = sqlsrv_errors();
            echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
            die(print_r(sqlsrv_errors(), true));
        }

        sqlsrv_free_stmt($stmt);
        sqlsrv_close($conn);
    }
}
