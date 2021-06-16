<?php

include '../../db.php';

$id 	= mysql_real_escape_string($_GET['q']);

$query 	= mysql_query("SELECT * FROM articulos WHERE articulo LIKE '%".$id."%' OR descripcion LIKE '%".$id."%' ORDER BY articulo ASC") or die(mysql_error());

#echo '<option></option>';
while($q = mysql_fetch_object($query)){
	#echo '<option value="'.$q->idramos.'">'.$q->numramo.' - '.$q->descripcion.'</option>';

	$row['id'] 		= (int)$q->idarticulos;
	$row['text']	= htmlentities(stripslashes($q->articulo." - ".$q->descripcion));
	$row_set[] 		= $row;

}
echo json_encode($row_set);

?>