<section class="panel panel-default pos-rlt clearfix">

	<header class="panel-heading"> <i class="fa fa-users"></i> Clientes</header>
	
	<div class="row wrapper">
		<div class="col-md-6 m-b-xs">
			<a href="admin.php?m=clientesAgregar" class="btn btn-md btn-success"><i class="fa fa-plus"></i> Agregar Cliente</a>
		</div>
		<div class="col-md-6">
			<form class="" method="get" action="">
				<input type="hidden" name="m" value="clientes">
				<div class="input-group">
					<input type="text" class="input-md form-control" name="buscar" placeholder="Buscar por nombre, correo, telefono"> <span class="input-group-btn"> <button type="submit" class="btn btn-md btn-default"> <i class="fa fa-search"></i> </button> </span>
				</div>
			</form>
		</div>
	</div>

	<div class="table-responsive">
		<table class="table table-striped b-t b-light">
			<thead>
				<tr>
					<th>Nombre</th>
					<th>Direccion</th>
					<th>Telefono</th>
					<th>E-mail</th>
					<th width="220"></th>
				</tr>
			</thead>
			<tbody>

<?php
			if ( isset($_GET['del']) ){
				$del = mysql_real_escape_string($_GET['del']);
				mysql_query("DELETE FROM clientes WHERE idclientes='".$del."'");
			}

			if ( isset($_GET['buscar']) ){
				$buscar = mysql_real_escape_string($_GET['buscar']);
				$consulta  = "SELECT * FROM clientes WHERE 
					(nombre LIKE '%".$buscar."%' OR 
						correo LIKE '%".$buscar."%' OR
						telefono LIKE '%".$buscar."%' OR
						celular LIKE '%".$buscar."%') 
					ORDER BY nombre ASC"; 
					$url = "admin.php?m=clientes&buscar=".$buscar;
			} else {
				$consulta  = "SELECT * FROM clientes ORDER BY nombre ASC";
				$url = "admin.php?m=clientes";
			}

##### PAGINADOR #####
$rows_per_page = 30;

if(isset($_GET['pag']))
	$page = mysql_real_escape_string($_GET['pag']);
else if (@$_GET['pag'] == "0")
	$page = 1;
else 
	$page = 1;

$num_rows 		= mysql_num_rows(mysql_query($consulta));
$lastpage		= ceil($num_rows / $rows_per_page);    		
$page     = (int)$page;
if($page > $lastpage){
	$page = $lastpage;
}
if($page < 1){
	$page = 1;
}
$limit 		= 'LIMIT '. ($page -1) * $rows_per_page . ',' .$rows_per_page;
$consulta  .=" $limit";

$consulta = mysql_query($consulta);
##### PAGINADOR #####

			while($q = mysql_fetch_object($consulta)){ 
?>
					<td><?php echo $q->nombre; ?></td>
					<td><?php echo $q->domicilio; ?></td>
					<td><?php echo $q->telefono; ?></td>
					<td><?php echo $q->correo; ?></td>
					<td class="text-right">
						<a href="admin.php?m=clientePagos&id=<?php echo $q->idclientes; ?>" class="btn btn-sm btn-success"> <i class="fa fa-usd"></i> </a> &nbsp;
						<a href="admin.php?m=clientesEditar&id=<?php echo $q->idclientes; ?>" class="btn btn-sm btn-default"> <i class="fa fa-pencil"></i> </a> &nbsp;
						<a href="admin.php?m=clientes&del=<?php echo $q->idclientes; ?>" class="btn btn-sm btn-danger"> <i class="fa fa-times"></i> </a>					
					</td>
				</tr>
<?php
			}
?>
			</tbody>
		</table>
	</div>


<footer class="panel-footer">
		<div class="row">
			<div class="col-sm-12 text-right text-center-xs">
				<ul class="pagination pagination-sm m-t-none m-b-none">
<?php
	if($num_rows != 0){
		$nextpage = $page + 1;
		$prevpage = $page - 1;

		if ($page == 1) {
			echo '<li class="disabled"><a href="#"><i class="fa fa-chevron-left"></i></a></li>';
			
			echo '<li class="active"><a href="">1</a></li>';
			
			for($i= $page+1; $i<= $lastpage ; $i++){
				echo '<li><a href="'.$url.'&pag='.$i.'">'.$i.'</a></li> ';
			}

			if($lastpage >$page ){
				echo '<li><a href="'.$url.'&pag='.$nextpage.'"><i class="fa fa-chevron-right"></i></a></li>';
			}else{	
				echo '<li class="disabled"><a href="#"><i class="fa fa-chevron-right"></i></a></li>';
			}
		} else {
			echo '<li><a href="'.$url.'&pag='.$prevpage.'"><i class="fa fa-chevron-left"></i></a></li>';
			
			for($i= 1; $i<= $lastpage ; $i++){
				if($page == $i){
					echo '<li class="active"><a href="#">'.$i.'</a></li>';
				} else {
					echo '<li><a href="'.$url.'&pag='.$i.'">'.$i.'</a></li> ';
				}
			}
         
			if($lastpage >$page ){
				echo '<li><a href="'.$url.'&pag='.$nextpage.'"><i class="fa fa-chevron-right"></i></a></li>';
			} else {
				echo '<li class="disabled"><a href="#"><i class="fa fa-chevron-right"></i></a></li>';
			}
		}
	}
?>
				</ul>
			</div>
		</div>
	</footer>
</section>
