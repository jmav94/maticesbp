<section class="panel panel-default pos-rlt clearfix">

	<header class="panel-heading"> <i class="fa fa-shopping-cart"></i> Servicios</header>
	
	<div class="row wrapper">
		<div class="col-xs-12 col-md-6 m-b-xs">
			<a href="admin.php?m=serviciosAgregar" class="btn btn-sm btn-success m-r"><i class="fa fa-plus"></i> Nuevo Servicio</a>
		</div>
		<div class="col-xs-12 col-md-6 m-b-xs">
			<form action="" id="buscarCliente" method="get">
				<div class="input-group">
					<input type="hidden" name="m" value="servicios">
					<input type="text" class="input-sm form-control" name="buscar" placeholder="Buscar por nombre o folio">
					<span class="input-group-btn"> <button class="btn btn-sm btn-default" id="buscar" type="submit"> <i class="fa fa-search"></i> </button> </span>
				</div>
			</form>
		</div>
	</div>

<?php
if ( isset($_POST['idservicio']) ){
	$idservicio 	= mysql_real_escape_string($_POST['idservicio']);
	$cantidad 	= mysql_real_escape_string($_POST['cantidad']);
	$metodo 	= mysql_real_escape_string($_POST['metodo']);
	$comentario = mysql_real_escape_string($_POST['comentario']);
	$fecha 	  	= date("Y-m-d");
	$hora 	  	= date("H:i:s");

	mysql_query("INSERT INTO servicios_pagos SET idservicio='".$idservicio."',fecha='".$fecha."',hora='".$hora."',cantidad='".$cantidad."',comentario='".$comentario."',metodo='".$metodo."'");

	echo '<div class="col-md-12">
		<div class="alert alert-success">
			<strong> <i class="fa fa-check"></i> Pago agregado correctamente.</strong>
		</div>
	</div>';
}
?>

	<div class="table-responsive">
		<table class="table table-striped b-t b-light">
			<thead>
				<tr>
					<th width="80" class="text-center"> # </th>
					<th width="100"> Fecha </th>
					<th width="80"> Hora </th>
					<th>Cliente</th>
					<th width="140" class="text-right">Total</th>
					<th width="140" class="text-right">Pagado</th>
					<th width="140" class="text-center">Estatus</th>
					<th width="260"> </th>
				</tr>

			<tbody>

<?php
			if ( isset($_GET['borrar']) ){
				# CUANDO SE BORRA LA VENTA SE SUMA LA CANTIDAD DE ARTICULOS
				$borrar = mysql_real_escape_string($_GET['borrar']);

				mysql_query("DELETE FROM servicios WHERE idservicios='".$borrar."'");
				mysql_query("DELETE FROM servicios_pagos WHERE idservicio='".$borrar."'");

				$query = mysql_query("SELECT * FROM servicios_articulos WHERE idservicio='".$borrar."'");
				while($q = mysql_fetch_object($query)){
					mysql_query("UPDATE articulos SET stock=stock+".$q->cantidad." WHERE idarticulos='".$q->idarticulo."'");
				}
				mysql_query("DELETE FROM servicios_articulos WHERE idservicio='".$borrar."'");
			}

			if ( isset($_GET['buscar']) ){
				$buscar = mysql_real_escape_string($_GET['buscar']);

				$query = "SELECT 
					servicios.idservicios,
					servicios.fecha,
					servicios.hora,
					servicios.descuento,
					clientes.nombre,
					(SELECT SUM(total) FROM servicios_articulos WHERE idservicio=servicios.idservicios) as total,
					(SELECT SUM(cantidad) FROM servicios_pagos WHERE idservicio=servicios.idservicios) as pagos
					FROM servicios 
					LEFT JOIN clientes ON clientes.idclientes=servicios.idcliente
					WHERE servicios.idservicios LIKE '%".$buscar."%' OR clientes.nombre LIKE '%".$buscar."%'
					ORDER BY servicios.idservicios DESC";
				$url = "admin.php?m=servicios&buscar=".$buscar;
			} else {
				$query = "SELECT 
					servicios.idservicios,
					servicios.fecha,
					servicios.hora,
					servicios.descuento,
					clientes.nombre,
					(SELECT SUM(total) FROM servicios_articulos WHERE idservicio=servicios.idservicios) as total,
					(SELECT SUM(cantidad) FROM servicios_pagos WHERE idservicio=servicios.idservicios) as pagos
					FROM servicios 
					LEFT JOIN clientes ON clientes.idclientes=servicios.idcliente
					GROUP BY servicios.idservicios
					ORDER BY servicios.idservicios DESC";
				$url = "admin.php?m=pventa";
				
			}

			

##### PAGINADOR #####
$rows_per_page = 30;

if(isset($_GET['pag']))
	$page = mysql_real_escape_string($_GET['pag']);
else if (@$_GET['pag'] == "0")
	$page = 1;
