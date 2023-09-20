<?php
	require './../conexion/conexion.php';
	
	$datos = json_decode($_POST['datos']);

    foreach($datos as $objdatos){
        $parametros = '';
        $id_usuario = $objdatos ->id_usuario;
        $id_empresa =  $objdatos ->id_empresa;
        $tipo_cedula = $objdatos ->tipo_cedula;
        $versionapp  = $objdatos ->versionapp;
        $fechaApp = $objdatos ->fechaApp;

        $versionapp ? null: $versionapp = 0;

        $parametros=array(
            array($id_usuario),
            array($id_empresa),
            array($tipo_cedula,SQLSRV_PARAM_IN,SQLSRV_PHPTYPE_STRING('UTF-8')),
            array($versionapp),
            array($fechaApp)
        );

        $sql_insertpregunta = "INSERT INTO MOBILE_logEnvio(id_usuario,id_empresa,tipo,versionapp,fechaApp) VALUES (?,?,?,?,?)";
        $stmt_insertpregunta = sqlsrv_query( $conn,$sql_insertpregunta,$parametros);

        if($stmt_insertpregunta === false) {
            die( print_r( sqlsrv_errors(), true));
        }else{
            echo 1;
        }
    }
    //sqlsrv_free_stmt($stmt_insertpregunta);
    sqlsrv_close($conn);
?>