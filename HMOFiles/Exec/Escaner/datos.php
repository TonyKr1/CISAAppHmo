<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require './../../conexion/conexion.php';

    isset($_GET['empresa']) ? $empresa = $_GET['empresa'] : $empresa = null;
    
    if($empresa){
        $nombre_fichero2 = './../../JSONS/Escaner/jsonName'.$empresa.'.json';

        if (file_exists($nombre_fichero2)) {
            //echo "El fichero $nombre_fichero SI existe";
        } else {
            $data_name = '[
    {
        "jsonName": "operadores.json"
    }
]';
            //echo "El fichero $nombre_fichero NO existe";
            $archivo = fopen('./../../JSONS/Escaner/jsonName'.$empresa.'.json', "w+b");    // Abrir el archivo, creándolo si no existe
            if( $archivo == false ){
                //echo 0;
            }else{
                //echo "El archivo ha sido creado";
                file_put_contents('./../../JSONS/Escaner/jsonName'.$empresa.'.json', $data_name);
                fclose($archivo);   // Cerrar el archivo
            }
        }
        //! ///////////////////////////////////// Operadores /////////////////////////////////////
        $array=array();
        $sql_modelos = "SELECT Personal, CONCAT(Personal, ' - ', Nombre, ' ', ApellidoPaterno, ' ', ApellidoMaterno) as fullName, CONCAT(Nombre, ' ', ApellidoPaterno, ' ', ApellidoMaterno) as operador FROM vwOperadoresHMO WHERE Personal NOT IN (SELECT clave FROM Licencias WHERE clave is not null);";
        
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
        echo $json_data = json_encode($datos, JSON_UNESCAPED_UNICODE);

        $nombre_fichero = './../../JSONS/Escaner/operadores.json';

        if (file_exists($nombre_fichero)) {
            //echo "El fichero $nombre_fichero SI existe";
            file_put_contents('./../../JSONS/Escaner/operadores.json', $json_data);
        } else {
            //echo "El fichero $nombre_fichero NO existe";
            $archivo = fopen('./../../JSONS/Escaner/operadores.json', "w+b");    // Abrir el archivo, creándolo si no existe
            if( $archivo == false ){
                //echo 0;
            }else{
                //echo "El archivo ha sido creado";
                file_put_contents('./../../JSONS/Escaner/operadores.json', $json_data);
                fclose($archivo);   // Cerrar el archivo
            }
        }

        sqlsrv_free_stmt( $stmt_modelos);
        sqlsrv_close($conn);
    } 
?>