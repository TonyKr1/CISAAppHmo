<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require './../../conexion/conexion.php';
require './../../conexion/conexionAzure.php';

    isset($_GET['empresa']) ? $empresa = $_GET['empresa'] : $empresa = null;
    
    if($empresa){
        $nombre_fichero2 = './../../JSONS/tecnologiasHmo/jsonName'.$empresa.'.json';

        if (file_exists($nombre_fichero2)) {
            //echo "El fichero $nombre_fichero SI existe";
        } else {
            $data_name = '[
    {
        "jsonName": "Unidades_empresa'.$empresa.'.json"
    },
    {
        "jsonName": "datos_check'.$empresa.'.json"
    },
    {
        "jsonName": "fallos.json"
    },
    {
        "jsonName": "personal_'.$empresa.'.json"
    },
    {
        "jsonName": "programa_'.$empresa.'.json"
    }
]';
            //echo "El fichero $nombre_fichero NO existe";
            $archivo = fopen('./../../JSONS/tecnologiasHmo/jsonName'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
            if( $archivo == false ){
                //echo 0;
            }else{
                //echo "El archivo ha sido creado";
                file_put_contents('./../../JSONS/tecnologiasHmo/jsonName'.$empresa.'.json', $data_name);
                fclose($archivo);   // Cerrar el archivo
            }
        }
        //! ///////////////////////////////////// Unidades /////////////////////////////////////
        $array=array();
        $sql_modelos = "SELECT Unidad, ID_unidad_danos as ID FROM [GPOCISAWEBAPPS].[dbo].[UnidadDanos] WHERE FK_unidad_danos_empresa IN(1,11);";
        
        $stmt_modelos = sqlsrv_query( $conn, $sql_modelos);
        
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

        $nombre_fichero = './../../JSONS/tecnologiasHmo/Unidades_empresa'.$empresa.'.json';

        if (file_exists($nombre_fichero)) {
            //echo "El fichero $nombre_fichero SI existe";
            file_put_contents('./../../JSONS/tecnologiasHmo/Unidades_empresa'.$empresa.'.json', $json_data);
        } else {
            //echo "El fichero $nombre_fichero NO existe";
            $archivo = fopen('./../../JSONS/tecnologiasHmo/Unidades_empresa'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
            if( $archivo == false ){
                //echo 0;
            }else{
                //echo "El archivo ha sido creado";
                file_put_contents('./../../JSONS/tecnologiasHmo/Unidades_empresa'.$empresa.'.json', $json_data);
                fclose($archivo);   // Cerrar el archivo
            }
        }

        //! ///////////////////////////////////// CheckList /////////////////////////////////////
        $sql_modelos = "SELECT ID, Pregunta,Multiple,FK_formato,FK_equipo FROM TEC_Revisones WHERE Estatus = 1 ORDER BY ID;";
        
        $stmt_modelos = sqlsrv_query( $conn, $sql_modelos);
        
        if( $stmt_modelos === false ) {
            die( print_r( sqlsrv_errors(), true));
        }

        while($valueschek=sqlsrv_fetch_array($stmt_modelos,SQLSRV_FETCH_ASSOC)){
            $datos1[] = $valueschek;
        }
        //var_dump($datos);

        //echo json_encode($datos1, JSON_UNESCAPED_UNICODE);
        echo $json_data1 = json_encode($datos1, JSON_UNESCAPED_UNICODE);

        $nombre_fichero1 = './../../JSONS/tecnologiasHmo/datos_check'.$empresa.'.json';

        if (file_exists($nombre_fichero1)) {
            //echo "El fichero $nombre_fichero SI existe";
            file_put_contents('./../../JSONS/tecnologiasHmo/datos_check'.$empresa.'.json', $json_data1);
        } else {
            //echo "El fichero $nombre_fichero NO existe";
            $archivo = fopen('./../../JSONS/tecnologiasHmo/datos_check'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
            if( $archivo == false ){
                //echo 0;
            }else{
                //echo "El archivo ha sido creado";
                file_put_contents('./../../JSONS/tecnologiasHmo/datos_check'.$empresa.'.json', $json_data1);
                fclose($archivo);   // Cerrar el archivo
            }
        }
        //! ///////////////////////////////////// danios /////////////////////////////////////
        $sql_modelos1 = "SELECT id_tipo_falla, nombre_tipo_falla, id_tipo_equipo_recaudo FROM TipoFallaRecaudo;";
    
        $stmt_modelos1 = sqlsrv_query( $conn, $sql_modelos1);
        
        if( $stmt_modelos1 === false ) {
            die( print_r( sqlsrv_errors(), true));
        }

        while($valueschek1=sqlsrv_fetch_array($stmt_modelos1,SQLSRV_FETCH_ASSOC)){
            $datos2[] = $valueschek1;
        }
        //var_dump($datos);

        // echo json_encode($datos2, JSON_UNESCAPED_UNICODE);
        $json_data2 = json_encode($datos2, JSON_UNESCAPED_UNICODE);
        
        $nombre_fichero2 = './../../JSONS/tecnologiasHmo/fallos.json';

        if (file_exists($nombre_fichero2)) {
            //echo "El fichero $nombre_fichero SI existe";
            file_put_contents('./../../JSONS/tecnologiasHmo/fallos.json', $json_data2);
        } else {
            //echo "El fichero $nombre_fichero NO existe";
            $archivo = fopen('./../../JSONS/tecnologiasHmo/fallos.json', "w+b");    // Abrir el archivo, creándolo si no existe
            if( $archivo == false ){
                //echo 0;
            }else{
                //echo "El archivo ha sido creado";
                file_put_contents('./../../JSONS/tecnologiasHmo/fallos.json', $json_data2);
                fclose($archivo);   // Cerrar el archivo
            }
        }

        //! ///////////////////////////////////// Operadores /////////////////////////////////////
        $sql_users = "SELECT ID_personal as ID, clave_pers as clave, CONCAT(nombre_pers ,' ',apellidop_pers,' ' ,apellidom_pers) as fullName, CONCAT(clave_pers, ' - ',nombre_pers ,' ',apellidop_pers,' ' ,apellidom_pers) as buscador FROM Personal WHERE estatus_pers = 'ALTA' AND puesto_pers = 'OPERADOR' AND clave_pers IS NOT NULL";

        $stmt_users = sqlsrv_query( $conn, $sql_users);
        
        if( $stmt_users === false ) {
            die( print_r( sqlsrv_errors(), true));
        }

        while($valuesusuarios=sqlsrv_fetch_array($stmt_users,SQLSRV_FETCH_ASSOC)){
        $datos3[] = $valuesusuarios;
        }
        //var_dump($datos);

        // echo json_encode($datos2, JSON_UNESCAPED_UNICODE);
        $json_data3 = json_encode($datos3, JSON_UNESCAPED_UNICODE);
        
        $nombre_fichero3 = './../../JSONS/tecnologiasHmo/personal_'.$empresa.'.json';

        if (file_exists($nombre_fichero3)) {
            //echo "El fichero $nombre_fichero SI existe";
            file_put_contents('./../../JSONS/tecnologiasHmo/personal_'.$empresa.'.json', $json_data3);
        } else {
            //echo "El fichero $nombre_fichero NO existe";
            $archivo = fopen('./../../JSONS/tecnologiasHmo/personal_'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
            if( $archivo == false ){
                //echo 0;
            }else{
                //echo "El archivo ha sido creado";
                file_put_contents('./../../JSONS/tecnologiasHmo/personal_'.$empresa.'.json', $json_data3);
                fclose($archivo);   // Cerrar el archivo
            }
        }

        //! ///////////////////////////////////// UnidadesAzue /////////////////////////////////////
        // $sql_unidadesAzure = "SELECT Id_Unidad AS ID, Economico as Unidad FROM Flota WHERE Id_Empresa IN (1,11)";

        // $stmt_unidadesAzure = sqlsrv_query( $connAzure, $sql_unidadesAzure);
        
        // if( $stmt_unidadesAzure === false ) {
        //     die( print_r( sqlsrv_errors(), true));
        // }

        // while($valuesunidadesAzure = sqlsrv_fetch_array($stmt_unidadesAzure,SQLSRV_FETCH_ASSOC)){
        //     $datos4[] = $valuesunidadesAzure;
        // }
        // //var_dump($datos);

        // // echo json_encode($datos2, JSON_UNESCAPED_UNICODE);
        // echo $json_data4 = json_encode($datos4, JSON_UNESCAPED_UNICODE);
        
        // $nombre_fichero3 = './../../JSONS/tecnologiasHmo/unidades'.$empresa.'.json';

        // if (file_exists($nombre_fichero3)) {
        //     //echo "El fichero $nombre_fichero SI existe";
        //     file_put_contents('./../../JSONS/tecnologiasHmo/personal_'.$empresa.'.json', $json_data3);
        // } else {
        //     //echo "El fichero $nombre_fichero NO existe";
        //     $archivo = fopen('./../../JSONS/tecnologiasHmo/personal_'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
        //     if( $archivo == false ){
        //         //echo 0;
        //     }else{
        //         //echo "El archivo ha sido creado";
        //         file_put_contents('./../../JSONS/tecnologiasHmo/personal_'.$empresa.'.json', $json_data3);
        //         fclose($archivo);   // Cerrar el archivo
        //     }
        // }

        //! ///////////////////////////////////// Programacion /////////////////////////////////////

        $sql_programa = "SELECT ct.id_turno_asignado_gest AS ID, ct.FK_id_unidad as FKUnidad, ct.clave_unidad_gest as Unidad, ct.FK_id_personal as FKOperador, 
        CONCAT(p.nombre_pers ,' ',p.apellidop_pers,' ' ,p.apellidom_pers) as NombreOperador,
        ct.clave_personal_gest as Credencial, convert(varchar, ct.fecha_asignacion_ref_gest, 23) as Fecha, ct.clave_unidad_gest as buscador
        FROM CT_GESTION_TURNOS_ASIGNADOS ct
        INNER JOIN Personal p ON ct.FK_id_personal = p.ID_personal
        WHERE fecha_asignacion_ref_gest = '2023-09-08' AND no_turno_gest = 1 ORDER BY ID";

        $stmt_programa = sqlsrv_query( $conn, $sql_programa);
        
        if( $stmt_programa === false ) {
            die( print_r( sqlsrv_errors(), true));
        }
        
        $values_programa = sqlsrv_fetch_array($stmt_programa,SQLSRV_FETCH_ASSOC);

        if($values_programa){
            while($values_programa = sqlsrv_fetch_array($stmt_programa,SQLSRV_FETCH_ASSOC)){
                $datos5[] = $values_programa;
            }
    
            $json_data5 = json_encode($datos5, JSON_UNESCAPED_UNICODE);
            
            $nombre_fichero5 = './../../JSONS/tecnologiasHmo/programa_'.$empresa.'.json';
    
            if (file_exists($nombre_fichero3)) {
                //echo "El fichero $nombre_fichero SI existe";
                file_put_contents('./../../JSONS/tecnologiasHmo/programa_'.$empresa.'.json', $json_data5);
            } else {
                //echo "El fichero $nombre_fichero NO existe";
                $archivo = fopen('./../../JSONS/tecnologiasHmo/programa_'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
                if( $archivo == false ){
                    //echo 0;
                }else{
                    //echo "El archivo ha sido creado";
                    file_put_contents('./../../JSONS/tecnologiasHmo/programa_'.$empresa.'.json', $json_data5);
                    fclose($archivo);   // Cerrar el archivo
                }
            }
        }

        sqlsrv_free_stmt( $stmt_modelos);
        sqlsrv_close($conn);
        sqlsrv_close($connAzure);
    } 
?>