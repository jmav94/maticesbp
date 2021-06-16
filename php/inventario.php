<section class="panel panel-default pos-rlt clearfix">

	<header class="panel-heading"> <i class="fa fa-list "></i> Inventario </header>
	
	<div class="row wrapper">
		<div class="col-md-6 m-b-xs">
			<a href="admin.php?m=inventarioAgregar" class="pull-left btn btn-md btn-success"><i class="fa fa-plus"></i> Registrar Articulo </a>
		</div>
		<div class="col-md-6">
			<form action="" method="get">
				<input type="hidden" name="m" value="inventario">
				<div class="input-group">
					<input type="text" class="input-md form-control" name="buscar" placeholder="Buscar"> <span class="input-group-btn"> <button class="btn btn-md btn-default" type="submit"> <i class="fa fa-search"></i> </button> </span>
				</div>
			</form>
		</div>
	</div>

	<div class="table-responsive">
		<table class="table table-striped b-t b-light">
			<thead>
				<tr>
					<th>Articulo</th>
					<th>Descripcion</th>
					<th>Precio</th>
					<th>Stock</th>
					<th width="180"></th>
				</tr>
			</thead>
			<tbody>
<?php

			if ( isset($_GET['del']) ){
				$del = mysql_real_escape_string($_GET['del']);
				mysql_query("DELETE FROM articulos WHERE idarticulos='".$del."'");
				mysql_query("DELETE FROM articulos_componentes WHERE idarticulo='".$del."'");
			}

			if ( isset($_GET['buscar']) ){
				$buscar = mysql_real_escape_string($_GET['buscar']);
				$consulta  = "SELECT * FROM articulos 
					WHERE (articulos.articulo LIKE '%".$buscar."%' OR articulos.descripcion LIKE '%".$buscar."%') 
					ORDER BY articulos.articulo ASC"; 
					$url = "admin.php?m=inventario&buscar=".$buscar;
			} else {
				$consulta  = "SELECT * FROM articulos ORDER BY articulo ASC";
				$url = "admin.php?m=inventario";
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
				
				if ( ($q->alerta > 0) && ($q->stock <= $q->alerta) ){
					$alerta = "<label class='label bg-danger'> bajo stock </label>";
				} else {
					$alerta = "";
				}
?>				
				<tr>
					<td><?php echo $q->articulo;?></td>
					<td><?php echo $q->descripcion;?></td>
					<td><?php echo $q->precio;?></td>
					<td class="text-center"><?php echo $q->stock;?> <?php echo $alerta; ?></td>
					<td class="text-right">
						<a href="admin.php?m=inventarioEditar&id=<?php echo $q->idarticulos; ?>" class="btn btn-sm btn-default"> <i class="fa fa-pencil"></i> </a> &nbsp;
						<a href="admin.php?m=inventario&del=<?php echo $q->idarticulos; ?>" class="btn btn-sm btn-danger"> <i class="fa fa-times"></i> </a>
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
<?php
		if ( isset( $_GET['buscar']) ){
?>
			<div class="col-md-6">
			</div>
			<div class="col-md-6 text-right text-center-xs">
<?php
		} else {
?>
			<div class="col-md-12 text-right text-center-xs">
<?php
		}
?>
			
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