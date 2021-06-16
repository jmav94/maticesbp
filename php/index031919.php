<?php

$ventas = mysql_num_rows(mysql_query("SELECT * FROM ventas WHERE fecha=CURDATE()"));
$ingresos = mysql_fetch_object(mysql_query("SELECT SUM(cantidad) cantidad FROM ventas_pagos WHERE fecha=CURDATE()"));

?>

<section class="panel panel-default">
			<div class="row m-l-none m-r-none bg-light lter">
				<div class="col-sm-12 col-md-4 padder-v b-r b-light">
					<span class="fa-stack fa-2x pull-left m-r-sm"> <i class="fa fa-circle fa-stack-2x text-info"></i> <i class="fa fa-tag fa-stack-1x text-white"></i> </span>
					<a class="clear" href="admin.php?m=pventa">
						<span class="h3 block m-t-xs"><strong><?php echo $ventas; ?></strong></span>
						<small class="text-muted text-uc">Ventas de Hoy</small>
					</a>
				</div>
				<div class="col-sm-12 col-md-4 padder-v b-r b-light lt">
					<span class="fa-stack fa-2x pull-left m-r-sm"> <i class="fa fa-circle fa-stack-2x text-success"></i> <i class="fa fa-usd fa-stack-1x text-white"></i></span>
					<a class="clear" href="admin.php?m=ingresos">
						<span class="h3 block m-t-xs"><strong>$ <?php echo $ingresos->cantidad; ?></strong></span>
						<small class="text-muted text-uc">Ingresos de Hoy</small>
					</a>
				</div>
				<div class="col-sm-12 col-md-4 padder-v b-r b-light lt">
					<span class="fa-stack fa-2x pull-left m-r-sm"> <i class="fa fa-circle fa-stack-2x text-warning"></i> <i class="fa fa-archive fa-stack-1x text-white"></i></span>
					<a class="clear" href="admin.php?m=ingresos">
						<span class="h3 block m-t-xs"><strong><?php echo $ingresos->cantidad; ?></strong></span>
						<small class="text-muted text-uc">Pedidos para Hoy</small>
					</a>
				</div>
			</div>
</section>

<div class="row">
	<div class="col-md-12">
		<section class="panel panel-default pos-rlt clearfix">

			<header class="panel-heading"> <i class="fa fa-archive"></i> Pedidos para hoy</header>

			<div class="table-responsive">
				<table class="table ">
					<tr>
						<th>#</th>
						<th width="120">Fecha</th>
						<th width="100"> Hora</th>
						<th width="120">Estado</th>
						<th>Articulos</th>
						<th>Nombre</th>
						<th>Direccion</th>
						<th></th>
					</tr>
<?php
					$query = mysql_query("SELECT * FROM destinatarios JOIN ventas ON ventas.idventas=destinatarios.idventa WHERE fechaentrega='".$fechaHoy."' ORDER BY idventa ASC");
					while($q = mysql_fetch_object($query)){

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

						echo "<td class='v-middle'>".$q->nombre."</td>
							<td class='v-middle'>".$q->direccion."</td>
							<td class='text-right'><a class='btn btn-sm btn-info' href='admin.php?m=pventaVer&id=".$q->idventa."'><i class='fa fa-archive'></i></a></td>
						<tr>";
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
					WHERE ventas_creditos.fecha=CURDATE()
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