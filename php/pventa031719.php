<section class="panel panel-default pos-rlt clearfix">

	<header class="panel-heading"> <i class="fa fa-shopping-cart"></i> Historial de Ventas</header>

	<div class="row wrapper">
		<div class="col-xs-12 col-md-3 m-b-xs">
			<a href="admin.php?m=pventaAgregar" class="btn btn-md btn-success m-r"><i class="fa fa-plus"></i> Nueva Orden</a>
		</div>
		<div class="col-xs-12 col-md-3">
			<form action="" id="buscarCliente" method="get">
				<div class="input-group">
					<input type="hidden" name="m" value="pventa">
					<select class="form-control" name="estado">
						<?php
								$estado = mysql_real_escape_string($_GET['estado']);
								if ($estado =="Pendiente"){
									echo'<option>Todas</option>
									<option selected="selected">Pendiente</option>
									<option>En Proceso</option>
									<option>En Ruta</option>
									<option>Entregado</option>
									<option>No Entregado</option>';
								}elseif ($estado =="En Proceso") {
									echo'<option>Todas</option>
									<option >Pendiente</option>
									<option selected="selected">En Proceso</option>
									<option>En Ruta</option>
									<option>Entregado</option>
									<option>No Entregado</option>';
								}elseif ($estado == "En Ruta") {
									echo'<option>Todas</option>
									<option >Pendiente</option>
									<option >En Proceso</option>
									<option selected="selected">En Ruta</option>
									<option>Entregado</option>
									<option>No Entregado</option>';
								}elseif ($estado =="Entregado") {
									echo'<option>Todas</option>
									<option >Pendiente</option>
									<option>En Proceso</option>
									<option>En Ruta</option>
									<option  selected="selected">Entregado</option>
									<option>No Entregado</option>';
								}elseif ($estado == "No Entregado") {
									echo'<option>Todas</option>
									<option >Pendiente</option>
									<option >En Proceso</option>
									<option >En Ruta</option>
									<option>Entregado</option>
									<option selected="selected">No Entregado</option>';
								}else {
									echo'<option>Todas</option>
									<option >Pendiente</option>
									<option >En Proceso</option>
									<option >En Ruta</option>
									<option>Entregado</option>
									<option >No Entregado</option>';
								}
						 ?>
					</select>
					<span class="input-group-btn"> <button class="btn btn-md btn-default" id="buscar" type="submit"> <i class="fa fa-search"></i> </button> </span>
				</div>
			<!-- </form> -->
		</div>
		<div class="col-xs-12 col-md-6 m-b-xs">
			<!-- <form action="" id="buscarCliente" method="get"> -->
				<div class="input-group">
					<!-- <input type="hidden" name="m" value="pventa"> -->
					<input type="text" class="input-md form-control" name="buscar" placeholder="Buscar por nombre o folio">
					<span class="input-group-btn"> <button class="btn btn-md btn-default" id="buscar" type="submit"> <i class="fa fa-search"></i> </button> </span>
				</div>
			</form>
		</div>
	</div>

	<?php
	if ( isset($_POST['idventa']) ){
		$idventa 	= mysql_real_escape_string($_POST['idventa']);
		$cantidad 	= mysql_real_escape_string($_POST['cantidad']);
		$metodo 	= mysql_real_escape_string($_POST['metodo']);
		$comentario = mysql_real_escape_string($_POST['comentario']);
		$fecha 	  	= date("Y-m-d");
		$hora 	  	= date("H:m:s");

		mysql_query("INSERT INTO ventas_pagos SET idventa='".$idventa."',fecha='".$fecha."',hora='".$hora."',cantidad='".$cantidad."',comentario='".$comentario."',metodo='".$metodo."'");

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
					<th width="120"> Fecha </th>
					<th width="80"> Hora </th>
					<th>Cliente</th>
					<th>Destinatario</th>
					<th>Direccion</th>
					<th>Referencia</th>
					<th width="140" class="text-right">Total</th>
					<th width="140" class="text-right">Pagado</th>
					<th width="140" class="text-center">Estatus</th>
					<th width="140" class="text-center">Pedido</th>
					<th width="120" class="text-center">F. Entrega</th>
					<th width="238"> </th>
				</tr>
			</thead>
			<tbody>

				<?php
				if ( isset($_GET['borrar']) ){
					# CUANDO SE BORRA LA VENTA SE SUMA LA CANTIDAD DE ARTICULOS
					$borrar = mysql_real_escape_string($_GET['borrar']);

					mysql_query("DELETE FROM ventas WHERE idventas='".$borrar."'");
					mysql_query("DELETE FROM ventas_pagos WHERE idventa='".$borrar."'");

					$query = mysql_query("SELECT * FROM ventas_articulos WHERE idventa='".$borrar."'");
					while($q = mysql_fetch_object($query)){
						mysql_query("UPDATE articulos SET stock=stock+".$q->cantidad." WHERE idarticulos='".$q->idarticulo."'");
					}
					mysql_query("DELETE FROM ventas_articulos WHERE idventa='".$borrar."'");
				}

				if ( isset($_GET['estado']) ) {
					$estado = mysql_real_escape_string($_GET['estado']);
					$buscar = mysql_real_escape_string($_GET['buscar']);
					if ($buscar != "" ) {
							//echo "ENTRA EN ESTADO Y BUSCAR";
						$buscar = mysql_real_escape_string($_GET['buscar']);
						$estado = mysql_real_escape_string($_GET['estado']);
						if ($estado != "Todas"){
							$query = "SELECT
							ventas.idventas,
							ventas.fecha,
							ventas.hora,
							clientes.nombre,
							( (SELECT SUM(total) FROM ventas_articulos WHERE idventa=ventas.idventas) - ventas.descuento) as total,
							(SELECT SUM(cantidad) FROM ventas_pagos WHERE idventa=ventas.idventas) as pagos,
							ventas.estatus as pedido,
							destinatarios.fechaentrega,
							destinatarios.nombre as ndest,
							destinatarios.direccion as dirdest,
							destinatarios.referencia as refdest
							FROM ventas
							LEFT JOIN clientes ON clientes.idclientes=ventas.idcliente
							LEFT JOIN destinatarios ON destinatarios.idventa=ventas.idventas
							WHERE ventas.idventas LIKE '%".$buscar."%' OR clientes.nombre LIKE '%".$buscar."%' AND  ventas.estatus='".$estado."'
							ORDER BY ventas.idventas DESC";
							$url = "admin.php?m=pventa&buscar=".$buscar."&estado=".$estado;
						}else{
							//echo "ENTRA EN BUSCAR";
							$buscar = mysql_real_escape_string($_GET['buscar']);

							$query = "SELECT
							ventas.idventas,
							ventas.fecha,
							ventas.hora,
							clientes.nombre,
							( (SELECT SUM(total) FROM ventas_articulos WHERE idventa=ventas.idventas) - ventas.descuento) as total,
							(SELECT SUM(cantidad) FROM ventas_pagos WHERE idventa=ventas.idventas) as pagos,
							ventas.estatus as pedido,
							destinatarios.fechaentrega,
							destinatarios.nombre as ndest,
							destinatarios.direccion as dirdest,
							destinatarios.referencia as refdest
							FROM ventas
							LEFT JOIN clientes ON clientes.idclientes=ventas.idcliente
							LEFT JOIN destinatarios ON destinatarios.idventa=ventas.idventas
							WHERE ventas.idventas LIKE '%".$buscar."%' OR clientes.nombre LIKE '%".$buscar."%'
							ORDER BY ventas.idventas DESC";
							$url = "admin.php?m=pventa&buscar=".$buscar;

						}



					}else{
						//echo "ENTRA EN ESTADO";
						$estado = mysql_real_escape_string($_GET['estado']);
						if ($estado != "Todas"){
							$sql = "WHERE ventas.estatus='".$estado."'";
						} else {
							$sql = "";	
						}

						$query = "SELECT
						ventas.idventas,
						ventas.fecha,
						ventas.hora,
						clientes.nombre,
						( (SELECT SUM(total) FROM ventas_articulos WHERE idventa=ventas.idventas) - ventas.descuento) as total,
						(SELECT SUM(cantidad) FROM ventas_pagos WHERE idventa=ventas.idventas) as pagos,
						ventas.estatus as pedido,
						destinatarios.fechaentrega,
						destinatarios.nombre as ndest,
						destinatarios.direccion as dirdest,
						destinatarios.referencia as refdest
						FROM ventas
						LEFT JOIN clientes ON clientes.idclientes=ventas.idcliente
						LEFT JOIN destinatarios ON destinatarios.idventa=ventas.idventas
						".$sql."
						ORDER BY ventas.idventas DESC";
						$url = "admin.php?m=pventa&estado=".$estado;
					}
				}else{
						$query = "SELECT
						ventas.idventas,
						ventas.fecha,
						ventas.hora,
						clientes.nombre,
						( (SELECT SUM(total) FROM ventas_articulos WHERE idventa=ventas.idventas) - ventas.descuento) as total,
						(SELECT SUM(cantidad) FROM ventas_pagos WHERE idventa=ventas.idventas) as pagos,
						ventas.estatus as pedido,
						destinatarios.fechaentrega,
						destinatarios.nombre as ndest,
						destinatarios.direccion as dirdest,
						destinatarios.referencia as refdest
						FROM ventas
						LEFT JOIN clientes ON clientes.idclientes=ventas.idcliente
						LEFT JOIN destinatarios ON destinatarios.idventa=ventas.idventas
						GROUP BY ventas.idventas
						ORDER BY ventas.idventas DESC";
						$url = "admin.php?m=pventa";
					}


					##### PAGINADOR #####
					$rows_per_page = 10;

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

					$query = mysql_query($query);
					##### PAGINADOR #####

				while($q = mysql_fetch_object($query)){
					if($q->pedido == "Pendiente"){
						$pedido = '<label class="label label-warning">Pendiente</strong>';
					} else if ($q->pedido == "En Proceso"){
						$pedido = '<label class="label label-warning">En Proceso</strong>';
					} else if ($q->pedido == "En Ruta"){
						$pedido = '<label class="label label-info">En Ruta</strong>';
					} else if ($q->pedido == "Entregado"){
						$pedido = '<label class="label label-success">Entregado</strong>';
					} else if ($q->pedido == "No Entregado"){
						$pedido = '<label class="label label-danger">No Entregado</strong>';
					}
					echo '<tr>
					<td class="text-center">'.$q->idventas.'</td>
					<td>'.$q->fecha.'</td>
					<td>'.$q->hora.'</td>
					<td>'.$q->nombre.'</td>
					<td>'.$q->ndest.'</td>
					<td>'.$q->dirdest.'</td>
					<td>'.$q->refdest.'</td>
					<td class="text-right">$ '.$q->total.' pesos </td>
					<td class="text-right">$ '.$q->pagos.' pesos</td>
					<td class="text-center">';
					if ($q->pagos >= $q->total){
						echo '<label class="label label-success"> liquidado</label>';
					} else {
						echo '<label class="label label-warning"> pendiente</label>';
					}

					echo '<td class="text-center">'.$pedido.'</td>';
					echo '<td>'.$q->fechaentrega.'</td>';

					echo '</td>
					<td class="text-right">
					<a href="#" data-id="'.$q->idventas.'" class="agregarPago btn btn-sm btn-success"> <i class="fa fa-usd"></i> </a> &nbsp;
					<a href="admin.php?m=pventaVer&id='.$q->idventas.'" class="btn btn-sm btn-info"> <i class="fa fa-archive"></i> </a> &nbsp;
					<a href="#" data-id="'.$q->idventas.'" class="btn btn-sm btn-default print"> <i class="fa fa-print"></i> </a> &nbsp;
					<a href="admin.php?m=pventaEditar&id='.$q->idventas.'" class="btn btn-sm btn-default"> <i class="fa fa-pencil"></i> </a> &nbsp;
					<a href="admin.php?m=pventa&borrar='.$q->idventas.'" class="btn btn-sm btn-danger"> <i class="fa fa-times"></i> </a>
					</td>
					</tr>';
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

<div class="modal fade" id="modal-pagos">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<h3 class="m-t-none m-b">Agregar pago</h3>
						<form role="form" action="" method="post">
							<input type="hidden" name="idventa" id="idventa" value="" >
							<div class="form-group">
								<div class="row">
									<label class="col-md-6 control-label"><strong>Metodo de Pago</strong></label>
									<div class="col-md-6">
										<select name="metodo" class="form-control">
											<option>Efectivo</option>
											<option>Tarjeta Debido/Credito</option>
											<option>Oxxo</option>
											<option>Paypal</option>
											<option>TEF</option>
											<option>Credito</option>
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
		$("#idventa").val($(this).data("id"))
		$("#modal-pagos").modal("show");
	});

	$("#cancelar").click(function(){
		$("#modal-pagos").modal("hide");
	});

	$(".print").click(function(){
		var id = $(this).data("id");

		var popupWin = window.open("print.php?id="+id, '_blank', 'width=1024,height=800,scrollbars=no,menubar=no,toolbar=no,location=no,status=no,titlebar=no,top=50');
		popupWin.window.focus();
		//window.open('../print.php', '_blank', 'width=1024,height=800,scrollbars=no,menubar=no,toolbar=no,location=no,status=no,titlebar=no,top=50');

		//popupWin.document.write('<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>ORDER DE TRABAJO</title><link href="css/bootstrap.min.css" rel="stylesheet"><link href="font-awesome/css/font-awesome.css" rel="stylesheet"><link href="css/animate.css" rel="stylesheet"><link href="css/style.css" rel="stylesheet">' +
		//        '<style type="text/css">.fuentechica{font-size:10px;}.top{margin-top:5px;}.under{border-bottom:1px solid #DDDDDD;padding-bottom: 3px;}</style>' +
		//        '</head><body class="white-bg" onload="window.print(); window.close();">' + printContents + '</html>');

		//
	});


});
</script>