else 
	$page = 1;

$num_rows 		= mysql_num_rows(mysql_query($query));
$lastpage		= ceil($num_rows / $rows_per_page);    		
$page     = (int)$page;
if($page > $lastpage){
	$page = $lastpage;
}
if($page < 1){
	$page = 1;
}
$limit 		= 'LIMIT '. ($page -1) * $rows_per_page . ',' .$rows_per_page;
$query  .=" $limit";

$query = mysql_query($query) or die(mysql_error());
##### PAGINADOR #####

			while($q = mysql_fetch_object($query)){

				$descuento = ($q->descuento / 100) * $q->total;
				$total = ($q->total - $descuento);
				
				echo '<tr>
					<td class="text-center">'.$q->idservicios.'</td>
					<td>'.$q->fecha.'</td>
					<td>'.$q->hora.'</td>
					<td>'.$q->nombre.'</td>
					<td class="text-right">$ '.$total.' pesos </td>
					<td class="text-right">$ '.$q->pagos.' pesos</td>
					<td class="text-center">';
					if ($q->pagos >= $total){
						echo '<label class="label label-success"> liquidado</label>';
					} else {
						echo '<label class="label label-warning"> pendiente</label>';
					}
					echo '</td>
					<td class="text-right">
						<a href="#" data-id="'.$q->idservicios.'" class="agregarPago btn btn-sm btn-success"> <i class="fa fa-usd"></i> </a> &nbsp;
						<a href="admin.php?m=serviciosVer&id='.$q->idservicios.'" class="btn btn-sm btn-info"> <i class="fa fa-archive"></i> </a> &nbsp;
						<a href="#" data-id="'.$q->idservicios.'" class="btn btn-sm btn-default print"> <i class="fa fa-print"></i> </a> &nbsp;
						<a href="admin.php?m=serviciosEditar&id='.$q->idservicios.'" class="btn btn-sm btn-default"> <i class="fa fa-pencil"></i> </a> &nbsp;
						<a href="admin.php?m=servicios&borrar='.$q->idservicios.'" class="btn btn-sm btn-danger"> <i class="fa fa-times"></i> </a>
					</td>
				</tr>';
			}
?>
		    </tbody>
			</thead>
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

<div class="modal fade" id="modal-pagos">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<h3 class="m-t-none m-b">Agregar pago</h3>
						<form role="form" action="" method="post">
							<input type="hidden" name="idservicio" id="idservicio" value="" >
							<div class="form-group">
								<div class="row">
									<label class="col-md-6 control-label"><strong>Metodo de Pago</strong></label>
									<div class="col-md-6">
										<select name="metodo" class="form-control">
											<option>Efectivo</option>
											<option>Tarjeta Debido/Credito</option>
										</select>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<label class="col-md-6 control-label"><strong>Pago</strong></label>
									<div class="col-md-6"><input type="text" class="form-control" name="cantidad" value="0" ></div>
								</div>
							</div>
							<div class="form-group">
								<label><strong>Comentarios</strong></label>
								<textarea class="form-control" name="comentario" style="height:150px;"></textarea>
							</div>
							<div class="checkbox m-t-lg">
								<a class="btn btn-md btn-default m-t-n-xs" id="cancelar"> <i class="fa fa-times"></i> <strong>Cancelar</strong></a>
								<button type="submit" class="btn btn-md pull-right btn-success m-t-n-xs"> <i class="fa fa-check"></i> <strong>Agregar pago</strong></button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>

<script type="text/javascript">
$(function(){

	$(".agregarPago").click(function(){
		$("#idservicio").val($(this).data("id"))
		$("#modal-pagos").modal("show");
	});

	$("#cancelar").click(function(){
		$("#modal-pagos").modal("hide");
	});
	
	$(".print").click(function(){
		var id = $(this).data("id");

		var popupWin = window.open("printS.php?id="+id, '_blank', 'width=1024,height=800,scrollbars=no,menubar=no,toolbar=no,location=no,status=no,titlebar=no,top=50');
		popupWin.window.focus();
        //window.open('../print.php', '_blank', 'width=1024,height=800,scrollbars=no,menubar=no,toolbar=no,location=no,status=no,titlebar=no,top=50');
       	
       	//popupWin.document.write('<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>ORDER DE TRABAJO</title><link href="css/bootstrap.min.css" rel="stylesheet"><link href="font-awesome/css/font-awesome.css" rel="stylesheet"><link href="css/animate.css" rel="stylesheet"><link href="css/style.css" rel="stylesheet">' +
        //        '<style type="text/css">.fuentechica{font-size:10px;}.top{margin-top:5px;}.under{border-bottom:1px solid #DDDDDD;padding-bottom: 3px;}</style>' +
        //        '</head><body class="white-bg" onload="window.print(); window.close();">' + printContents + '</html>');

        //
	});

});
</script>