<?php
$serverName = "cisa.database.windows.net"; //serverName\instanceName
$connectionInfo = array( "Database"=>"WebApps", "UID"=>"WebApps", "PWD"=>"Goldennuts1990*","CharacterSet" => "UTF-8");
$connAzure = sqlsrv_connect( $serverName, $connectionInfo);

if( !$connAzure ) {
     die( print_r( sqlsrv_errors(), true));
}

// function getConectaDBAzure(){
//     $serverName = 'cisa.database.windows.net';
//     $serverUser = 'WebApps';
//     $serverPassword = 'Goldennuts1990*';
//     $serverDataBase = 'WebApps';
//     $serverCharacter = 'UTF-8';

//     $conexionInfo = array( "Database" => $serverDataBase, "UID" => $serverUser, "PWD" => $serverPassword, "CharacterSet" => $serverCharacter);
//     $conexion = sqlsrv_connect( $serverName, $conexionInfo);
//     if( $conexion ) {
//          return $conexion;
//     }else{
//          die( print_r( sqlsrv_errors(), true));
//     }
// }
?>