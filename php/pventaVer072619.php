<?php

$id = mysql_real_escape_string($_GET['id']);

if ( isset($_POST['e_nombre']) ){
	$e_nombre 			= mysql_real_escape_string($_POST['e_nombre']);
	$e_direccion 		= mysql_real_escape_string($_POST['e_direccion']);
	$e_fechaentrega 	= mysql_real_escape_string($_POST['e_fechaentrega']);
	$e_horaentrega 		= mysql_real_escape_string($_POST['e_horaentrega']);
	$e_referencia 		= mysql_real_escape_string($_POST['e_referencia']);
	$e_colonia 			= mysql_real_escape_string($_POST['e_colonia']);
	$e_codigopostal 	= mysql_real_escape_string($_POST['e_codigopostal']);
	$e_mensaje 			= mysql_real_escape_string($_POST['e_mensaje']);
	$comen  = mysql_real_escape_string($_POST['comen']);

	$query = "SELECT * FROM destinatarios WHERE idventa='".$id."' LIMIT 1";
	if ( mysql_num_rows(mysql_query($query)) ){
		mysql_query("UPDATE destinatarios SET comen='".$comen."', nombre='".$e_nombre."',direccion='".$e_direccion."',fechaentrega='".$e_fechaentrega."',horaentrega='".$e_horaentrega."',referencia='".$e_referencia."',colonia='".$e_colonia."',codigopostal='".$e_codigopostal."',mensaje='".$e_mensaje."' WHERE idventa='".$id."'") or die(mysql_error());
	} else {
		mysql_query("INSERT INTO destinatarios SET comen='".$comen."',nombre='".$e_nombre."',direccion='".$e_direccion."',fechaentrega='".$e_fechaentrega."',horaentrega='".$e_horaentrega."',referencia='".$e_referencia."',colonia='".$e_colonia."',codigopostal='".$e_codigopostal."',mensaje='".$e_mensaje."',idventa='".$id."'") or die(mysql_error());
	}

}

if ( isset($_POST['estado']) ){
	$estado 		= mysql_real_escape_string($_POST['estado']);
	$comentarios 	= mysql_real_escape_string($_POST['comentarios']);
	$fecha 			= date("Y-m-d");
	$hora 			= date("H:m:s");

	mysql_query("INSERT INTO ventas_estados SET fecha='".$fecha."',hora='".$hora."',estado='".$estado."',comentarios='".$comentarios."',idventa='".$id."'");
	mysql_query("UPDATE ventas SET estatus='".$estado."' WHERE idventas='".$id."'");
}

$data = mysql_fetch_object(mysql_query("SELECT * FROM ventas JOIN clientes ON clientes.idclientes=ventas.idcliente WHERE idventas='".$id."' LIMIT 1"));
?>
<form class="bs-example form-horizontal" action="" method="post">
	<div class="row">
		<?php echo @$errorMsg; ?>
		<div class="col-md-4">
			<section class="panel panel-default">
				<header class="panel-heading">
					<i class="fa fa-user icon"></i> Cliente
				</header>
				<div class="panel-body">
<?php

				if ($data->idcliente != 0){
					$cliente = mysql_fetch_object(mysql_query("SELECT * FROM clientes WHERE idclientes='".$data->idcliente."' LIMIT 1"));

					echo '<div class="row m-t">
						<div class="col-xs-12 col-md-4"><strong>Nombre:</strong></div>
						<div class="col-xs-12 col-md-8">'.$cliente->nombre.'</div>
					</div>';


					echo '<div class="row m-t">
						<div class="col-xs-12 col-md-4"><strong>Direcci&oacute;n:</strong></div>
						<div class="col-xs-12 col-md-8">'.$cliente->domicilio.'</div>
					</div>';

					if (!empty($cliente->telefono)){
						echo '<div class="row m-t">
							<div class="col-xs-12 col-md-4"><strong>Tel&eacute;fono:</strong></div>
							<div class="col-xs-12 col-md-8">'.$cliente->telefono.'</div>
						</div>';
					}

					if (!empty($cliente->celular)){
						echo '<div class="row m-t">
							<div class="col-xs-12 col-md-4"><strong>Celular:</strong></div>
							<div class="col-xs-12 col-md-8">'.$cliente->celular.'</div>
						</div>';
					}

					if (!empty($cliente->correo)){
						echo '<div class="row m-t">
							<div class="col-xs-12 col-md-4"><strong>Correo:</strong></div>
							<div class="col-xs-12 col-md-8">'.$cliente->correo.'</div>
						</div>';
					}
				}
?>
				</div>
			</section>

			<section class="panel panel-default">
				<header class="panel-heading">
					<i class="fa fa-archive icon"></i> Estado del pedido
				</header>
				<div class="panel-body">
