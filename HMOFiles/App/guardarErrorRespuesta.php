<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    require './../conexion/conexion.php';

    date_default_timezone_set('America/Mexico_City');
    header("Content-Type: text/html;charset=utf-8");

	$datos = json_decode($_POST['datos']);
    $hoy = date("Y-m-d H:i:s"); 

    foreach($datos as $objdatos){
        $parametros = '';
        $id_usario = $objdatos ->id_usario;
        $id_empresa =  $objdatos ->id_empresa;
        $respuesta = $objdatos ->respuesta;
        $versionapp = $objdatos ->versionapp;
        $fechaApp = $objdatos ->fechaApp;
       isset($objdatos ->tipo) ? $tipo = $objdatos ->tipo : $tipo = "LIC";

        $versionapp ? null: $versionapp = 0;

        $parametros=array(
            array($id_usario),
            array($id_empresa),
            array($tipo),
            array($respuesta,SQLSRV_PARAM_IN,SQLSRV_PHPTYPE_STRING('UTF-8')),
            array($versionapp),
            array($fechaApp)
        );

        $sql_insertpregunta = "INSERT INTO MOBILE_logErrores(id_usuario,id_empresa,tipo,respuesta,versionapp,fechaApp) VALUES (?,?,?,?,?,?)";
        $stmt_insertpregunta = sqlsrv_query( $conn,$sql_insertpregunta,$parametros);

        if($stmt_insertpregunta === false) {
            //die( print_r( sqlsrv_errors(), true));
            //echo 0;
        }else{
            echo 1;
        }
    }
//    sqlsrv_free_stmt($stmt_insertpregunta);
    sqlsrv_close($conn); 

?>