<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/Hermosillo');
$Fecha = date('Y-m-d');
//$Fecha = "2023-09-15";

require './../../conexion/conexion.php';
require './../../conexion/conexionAzure.php';

    isset($_GET['empresa']) ? $empresa = $_GET['empresa'] : $empresa = null;
    
    if($empresa){
        $nombre_fichero2 = './../../JSONS/Relevos/jsonName'.$empresa.'.json';

        if (file_exists($nombre_fichero2)) {
            //echo "El fichero $nombre_fichero SI existe";
        } else {
            $data_name = '[
    {
        "jsonName": "Unidades_empresa'.$empresa.'.json"
    },
    {
        "jsonName": "personal_'.$empresa.'.json"
    },
    {
        "jsonName": "programa_'.$empresa.'.json"
    }
]';
            //echo "El fichero $nombre_fichero NO existe";
            $archivo = fopen('./../../JSONS/Relevos/jsonName'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
            if( $archivo == false ){
                //echo 0;
            }else{
                //echo "El archivo ha sido creado";
                file_put_contents('./../../JSONS/Relevos/jsonName'.$empresa.'.json', $data_name);
                fclose($archivo);   // Cerrar el archivo
            }
        }
        //! ///////////////////////////////////// Unidades AZURE /////////////////////////////////////
        $array=array();
        $sql_modelos = "SELECT Id_Unidad AS ID, Economico as Unidad FROM Flota WHERE Id_Empresa IN (1,11)";
        
        $stmt_modelos = sqlsrv_query( $connAzure, $sql_modelos);
        
        if( $stmt_modelos === false ) {
            echo 1;
            die( print_r( sqlsrv_errors(), true));
        }

        $connt=0;

        while($valueschek=sqlsrv_fetch_array($stmt_modelos,SQLSRV_FETCH_ASSOC)){
            $datos[] = $valueschek;
        }
        //var_dump($datos);

        json_encode($datos, JSON_UNESCAPED_UNICODE);
        $json_data = json_encode($datos, JSON_UNESCAPED_UNICODE);

        $nombre_fichero = './../../JSONS/Relevos/Unidades_empresa'.$empresa.'.json';

        if (file_exists($nombre_fichero)) {
            //echo "El fichero $nombre_fichero SI existe";
            file_put_contents('./../../JSONS/Relevos/Unidades_empresa'.$empresa.'.json', $json_data);
        } else {
            //echo "El fichero $nombre_fichero NO existe";
            $archivo = fopen('./../../JSONS/Relevos/Unidades_empresa'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
            if( $archivo == false ){
                //echo 0;
            }else{
                //echo "El archivo ha sido creado";
                file_put_contents('./../../JSONS/Relevos/Unidades_empresa'.$empresa.'.json', $json_data);
                fclose($archivo);   // Cerrar el archivo
            }
        }
        
        //! ///////////////////////////////////// Operadores /////////////////////////////////////
        $sql_users = "SELECT licen.ID_licencias AS ID, licen.qr_lcnc AS QR, licen.estatus_lcnc as Estatus, convert(varchar, licen.vigencia_lcnc, 23) as FechaVigencia,
        CONCAT(pers.nombre_pers, ' ' , pers.apellidop_pers , ' ' , pers.apellidom_pers) AS fullName,
		DATEDIFF(DAY,GETDATE(),licen.vigencia_lcnc) as dias,
        pers.ID_personal,  pers.clave_pers, pers.puesto_pers , pers.estatus_pers , est_ope.estatus_cotu_esop as EstatusOperador, est_ope.clave_empleado_cotu_esop as claveEmpleado
        FROM Licencias licen 
        JOIN Personal pers ON licen.FK_personal_lcnc = pers.ID_personal
        JOIN CT_EstatusOperacion est_ope ON est_ope.FK_personal_cotu_esop = pers.ID_personal
        WHERE pers.estatus_pers = 'ALTA';";

        $stmt_users = sqlsrv_query( $conn, $sql_users);
        
        if( $stmt_users === false ) {
            die( print_r( sqlsrv_errors(), true));
        }

        while($valuesusuarios=sqlsrv_fetch_array($stmt_users,SQLSRV_FETCH_ASSOC)){
        $datos3[] = $valuesusuarios;
        }
        //var_dump($datos);

        // echo json_encode($datos2, JSON_UNESCAPED_UNICODE);
        echo $json_data3 = json_encode($datos3, JSON_UNESCAPED_UNICODE);
        
        $nombre_fichero3 = './../../JSONS/Relevos/personal_'.$empresa.'.json';

        if (file_exists($nombre_fichero3)) {
            //echo "El fichero $nombre_fichero SI existe";
            file_put_contents('./../../JSONS/Relevos/personal_'.$empresa.'.json', $json_data3);
        } else {
            //echo "El fichero $nombre_fichero NO existe";
            $archivo = fopen('./../../JSONS/Relevos/personal_'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
            if( $archivo == false ){
                //echo 0;
            }else{
                //echo "El archivo ha sido creado";
                file_put_contents('./../../JSONS/Relevos/personal_'.$empresa.'.json', $json_data3);
                fclose($archivo);   // Cerrar el archivo
            }
        }

        //! ///////////////////////////////////// Programacion /////////////////////////////////////
        $array=array();

        $sqlCargas = "SELECT id_turno_asignado_gest AS ID ,no_linea_asignada_gest as Linea, no_jornada_gest as Jornada,FK_id_unidad as FKUnidad, clave_unidad_gest as Eco, FK_id_personal as FKPersonal,
        clave_personal_gest as Clave, no_turno_gest as Turno, estatus_asignacion_turno_gest as EstatusTurno, estatus_asignacion_ruta_gest as EstatusRuta, bandera_cierre_manual_gest as FlagCierreManual, 
        convert(varchar, fecha_asignacion_ref_gest, 23) as Fecha, id_turno_referencia as IDRef, estatus_registro_turno_asignado_gest as FlagCierre -- 0 teminado, 1 abierto
        FROM CT_GESTION_TURNOS_ASIGNADOS WHERE fecha_asignacion_ref_gest = '{$Fecha}' ORDER BY id_turno_asignado_gest DESC";
        
        $stmtCargas = sqlsrv_query( $conn, $sqlCargas);
        
        if( $stmtCargas === false ) {
            echo 1;
            die( print_r( sqlsrv_errors(), true));
        }

        $valuesCargas=sqlsrv_fetch_array($stmtCargas,SQLSRV_FETCH_ASSOC);

        if($valuesCargas){
            while($valuesCargas=sqlsrv_fetch_array($stmtCargas,SQLSRV_FETCH_ASSOC)){
                $datos1[] = $valuesCargas;
            }
        } else {
            $datos1 = [];
        }

        $json_data1 = json_encode($datos1, JSON_UNESCAPED_UNICODE);
    
        $nombre_fichero = './../../JSONS/Relevos/programa_'.$empresa.'.json';

        if (file_exists($nombre_fichero)) {
            //echo "El fichero $nombre_fichero SI existe";
            file_put_contents('./../../JSONS/Relevos/programa_'.$empresa.'.json', $json_data1);
        } else {
            //echo "El fichero $nombre_fichero NO existe";
            $archivo = fopen('./../../JSONS/Relevos/programa_'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
            if( $archivo == false ){
                //echo 0;
            }else{
                //echo "El archivo ha sido creado";
                file_put_contents('./../../JSONS/Relevos/programa_'.$empresa.'.json', $json_data1);
                fclose($archivo);   // Cerrar el archivo
            }
        }    

        // id_turno_asignado_gest ,CODIGO_UNIQUE_TURNO ,CODIGO_UNIQUE_TURNO_TERMINO ,ID_INIQUE_REGISTRO_REF_ASIG ,no_linea_asignada_gest ,no_jornada_gest ,FK_id_unidad ,clave_unidad_gest ,FK_id_personal ,clave_personal_gest ,no_turno_gest ,fecha_asignacion_real_gest ,fecha_cierre_real_gest ,hora_entrada_real_gest ,hora_cierre_real_gest ,hora_asignacion_real_gest ,hora_inicio_servicio_real_gest ,estatus_asignacion_turno_gest ,estatus_asignacion_ruta_gest ,bandera_patio_terminojornada_gest ,bandera_cierre_manual_gest ,fecha_asignacion_ref_gest ,fecha_inicio_ref_gest ,fecha_fin_ref_gest ,hora_inicio_servicio_ref_gest ,candado_carga_ref_gest ,estatus_registro_turno_asignado_gest ,id_turno_referencia ,FK_id_reg_op_disp_asig ,FK_id_usuario_registro_gest ,clave_usua_resgistro_gest ,fecha_registro_gest

        sqlsrv_free_stmt( $stmt_modelos);
        sqlsrv_close($conn);
        sqlsrv_close($connAzure);
    } 
?>