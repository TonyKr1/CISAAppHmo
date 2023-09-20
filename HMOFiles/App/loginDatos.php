<?php
require './../conexion/conexion.php';
if($_POST['user']){
    if($_POST['metodo'] == 1){//obtiene el nombre de usuario segun el user
        $usuario = $_POST['user'];
        $parametros=array(
            array($usuario)
        );
    
        $sql_nombre  = "SELECT ID_usuario FROM Usuario WHERE usuario_usua=?";
        $stmt_nombre  = sqlsrv_query( $conn,$sql_nombre,$parametros);
    
        if($stmt_nombre  === false) {
            //die( print_r( sqlsrv_errors(), true));
            echo 0;
        }
        $nombre=sqlsrv_fetch_array($stmt_nombre ,SQLSRV_FETCH_ASSOC);
        $tieneNombre=sqlsrv_has_rows($stmt_nombre);
    
        if($tieneNombre){
            echo $nombre['ID_usuario'];
        }else{
            echo 0;
        }
        sqlsrv_free_stmt($stmt_nombre);
        sqlsrv_close($conn);
    }else if($_POST['metodo'] == 2){//obtiene las empresas ligadas a ese user
        $usuario = $_POST['user'];
        $listaOpciones='';
        $sql_empresaUser = "SELECT t1.FK_empresa_usem, t2.Sigla_intelisis, t2.Empresa, t2.Nombre, t2.ID_Tipo_Unidades FROM UsuarioEmpresa t1 JOIN Empresa t2 ON t1.FK_empresa_usem = t2.ID_empresa WHERE t1.flag_visible_usem = '1' AND t1.FK_usuario_usem = ?";
    
        $paramsUserEmpresa=array(
            array($usuario)
        );
    
        $stmt_empresaUser = sqlsrv_query( $conn, $sql_empresaUser,$paramsUserEmpresa);
    
        if( $stmt_empresaUser === false ) {
            //die( print_r( sqlsrv_errors(), true));
            echo 0;
        }
        
        $tieneAccesos=sqlsrv_has_rows($stmt_empresaUser);
    
        if($tieneAccesos){
                $listaOpciones='<option value="">Selecciona una empresa</option>
                <option value="1">ACHSA</option>
                <option value="11">MIHSA</option>';
                while($empresaUser=sqlsrv_fetch_array($stmt_empresaUser,SQLSRV_FETCH_ASSOC)){
                    $listaOpciones=$listaOpciones.'<option value="'.$empresaUser['FK_empresa_usem'].'">'.$empresaUser['Empresa'].'</option>';
                }
                echo $listaOpciones;
        }else{
            echo 0;
        }
    }else if($_POST['metodo'] == 3){//valida el user y la pass
        $status = true; $err = 0; $code = 0;
        $usuario=$_POST['user'];
        $pwd=$_POST['pwd'];
        $sql_login = " SELECT t2.nombre_modu as nombre_n1_modu, us.ID_usuario, us.usuario_usua, us.contrasena_usua, us.tipo_usua, CONCAT(pe.nombre_pers,' ', pe.apellidop_pers,' ',pe.apellidom_pers) as name
        FROM UsuarioModulo t1
        INNER JOIN Modulo t2 ON t2.ID_modulo = t1.FK_modulo_nivel1_usmo
        INNER JOIN Usuario us ON us.ID_usuario = t1.FK_usuario_usmo
        INNER JOIN Personal pe ON pe.ID_personal = us.FK_pers_usua
        WHERE FK_usuario_usmo = ? AND t2.FK_aplicativo_modu = 5
        ORDER BY t2.titulo_modu ASC";

        $parametros=array(
            array($usuario)
        );

        $stmt_logins = sqlsrv_query( $conn, $sql_login, $parametros);
        $stmt_login = sqlsrv_query( $conn, $sql_login, $parametros);

        if($stmt_logins === false) {
            if( ($errors = sqlsrv_errors() ) != null) {
                foreach( $errors as $error ) {
                    $message = $error[ 'message'];
                }
                $err = 1;
                $code = 1;
            } else {
                $message = "Response invalid";
                $code = 2;
            }
            $status = false;
            $array[] = $message;
        } else {
            $tieneDatos=sqlsrv_has_rows($stmt_login);
            if($tieneDatos){
                $login=sqlsrv_fetch_array($stmt_login,SQLSRV_FETCH_ASSOC);
                if(password_verify($pwd, $login['contrasena_usua'])) {
                    $datosUser[] = array(
                        'ID_usuario' => $login['ID_usuario'],
                        'usuario_usua' => $login['usuario_usua'],
                        'tipo_usua' => $login['tipo_usua'],
                        'name' => $login['name'],
                    );
                    while ($logins = sqlsrv_fetch_array($stmt_logins,SQLSRV_FETCH_ASSOC)){
                        $array[] = array(
                            'nombre_n1_modu' => $logins["nombre_n1_modu"]
                        );
                    }
                }else{
                    $code = 3;
                }
            } else {
                $code = 4;
                $status = false;
                $message = "Response empty";
                $array[] = $message;
            }
        }

        $resultArray = array('status' => $status, 'datosUser' => $datosUser, 'accesos' => $array,  "errors" => $err, "code" => $code);

        $resultJson = json_encode( $resultArray);
        echo $resultJson;
    }
}

?>