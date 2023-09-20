<?php
	require './../../conexion/conexion.php';
	
    date_default_timezone_set('America/Mexico_City');

    $cedulageneral = json_decode($_POST['datosCedulaGeneral']);
    $Relevos = json_decode($_POST['Relevos']);
    
    function getSiglasEmpresa($Empresa){
        if($Empresa == 1){
            return 'ACHSA';
        } else if($Empresa == 35){
            return 'AMTM';
        } else if($Empresa == 37){
            return 'AULSA';
        } else if($Empresa == 3){
            return 'CCA';
        } else if($Empresa == 4){
            return 'CISA';
        } else if($Empresa == 5){
            return 'COAVE';
        } else if($Empresa == 41){
            return 'CODIV';
        } else if($Empresa == 6){
            return 'COPE';
        } else if($Empresa == 7){
            return 'COR3N';
        } else if($Empresa == 8){
            return 'COREV';
        } else if($Empresa == 9){
            return 'COTAN';
        } else if($Empresa == 10){
            return 'COTOB';
        } else if($Empresa == 39){
            return 'COTXS';
        } else if($Empresa == 11){
            return 'MIHSA';
        } else if($Empresa == 13){
            return 'SIMES';
        } else if($Empresa == 14){
            return 'SKYB';
        } else if($Empresa == 15){
            return 'STMP';
        } else if($Empresa == 16){
            return 'TCG';
        } else if($Empresa == 19){
            return 'TUZO';
        } else if($Empresa == 18){
            return 'VYCSA';
        } else {
            return 0;
        }
        // ATROL => 2, BUSSI => 20, CORET => 26, ESASA => 22, IXTAP => 44, RECSA => 12, REFORMA => 40, TREPSA => 17
    }

    function llamaRelevos($IDEntra, $IDSale, $FkUsuarioMovE, $UsuarioMovE, $fechaEntrada, $fechaSalida, $EcoE, $FKUnidadE){
        require './../../conexion/conexion.php';
        
        //* Obtener los consecutivos para cierre de turno
        $sql_max1 = "SELECT MAX(CODIGO_UNIQUE_TURNO) consecutivo , MAX(CODIGO_UNIQUE_TURNO_TERMINO) consecutivo_termino FROM CT_GESTION_TURNOS_ASIGNADOS --WHERE convert(varchar, fecha_registro_gest, 23) = '2023-08-24'";    
        $stmt_max1 = sqlsrv_query( $conn, $sql_max1);
        
        if( $stmt_max1 === false ) {
            die( print_r( sqlsrv_errors(), true));
        }

        $valuesMax1 = sqlsrv_fetch_array($stmt_max1,SQLSRV_FETCH_ASSOC);

        if ($valuesMax1['consecutivo_termino'] == $valuesMax1['consecutivo']) { 
            $consecutivo_termino = $valuesMax1['consecutivo']+1; 
        } 
        if ($valuesMax1['consecutivo'] > $valuesMax1['consecutivo_termino']){
            $consecutivo_termino = $valuesMax1['consecutivo']+1;
        } 
        if ($valuesMax1['consecutivo'] < $valuesMax1['consecutivo_termino']) {
            $consecutivo_termino = $valuesMax1['consecutivo_termino']+1;
        }
            
        //* Obtener la historia del turno 1 si existe
        $sql = "SELECT id_turno_referencia as CampoArray FROM CT_GESTION_TURNOS_ASIGNADOS WHERE id_turno_asignado_gest = '{$IDSale}'";
        $stmt = sqlsrv_query( $conn, $sql);
        
        if( $stmt === false ) {
            die( print_r( sqlsrv_errors(), true));
        }

        $value = sqlsrv_fetch_array($stmt,SQLSRV_FETCH_ASSOC);
        $CampoArray = $value['CampoArray'];
        if($CampoArray == 0){
            $array = '';
        } else {
            $array = '"'.$IDEntra.'",'.$CampoArray; 
        }
        $fechaSalidas = explode("T", $fechaSalida);

        //* Cerrar turno uno
        $parametros = '';
        $parametros=array(
            array($consecutivo_termino),
            array($fechaSalidas[0]),
            array($fechaSalidas[1]),
            array('CONCLUIDA'),
            array('CONCLUIDA'),
            array(0)
        );

        $C_UPDATE = "UPDATE CT_GESTION_TURNOS_ASIGNADOS SET CODIGO_UNIQUE_TURNO_TERMINO = ?, fecha_cierre_real_gest = ?, hora_cierre_real_gest = ?, estatus_asignacion_turno_gest = ?, estatus_asignacion_ruta_gest = ?, estatus_registro_turno_asignado_gest = ? WHERE id_turno_asignado_gest = '{$IDSale}';";
        $stmt_C_UPDATE = sqlsrv_query( $conn,$C_UPDATE,$parametros);

        if($stmt_C_UPDATE === false) {
            echo 'RELHMO2:['.$C_UPDATE.'] \n HMO2:';
            die( print_r( sqlsrv_errors(), true));
            $enviar = 0;
        }else{
            $enviar = 1;
        }

        //* Obtener los consecutivos para apertura de turno

        $sql_max1 = "SELECT MAX(CODIGO_UNIQUE_TURNO) consecutivo , MAX(CODIGO_UNIQUE_TURNO_TERMINO) consecutivo_termino FROM CT_GESTION_TURNOS_ASIGNADOS --WHERE convert(varchar, fecha_registro_gest, 23) = '2023-08-24'";    
        $stmt_max1 = sqlsrv_query( $conn, $sql_max1);
        
        if( $stmt_max1 === false ) {
            die( print_r( sqlsrv_errors(), true));
        }

        $valuesMax1 = sqlsrv_fetch_array($stmt_max1,SQLSRV_FETCH_ASSOC);

        if ($valuesMax1['consecutivo_termino'] == $valuesMax1['consecutivo']) { 
            $consecutivo_termino = $valuesMax1['consecutivo']+1; 
        } 
        if ($valuesMax1['consecutivo'] > $valuesMax1['consecutivo_termino']){
            $consecutivo_termino = $valuesMax1['consecutivo']+1;
        } 
        if ($valuesMax1['consecutivo'] < $valuesMax1['consecutivo_termino']) {
            $consecutivo_termino = $valuesMax1['consecutivo_termino']+1;
        }

        //* Abrir turno 1
        $fechaEntradas = explode("T", $fechaEntrada);
        $parametros1=array(
            array($consecutivo_termino),
            array($fechaEntradas[0]),
            array($fechaEntradas[1]),
            array($fechaEntradas[1]),
            array('ASIGNADO'),
            array('EN RUTA'),
            array($FkUsuarioMovE),
            array($UsuarioMovE),
            array($EcoE),
            array($FKUnidadE),
            array($fechaEntradas[1])
        );

        $C_UPDATE = "UPDATE CT_GESTION_TURNOS_ASIGNADOS SET CODIGO_UNIQUE_TURNO = ?, fecha_asignacion_real_gest = ?, hora_entrada_real_gest = ?, hora_asignacion_real_gest = ?, estatus_asignacion_turno_gest = ?, estatus_asignacion_ruta_gest = ?, FK_id_usuario_registro_gest = ?, clave_usua_resgistro_gest = ?, clave_unidad_gest = ?, FK_id_unidad = ?, hora_inicio_servicio_ref_gest = ?, fecha_registro_gest = GETDATE() WHERE id_turno_asignado_gest = '{$IDEntra}';";
        $stmt_C_UPDATE = sqlsrv_query( $conn,$C_UPDATE,$parametros1);

        if($stmt_C_UPDATE === false) {
            echo 'RELHMO2:['.$C_UPDATE.'] \n HMO2:';
            die( print_r( sqlsrv_errors(), true));
            $enviar = 0;
        }else{
            $enviar = 1;
        }
    }

    foreach($cedulageneral as $objResS){
        // $sql_checks = "SELECT id_cedula FROM registro_general_app WHERE tipo_cedula = ? AND id_usuario = ? AND fecha_entrada = ?";

        // $parametros=array(
        //     array($objResS->tipo_cedula,SQLSRV_PARAM_IN,SQLSRV_PHPTYPE_STRING('UTF-8')),
        //     array($objResS->id_usuario),
        //     array($objResS->fecha_entrada)
        // );
    
        // $stmt_checks = sqlsrv_query( $conn, $sql_checks, $parametros);
        // if( $stmt_checks === false ) {
        //     echo 'C0:['.$InsertCed.'] \n E0:';
        //     die( print_r( sqlsrv_errors(), true));
        // }
    
        // $datos=sqlsrv_fetch_array($stmt_checks,SQLSRV_FETCH_ASSOC);
    
        // $tieneDatos=sqlsrv_has_rows($stmt_checks);
    
        // if($tieneDatos){
        //     $exis = 0;
        //     echo "CEDULA._." . $datos['id_cedula'];
        // }else{

            $parametros = '';
            $parametros=array(
                array($objResS->tipo_cedula),
                array($objResS->id_usuario),
                array($objResS->nombre_usuario,SQLSRV_PARAM_IN,SQLSRV_PHPTYPE_STRING('UTF-8')),
                array($objResS->fecha_entrada),
                array($objResS->fecha_salida),
                array($objResS->fecha_envio),
                array($objResS->id_cliente)
            );

            $InsertCed = "INSERT INTO REL_Registros(tipoMovimiento, FK_Usuario, nombreUsuario, fechaInicio, fechaFin, fechaEnvio, FKEmpresa) 
            VALUES (?, ?, ?, ?, ?, ?, ?);";
            $stmt_InsertCed = sqlsrv_query( $conn,$InsertCed,$parametros);

            if($stmt_InsertCed === false) {
                echo 'RELHMO1:['.$InsertCed.'] \n HMO1:';
                die( print_r( sqlsrv_errors(), true));
            }else{
                $sql_max = "SELECT MAX(ID) as maxced FROM REL_Registros";
            
                $stmt_max = sqlsrv_query( $conn, $sql_max);
                
                if( $stmt_max === false ) {
                    die( print_r( sqlsrv_errors(), true));
                }
        
                $valuesMax=sqlsrv_fetch_array($stmt_max,SQLSRV_FETCH_ASSOC);
                $id_cedulaPadre = $valuesMax['maxced'];
                $exis = 1;
            }
        // }
    }

    if($exis == 1){
        foreach($Relevos as $objRelevos){
            $parametros = '';
            $parametros=array(
                array($id_cedulaPadre),
                array($objRelevos->Eco),
                array($objRelevos->EcoE),
                array($objRelevos->FKUnidad),
                array($objRelevos->FKUnidadE),
                array($objRelevos->FkUsuarioMov),
                array($objRelevos->FkUsuarioMovE),
                array($objRelevos->IDEntra),
                array($objRelevos->IDSale),
                array($objRelevos->ID_personal),
                array($objRelevos->ID_personalE),
                array($objRelevos->UsuarioMov),
                array($objRelevos->UsuarioMovE),
                array($objRelevos->claveEmpleado),
                array($objRelevos->claveEmpleadoE),
                array($objRelevos->fechaEntrada),
                array($objRelevos->fechaSalida),
                array($objRelevos->fullName),
                array($objRelevos->fullNameE),
                array($objRelevos->jornada),
                array($objRelevos->jornadaE),
                array($objRelevos->linea),
                array($objRelevos->lineaE)
            );

            $C_Insert_datos = "INSERT INTO REL_Detalle(FK_registro, Eco, EcoE, FKUnidad, FKUnidadE, FkUsuarioMov, FkUsuarioMovE, IDEntra, IDSale, ID_personal, ID_personalE, UsuarioMov, UsuarioMovE, claveEmpleado, claveEmpleadoE, fechaEntrada, fechaSalida, fullName, fullNameE, jornada, jornadaE, linea, lineaE)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
            $stmt_Insert_datos = sqlsrv_query( $conn,$C_Insert_datos,$parametros);
    
            if($stmt_Insert_datos === false) {
                echo 'RELHMO2:['.$C_Insert_datos.'] \n HMO2:';
                die( print_r( sqlsrv_errors(), true));
                $enviar = 0;
            }else{
                llamaRelevos($objRelevos->IDEntra, $objRelevos->IDSale, $objRelevos->FkUsuarioMovE, $objRelevos->UsuarioMovE, $objRelevos->fechaEntrada, $objRelevos->fechaSalida, $objRelevos->EcoE, $objRelevos->FKUnidadE);
                $enviar = 1;
            }
        }
        
        sqlsrv_close($conn);

        if($enviar == 1){
            echo "CEDULA._." . $id_cedulaPadre;
        }
    }

?>

