<?php

include '../../db.php';

$id 	= mysql_real_escape_string($_GET['id']);

$query 	= mysql_fetch_object(mysql_query("SELECT * FROM servicios_destinatarios WHERE idservicio='".$id."' LIMIT 1"));

$row['e_nombre'] 		= htmlentities(stripslashes($query->nombre));
$row['e_direccion']		= htmlentities(stripslashes($query->direccion));
$row['e_fechaentrega']	= htmlentities(stripslashes($query->fechaentrega));
$row['e_horaentrega'] 	= htmlentities(stripslashes($query->horaentrega));
$row['e_referencia']	= htmlentities(stripslashes($query->referencia));
$row['e_colonia'] 		= htmlentities(stripslashes($query->colonia));
$row['e_codigopostal'] 	= htmlentities(stripslashes($query->codigopostal));
$row['e_mensaje'] 		= htmlentities(stripslashes($query->mensaje));


#$row_set[] 		= $row;

echo json_encode($row);

?>
