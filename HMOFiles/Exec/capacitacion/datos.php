<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    date_default_timezone_set('America/Mexico_City');
    require './../../conexion/conexion.php';
    if(isset($_GET['empresa'])){
        $empresa = $_GET['empresa'];
        if(isset($_GET['paso'])){
            if($_GET['paso'] == 1){
                //*====     DatosEvaluacionSimple     ========================================================
                              
                $sql_DatosEvaluacion = "SELECT promedio,FK_TipoCurso,FK_IDCurso,FK_Instructor,FK_Becario,nameInstructor,nameBecario,apto,convert(varchar, fecha, 105) as fecha,observaciones FROM CAP_DatosGeneralesCurso WHERE FK_TipoCurso <> 2;";
                            
                $stmt_DatosEvaluacion = sqlsrv_query( $conn, $sql_DatosEvaluacion);
    
                if( $stmt_DatosEvaluacion === false ) {
                    die( print_r( sqlsrv_errors(), true));
                }
    
                $tieneDatos=sqlsrv_has_rows($stmt_DatosEvaluacion);
                if($tieneDatos){
                    while($valuesDatosEvaluacion = sqlsrv_fetch_array($stmt_DatosEvaluacion,SQLSRV_FETCH_ASSOC)){
                        $datos5[] = [
                            'FK_TipoCurso' => $valuesDatosEvaluacion['FK_TipoCurso'],
                            'FK_IDCurso' => $valuesDatosEvaluacion['FK_IDCurso'],
                            'FK_Instructor' => $valuesDatosEvaluacion['FK_Instructor'],
                            'nameInstructor' => $valuesDatosEvaluacion['nameInstructor'],
                            'FK_Becario' => $valuesDatosEvaluacion['FK_Becario'],
                            'nameBecario' => $valuesDatosEvaluacion['nameBecario'],
                            'apto' => $valuesDatosEvaluacion['apto'],
                            'fecha' => $valuesDatosEvaluacion['fecha'],
                            'observaciones' => $valuesDatosEvaluacion['observaciones'],
                            'promedio' => $valuesDatosEvaluacion['promedio']
                        ];
                    }
    
                    echo $json_data5 = json_encode($datos5, JSON_UNESCAPED_UNICODE);
    
                    $nombre_fichero = './../../JSONS/capacitacion/DatosEvaluacionS_'.$empresa.'.json';
    
                    if (file_exists($nombre_fichero)) {
                        file_put_contents('./../../JSONS/capacitacion/DatosEvaluacionS_'.$empresa.'.json', $json_data5);
                    } else {
                        $archivo = fopen('./../../JSONS/capacitacion/DatosEvaluacionS_'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
                        if( $archivo == false ){
                        }else{
                            file_put_contents('./../../JSONS/capacitacion/DatosEvaluacionS_'.$empresa.'.json', $json_data5);
                            fclose($archivo);
                        }
                    }
                }
    
                //*====     DatosEvaluacionDiaria     ========================================================
    
                $sql_DatosEvaluacionDia = "SELECT DISTINCT dg.FK_CursosHeader,FK_TipoCurso,dg.FK_IDCurso,nameInstructor,FK_Instructor,FK_Becario,nameBecario,apto,convert(varchar, dg.fecha, 105) as fecha,observaciones, 
                (SELECT COUNT(CASE WHEN (CASE WHEN Respuesta = 0 THEN 2 WHEN Respuesta = 1 THEN 1 END) = OpCorrecta THEN 1 END) FROM CAP_RespuestasSiNoPuntuacion re INNER JOIN CAP_SiNoPuntuacion ca ON re.FK_IDPregunta = ca.ID WHERE re.FK_IDCurso = dg.FK_IDCurso AND re.FK_CursosHeader = dg.FK_CursosHeader) as Puntos,
                (SELECT SUM(CASE WHEN (CASE WHEN Respuesta = 0 THEN 2 WHEN Respuesta = 1 THEN 1 END) = OpCorrecta THEN Valor END) as Valores FROM CAP_RespuestasSiNoPuntuacion re
                INNER JOIN CAP_SiNoPuntuacion ca ON re.FK_IDPregunta = ca.ID
                WHERE re.FK_IDCurso = dg.FK_IDCurso AND re.FK_CursosHeader = dg.FK_CursosHeader) as Valores,
                cat.Calif10,cat.Calif9,cat.Calif8,cat.Calif7
                FROM CAP_DatosGeneralesCurso dg
                INNER JOIN CAP_RespuestasSiNoPuntuacion re
                ON dg.FK_IDCurso = re.FK_IDCurso
                INNER JOIN CAP_CatalgoCalificaciones cat ON dg.FK_IDCurso = cat.FKCurso
                WHERE dg.FK_TipoCurso = 2;";
    
                $stmt_DatosEvaluacionDia = sqlsrv_query( $conn, $sql_DatosEvaluacionDia);
    
                if( $stmt_DatosEvaluacionDia === false ) {
                    die( print_r( sqlsrv_errors(), true));
                }
    
    
                $tieneDatos=sqlsrv_has_rows($stmt_DatosEvaluacionDia);
                if($tieneDatos){
                    while($valuesDatosEvaluacionDia = sqlsrv_fetch_array($stmt_DatosEvaluacionDia,SQLSRV_FETCH_ASSOC)){
                        $Valores = 0;
                        $Valores = $valuesDatosEvaluacionDia['Valores'];
                        $Valores ? : $Valores = 0;
                        $Calif10 = $valuesDatosEvaluacionDia['Calif10'];
                        $Calif9 = $valuesDatosEvaluacionDia['Calif9'];
                        $Calif8 = $valuesDatosEvaluacionDia['Calif8'];
                        $Calif7 = $valuesDatosEvaluacionDia['Calif7'];
                        $min = $Calif7-1;
                    
                        if($Valores > $min AND $Valores <= $Calif7){
                            $califf = 7;
                        } else if($Valores >= $Calif7 AND $Valores <= $Calif8){
                            $califf = 8;
                        } else if($Valores > $Calif8 AND $Valores <= $Calif9){
                            $califf = 9;
                        } else if($Valores > $Calif9 AND $Valores <= $Calif10){
                            $califf = 10;
                        } else {
                            $califf = 0;
                        }
                        
                        $datos6[] = [
                            'FK_TipoCurso' => $valuesDatosEvaluacionDia['FK_TipoCurso'],
                            'FK_IDCurso' => $valuesDatosEvaluacionDia['FK_IDCurso'],
                            'nameInstructor' => $valuesDatosEvaluacionDia['nameInstructor'],
                            'FK_Instructor' => $valuesDatosEvaluacionDia['FK_Instructor'],
                            'FK_Becario' => $valuesDatosEvaluacionDia['FK_Becario'],
                            'nameBecario' => $valuesDatosEvaluacionDia['nameBecario'],
                            'apto' => $valuesDatosEvaluacionDia['apto'],
                            'fecha' => $valuesDatosEvaluacionDia['fecha'],
                            'observaciones' => $valuesDatosEvaluacionDia['observaciones'],
                            'Puntos' => $valuesDatosEvaluacionDia['Puntos'],
                            'Valores' => $valuesDatosEvaluacionDia['Valores'],
                            'califf' => $califf
                        ];
                    }
    
                    $json_data6 = json_encode($datos6, JSON_UNESCAPED_UNICODE);
    
                    $nombre_fichero = './../../JSONS/capacitacion/DatosEvaluacionD_'.$empresa.'.json';
    
                    if (file_exists($nombre_fichero)) {
                        file_put_contents('./../../JSONS/capacitacion/DatosEvaluacionD_'.$empresa.'.json', $json_data6);
                    } else {
                        $archivo = fopen('./../../JSONS/capacitacion/DatosEvaluacionD_'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
                        if( $archivo == false ){
                        }else{
                            file_put_contents('./../../JSONS/capacitacion/DatosEvaluacionD_'.$empresa.'.json', $json_data6);
                            fclose($archivo);
                        }
                    }
                }
    
                //*====     BecariosVsCrusos     ========================================================
        
                $sql_BecariosCursos = "SELECT bc.FK_Becario, cc.FK_TipoCurso, bc.aprobado,bc.realizado, cc.ID, cc.NombreCurso, cc.Diario, cc.Certificadora, cc.Costo, cc.Costo FROM CAP_BecariosVsCursos bc INNER JOIN CAP_Cursos cc ON bc.FK_curso = cc.ID;";
                
                $stmt_BecariosCursos = sqlsrv_query( $conn, $sql_BecariosCursos);
                
                if( $stmt_BecariosCursos === false ) {
                    die( print_r( sqlsrv_errors(), true));
                }
    
                $tieneDatos=sqlsrv_has_rows($stmt_BecariosCursos);
                if($tieneDatos){
                    while($values_BecariosCursos = sqlsrv_fetch_array($stmt_BecariosCursos,SQLSRV_FETCH_ASSOC)){
                        $datos9[] = $values_BecariosCursos;
                    }
    
                    $json_datos9 = json_encode($datos9, JSON_UNESCAPED_UNICODE);
    
                    $nombre_fichero = './../../JSONS/capacitacion/BecariosCursos_'.$empresa.'.json';
    
                    if (file_exists($nombre_fichero)) {
                        file_put_contents('./../../JSONS/capacitacion/BecariosCursos_'.$empresa.'.json', $json_datos9);
                    } else {
                        $archivo = fopen('./../../JSONS/capacitacion/BecariosCursos_'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
                        if( $archivo == false ){
                        }else{
                            file_put_contents('./../../JSONS/capacitacion/BecariosCursos_'.$empresa.'.json', $json_datos9);
                            fclose($archivo);
                        }
                    }
                }
    
                //*====     BecariosVsInstructor     ========================================================
                $sql_datosG1 = "SELECT FK_Becario FROM CAP_BecariosVsInstructores";
        
                $stmt_datosG1 = sqlsrv_query( $conn, $sql_datosG1);
    
                $sql_errors = sqlsrv_errors( SQLSRV_ERR_ALL );
                if( $stmt_datosG1 ){
                    $num_row = sqlsrv_has_rows( $stmt_datosG1 );
                    if ( $num_row ) {                
                        while( $row1 = sqlsrv_fetch_array( $stmt_datosG1 ,SQLSRV_FETCH_ASSOC ) ){ 
                            $FK_Becario = $row1['FK_Becario'];
                            $sql_BecariosVsInstructor = "SELECT DISTINCT vs.FK_Becario as ID, vs.FK_Becario as FKPersonalBecario, convert(varchar, vs.fecha, 105) as fecha,
                            (SELECT (SELECT COUNT(FK_Becario) FROM CAP_BecariosVsCursos WHERE FK_Becario = {$FK_Becario} AND realizado = 1)*100 / COUNT(estatus) FROM CAP_BecariosVsCursos WHERE FK_Becario = {$FK_Becario}) as promedioAvance, 
                            (SELECT COUNT(FK_Becario) FROM CAP_BecariosVsCursos WHERE FK_Becario = {$FK_Becario}) as cursosAsignados,
                            (SELECT COUNT(FK_Becario) FROM CAP_BecariosVsCursos WHERE FK_Becario = {$FK_Becario} AND realizado = 1) as cursosTerminados,
                            per.clave_pers as claveBecario,
                            CONCAT(per.nombre_pers ,' ',per.apellidop_pers,' ' ,per.apellidom_pers) as nameBecario,pe.ID_personal as FKPersonalInstructor,
                            pe.clave_pers as claveInstructor, CONCAT(pe.nombre_pers ,' ',pe.apellidop_pers,' ' ,pe.apellidom_pers) as nameInstructor FROM CAP_BecariosVsCursos vs 
                            INNER JOIN Personal per ON vs.FK_Becario = per.ID_personal  INNER JOIN CAP_BecariosVsInstructores cap ON cap.FK_Becario = vs.FK_Becario INNER JOIN Personal pe ON cap.FK_Instructor = pe.ID_personal 
                            WHERE vs.FK_Becario = {$FK_Becario};";
                        
                            $stmt_BecariosVsInstructor = sqlsrv_query( $conn, $sql_BecariosVsInstructor);
                            
                            if( $stmt_BecariosVsInstructor === false ) {
                                die( print_r( sqlsrv_errors(), true));
                            }
    
                            
                            $tieneDatos=sqlsrv_has_rows($stmt_BecariosVsInstructor);
                            if($tieneDatos){
                                $cont = 0;
                                while($valuesBecariosVsInstructor = sqlsrv_fetch_array($stmt_BecariosVsInstructor,SQLSRV_FETCH_ASSOC)){
                                    $cont++;
                                    $valuesBecariosVsInstructor['claveBecario'] ? $claveBecario = $valuesBecariosVsInstructor['claveBecario'] : $claveBecario = 'T99'.$cont;
                                    $datos3[] = [
                                        'ID' => $valuesBecariosVsInstructor['ID'],
                                        'FKPersonalBecario' => $valuesBecariosVsInstructor['FKPersonalBecario'],
                                        'claveBecario' => $claveBecario,
                                        'nameBecario' => $valuesBecariosVsInstructor['nameBecario'],
                                        'FKPersonalInstructor' => $valuesBecariosVsInstructor['FKPersonalInstructor'],
                                        'claveInstructor' => $valuesBecariosVsInstructor['claveInstructor'],
                                        'nameInstructor' => $valuesBecariosVsInstructor['nameInstructor'],
                                        'cursosAsignados' => $valuesBecariosVsInstructor['cursosAsignados'],
                                        'cursosTerminados' => $valuesBecariosVsInstructor['cursosTerminados'],
                                        'promedioAvance' => $valuesBecariosVsInstructor['promedioAvance'],
                                        'fecha' => $valuesBecariosVsInstructor['fecha']
                                    ];
                                }
    
                                $json_data3 = json_encode($datos3, JSON_UNESCAPED_UNICODE);
    
                                $nombre_fichero = './../../JSONS/capacitacion/BecariosVsInstructor_'.$empresa.'.json';
    
                                if (file_exists($nombre_fichero)) {
                                    file_put_contents('./../../JSONS/capacitacion/BecariosVsInstructor_'.$empresa.'.json', $json_data3);
                                } else {
                                    $archivo = fopen('./../../JSONS/capacitacion/BecariosVsInstructor_'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
                                    if( $archivo == false ){
                                    }else{
                                        file_put_contents('./../../JSONS/capacitacion/BecariosVsInstructor_'.$empresa.'.json', $json_data3);
                                        fclose($archivo);
                                    }
                                }
                            }
                        }
                    }
                }
            } else if($_GET['paso'] == 2){
                //*====     PROSPECTOS(ID,Nombre,Numero,Edad)     ========================================================
    
                $sql_personal = "SELECT P.ID_Personal as ID_personal, CONCAT(P.nombre_pers,' ',P.apellidop_pers,' ' ,P.apellidom_pers ) as Nombre,AT.ID_atraccion_talento as ID_AT, DATEDIFF(YEAR, P.fecha_nacimiento_pers, GETDATE()) as edad, AT.tipo_proceso_at FROM Personal P INNER JOIN AT_ATRACCION_TALENTO AT ON P.ID_personal=AT.FK_personal_at WHERE AT.REF_estatus_at='CANDIDATO' AND (AT.puesto_sugerido_at='OPERADOR' OR AT.puesto_sugerido_at='OPERADOR EN ENTRENAMIENTO');";
            
                $stmt_personal = sqlsrv_query( $conn, $sql_personal);
                
                if( $stmt_personal === false ) {
                    die( print_r( sqlsrv_errors(), true));
                }
    
                while($valuesPersonal = sqlsrv_fetch_array($stmt_personal,SQLSRV_FETCH_ASSOC)){
                    $valuesPersonal['edad'] ? $edad = $valuesPersonal['edad']: $edad = '';
                    $datos2[] = [          
                        'ID' => $valuesPersonal['ID_personal'],
                        'ID_AT' => $valuesPersonal['ID_AT'],
                        'Nombre' => $valuesPersonal['Nombre'],
                        'numero' => '',
                        'edad' => $edad,
                        'tipo_proceso_at' => $valuesPersonal['tipo_proceso_at']
                    ];   
                }
                echo $json_data2 = json_encode($datos2, JSON_UNESCAPED_UNICODE);
    
                $nombre_fichero = './../../JSONS/capacitacion/Prospectos_'.$empresa.'.json';
    
                if (file_exists($nombre_fichero)) {
                    file_put_contents('./../../JSONS/capacitacion/Prospectos_'.$empresa.'.json', $json_data2);
                } else {
                    $archivo = fopen('./../../JSONS/capacitacion/Prospectos_'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
                    if( $archivo == false ){
                    }else{
                        file_put_contents('./../../JSONS/capacitacion/Prospectos_'.$empresa.'.json', $json_data2);
                        fclose($archivo);
                    }
                }
            }
        } else {
            if($empresa){
                $nombre_fichero2 = './../../JSONS/capacitacion/jsonName'.$empresa.'.json';
    
                if (file_exists($nombre_fichero2)) {
                } else {
                    $data_name = '[{"jsonName": "Cursos_'.$empresa.'.json"},{"jsonName": "CursoCiertoFalso'.$empresa.'.json"},{"jsonName": "Prospectos_'.$empresa.'.json"},
                    {"jsonName": "BecariosVsInstructor_'.$empresa.'.json"},{"jsonName": "CursoSiNoValor'.$empresa.'.json"},{"jsonName": "DatosEvaluacionS_'.$empresa.'.json"}
                    ,{"jsonName": "DatosEvaluacionD_'.$empresa.'.json"},{"jsonName": "PreguntasMultiple_'.$empresa.'.json"},{"jsonName": "RespuestasMultiples_'.$empresa.'.json"},
                    {"jsonName": "BecariosCursos_'.$empresa.'.json"}, {"jsonName": "ViewIncidencias_'.$empresa.'.json"}, {"jsonName": "Calificaciones_'.$empresa.'.json"}]';
    
                    $archivo = fopen('./../../JSONS/capacitacion/jsonName'.$empresa.'.json', "w+b"); //
                    if( $archivo == false ){
                    }else{
                        file_put_contents('./../../JSONS/capacitacion/jsonName'.$empresa.'.json', $data_name);
                        fclose($archivo);
                    }
                }
                //*====     preguntas de crusos CIERTO_FALSO    ========================================================
                $sql_preguntas = "SELECT ct.ID as IDTipoCurso, ct.NombreTipo, cc.ID as IDNombreCurso, cc.Empresa, cc.NombreCurso, cf.ID as IDPregunta, cf.Pregunta, cf.OpCorrecta, cf.texto1, cf.texto2, cc.Costo FROM CAP_TiposCursos ct INNER JOIN  CAP_Cursos cc ON ct.ID = cc.FK_TipoCurso INNER JOIN CAP_CursoCiertoFalso cf ON cc.ID = cf.FK_Cursos WHERE cc.Empresa = 1;";
                
                $stmt_preguntas = sqlsrv_query( $conn, $sql_preguntas);
                
                if( $stmt_preguntas === false ) {
                    die( print_r( sqlsrv_errors(), true));
                }
    
                while($valuesPreguntas = sqlsrv_fetch_array($stmt_preguntas,SQLSRV_FETCH_ASSOC)){
    
                    $datos[] = [          
                        'IDTipoCurso' => $valuesPreguntas['IDTipoCurso'],
                        'NombreTipo' => $valuesPreguntas['NombreTipo'],
                        'IDNombreCurso' => $valuesPreguntas['IDNombreCurso'],
                        'Empresa' => $valuesPreguntas['Empresa'],
                        'NombreCurso' => $valuesPreguntas['NombreCurso'],
                        'IDPregunta' => $valuesPreguntas['IDPregunta'],
                        'Pregunta' => $valuesPreguntas['Pregunta'],
                        'OpCorrecta' => $valuesPreguntas['OpCorrecta'],
                        'texto1' => $valuesPreguntas['texto1'],
                        'texto2' => $valuesPreguntas['texto2'],
                    ];   
                }
                $json_data = json_encode($datos, JSON_UNESCAPED_UNICODE);
    
                $nombre_fichero = './../../JSONS/capacitacion/CursoCiertoFalso'.$empresa.'.json';
    
                if (file_exists($nombre_fichero)) {
                    file_put_contents('./../../JSONS/capacitacion/CursoCiertoFalso'.$empresa.'.json', $json_data);
                } else {
                    $archivo = fopen('./../../JSONS/capacitacion/CursoCiertoFalso'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
                    if( $archivo == false ){
                    }else{
                        file_put_contents('./../../JSONS/capacitacion/CursoCiertoFalso'.$empresa.'.json', $json_data);
                        fclose($archivo);
                    }
                }
                //*====================================================================================================================
                //*====     preguntas de crusos SI NO CON VALOR    ========================================================
                $sql_preguntas4 = "SELECT ct.ID as IDTipoCurso, ct.NombreTipo, cc.ID as IDNombreCurso, cc.Empresa, cc.NombreCurso, cf.ID as IDPregunta, cf.Pregunta, cf.OpCorrecta, cf.Valor, cc.Costo FROM CAP_TiposCursos ct INNER JOIN  CAP_Cursos cc ON ct.ID = cc.FK_TipoCurso  INNER JOIN CAP_SiNoPuntuacion cf ON cc.ID = cf.FK_Cursos  WHERE cc.Empresa = 1;";
                
                $stmt_preguntas4 = sqlsrv_query( $conn, $sql_preguntas4);
                
                if( $stmt_preguntas4 === false ) {
                    die( print_r( sqlsrv_errors(), true));
                }
    
                while($valuesPreguntas4 = sqlsrv_fetch_array($stmt_preguntas4,SQLSRV_FETCH_ASSOC)){
    
                    $datos4[] = [          
                        'IDTipoCurso' => $valuesPreguntas4['IDTipoCurso'],
                        'NombreTipo' => $valuesPreguntas4['NombreTipo'],
                        'IDNombreCurso' => $valuesPreguntas4['IDNombreCurso'],
                        'Empresa' => $valuesPreguntas4['Empresa'],
                        'NombreCurso' => $valuesPreguntas4['NombreCurso'],
                        'IDPregunta' => $valuesPreguntas4['IDPregunta'],
                        'Pregunta' => $valuesPreguntas4['Pregunta'],
                        'OpCorrecta' => $valuesPreguntas4['OpCorrecta'],
                        'Valor' => $valuesPreguntas4['Valor']
                    ];   
                }
                $json_data4 = json_encode($datos4, JSON_UNESCAPED_UNICODE);
    
                $nombre_fichero = './../../JSONS/capacitacion/CursoSiNoValor'.$empresa.'.json';
    
                if (file_exists($nombre_fichero)) {
                    file_put_contents('./../../JSONS/capacitacion/CursoSiNoValor'.$empresa.'.json', $json_data4);
                } else {
                    $archivo = fopen('./../../JSONS/capacitacion/CursoSiNoValor'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
                    if( $archivo == false ){
                    }else{
                        file_put_contents('./../../JSONS/capacitacion/CursoSiNoValor'.$empresa.'.json', $json_data4);
                        fclose($archivo);
                    }
                }
                //*====================================================================================================================
                //*====     Names de crusos     ========================================================
                $sql_cours = "SELECT ct.ID as IDTipoCurso, ct.NombreTipo, cc.ID as IDNombreCurso, cc.Empresa, cc.NombreCurso,ct.Diario, cc.Costo FROM CAP_TiposCursos ct INNER JOIN  CAP_Cursos cc ON ct.ID = cc.FK_TipoCurso WHERE cc.Empresa = 1;";
                
                $stmt_courses = sqlsrv_query( $conn, $sql_cours);
                
                if( $stmt_courses === false ) {
                    die( print_r( sqlsrv_errors(), true));
                }
    
                while($valuesCourses = sqlsrv_fetch_array($stmt_courses,SQLSRV_FETCH_ASSOC)){
    
                    $datos1[] = [          
                        'IDTipoCurso' => $valuesCourses['IDTipoCurso'],
                        'NombreTipo' => $valuesCourses['NombreTipo'],
                        'IDNombreCurso' => $valuesCourses['IDNombreCurso'],
                        'Empresa' => $valuesCourses['Empresa'],
                        'Diario' => $valuesCourses['Diario'],
                        'NombreCurso' => $valuesCourses['NombreCurso']
                    ];   
                }
                $json_data1 = json_encode($datos1, JSON_UNESCAPED_UNICODE);
    
                $nombre_fichero = './../../JSONS/capacitacion/Cursos_'.$empresa.'.json';
    
                if (file_exists($nombre_fichero)) {
                    file_put_contents('./../../JSONS/capacitacion/Cursos_'.$empresa.'.json', $json_data1);
                } else {
                    $archivo = fopen('./../../JSONS/capacitacion/Cursos_'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
                    if( $archivo == false ){
                    }else{
                        file_put_contents('./../../JSONS/capacitacion/Cursos_'.$empresa.'.json', $json_data1);
                        fclose($archivo);
                    }
                }
    
                //*====     PROSPECTOS(ID,Nombre,Numero,Edad)     ========================================================
    
                $sql_personal = "SELECT P.ID_Personal as ID_personal, CONCAT(P.nombre_pers,' ',P.apellidop_pers,' ' ,P.apellidom_pers ) as Nombre,AT.ID_atraccion_talento as ID_AT, DATEDIFF(YEAR, P.fecha_nacimiento_pers, GETDATE()) as edad, AT.tipo_proceso_at FROM Personal P INNER JOIN AT_ATRACCION_TALENTO AT ON P.ID_personal=AT.FK_personal_at WHERE AT.REF_estatus_at='CANDIDATO' AND (AT.puesto_sugerido_at='OPERADOR' OR AT.puesto_sugerido_at='OPERADOR EN ENTRENAMIENTO');";
            
                $stmt_personal = sqlsrv_query( $conn, $sql_personal);
                
                if( $stmt_personal === false ) {
                    die( print_r( sqlsrv_errors(), true));
                }
    
                while($valuesPersonal = sqlsrv_fetch_array($stmt_personal,SQLSRV_FETCH_ASSOC)){
                    $valuesPersonal['edad'] ? $edad = $valuesPersonal['edad']: $edad = '';
                    $datos2[] = [          
                        'ID' => $valuesPersonal['ID_personal'],
                        'ID_AT' => $valuesPersonal['ID_AT'],
                        'Nombre' => $valuesPersonal['Nombre'],
                        'numero' => '',
                        'edad' => $edad,
                        'tipo_proceso_at' => $valuesPersonal['tipo_proceso_at']
                    ];   
                }
                $json_data2 = json_encode($datos2, JSON_UNESCAPED_UNICODE);
    
                $nombre_fichero = './../../JSONS/capacitacion/Prospectos_'.$empresa.'.json';
    
                if (file_exists($nombre_fichero)) {
                    file_put_contents('./../../JSONS/capacitacion/Prospectos_'.$empresa.'.json', $json_data2);
                } else {
                    $archivo = fopen('./../../JSONS/capacitacion/Prospectos_'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
                    if( $archivo == false ){
                    }else{
                        file_put_contents('./../../JSONS/capacitacion/Prospectos_'.$empresa.'.json', $json_data2);
                        fclose($archivo);
                    }
                }
    
                //*====     BecariosVsInstructor     ========================================================
                $sql_datosG1 = "SELECT FK_Becario FROM CAP_BecariosVsInstructores";
    
                $stmt_datosG1 = sqlsrv_query( $conn, $sql_datosG1);
    
                $sql_errors = sqlsrv_errors( SQLSRV_ERR_ALL );
                if( $stmt_datosG1 ){
                    $num_row = sqlsrv_has_rows( $stmt_datosG1 );
                    if ( $num_row ) {                
                        while( $row1 = sqlsrv_fetch_array( $stmt_datosG1 ,SQLSRV_FETCH_ASSOC ) ){ 
                            $FK_Becario = $row1['FK_Becario'];
                            $sql_BecariosVsInstructor = "SELECT DISTINCT vs.FK_Becario as ID, vs.FK_Becario as FKPersonalBecario, convert(varchar, vs.fecha, 105) as fecha,
                            (SELECT (SELECT COUNT(FK_Becario) FROM CAP_BecariosVsCursos WHERE FK_Becario = {$FK_Becario} AND realizado = 1)*100 / COUNT(estatus) FROM CAP_BecariosVsCursos WHERE FK_Becario = {$FK_Becario}) as promedioAvance, 
                            (SELECT COUNT(FK_Becario) FROM CAP_BecariosVsCursos WHERE FK_Becario = {$FK_Becario}) as cursosAsignados,
                            (SELECT COUNT(FK_Becario) FROM CAP_BecariosVsCursos WHERE FK_Becario = {$FK_Becario} AND realizado = 1) as cursosTerminados,
                            per.clave_pers as claveBecario,
                            CONCAT(per.nombre_pers ,' ',per.apellidop_pers,' ' ,per.apellidom_pers) as nameBecario,pe.ID_personal as FKPersonalInstructor,
                            pe.clave_pers as claveInstructor, CONCAT(pe.nombre_pers ,' ',pe.apellidop_pers,' ' ,pe.apellidom_pers) as nameInstructor FROM CAP_BecariosVsCursos vs 
                            INNER JOIN Personal per ON vs.FK_Becario = per.ID_personal  INNER JOIN CAP_BecariosVsInstructores cap ON cap.FK_Becario = vs.FK_Becario INNER JOIN Personal pe ON cap.FK_Instructor = pe.ID_personal 
                            WHERE vs.FK_Becario = {$FK_Becario};";
                        
                            $stmt_BecariosVsInstructor = sqlsrv_query( $conn, $sql_BecariosVsInstructor);
                            
                            if( $stmt_BecariosVsInstructor === false ) {
                                die( print_r( sqlsrv_errors(), true));
                            }
    
                            
                            $tieneDatos=sqlsrv_has_rows($stmt_BecariosVsInstructor);
                            if($tieneDatos){
                                $cont = 0;
                                while($valuesBecariosVsInstructor = sqlsrv_fetch_array($stmt_BecariosVsInstructor,SQLSRV_FETCH_ASSOC)){
                                    $cont++;
                                    $valuesBecariosVsInstructor['claveBecario'] ? $claveBecario = $valuesBecariosVsInstructor['claveBecario'] : $claveBecario = 'T99'.$cont;
                                    $datos3[] = [
                                        'ID' => $valuesBecariosVsInstructor['ID'],
                                        'FKPersonalBecario' => $valuesBecariosVsInstructor['FKPersonalBecario'],
                                        'claveBecario' => $claveBecario,
                                        'nameBecario' => $valuesBecariosVsInstructor['nameBecario'],
                                        'FKPersonalInstructor' => $valuesBecariosVsInstructor['FKPersonalInstructor'],
                                        'claveInstructor' => $valuesBecariosVsInstructor['claveInstructor'],
                                        'nameInstructor' => $valuesBecariosVsInstructor['nameInstructor'],
                                        'cursosAsignados' => $valuesBecariosVsInstructor['cursosAsignados'],
                                        'cursosTerminados' => $valuesBecariosVsInstructor['cursosTerminados'],
                                        'promedioAvance' => $valuesBecariosVsInstructor['promedioAvance'],
                                        'fecha' => $valuesBecariosVsInstructor['fecha']
                                    ];
                                }
    
                                $json_data3 = json_encode($datos3, JSON_UNESCAPED_UNICODE);
    
                                $nombre_fichero = './../../JSONS/capacitacion/BecariosVsInstructor_'.$empresa.'.json';
    
                                if (file_exists($nombre_fichero)) {
                                    file_put_contents('./../../JSONS/capacitacion/BecariosVsInstructor_'.$empresa.'.json', $json_data3);
                                } else {
                                    $archivo = fopen('./../../JSONS/capacitacion/BecariosVsInstructor_'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
                                    if( $archivo == false ){
                                    }else{
                                        file_put_contents('./../../JSONS/capacitacion/BecariosVsInstructor_'.$empresa.'.json', $json_data3);
                                        fclose($archivo);
                                    }
                                }
                            }
                        }
                    }
                }
    
                //*====     DatosEvaluacionSimple     ========================================================
    
                $sql_DatosEvaluacion = "SELECT promedio,FK_TipoCurso,FK_IDCurso,FK_Instructor,FK_Becario,nameInstructor,nameBecario,apto,convert(varchar, fecha, 105) as fecha,observaciones FROM CAP_DatosGeneralesCurso WHERE FK_TipoCurso <> 2;";
            
                $stmt_DatosEvaluacion = sqlsrv_query( $conn, $sql_DatosEvaluacion);
                
                if( $stmt_DatosEvaluacion === false ) {
                    die( print_r( sqlsrv_errors(), true));
                }
    
                
                $tieneDatos=sqlsrv_has_rows($stmt_DatosEvaluacion);
                if($tieneDatos){
                    while($valuesDatosEvaluacion = sqlsrv_fetch_array($stmt_DatosEvaluacion,SQLSRV_FETCH_ASSOC)){
                        $datos5[] = [
                            'FK_TipoCurso' => $valuesDatosEvaluacion['FK_TipoCurso'],
                            'FK_IDCurso' => $valuesDatosEvaluacion['FK_IDCurso'],
                            'FK_Instructor' => $valuesDatosEvaluacion['FK_Instructor'],
                            'nameInstructor' => $valuesDatosEvaluacion['nameInstructor'],
                            'FK_Becario' => $valuesDatosEvaluacion['FK_Becario'],
                            'nameBecario' => $valuesDatosEvaluacion['nameBecario'],
                            'apto' => $valuesDatosEvaluacion['apto'],
                            'fecha' => $valuesDatosEvaluacion['fecha'],
                            'observaciones' => $valuesDatosEvaluacion['observaciones'],
                            'promedio' => $valuesDatosEvaluacion['promedio']
                        ];
                    }
    
                    echo $json_data5 = json_encode($datos5, JSON_UNESCAPED_UNICODE);
    
                    $nombre_fichero = './../../JSONS/capacitacion/DatosEvaluacionS_'.$empresa.'.json';
    
                    if (file_exists($nombre_fichero)) {
                        file_put_contents('./../../JSONS/capacitacion/DatosEvaluacionS_'.$empresa.'.json', $json_data5);
                    } else {
                        $archivo = fopen('./../../JSONS/capacitacion/DatosEvaluacionS_'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
                        if( $archivo == false ){
                        }else{
                            file_put_contents('./../../JSONS/capacitacion/DatosEvaluacionS_'.$empresa.'.json', $json_data5);
                            fclose($archivo);
                        }
                    }
                }
    
                //*====     DatosEvaluacionDiaria     ========================================================
    
                $sql_DatosEvaluacionDia = "SELECT DISTINCT dg.FK_CursosHeader,FK_TipoCurso,dg.FK_IDCurso,nameInstructor,FK_Instructor,FK_Becario,nameBecario,apto,convert(varchar, dg.fecha, 105) as fecha,observaciones, 
                (SELECT COUNT(CASE WHEN (CASE WHEN Respuesta = 0 THEN 2 WHEN Respuesta = 1 THEN 1 END) = OpCorrecta THEN 1 END) FROM CAP_RespuestasSiNoPuntuacion re INNER JOIN CAP_SiNoPuntuacion ca ON re.FK_IDPregunta = ca.ID WHERE re.FK_IDCurso = dg.FK_IDCurso AND re.FK_CursosHeader = dg.FK_CursosHeader) as Puntos,
                (SELECT SUM(CASE WHEN (CASE WHEN Respuesta = 0 THEN 2 WHEN Respuesta = 1 THEN 1 END) = OpCorrecta THEN Valor END) as Valores FROM CAP_RespuestasSiNoPuntuacion re
                INNER JOIN CAP_SiNoPuntuacion ca ON re.FK_IDPregunta = ca.ID
                WHERE re.FK_IDCurso = dg.FK_IDCurso AND re.FK_CursosHeader = dg.FK_CursosHeader) as Valores,
                cat.Calif10,cat.Calif9,cat.Calif8,cat.Calif7
                FROM CAP_DatosGeneralesCurso dg
                INNER JOIN CAP_RespuestasSiNoPuntuacion re
                ON dg.FK_IDCurso = re.FK_IDCurso
                INNER JOIN CAP_CatalgoCalificaciones cat ON dg.FK_IDCurso = cat.FKCurso
                WHERE dg.FK_TipoCurso = 2;";
            
                $stmt_DatosEvaluacionDia = sqlsrv_query( $conn, $sql_DatosEvaluacionDia);
                
                if( $stmt_DatosEvaluacionDia === false ) {
                    die( print_r( sqlsrv_errors(), true));
                }
    
                
                $tieneDatos=sqlsrv_has_rows($stmt_DatosEvaluacionDia);
                if($tieneDatos){
                    while($valuesDatosEvaluacionDia = sqlsrv_fetch_array($stmt_DatosEvaluacionDia,SQLSRV_FETCH_ASSOC)){
                        $Valores = 0;
                        $Valores = $valuesDatosEvaluacionDia['Valores'];
                        $Valores ? : $Valores = 0;
                        $Calif10 = $valuesDatosEvaluacionDia['Calif10'];
                        $Calif9 = $valuesDatosEvaluacionDia['Calif9'];
                        $Calif8 = $valuesDatosEvaluacionDia['Calif8'];
                        $Calif7 = $valuesDatosEvaluacionDia['Calif7'];
                        $min = $Calif7-1;
                       
                        if($Valores > $min AND $Valores <= $Calif7){
                            $califf = 7;
                        } else if($Valores >= $Calif7 AND $Valores <= $Calif8){
                            $califf = 8;
                        } else if($Valores > $Calif8 AND $Valores <= $Calif9){
                            $califf = 9;
                        } else if($Valores > $Calif9 AND $Valores <= $Calif10){
                            $califf = 10;
                        } else {
                            $califf = 0;
                        }
                        
                        $datos6[] = [
                            'FK_TipoCurso' => $valuesDatosEvaluacionDia['FK_TipoCurso'],
                            'FK_IDCurso' => $valuesDatosEvaluacionDia['FK_IDCurso'],
                            'nameInstructor' => $valuesDatosEvaluacionDia['nameInstructor'],
                            'FK_Instructor' => $valuesDatosEvaluacionDia['FK_Instructor'],
                            'FK_Becario' => $valuesDatosEvaluacionDia['FK_Becario'],
                            'nameBecario' => $valuesDatosEvaluacionDia['nameBecario'],
                            'apto' => $valuesDatosEvaluacionDia['apto'],
                            'fecha' => $valuesDatosEvaluacionDia['fecha'],
                            'observaciones' => $valuesDatosEvaluacionDia['observaciones'],
                            'Puntos' => $valuesDatosEvaluacionDia['Puntos'],
                            'Valores' => $valuesDatosEvaluacionDia['Valores'],
                            'califf' => $califf
                        ];
                    }
    
                    $json_data6 = json_encode($datos6, JSON_UNESCAPED_UNICODE);
    
                    $nombre_fichero = './../../JSONS/capacitacion/DatosEvaluacionD_'.$empresa.'.json';
    
                    if (file_exists($nombre_fichero)) {
                        file_put_contents('./../../JSONS/capacitacion/DatosEvaluacionD_'.$empresa.'.json', $json_data6);
                    } else {
                        $archivo = fopen('./../../JSONS/capacitacion/DatosEvaluacionD_'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
                        if( $archivo == false ){
                        }else{
                            file_put_contents('./../../JSONS/capacitacion/DatosEvaluacionD_'.$empresa.'.json', $json_data6);
                            fclose($archivo);
                        }
                    }
                }
                
                //*====     DatosPreguntasMultiples     ========================================================
    
                $sql_CAP_OPMultiple = "SELECT ct.ID as IDTipoCurso, ct.NombreTipo, cc.ID as IDNombreCurso, cc.Empresa, cc.NombreCurso, cf.ID as IDPregunta, cf.Pregunta, cf.OpCorrecta, cf.Justifica, cc.Costo FROM CAP_TiposCursos ct INNER JOIN  CAP_Cursos cc ON ct.ID = cc.FK_TipoCurso  INNER JOIN CAP_OPMultiple cf ON cc.ID = cf.FK_Cursos WHERE cc.Empresa = 1 AND cf.Estatus = 1;";
            
                $stmt_CAP_OPMultiple = sqlsrv_query( $conn, $sql_CAP_OPMultiple);
                
                if( $stmt_CAP_OPMultiple === false ) {
                    die( print_r( sqlsrv_errors(), true));
                }
    
                $tieneDatos=sqlsrv_has_rows($stmt_CAP_OPMultiple);
                if($tieneDatos){
                    while($valuesCAP_OPMultiple = sqlsrv_fetch_array($stmt_CAP_OPMultiple,SQLSRV_FETCH_ASSOC)){
    
                        $datos7[] = [
                            'IDTipoCurso' => $valuesCAP_OPMultiple['IDTipoCurso'],
                            'NombreTipo' => $valuesCAP_OPMultiple['NombreTipo'],
                            'IDNombreCurso' => $valuesCAP_OPMultiple['IDNombreCurso'],
                            'Empresa' => $valuesCAP_OPMultiple['Empresa'],
                            'NombreCurso' => $valuesCAP_OPMultiple['NombreCurso'],
                            'IDPregunta' => $valuesCAP_OPMultiple['IDPregunta'],
                            'Pregunta' => $valuesCAP_OPMultiple['Pregunta'],
                            'OpCorrecta' => $valuesCAP_OPMultiple['OpCorrecta'],
                            'Justifica' => $valuesCAP_OPMultiple['Justifica']
                        ];
                    }
    
                    $json_data7 = json_encode($datos7, JSON_UNESCAPED_UNICODE);
    
                    $nombre_fichero = './../../JSONS/capacitacion/PreguntasMultiple_'.$empresa.'.json';
    
                    if (file_exists($nombre_fichero)) {
                        file_put_contents('./../../JSONS/capacitacion/PreguntasMultiple_'.$empresa.'.json', $json_data7);
                    } else {
                        $archivo = fopen('./../../JSONS/capacitacion/PreguntasMultiple_'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
                        if( $archivo == false ){
                        }else{
                            file_put_contents('./../../JSONS/capacitacion/PreguntasMultiple_'.$empresa.'.json', $json_data7);
                            fclose($archivo);
                        }
                    }
                }
    
                //*====     DatosRespuestasMultiples     ========================================================
    
                $sql_CAP_OPMultipleOpts = "SELECT cc.ID,cc.FK_Pregunta,cc.Opcion,cc.Correcta,cc.FK_IDCurso,ISNULL(ce.Imagen, 'No Img') as Image FROM CAP_OPMultipleOpts cc LEFT JOIN CAP_EvidenciasOpts ce ON cc.ID = ce.FK_Pregunta WHERE cc.Estatus = 1;";
            
                $stmt_CAP_OPMultipleOpts = sqlsrv_query( $conn, $sql_CAP_OPMultipleOpts);
                
                if( $stmt_CAP_OPMultipleOpts === false ) {
                    die( print_r( sqlsrv_errors(), true));
                }
    
                $tieneDatos=sqlsrv_has_rows($stmt_CAP_OPMultipleOpts);
                if($tieneDatos){
                    while($valuesCAP_OPMultipleOpts = sqlsrv_fetch_array($stmt_CAP_OPMultipleOpts,SQLSRV_FETCH_ASSOC)){
                        $datos8[] = $valuesCAP_OPMultipleOpts;
                    }
    
                    $json_data8 = json_encode($datos8, JSON_UNESCAPED_UNICODE);
    
                    $nombre_fichero = './../../JSONS/capacitacion/RespuestasMultiples_'.$empresa.'.json';
    
                    if (file_exists($nombre_fichero)) {
                        file_put_contents('./../../JSONS/capacitacion/RespuestasMultiples_'.$empresa.'.json', $json_data8);
                    } else {
                        $archivo = fopen('./../../JSONS/capacitacion/RespuestasMultiples_'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
                        if( $archivo == false ){
                        }else{
                            file_put_contents('./../../JSONS/capacitacion/RespuestasMultiples_'.$empresa.'.json', $json_data8);
                            fclose($archivo);
                        }
                    }
                }
    
                //*====     BecariosVsCrusos     ========================================================
    
                $sql_BecariosCursos = "SELECT bc.FK_Becario, cc.FK_TipoCurso, bc.aprobado,bc.realizado, cc.ID, cc.NombreCurso, cc.Diario, cc.Certificadora, cc.Costo, cc.Costo FROM CAP_BecariosVsCursos bc INNER JOIN CAP_Cursos cc ON bc.FK_curso = cc.ID;";
            
                $stmt_BecariosCursos = sqlsrv_query( $conn, $sql_BecariosCursos);
                
                if( $stmt_BecariosCursos === false ) {
                    die( print_r( sqlsrv_errors(), true));
                }
    
                $tieneDatos=sqlsrv_has_rows($stmt_BecariosCursos);
                if($tieneDatos){
                    while($values_BecariosCursos = sqlsrv_fetch_array($stmt_BecariosCursos,SQLSRV_FETCH_ASSOC)){
                        $datos9[] = $values_BecariosCursos;
                    }
    
                    $json_datos9 = json_encode($datos9, JSON_UNESCAPED_UNICODE);
    
                    $nombre_fichero = './../../JSONS/capacitacion/BecariosCursos_'.$empresa.'.json';
    
                    if (file_exists($nombre_fichero)) {
                        file_put_contents('./../../JSONS/capacitacion/BecariosCursos_'.$empresa.'.json', $json_datos9);
                    } else {
                        $archivo = fopen('./../../JSONS/capacitacion/BecariosCursos_'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
                        if( $archivo == false ){
                        }else{
                            file_put_contents('./../../JSONS/capacitacion/BecariosCursos_'.$empresa.'.json', $json_datos9);
                            fclose($archivo);
                        }
                    }
                }
    
                //*====     IncidenciasBecarios     ========================================================
    
                $sql_ViewIncidencias = "SELECT ID, FK_Becario ,FK_incidencia ,flag_incidencia ,convert(varchar, i.fecha_programacion, 105) as fecha_programacion, CONCAT(p.nombre_pers ,' ',p.apellidop_pers,' ' ,p.apellidom_pers) as nameBecario, convert(varchar, i.fecha_programacion, 23) as fecha_e FROM CAP_ProgramaIncidencias i INNER JOIN Personal p ON p.ID_personal = i.FK_Becario WHERE Estatus = 1 AND (FK_incidencia = 'IMTES' OR FK_incidencia = 'D' OR  FK_incidencia = 'I' OR FK_incidencia = 'V');";
                // ViewIncidencias
                $stmt_ViewIncidencias = sqlsrv_query( $conn, $sql_ViewIncidencias);
                
                if( $stmt_ViewIncidencias === false ) {
                    die( print_r( sqlsrv_errors(), true));
                }
    
                
                $tieneDatos=sqlsrv_has_rows($stmt_ViewIncidencias);
                if($tieneDatos){
                    while($valuesViewIncidencias = sqlsrv_fetch_array($stmt_ViewIncidencias,SQLSRV_FETCH_ASSOC)){
                        $datos10[] = $valuesViewIncidencias;
                    }
    
                    $json_data10 = json_encode($datos10, JSON_UNESCAPED_UNICODE);
    
                    $nombre_fichero = './../../JSONS/capacitacion/ViewIncidencias_'.$empresa.'.json';
    
                    if (file_exists($nombre_fichero)) {
                        file_put_contents('./../../JSONS/capacitacion/ViewIncidencias_'.$empresa.'.json', $json_data10);
                    } else {
                        $archivo = fopen('./../../JSONS/capacitacion/ViewIncidencias_'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
                        if( $archivo == false ){
                        }else{
                            file_put_contents('./../../JSONS/capacitacion/ViewIncidencias_'.$empresa.'.json', $json_data10);
                            fclose($archivo);
                        }
                    }
                }
    
                //*====     Catalogo calificaciones     ========================================================
    
                $sql_Calificaciones = "SELECT FKCurso ,Calif10 ,Calif9 ,Calif8 ,Calif7 FROM CAP_CatalgoCalificaciones";
                // ViewIncidencias
                $stmt_Calificaciones = sqlsrv_query( $conn, $sql_Calificaciones);
                
                if( $stmt_Calificaciones === false ) {
                    die( print_r( sqlsrv_errors(), true));
                }
    
                
                $tieneDatos=sqlsrv_has_rows($stmt_Calificaciones);
                if($tieneDatos){
                    while($valuesCalificaciones = sqlsrv_fetch_array($stmt_Calificaciones,SQLSRV_FETCH_ASSOC)){
                        $datos11[] = $valuesCalificaciones;
                    }
    
                    $json_data11 = json_encode($datos11, JSON_UNESCAPED_UNICODE);
    
                    $nombre_fichero = './../../JSONS/capacitacion/Calificaciones_'.$empresa.'.json';
    
                    if (file_exists($nombre_fichero)) {
                        file_put_contents('./../../JSONS/capacitacion/Calificaciones_'.$empresa.'.json', $json_data11);
                    } else {
                        $archivo = fopen('./../../JSONS/capacitacion/Calificaciones_'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
                        if( $archivo == false ){
                        }else{
                            file_put_contents('./../../JSONS/capacitacion/Calificaciones_'.$empresa.'.json', $json_data11);
                            fclose($archivo);
                        }
                    }
                }
    
                // sqlsrv_free_stmt( $stmt_personal);
                // sqlsrv_free_stmt( $stmt_BecariosVsInstructor);
                sqlsrv_free_stmt( $stmt_courses);
                sqlsrv_free_stmt( $stmt_preguntas);
    
                sqlsrv_close($conn);
            }
        }
    }
    
?>