<?php

include '../../db.php';

$servicio 		= mysql_real_escape_string($_POST['servicio']);
$precio			= mysql_real_escape_string($_POST['precio']);
$descripcion 	= mysql_real_escape_string($_POST['descripcion']);
$observaciones 	= mysql_real_escape_string($_POST['observaciones']);

if ( mysql_query("INSERT INTO articulos SET articulo='".$servicio."',precio='".$precio."',descripcion='".$descripcion."',observaciones='".$observaciones."' ") ){
	#$ref = mysql_insert_id();
	#echo '[{"oid":'.$ref.',"nombre":"'.$nombre.'"}]';
} else {
	echo "ERROR";
}

?>