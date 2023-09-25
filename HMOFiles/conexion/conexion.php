<?php
$serverName = "192.168.150.8"; //serverName\instanceName
$connectionInfo = array( "Database"=>"CISAWEBAPPSHMOQA", "UID"=>"sa", "PWD"=>"System123","CharacterSet" => "UTF-8");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

if( !$conn ) {
     die( print_r( sqlsrv_errors(), true));
}
?>