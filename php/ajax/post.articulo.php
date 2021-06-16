<?php

include '../../db.php';

$nombre = mysql_real_escape_string($_POST['articulo_nombre']);
$precio	= mysql_real_escape_string($_POST['articulo_precio']);
$desc   = mysql_real_escape_string($_POST['articulo_descripcion']);

if ( mysql_query("INSERT INTO articulos SET articulo='".$nombre."',precio='".$precio."',descripcion='".$desc."'") ){
	#$ref = mysql_insert_id();
	#echo '[{"oid":'.$ref.',"nombre":"'.$nombre.'"}]';
} else {
	echo "ERROR";
}

?>