<?php

include '../../db.php';

$id 	= mysql_real_escape_string($_GET['q']);

$query 	= mysql_query("SELECT * FROM articulos WHERE idarticulos='".$id."' LIMIT 1") or die(mysql_error());

#echo '<option></option>';
while($q = mysql_fetch_object($query)){

	$row['idarticulo'] 	= (int)$q->idarticulos;
	$row['articulo']	= htmlentities(stripslashes($q->articulo." - ".$q->descripcion));
	$row['precio']		= htmlentities(stripslashes($q->precio));

	$row_set[] 		= $row;

}

echo json_encode($row_set);

?>