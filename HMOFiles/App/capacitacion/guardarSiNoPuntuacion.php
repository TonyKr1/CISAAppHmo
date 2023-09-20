<?php
	require './../../conexion/conexion.php';
	
    date_default_timezone_set('America/Mexico_City');

    $cedulageneral = json_decode($_POST['datosCedulaGeneral']);
    $datosGeneralesCurso = json_decode($_POST['datosGeneralesCurso']);
    $cursoCiertoFalso = json_decode($_POST['CAP_RespuestasSiNoPuntuacion']);

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

    function actualizaDatoAvance($id_course, $id_candidato, $apto){
        require './../../conexion/conexion.php';
        $parametros2 = array(
            array($apto),
            array($id_candidato),
            array($id_course)
        );

        $C_Update_process = "UPDATE CAP_BecariosVsCursos SET realizado = 1, aprobado = ? WHERE FK_Becario = ? AND FK_curso = ?;";
        $stmt_Update_process = sqlsrv_query( $conn,$C_Update_process,$parametros2);
        if($stmt_Update_process === false) {}
        return 1;
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
                array(1),
                array(1),
                array($objResS->nombre_evalua,SQLSRV_PARAM_IN,SQLSRV_PHPTYPE_STRING('UTF-8')),
                array($objResS->id_cliente),
                array(getSiglasEmpresa($objResS->id_cliente)),
                array($objResS->fecha_entrada),
                array($objResS->fecha_salida),
                array($objResS->fecha_envio)
            );

            $InsertCed = "INSERT INTO CAP_CursosHeader(FK_TipoCurso, FK_IDCurso, nameCourse, FK_empresa, siglasEmpresa, fechainicio, fechaFin, fechaApp) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?);";
            $stmt_InsertCed = sqlsrv_query( $conn,$InsertCed,$parametros);

            if($stmt_InsertCed === false) {
                echo 'CHMO1:['.$InsertCed.'] \n HMO1:';
                die( print_r( sqlsrv_errors(), true));
            }else{
                $sql_max = "SELECT MAX(ID) as maxced FROM CAP_CursosHeader";
            
                $stmt_max = sqlsrv_query( $conn, $sql_max);
                
                if( $stmt_max === false ) {
                    die( print_r( sqlsrv_errors(), true));
                }
        
                $valuesMax=sqlsrv_fetch_array($stmt_max,SQLSRV_FETCH_ASSOC);
                $id_cedulaPadre = $valuesMax['maxced'];
                $exis = 1;
            }
        // }

        $FK_IdTipoCurso = $objResS->geolocalizacion_salida;
    }

    //sqlsrv_free_stmt($stmt_InsertCed);


    // if($exis == 1){
        foreach($datosGeneralesCurso as $objDataG){
            $parametros = '';
            $parametros=array(
                array($id_cedulaPadre),
                array($FK_IdTipoCurso),
                array($objDataG->id_course),
                array($objDataG->name_course),
                array($objDataG->id_instructor),
                array($objDataG->nombreInstructor),
                array($objDataG->id_candidato),
                array($objDataG->nombreCandidato),
                array($objDataG->edad),
                array($objDataG->telCelular),
                array($objDataG->antecedentesManejo),
                array($objDataG->fecha),
                array($objDataG->fecha_captura),
                array($objDataG->apto),
                array($objDataG->observaciones),
                array($objDataG->promedio),
                array($objDataG->ID_AT),
                array($objDataG->Prueba),
                array($objDataG->costo),
                array($id_cedulaPadre),
                array($objDataG->firmaInstructor),
                array($objDataG->id_instructor)
            );

            $C_Insert_datos = "INSERT INTO CAP_DatosGeneralesCurso(FK_CursosHeader, FK_TipoCurso, FK_IDCurso, name_course, FK_Instructor, nameInstructor, FK_Becario, nameBecario, edad, telCelular, antecedentesManejo, fecha, fechaCaptura, apto, observaciones, Promedio, ID_AT, Prueba, Costo) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); INSERT INTO CAP_Firmas(FK_CursosHeader, firma, FK_firmaPertenece) VALUES (?,?,?);";
            $stmt_Insert_datos = sqlsrv_query( $conn,$C_Insert_datos,$parametros);
    
            if($stmt_Insert_datos === false) {
                echo 'CHMO2:['.$C_Insert_datos.'] \n HMO2:';
                die( print_r( sqlsrv_errors(), true));
                $enviar = 0;
            }else{
                $sql_max2 = "SELECT MAX(ID) as maxced FROM CAP_DatosGeneralesCurso";
            
                $stmt_max2 = sqlsrv_query( $conn, $sql_max2);
                
                if( $stmt_max2 === false ) {
                    die( print_r( sqlsrv_errors(), true));
                }
        
                $valuesMax2=sqlsrv_fetch_array($stmt_max2,SQLSRV_FETCH_ASSOC);
                $FK_DatosGeneralesCurso = $valuesMax2['maxced'];
                $enviar = 1;
            }
            $dato = actualizaDatoAvance($objDataG->id_course, $objDataG->id_candidato, $objDataG->apto);
        }
    
    //     //sqlsrv_free_stmt($stmt_Insert_datos);
        $correctas = 0;
        $length = 0;
        foreach($cursoCiertoFalso as $objCurso){
            $length++;
            
            if($objCurso->Respuesta == $objCurso->OpCorrecta){
                $correctas++;
            }

            $parametros = '';
            $parametros = array(
                array($id_cedulaPadre),
                array($FK_DatosGeneralesCurso),
                array($objCurso->FK_IDCurso),
                array($objCurso->FK_IDPregunta),
                array($objCurso->Respuesta),
                array($objCurso->fecha)
            );

            $C_Insert_check = "INSERT INTO CAP_RespuestasSiNoPuntuacion(FK_CursosHeader, FK_DatosGenerales, FK_IDCurso, FK_IDPregunta, Respuesta, fecha) 
            VALUES (?, ?, ?, ?, ?, ?);";
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