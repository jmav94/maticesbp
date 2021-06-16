<?php
$id   = mysql_real_escape_string($_GET['id']);
$data = mysql_fetch_object(mysql_query("SELECT * FROM clientes WHERE idclientes='".$id."' LIMIT 1"));
?>
<div class="row">
	<div class="col-sm-12 col-md-4">
		<section class="panel panel-default pos-rlt clearfix">
			<header class="panel-heading"> <i class="fa fa-users"></i> Cliente</header>
			<br>
			<div class="table-responsive">
				<table class="table table-striped b-t b-light">
					<tr>
						<th>Nombre</th>
						<td><?php echo $data->nombre; ?></td>
					</tr>
<?php
				if (!empty($data->domicilio)){
?>
					<tr>
						<th>Domicilio</th>
						<td><?php echo $data->domicilio; ?></td>
					</tr>
<?php
				} 

				if (!empty($data->colonia)){
?>
					<tr>
						<th>Colonia</th>
						<td><?php echo $data->colonia; ?></td>
					</tr>
<?php
				}

				if (!empty($data->codigopostal)){
?>
					<tr>
						<th>Codigo Postal</th>
						<td><?php echo $data->codigopostal; ?></td>
					</tr>
<?php
				}

				if (!empty($data->telefono)){
?>
					<tr>
						<th>Telefono</th>
						<td><?php echo $data->telefono; ?></td>
					</tr>
<?php
				}

				if (!empty($data->celular)){
?>
					<tr>
						<th>Celular</th>
						<td><?php echo $data->celular; ?></td>
					</tr>
<?php
				}

				if (!empty($data->correo)){
?>
					<tr>
						<th>Correo</th>
						<td><?php echo $data->correo; ?></td>
					</tr>
<?php
				}

				if (!empty($data->sexo)){
?>
					<tr>
						<th>Sexo</th>
						<td><?php echo $data->sexo; ?></td>
					</tr>
<?php
				}

				if (!empty($data->edad)){
?>
					<tr>
						<th>Edad</th>
						<td><?php echo $data->edad; ?></td>
					</tr>
<?php
				}
?>
				</table>
			</div>
		</section>
	</div>

	<div class="col-sm-12 col-md-8">
		<section class="panel panel-default pos-rlt clearfix">

			<header class="panel-heading"> <i class="fa fa-usd"></i> Ventas</header>
		
			<div class="row wrapper">
				<div class="col-md-12 m-b-xs">
					<a href="admin.php?m=clientes" class="btn btn-sm btn-warning"><i class="fa fa-reply"></i> Regresar</a> &nbsp;&nbsp;&nbsp;
				</div>
			</div>

			<div class="table-responsive">
				<table class="table table-striped b-t b-light">
					<thead>
						<tr>
							<th width="120">Fecha</th>
							<th width="120">Hora</th>
							<th width="200" class="text-center">Total</th>
							<th width="200" class="text-center">Liquidado</th>
							<th></th>
							<th width="120"></th>
						</tr>
					</thead>
					<tbody>

		<?php
					if ( isset($_GET['del']) ){
						$del = mysql_real_escape_string($_GET['del']);
						mysql_query("DELETE FROM citas WHERE idcitas='".$del."'");
					}

					$consulta  = mysql_query("SELECT 
						ventas.*,
						(SELECT SUM(total) FROM ventas_articulos WHERE idventa=ventas.idventas) as total,
						(SELECT SUM(cantidad) FROM ventas_pagos WHERE idventa=ventas.idventas) as liquidado
						FROM ventas 
						WHERE idcliente='".$id."' ORDER BY fecha DESC");
					
					while($q = mysql_fetch_object($consulta)){

						$descuento = ($q->descuento / 100) * $q->total;
						$total = ($q->total - $descuento);

						if ($q->liquidado >= $total){
							$estado = '<label class="label label-success"> liquidado</label>';
						} else {
							$estado = '<label class="label label-warning"> pendiente</label>';
						}
		?>
						<tr>
							<td><?php echo $q->fecha; ?></td>
							<td><?php echo $q->hora; ?></td>
							<td class="text-right">$ <?php echo $total; ?> pesos</td>
							<td class="text-right">$ <?php echo $q->liquidado; ?> pesos</td>
							<td class="text-center"><?php echo $estado; ?></td>
							<td class="text-right">
								<a href="admin.php?m=pventaEditar&id=<?php echo $q->idventas; ?>" class="btn btn-sm btn-default"> <i class="fa fa-pencil"></i> </a> &nbsp;				
							</td>
						</tr>
		<?php
					}
		?>
					</tbody>
				</table>
			</div>
		</section>
	</div>
</div>