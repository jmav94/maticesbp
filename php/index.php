<?php
$fechaActual = date('Y-m-d');
$ventas = mysql_num_rows(mysql_query("SELECT * FROM ventas WHERE fecha='".$fechaActual."'"));

$ingresos = mysql_fetch_object(mysql_query("SELECT SUM(cantidad) cantidad FROM ventas_pagos WHERE fecha='".$fechaActual."'"));

$ventasI = mysql_query("SELECT (SELECT SUM(total) FROM ventas_articulos WHERE idventa=ventas.idventas - ventas.descuento) as total FROM ventas WHERE ventas.fecha = '".$fechaActual."'");
$tventas = 0;
while ($a = mysql_fetch_object($ventasI)) {
	$tventas += $a->total;
}

$pedidos = mysql_num_rows(mysql_query("SELECT idventa FROM destinatarios WHERE fechaentrega='".$fechaActual."'"));
?>
<section class="panel panel-default">
	<div class="row m-l-none m-r-none bg-light lter">
		<div class="col-sm-12 col-md-3 padder-v b-r b-light">
			<span class="fa-stack fa-2x pull-left m-r-sm"> <i class="fa fa-circle fa-stack-2x text-info"></i> <i class="fa fa-tag fa-stack-1x text-white"></i> </span>
			<a class="clear" href="admin.php?m=ingresos&daterange=<?php echo($fechaActual. "+-+" .$fechaActual); ?>">
				<span class="h3 block m-t-xs"><strong>$ <?php echo $tventas; ?></strong></span>
				<small class="text-muted text-uc">Ventas de Hoy</small>
			</a>
		</div>
		<div class="col-sm-12 col-md-3 padder-v b-r b-light">
			<span class="fa-stack fa-2x pull-left m-r-sm"> <i class="fa fa-circle fa-stack-2x text-info"></i> <i class="fa fa-tag fa-stack-1x text-white"></i> </span>
			<a class="clear" href="#">
				<span class="h3 block m-t-xs"><strong> <?php echo $ventas; ?></strong></span>
				<small class="text-muted text-uc">Ventas de Hoy</small>
			</a>
		</div>
		<div class="col-sm-12 col-md-3 padder-v b-r b-light lt">
			<span class="fa-stack fa-2x pull-left m-r-sm"> <i class="fa fa-circle fa-stack-2x text-success"></i> <i class="fa fa-usd fa-stack-1x text-white"></i></span>
			<!-- Cambio para que el boton ingresos de hoy regrese el reporte de ingresos con el filtro de busqueda con la fecha acutal 03-16-19 -->
			<a class="clear" href="admin.php?m=ingresosHoy">
				<span class="h3 block m-t-xs"><strong>$ <?php  if ($ingresos->cantidad == "") {
				echo "0";}else{echo $ingresos->cantidad;} ?></strong></span>
				<small class="text-muted text-uc">Ingresos de Hoy</small>
			</a>
		</div>
		<div class="col-sm-12 col-md-3 padder-v b-r b-light lt">
			<span class="fa-stack fa-2x pull-left m-r-sm"> <i class="fa fa-circle fa-stack-2x text-warning"></i> <i class="fa fa-archive fa-stack-1x text-white"></i></span>
			<a class="clear" href="admin.php?m=pedidos&daterange=<?php echo($fechaActual. "+-+" .$fechaActual); ?>">
				<span class="h3 block m-t-xs"><strong> <?php echo $pedidos; ?></strong></span>
				<small class="text-muted text-uc">Pedidos para Hoy</small>
			</a>
		</div>
	</div>
</section>
<div class="row">
	<div class="col-md-12">
		<section class="panel panel-default pos-rlt clearfix">
			<header class="panel-heading"> <i class="fa fa-archive"></i> Pedidos para hoy</header>
			<?php
if ( isset($_POST['idventa']) ){
	$idventa 	= mysql_real_escape_string($_POST['idventa']);
	$cantidad 	= mysql_real_escape_string($_POST['cantidad']);
	$metodo 	= mysql_real_escape_string($_POST['metodo']);
	$comentario = mysql_real_escape_string($_POST['comentario']);
	$fecha 	  	= date("Y-m-d");
	$hora 	  	= date("H:i:s");

	mysql_query("INSERT INTO ventas_pagos SET idventa='".$idventa."',fecha='".$fecha."',hora='".$hora."',cantidad='".$cantidad."',comentario='".$comentario."',metodo='".$metodo."'");

	echo '<div class="row">
	<div class="col-md-12">
			<div class="alert alert-success">
			<strong> <i class="fa fa-check"></i> Pago agregado correctamente.</strong>
			</div>
		  </div>
		  </div>';
}
?>
			<div class="table-responsive">
				<table class="table ">
					<tr>
						<th width="10">#</th>
						<th width="100">Fecha</th>
						<th width="100"> Hora</th>
						<th width="100">Estado</th>
						<th>Articulos</th>
						<th>Cliente</th>
						<th>Destinarario</th>
						<th>Direccion</th>
						<th width="70">Total</th>
						<th width="70">Pagado</th>
						<th width="50">Estatus</th>
						<th width="10"></th>
						<th width="10"></th>
					</tr>
