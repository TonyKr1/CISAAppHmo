<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	require './../../conexion/conexion.php';
	
    date_default_timezone_set('America/Mexico_City');

    $LicenciasDetails = json_decode($_POST['LicenciasDetails']);

    foreach($LicenciasDetails as $objResS){
        $credencial = $objResS->credencial;

        $sql1 = "SELECT ID_licencias as ID FROM Licencias WHERE clave = '$credencial' AND flag_visible_lcnc = 1";
        $stmt1 = sqlsrv_query( $conn, $sql1);
        
        if( $stmt1 === false ) {
            echo 'LICHMO2:['.$sql1.'] \n HMO1:';
            die( print_r( sqlsrv_errors(), true));
        }

        $values1=sqlsrv_fetch_array($stmt1,SQLSRV_FETCH_ASSOC);

        if($values1){
            echo "ERROR._.1";
        } else {
            if($objResS->IDServidor == 0){
                $fecha_actual = strtotime(date("d-m-Y",time()));
                $fecha_entrada = strtotime($objResS->vigencia,time());
                $fecha = date("Y-m-d H:i:s");
                $fecha = str_replace(" ", "T", $fecha);
        
                if($fecha_actual < $fecha_entrada) {
                    $Vigencia = "VIGENTE";
                } else {
                    $Vigencia = "NO VIGENTE";
                }
    
                $parametros=array(
                    array($objResS->tipo),
                    array($objResS->vigencia."T00:00:00"),
                    array($Vigencia),
                    array($objResS->qrData),
                    array(1),
                    array($fecha),
                    array($fecha),
                    array("Carga Inicial"),
                    array(5),
                    array($objResS->credencial)
                );
    
                $InsertCed = "INSERT INTO Licencias(tipo_lincencia_lcnc, vigencia_lcnc, estatus_lcnc, qr_lcnc, flag_visible_lcnc, fecha_registro_lcnc, fecha_movimiento_lcnc, motivo_movimiento_lcnc, FK_usuario_movimiento_lcnc, clave) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
                $stmt_InsertCed = sqlsrv_query( $conn,$InsertCed,$parametros);
    
                if($stmt_InsertCed === false) {
                    var_dump($parametros);
                    echo 'LICHMO3:['.$InsertCed.'] \n HMO1:';
                    die( print_r( sqlsrv_errors(), true));
                    
                }else{
                    $sql_max = "SELECT MAX(ID_licencias) as maxced FROM Licencias";
                
                    $stmt_max = sqlsrv_query( $conn, $sql_max);
                    
                    if( $stmt_max === false ) {
                        die( print_r( sqlsrv_errors(), true));
                    }
            
                    $valuesMax=sqlsrv_fetch_array($stmt_max,SQLSRV_FETCH_ASSOC);
                    echo "CEDULA._." . $valuesMax['maxced'];
                }
            }
        }
    }

?>