<?php
			if($data->estatus == "Pendiente"){
				echo '<div class="alert alert-warning">
						<strong><i class="fa fa-warning"></i> Pendiente</strong>
					</div>';
			} else if ($data->estatus == "En Proceso"){
				echo '<div class="alert alert-warning">
						<strong><i class="fa fa-archive"></i> En Proceso</strong>
					</div>';
			} else if ($data->estatus == "En Ruta"){
				echo '<div class="alert alert-info">
						<strong><i class="fa fa-truck"></i> En Ruta</strong>
					</div>';
			} else if ($data->estatus == "Entregado"){
				echo '<div class="alert alert-success">
						<strong><i class="fa fa-check"></i> Entregado</strong>
					</div>';
			} else if ($data->estatus == "No Entregado"){
				echo '<div class="alert alert-danger">
						<strong><i class="fa fa-times"></i> No Entregado</strong>
					</div>';
			}
?>
					<div class="col-md-12">
						<form class="form-horizontal" action="" method="post">
							<div class="form-group">
								<label> Nuevo estado:</label>
								<select required class="form-control" name="estado">
									<option>Pendiente</option>
									<option>En Proceso</option>
									<option>En Ruta</option>
									<option>Entregado</option>
									<option>No Entregado</option>
								</select>
							</div>
							<div class="form-group">
								<label>Comentarios:</label>
								<textarea name="comentarios" class="form-control"> </textarea>
							</div>
							<div class="form-group">
								<button class="btn btn-block btn-success btn-sm"> <i class="fa fa-check"></i> Cambiar estado</button>
							</div>
						</form>
					</div>
				</div>
			</section>

			<section class="panel panel-default">
				<header class="panel-heading">
					<i class="fa fa-archive icon"></i> Datos de Entrega
				</header>
				<div class="panel-body">
<?php
				$query = "SELECT * FROM destinatarios WHERE idventa='".$id."' LIMIT 1";
				if ( mysql_num_rows(mysql_query($query)) ){
					$dest = mysql_fetch_object(mysql_query($query));

					echo '<div class="row">
						<div class="col-xs-12 col-md-4"><strong>Nombre:</strong></div>
						<div class="col-xs-12 col-md-8">'.$dest->nombre.'</div>
					</div>';

					echo '<div class="row m-t">
						<div class="col-xs-12 col-md-4"><strong>Dirección:</strong></div>
						<div class="col-xs-12 col-md-8">'.$dest->direccion.'</div>
					</div>';

					echo '<div class="row m-t">
						<div class="col-xs-12 col-md-4"><strong>Fecha Entrega:</strong></div>
						<div class="col-xs-12 col-md-8">'.$dest->fechaentrega.'</div>
					</div>';

					echo '<div class="row m-t">
						<div class="col-xs-12 col-md-4"><strong>Hora de Entrega:</strong></div>
						<div class="col-xs-12 col-md-8">'.$dest->horaentrega.'</div>
					</div>';

					echo '<div class="row m-t">
						<div class="col-xs-12 col-md-4"><strong>Referencia:</strong></div>
						<div class="col-xs-12 col-md-8">'.$dest->referencia.'</div>
					</div>';

					echo '<div class="row m-t">
						<div class="col-xs-12 col-md-4"><strong>Colonia:</strong></div>
						<div class="col-xs-12 col-md-8">'.$dest->colonia.'</div>
					</div>';

					echo '<div class="row m-t m-b">
						<div class="col-xs-12 col-md-4"><strong>Código Postal:</strong></div>
						<div class="col-xs-12 col-md-8">'.$dest->codigopostal.'</div>
					</div>';

					echo '<div class="row m-t">
						<div class="col-xs-12 col-md-4"><strong>Mensaje:</strong></div>
						<div class="col-xs-12 col-md-8">'.$dest->mensaje.'</div>
					</div>';

					echo '<div class="row m-t">
						<div class="col-xs-12 col-md-4"><strong>Comentarios: </strong></div>
						<div class="col-xs-12 col-md-8">'.$dest->comen.'</div>
					</div>';


					echo '<a href="#" class="agregarDatos btn btn-md btn-default btn-block m-t" data-id="'.$dest->idventa.'"> <i class="fa fa-pencil"></i> Editar datos </a>';

				} else {
					echo '<a href="#" class="agregarDatos btn btn-md btn-success btn-block"> <i class="fa fa-plus"></i> Agregar Destinatario</a>';
				}
?>
				</div>
			</section>

		</div>
		<div class="col-md-8">
			<section class="panel panel-default">
				<header class="panel-heading">
					<i class="fa fa-shopping-cart icon"></i> Articulos
				</header>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-striped" id="productos">
							<tr>
								<th>Articulo</th>
								<th>Precio U.</th>
								<th width="100">Cantidad</th>
								<th width="100">Precio</th>
							</tr>