<?php
$query = mysql_query("SELECT 
	destinatarios.*,
	ventas.estatus,
	ventas.idventas,
	ventas.descuento,
	clientes.nombre AS nomCliente
	FROM destinatarios 
	JOIN ventas ON ventas.idventas=destinatarios.idventa
	INNER JOIN clientes ON clientes.idclientes=ventas.idcliente 
	WHERE fechaentrega='".$fechaHoy."' ORDER BY idventa ASC");

while($q = mysql_fetch_object($query)){
# sacamos el total 
$asd = mysql_fetch_object(mysql_query("SELECT SUM(total) total FROM ventas_articulos WHERE idventa='".$q->idventas."'"));
#??sacamos los pagos
$asd2 = mysql_fetch_object(mysql_query("SELECT SUM(cantidad) cantidad FROM ventas_pagos WHERE idventa='".$q->idventas."'"));
$total = ($asd->total - $q->descuento);

		if($q->estatus == "Pendiente"){
			$estado = '<label class="label label-warning">Pendiente</strong>';
		} else if ($q->estatus == "En Proceso"){
			$estado = '<label class="label label-warning">En Proceso</strong>';
		} else if ($q->estatus == "En Ruta"){
			$estado = '<label class="label label-info">En Ruta</strong>';
		} else if ($q->estatus == "Entregado"){
			$estado = '<label class="label label-success">Entregado</strong>';
		} else if ($q->estatus == "No Entregado"){
			$estado = '<label class="label label-danger">No Entregado</strong>';
		}
	echo "<tr>
	<td class='v-middle'>".$q->idventas."</td>
		<td class='v-middle'>".$q->fechaentrega."</td>
		<td class='v-middle'>".$q->horaentrega."</td>
		<td class='v-middle'>".$estado."</td>";

		$articulo = "";
		$queryy = mysql_query("SELECT
			articulos.articulo
			FROM ventas_articulos
			LEFT JOIN articulos ON articulos.idarticulos=ventas_articulos.idarticulo
			WHERE ventas_articulos.idventa='".$q->idventas."'");
		while($y = mysql_fetch_object($queryy)){

			if (!preg_match("/envio/", $y->articulo) ){
				$articulo .= $y->articulo."<br>";
			}

		}
	echo '<td class="v-middle">'.$articulo.'</td>';

	echo "<td class='v-middle'>".$q->nomCliente."</td>
		<td class='v-middle'>".$q->nombre."</td>
		<td class='v-middle'>".$q->direccion."</td>";
		if ($asd2->cantidad == "") {
			echo "<td class='text-right'>$".$total."</td>
		<td class='text-right'>$ 0.00</td>
		<td class='text-center'>";
		}else{
			echo "<td class='text-right'>$ ".$total."</td>
		<td class='text-right'>$ ".$asd2->cantidad."</td>
		<td class='text-center'>";
		}
	if ($asd2->cantidad >= $total){
		echo '<label class="label label-success"> liquidado</label>';
	} else {
		echo '<label class="label label-warning"> pendiente</label>';
	}
		echo '</td><td class="text-right">
		<a href="#" data-id="'.$q->idventas.'" class="agregarPago btn btn-sm btn-success"> <i class="fa fa-usd"></i> </a></td>';
		echo "<td class='text-right'><a class='btn btn-sm btn-info' href='admin.php?m=pventaVer&id=".$q->idventa."'><i class='fa fa-archive'></i></a></td><tr>";
}
?>
				</table>
			</div>

		</section>
	</div>
	<div class="col-md-12">
		<section class="panel panel-default pos-rlt clearfix">
			<header class="panel-heading"> <i class="fa fa-warning"></i> Cuentas Por cobrar</header>
			<div class="table-responsive">
				<table class="table ">
					<tr>
						<th width="100"># Orden</th>
						<th width="150"> Fecha de pago </th>
						<th>Comentarios</th>
						<th>Cliente</th>
						<th width="100"> </th>
					</tr>
<?php
				$query = mysql_query("SELECT
					ventas_creditos.comentarios,
					ventas_creditos.fecha,
					ventas_creditos.idventa,
					clientes.nombre
					FROM ventas_creditos
					JOIN ventas ON ventas.idventas=ventas_creditos.idventa
					JOIN clientes ON clientes.idclientes=ventas.idcliente
					WHERE ventas_creditos.fecha='".$fechaActual."'
					ORDER BY ventas_creditos.idcreditos DESC"
				);
				while($q = mysql_fetch_object($query)){
					echo '<tr>
						<td class="text-center v-middle">'.$q->idventa.'</td>
						<td class="v-middle">'.$q->fecha.'</td>
						<td class="v-middle">'.$q->comentarios.'</td>
						<td class="v-middle">'.$q->nombre.'</td>
						<td class="text-right v-middle">
							<a href="admin.php?m=pventaEditar&id='.$q->idventa.'" class="btn btn-sm btn-success"> <i class="fa fa-usd"></i> </a>
						</td>
					</tr>';
				}
?>
			</table>
		</div>
	</section>
	</div>
</div>
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