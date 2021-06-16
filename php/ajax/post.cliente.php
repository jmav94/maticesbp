<?php

include '../../db.php';

$nombre 	= mysql_real_escape_string($_POST['nombre']);
$telefono	= mysql_real_escape_string($_POST['telefono']);
$correo 	= mysql_real_escape_string($_POST['correo']);
$direccion 	= mysql_real_escape_string($_POST['direccion']);

if ( mysql_query("INSERT INTO clientes SET nombre='".$nombre."',telefono='".$telefono."',correo='".$correo."',domicilio='".$direccion."' ") ){
	#$ref = mysql_insert_id();
	#echo '[{"oid":'.$ref.',"nombre":"'.$nombre.'"}]';
} else {
	echo "ERROR";
}

?>