<?php
						$next  = 1;
						$query = mysql_query("SELECT ventas_articulos.precio,ventas_articulos.cantidad,articulos.articulo
							FROM ventas_articulos,articulos

							WHERE ventas_articulos.idventa='".$data->idventas."' AND articulos.idarticulos = ventas_articulos.idarticulo
							ORDER BY idva ASC");
							$sum = 0;
						while($q = mysql_fetch_object($query)){
							$t = $q->precio * $q->cantidad;
							$sum = $sum + $t;
							echo '<tr>
                        		<td>'.$q->articulo.'</td>
														<td class="text-center v-middle" >$ '.$q->precio.'</td>
								<td class="text-center v-middle">'.$q->cantidad.'</td>
								<td class="text-center v-middle">'.$t.'</td>
                    		</tr>';
                    		$next++;
						}

?>
						</table>

					</div>
					<div class="row">
						<div class="col-sm-12 text-right text-center-xs">
							<strong>Total: $ <?php echo $sum; ?> pesos</strong></strong>
						</div>
					</div>
				</div>
			</section>

			<section class="panel panel-default">
				<header class="panel-heading">
					<i class="fa fa-clock-o icon"></i> Historial de Estados
				</header>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-striped" id="productos">
							<tr>
								<th width="120">Fecha</th>
								<th width="120">Hora</th>
								<th width="150">Estado</th>
								<th>Comentarios</th>
							</tr>
<?php
						$query = mysql_query("SELECT * FROM ventas_estados WHERE idventa='".$data->idventas."' ORDER BY idestados DESC");
						while($q = mysql_fetch_object($query)){

							if($q->estado == "Pendiente"){
								$estado = '<label class="label label-warning">Pendiente</strong>';
							} else if ($q->estado == "En Proceso"){
								$estado = '<label class="label label-warning">En Proceso</strong>';
							} else if ($q->estado == "En Ruta"){
								$estado = '<label class="label label-info">En Ruta</strong>';
							} else if ($q->estado == "Entregado"){
								$estado = '<label class="label label-success">Entregado</strong>';
							} else if ($q->estado == "No Entregado"){
								$estado = '<label class="label label-danger">No Entregado</strong>';
							}

							echo '<tr>
                        		<td>'.$q->fecha.'</td>
                        		<td>'.$q->hora.'</td>
								<td>'.$estado.'</td>
								<td>'.$q->comentarios.'</td>
                    		</tr>';
                    		$next++;
						}
?>
						</table>
					</div>
				</div>

			</section>
			<?php echo "<strong> Pedido # ".$id."</strong>"?>
			

		</div>
	</div>
</form>

<div class="modal fade" id="modal-datos">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<h3 class="m-t-none m-b">Datos de Entrega</h3>
						<form role="form" action="" class="form-horizontal" method="post">
							<div class="form-group">
								<label class="col-md-4 control-label"><strong>Nombre</strong></label>
								<div class="col-md-8"><input required type="text" class="form-control" name="e_nombre" id="e_nombre"></div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label"><strong>Dirección</strong></label>
								<div class="col-md-8"><input required type="text" class="form-control" name="e_direccion" id="e_direccion"></div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label"><strong>Fecha de Entrega</strong></label>
								<div class="col-md-8"><input required type="text" class="form-control" name="e_fechaentrega" id="e_fechaentrega" value="<?php echo date("Y-m-d"); ?>" data-date-format="yyyy-mm-dd"></div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label"><strong>Hora de Entrega</strong></label>
								<div class="col-md-8"><input required type="text" class="form-control" name="e_horaentrega" id="e_horaentrega"></div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label"><strong>Referencia</strong></label>
								<div class="col-md-8"><input type="text" class="form-control" name="e_referencia" id="e_referencia"></div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label"><strong>Colonia</strong></label>
								<div class="col-md-8"><input type="text" class="form-control" name="e_colonia" id="e_colonia"></div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label"><strong>Código Postal</strong></label>
								<div class="col-md-8"><input type="text" class="form-control" name="e_codigopostal" id="e_codigopostal"></div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label"><strong>Mensaje de Tarjeta</strong></label>
								<div class="col-md-8"><textarea required style="height:100px;" class="form-control" name="e_mensaje" id="e_mensaje"></textarea></div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label"><strong>Comentarios</strong></label>
								<div class="col-md-8"><textarea style="height:100px;" class="form-control" name="comen" id="comen"></textarea></div>
							</div>
							<div class="checkbox m-t-lg">
								<a class="btn btn-sm btn-default m-t-n-xs" id="cancelar2"> <i class="fa fa-times"></i> <strong>Cancelar</strong></a>
								<button type="submit" class="btn btn-sm pull-right btn-success"> <i class="fa fa-check"></i> <strong>Terminar</strong></button>
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

		$(".agregarDatos").click(function(){

			var id = $(this).data("id");

			if (id){
				$.getJSON("php/ajax/destinatario.php?id="+id, function(e){
					$("#e_nombre").val(e.e_nombre);
					$("#e_direccion").val(e.e_direccion);
					$("#e_fechaentrega").val(e.e_fechaentrega);
					$("#e_horaentrega").val(e.e_horaentrega);
					$("#e_referencia").val(e.e_referencia);
					$("#e_colonia").val(e.e_colonia);
					$("#e_codigopostal").val(e.e_codigopostal);
					$("#e_mensaje").val(e.e_mensaje);
					$("#comen").val(e.comen);
				});
			}

			$("#modal-datos").modal("show");
		});

		$("#e_fechaentrega").datepicker();

		$("#cancelar2").click(function(){
			$("#modal-datos").modal("hide");
		});

	});
</script>
