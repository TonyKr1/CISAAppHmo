<?php
	require './../../conexion/conexion.php';
	
    date_default_timezone_set('America/Mexico_City');

    $cedulageneral = json_decode($_POST['datosCedulaGeneral']);
    $DesTechHeader = json_decode($_POST['DesTechHeader']);
    $DesTechDetails = json_decode($_POST['DesTechDetails']);
    // $DesTecFirmas = json_decode($_POST['DesTecFirmas']);
    
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

    function saveInAzure($id_empresa, $id_usuario, $id_unidad, $observacion, $id_tipo_falla_fk, $FK_Pregunta){
        require './../../conexion/conexionAzure.php';
        require './../../conexion/conexion.php';
        $sql_max = "SELECT MAX(consecutivo_incidencia)+1 as nextID FROM RecaudoIncidencias;";
            
        $stmt_max = sqlsrv_query( $connAzure, $sql_max);
        
        if( $stmt_max === false ) {
            die( print_r( sqlsrv_errors(), true));
        }

        $valuesMax=sqlsrv_fetch_array($stmt_max,SQLSRV_FETCH_ASSOC);
        $nextID = $valuesMax['nextID'];
        
        ///////////////////////////////*****************--------        -------****************** *////////////////////////////////

        $sql_max1 = "SELECT FK_equipo FROM TEC_Revisones WHERE ID = $FK_Pregunta;";
            
        $stmt_max1 = sqlsrv_query( $conn, $sql_max1);
        
        if( $stmt_max1 === false ) {
            echo 'TECHMO2:['.$InsertCed.'] \n HMO2:';
            die( print_r( sqlsrv_errors(), true));
        }

        $valuesMax1=sqlsrv_fetch_array($stmt_max1,SQLSRV_FETCH_ASSOC);

        $id_tipo_equipo_fk = $valuesMax1['FK_equipo'];

        $folio_recaudo_incidencia = "CDNT-HMO-".$nextID;

        $pos = strrpos($id_tipo_falla_fk, ",");
        
        if($pos){
            $partes = explode(",",$id_tipo_falla_fk);
            $id_tipo_falla_fk = trim($partes[0]);
        } else {
            $id_tipo_falla_fk = $id_tipo_falla_fk;
        }

        $hoy = date("Y-m-d H:i:s"); 
        $hoy = str_replace(" ", "T", $hoy);

        $parametros = '';
        $parametros=array(
            array($folio_recaudo_incidencia),
            array($observacion),
            array($hoy),
            array("PENDIENTE"),
            array($nextID),
            array("SIN IMAGEN"),
            array(1),
            array($id_unidad),
            array($id_tipo_equipo_fk),
            array($id_tipo_falla_fk),
            array(1),
            array(getSiglasEmpresa($id_empresa)),
            array($id_usuario),
            array("TMSHMO"),
            array("MOBILE"),
            array(1),
            array(1)
        );

        $InsertCed = "INSERT INTO RecaudoIncidencias(folio_recaudo_incidencia, observacion, fecha_reporte, status_incidencia, consecutivo_incidencia, url_files, numero_proceso, id_unidad, id_tipo_equipo_fk, id_tipo_falla_fk, id_tecnico_fk, id_empresa, id_usuario, modulo_acceso, canal_acceso, status_registro_recaudo_incidencia, flag_gobierno) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
            $stmt_InsertCed = sqlsrv_query( $connAzure,$InsertCed,$parametros);

            if($stmt_InsertCed === false) {
                echo 'TECHMO1:['.$InsertCed.'] \n HMO1:'.$parametros;
                die( print_r( sqlsrv_errors(), true));
            }else{
                $exisss = 1;
            }
    }

    //saveInAzure(1, 'jacruz', 1486, 'SIN CONFIGURACION, FALLA SISTEMA OPERATICO', '33 34', 1)

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
                array($objResS->fecha_envio)
            );

            $InsertCed = "INSERT INTO TEC_Registros(tipoMovimiento, FK_Usuario, nombreUsuario, fechaInicio, fechaFin, fechaEnvio) 
            VALUES (?, ?, ?, ?, ?, ?);";
            $stmt_InsertCed = sqlsrv_query( $conn,$InsertCed,$parametros);

            if($stmt_InsertCed === false) {
                echo 'TECHMO1:['.$InsertCed.'] \n HMO1:';
                die( print_r( sqlsrv_errors(), true));
            }else{
                $sql_max = "SELECT MAX(ID) as maxced FROM TEC_Registros";
            
                $stmt_max = sqlsrv_query( $conn, $sql_max);
                
                if( $stmt_max === false ) {
                    die( print_r( sqlsrv_errors(), true));
                }
        
                $valuesMax=sqlsrv_fetch_array($stmt_max,SQLSRV_FETCH_ASSOC);
                $id_cedulaPadre = $valuesMax['maxced'];
                $id_empresa = $objResS->id_cliente;
                $id_usuario = $objResS->nombre_usuario;
                $exis = 1;
            }
        // }
    }

    if($exis == 1){
        foreach($DesTechHeader as $objDesTechHeader){
            $parametros = '';
            $parametros=array(
                array($id_cedulaPadre),
                array($objDesTechHeader->credencial), 
                array($objDesTechHeader->fecha_fin), 
                array($objDesTechHeader->fecha_inicio), 
                array($objDesTechHeader->id_operador), 
                array($objDesTechHeader->id_unidad), 
                array($objDesTechHeader->observaciones), 
                array($objDesTechHeader->operador), 
                array($objDesTechHeader->unidad)
            );
            
            $id_unidad = $objDesTechHeader->id_unidad;

            $C_Insert_datos = "INSERT INTO TEC_Header(FK_registro, credencial, fechaFin, fechaInicio, FK_Operador, FK_Unidad, observaciones, operador, unidad) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);";
            $stmt_Insert_datos = sqlsrv_query( $conn,$C_Insert_datos,$parametros);
    
            if($stmt_Insert_datos === false) {
                echo 'TECHMO2:['.$C_Insert_datos.'] \n HMO2:';
                die( print_r( sqlsrv_errors(), true));
                $enviar = 0;
            }else{
                $sql_max2 = "SELECT MAX(ID) as maxced FROM TEC_Header";
            
                $stmt_max2 = sqlsrv_query( $conn, $sql_max2);
                
                if( $stmt_max2 === false ) {
                    die( print_r( sqlsrv_errors(), true));
                }
        
                $valuesMax2=sqlsrv_fetch_array($stmt_max2,SQLSRV_FETCH_ASSOC);
                $FK_TEC_Header = $valuesMax2['maxced'];
                $enviar = 1;

                $IdHeader = $objDesTechHeader->IdHeader;

                foreach($DesTechDetails as $objDesTechDetails){
                    if($IdHeader == $objDesTechDetails->IdHeader){
                        $parametros = '';
                        $parametros=array(
                            array($id_cedulaPadre),
                            array($FK_TEC_Header),
                            array($objDesTechDetails->id_formato),
                            array($objDesTechDetails->Fk_pregunta),
                            array($objDesTechDetails->comentarios),
                            array($objDesTechDetails->FKsFallas),
                            array($objDesTechDetails->falla),
                            array($objDesTechDetails->respuesta)
                        );
            
                        if($objDesTechDetails->respuesta == 2){
                            $observacion = $objDesTechDetails->falla;
                            $id_tipo_falla_fk = $objDesTechDetails->FKsFallas;
                            $id_tipo_equipo_fk = $objDesTechDetails->Fk_pregunta;
                            if($id_tipo_falla_fk){
                                if($id_tipo_falla_fk == 0 OR $id_tipo_falla_fk == '' OR $id_tipo_falla_fk == null){} else {
                                    saveInAzure($id_empresa, $id_usuario, $id_unidad, $observacion, $id_tipo_falla_fk, $id_tipo_equipo_fk);
                                }
                            }
                        }

                        $C_Insert_datos = "INSERT INTO TEC_Details(FK_registro, FK_header, FK_formato, FK_pregunta, comentarios, FKFallas, fallas, respuesta) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?);";
                        $stmt_Insert_datos = sqlsrv_query( $conn,$C_Insert_datos,$parametros);
                
                        if($stmt_Insert_datos === false) {
                            echo 'TECHMO3:['.$C_Insert_datos.'] \n HMO3:';
                            die( print_r( sqlsrv_errors(), true));
                            $enviar = 0;
                        }else{
                            $enviar = 1;
                        }
                    }
                }
        
                // foreach($DesTecFirmas as $objDesTecFirmas){
                //     if($IdHeader == $objDesTecFirmas->IdHeader){
                //         if($objDesTecFirmas->firma){
                //             $parametros = '';
                //             $parametros=array(
                //                 array($id_cedulaPadre),
                //                 array($FK_TEC_Header),
                //                 array($objDesTecFirmas->fecha),
                //                 array($objDesTecFirmas->firma)
                //             );
                
                //             $C_Insert_datos = "INSERT INTO TEC_Firmas(FK_registro, FK_header, fecha, firma) 
                //             VALUES (?, ?, ?, ?);";
                //             $stmt_Insert_datos = sqlsrv_query( $conn,$C_Insert_datos,$parametros);
                    
                //             if($stmt_Insert_datos === false) {
                //                 echo 'TECHMO4:['.$C_Insert_datos.'] \n HMO4:';
                //                 die( print_r( sqlsrv_errors(), true));
                //                 $enviar = 0;
                //             }else{
                //                 $enviar = 1;
                //             }
                //         }
                //     }
                // }
            }
        }
        
        sqlsrv_close($conn);
        sqlsrv_close($connAzure); 

        if($enviar == 1){
            echo "CEDULA._." . $id_cedulaPadre;
        }
    }

?>