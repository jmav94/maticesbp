<?php

include '../../db.php';

$id 	= mysql_real_escape_string($_GET['q']);

$query 	= mysql_query("SELECT * FROM clientes WHERE nombre LIKE '%".$id."%' OR telefono LIKE '%".$id."%'  ORDER BY nombre ASC") or die(mysql_error());

#echo '<option></option>';
while($q = mysql_fetch_object($query)){
	#echo '<option value="'.$q->idramos.'">'.$q->numramo.' - '.$q->descripcion.'</option>';

	$row['id'] 		= (int)$q->idclientes;
	$row['text']	= htmlentities(stripslashes($q->nombre));
	$row['text']	= htmlentities(stripslashes($q->telefono));
	$row_set[] 		= $row;

}
echo json_encode($row_set);

?>