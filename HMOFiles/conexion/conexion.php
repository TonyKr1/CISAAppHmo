<?php
$serverName = "192.168.30.5"; //serverName\instanceName
$connectionInfo = array( "Database"=>"CISAWEBAPPSHMODEV", "UID"=>"sa", "PWD"=>"a1b2c3d4*1","CharacterSet" => "UTF-8");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

if( !$conn ) {
     die( print_r( sqlsrv_errors(), true));
}
?>