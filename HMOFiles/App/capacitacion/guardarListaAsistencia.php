<?php
	require './../../conexion/conexion.php';
	
    date_default_timezone_set('America/Mexico_City');

    $cedulageneral = json_decode($_POST['datosCedulaGeneral']);
    $asistenciaHeader = json_decode($_POST['asistenciaHeader']);
    $asistenciaDetails = json_decode($_POST['asistenciaDetails']);

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


    foreach($cedulageneral as $objResS){
        foreach($asistenciaHeader as $objHeader){
            $fecha = $objHeader->fecha;
            $id_usuario = $objHeader->IDusuario;
            $nameUsuario = $objHeader->nameUsuario;
        }
            $parametros = '';
            $parametros=array(
                array($objResS->id_cliente),
                array(getSiglasEmpresa($objResS->id_cliente)),
                array($fecha),
                array($id_usuario),
                array($nameUsuario),
                array($objResS->fecha_entrada),
                array($objResS->fecha_salida),
                array($objResS->fecha_envio),
                array(0),
                array(0)
            );

            //* FK_empresa, siglasEmpresa, fecha, id_usuario, nameUsuario, fechainicio, fechaFin, fechaApp, fechaServidor -> auto
            $InsertCed = "INSERT INTO CAP_ListasHeader(FK_empresa, siglasEmpresa, fecha, IDusuario, nameUsuario, fechainicio, fechaFin, fechaApp, Validada, Nomina) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
            $stmt_InsertCed = sqlsrv_query( $conn,$InsertCed,$parametros);

            if($stmt_InsertCed === false) {
                echo 'CHMO1:['.$InsertCed.'] \n HMO1:';
                die( print_r( sqlsrv_errors(), true));
            }else{
                $sql_max = "SELECT MAX(ID) as maxced FROM CAP_ListasHeader";
            
                $stmt_max = sqlsrv_query( $conn, $sql_max);
                
                if( $stmt_max === false ) {
                    die( print_r( sqlsrv_errors(), true));
                }
        
                $valuesMax=sqlsrv_fetch_array($stmt_max,SQLSRV_FETCH_ASSOC);
                $id_cedulaPadre = $valuesMax['maxced'];
                $exis = 1;
            }
    }

    foreach($asistenciaDetails as $objDetails){
        $parametros = '';
        $parametros = array(
            array($id_cedulaPadre),
            array($objDetails->asiste),
            array($objDetails->claveBecario),
            array($objDetails->fecha),
            array($objDetails->fechaCaptura),
            array($objDetails->id_becario),
            array($objDetails->nameBecario),
            array(0),
            array(0)
        );

        $C_Insert_check = "INSERT INTO CAP_ListasDetails(FK_ListaHeader, asiste, claveBecario, fecha, fechaCaptura, id_becario, nameBecario, change, FK_cambio)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);";
        $stmt_Insert_check = sqlsrv_query( $conn,$C_Insert_check,$parametros);

        if($stmt_Insert_check === false) {
            echo 'C3:['.$C_Insert_check.'] \n E3:';
            die( print_r( sqlsrv_errors(), true));
            $enviar = 0;
        }else{
            $enviar = 1;
        }
    }

        //UpdatePromedio($correctas, $length, $FK_DatosGeneralesCurso);
        
    //     //sqlsrv_free_stmt($stmt_Insert_check);
        sqlsrv_close($conn);  

        if($enviar == 1){
            echo "CEDULA._." . $id_cedulaPadre;
        }
    // }
?>