<?php

$id = mysql_real_escape_string($_GET['id']);

if(isset($_POST['abono'])){
	$abono = mysql_real_escape_string($_POST['abono']);

	mysql_query("INSERT INTO cuentas_pagos SET fecha='".date("Y-m-d")."',idcuenta='".$id."',cantidad='".$abono."'");
}

if ( isset($_GET['del']) ){
	$del = mysql_real_escape_string($_GET['del']);
	mysql_query("DELETE FROM cuentas_pagos WHERE idpagos='".$del."'");
}

$data = mysql_fetch_object(mysql_query("SELECT 
	(SELECT SUM(cantidad) FROM cuentas_pagos WHERE idcuenta=cuentas.idcuentas) as abonos,
	cuentas.*,
	proveedores.*
	FROM cuentas 
	JOIN proveedores ON proveedores.idproveedores=cuentas.idproveedor 
	WHERE idcuentas='".$id."' LIMIT 1"));
?>
<form class="bs-example form-horizontal" action="" method="post">
	<div class="row">
		<?php echo @$errorMsg; ?>
		<div class="col-md-5">
			<section class="panel panel-default">
				<header class="panel-heading">
					<i class="fa fa-user icon"></i> Datos del Proveedor
				</header>
				<div class="panel-body">
<?php


					echo '<div class="row">
						<div class="col-xs-12 col-md-4"><strong>Nombre:</strong></div>
						<div class="col-xs-12 col-md-8">'.$data->nombre.'</div>
					</div>';


					echo '<div class="row m-t">
						<div class="col-xs-12 col-md-4"><strong>Direcci&oacute;n:</strong></div>
						<div class="col-xs-12 col-md-8">'.$data->domicilio.'</div>
					</div>';

					if (!empty($data->colonia)){
						echo '<div class="row m-t">
							<div class="col-xs-12 col-md-4"><strong>Colonia:</strong></div>
							<div class="col-xs-12 col-md-8">'.$data->colonia.'</div>
						</div>';
					}

					if (!empty($data->codigopostal)){
						echo '<div class="row m-t">
							<div class="col-xs-12 col-md-4"><strong>Código Postal:</strong></div>
							<div class="col-xs-12 col-md-8">'.$data->codigopostal.'</div>
						</div>';
					}

					if (!empty($data->telefono)){
						echo '<div class="row m-t">
							<div class="col-xs-12 col-md-4"><strong>Tel&eacute;fono:</strong></div>
							<div class="col-xs-12 col-md-8">'.$data->telefono.'</div>
						</div>';
					}

					if (!empty($data->rfc)){
						echo '<div class="row m-t">
							<div class="col-xs-12 col-md-4"><strong>Celular:</strong></div>
							<div class="col-xs-12 col-md-8">'.$data->rfc.'</div>
						</div>';
					}
				
					if (!empty($data->correo)){
						echo '<div class="row m-t">
							<div class="col-xs-12 col-md-4"><strong>Correo:</strong></div>
							<div class="col-xs-12 col-md-8">'.$data->correo.'</div>
						</div>';
					}
?>
				</div>
			</section>
			<section class="panel panel-default">
				<header class="panel-heading">
					<i class="fa fa-pencil icon"></i> Detalles
				</header>
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12 col-md-4"><strong>Importe:</strong></div>
						<div class="col-xs-12 col-md-8">$ <?php echo $data->importe; ?> pesos</div>
					</div>
					<div class="row m-t">
						<div class="col-xs-12 col-md-4"><strong>Comentarios:</strong></div>
						<div class="col-xs-12 col-md-8"><?php echo $data->comentarios; ?></div>
					</div>
				</div>
			</section>
		</div>
		<div class="col-md-7">

			<section class="panel panel-default">
				<header class="panel-heading">
					<i class="fa fa-usd icon"></i> Listado de Abonos
				</header>
				<div class="panel-body">
<?php
				if ( $data->abonos < $data->importe){
?>
					<div class="row">
						<div class="col-md-12 m-b">
							<a href="#" id="agregarAbono" class="btn btn-sm btn-success"> <i class="fa fa-plus"></i> Agregar Abono</a>
						</div>
					</div>
<?php
				} else {
?>
					<div class="alert alert-success">
						<strong><i class="fa fa-check"></i> Cuenta liquidada.</strong>
					</div>
<?php
				}
?>
					<div class="table-responsive">
						<table class="table table-striped">
							<tr>
								<th width="120">Fecha</th>
								<th width="150" class="text-right">Abonos</th>
								<th width="80"></th>
							</tr>
<?php

						$suma = 0;
						$query = mysql_query("SELECT * FROM cuentas_pagos WHERE idcuenta='".$id."'");
						while($q = mysql_fetch_object($query)){
							$suma += $q->cantidad;
?>	
							<tr>
								<td><?php echo $q->fecha; ?></td>
								<td class="text-right">$ <?php echo $q->cantidad; ?> pesos</td>
								<td class="text-right">
									<a href="admin.php?m=porpagarEditar&id=<?php echo $id; ?>&del=<?php echo $q->idpagos; ?>" class="btn btn-sm btn-danger"> <i class="fa fa-times"></i> </a>					
								</td>
							</tr>
<?php
						}
?>
							<tr>
								<th></th>
								<th class="text-right">Total: $ <?php echo $suma; ?> pesos</th>
								<th></th>
							</tr>
						</table>						
					</div>
				</div>
			</section>
		</div>
	</div>
</form>
		
<div class="modal fade" id="modal-pagos">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<h3 class="m-t-none m-b text-center">Agregar Abono</h3>
						<form role="form" class="form-horizontal" action="" method="post">
							<div class="form-group">
									<label class="col-md-6 control-label"><strong>Abono</strong></label>
									<div class="col-md-6">
										<div class="input-group m-b">
											<span class="input-group-addon">$</span>
											<input type="text" name="abono" value="0" class="form-control">
											<span class="input-group-addon"> pesos </span>
										</div>
									</div>
							</div>
							<div class="checkbox m-t-lg">
								<a class="btn btn-md btn-default m-t-n-xs" id="cancelar2"> <i class="fa fa-times"></i> <strong>Cancelar</strong></a>
								<button type="submit" class="btn btn-md pull-right btn-success m-t-n-xs"> <i class="fa fa-check"></i> <strong>Agregar abono</strong></button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>

<script>
$(function(){

	$("#agregarAbono").click(function(){
		$("#modal-pagos").modal("show");
	});

	$("#cancelar2").click(function(){
		$("#modal-pagos").modal("hide");
	});
});
</script>