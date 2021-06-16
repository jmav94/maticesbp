<?php
$errorMsg2 = "";
if (isset($_POST['cliente']) ){
	$fecha 	  = date("Y-m-d");
	$hora 	  = date("H:m:s");
	$cliente  = mysql_real_escape_string($_POST['cliente']);
	$pagocon  = mysql_real_escape_string($_POST['pagocon']);
	$metodo   = mysql_real_escape_string($_POST['metodo']);
	$descuento = mysql_real_escape_string($_POST['descuento']);

	# agregamos la venta
	mysql_query("INSERT INTO ventas SET fecha='".$fecha."',hora='".$hora."',idcliente='".$cliente."',descuento='".$descuento."',estatus='Pendiente',idusuario='".$_SESSION['userId']."'") or die(mysql_error());
	$idventa = mysql_insert_id();


	# recorremos cada articulo
	$idarticulo = $_POST['idarticulo'];
	$precio 	= $_POST['precio'];
	$cantidad 	= $_POST['cantidad'];
	$sql 		= array();

	$Total 		= 0;
	for ($i=0; $i < count($idarticulo); $i++) {

		$total 		= $cantidad[$i] * $precio[$i];

		$sql[] = "(".$idventa.",'".mysql_real_escape_string($idarticulo[$i])."','".mysql_real_escape_string($precio[$i])."','".mysql_real_escape_string($cantidad[$i])."','".mysql_real_escape_string($total)."')";
		$Total += $total;

		mysql_query("UPDATE articulos SET stock=stock-".$cantidad[$i]." WHERE idarticulos='".mysql_real_escape_string($idarticulo[$i])."'");
	}

	# insertamos todos los articulos
	mysql_query("INSERT INTO ventas_articulos(idventa,idarticulo,precio,cantidad,total) VALUES ".implode(",", $sql));

	if (!empty($_POST['pagocon'])){

		if ( $pagocon >= $Total){
			$pagototal = $Total;
		} else {
			$pagototal = $pagocon;
		}

		mysql_query("INSERT INTO ventas_pagos SET idventa='".$idventa."',fecha='".$fecha."',hora='".$hora."',cantidad='".$pagototal."',metodo='".$metodo."'");

		$cambio = $pagocon - $Total;

		$errorMsg = '<div class="col-md-12">
				<div class="alert alert-success">
					<i class="fa fa-check"></i> Venta No: <strong>'.$idventa.'</strong> agregada, cambio para el cliente <strong>$ '.$cambio.' pesos</strong>
				</div>
			</div>';
	} else {
		$errorMsg = '<div class="col-md-12">
				<div class="alert alert-success">
					<i class="fa fa-check"></i> Venta No: <strong>'.$idventa.'</strong> agregada.</strong>
				</div>
			</div>';
	}

	// if ( isset($_POST['e_nombre']) ){
		$e_nombre 			= mysql_real_escape_string($_POST['e_nombre']);
		$e_direccion 		= mysql_real_escape_string($_POST['e_direccion']);
		$e_fechaentrega 	= mysql_real_escape_string($_POST['e_fechaentrega']);
		$e_horaentrega 		= mysql_real_escape_string($_POST['e_horaentrega']);
		$e_referencia 		= mysql_real_escape_string($_POST['e_referencia']);
		$e_colonia 			= mysql_real_escape_string($_POST['e_colonia']);
		$e_codigopostal 	= mysql_real_escape_string($_POST['e_codigopostal']);
		$e_mensaje 			= mysql_real_escape_string($_POST['e_mensaje']);
		$comen  			= mysql_real_escape_string($_POST['comen']);

	// 	$query = "SELECT * FROM destinatarios WHERE idventa='".$id."' LIMIT 1";
	// 	if ( mysql_num_rows(mysql_query($query)) ){
	// 		mysql_query("UPDATE destinatarios SET comen='".$comen."', nombre='".$e_nombre."',direccion='".$e_direccion."',fechaentrega='".$e_fechaentrega."',horaentrega='".$e_horaentrega."',referencia='".$e_referencia."',colonia='".$e_colonia."',codigopostal='".$e_codigopostal."',mensaje='".$e_mensaje."' WHERE idventa='".$id."'") or die(mysql_error());
	// 	} else {
			mysql_query("INSERT INTO destinatarios SET comen='".$comen."',nombre='".$e_nombre."',direccion='".$e_direccion."',fechaentrega='".$e_fechaentrega."',horaentrega='".$e_horaentrega."',referencia='".$e_referencia."',colonia='".$e_colonia."',codigopostal='".$e_codigopostal."',mensaje='".$e_mensaje."',idventa='".$idventa."'") or die(mysql_error());
	// 	}
	//
	// }

	echo "<script>top.location.href='admin.php?m=pventaVer&id=".$idventa."';</script>";



}
?>
<form class="bs-example form-horizontal" role="form" action="" id="formaVenta" method="post">
	<div class="row">
		<?php echo @$errorMsg; ?>
		<div class="col-md-4">
			<section class="panel panel-default">
				<header class="panel-heading">
					<i class="fa fa-user icon"></i> Seleccionar Cliente
				</header>
				<div class="panel-body">
					<a href="#" class="btn btn-success btn-sm pull-right m-b agregarCliente"> <i class="fa fa-plus"></i> Agregar Cliente</a>
					<select class="form-control" name="cliente" id="cliente" style="width:100%;">
						<option></option>
					</select>

					<div class="alert alert-warning m-t" style="display:none;" id="errorCliente">
						<i class="fa fa-warning"></i> Favor de seleccionar un cliente.
					</div>
				</div>
			</section>





			<section class="panel panel-default">
				<header class="panel-heading">
					<i class="fa fa-usd icon"></i> Informacion de Venta
				</header>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-12">
							<table class="table table-striped">
								<tr>
									<th width="200">Subtotal: </th>
									<td class="text-right">
										$ <span id="subtotal"> 0.00 </span> pesos
										<input type="hidden" id="subtotalOculto" value=""/>
									</td>
								</tr>
								<tr>
									<th width="200">
										<label class="control-label"><strong>Descuento: ($) </strong></label>
									</th>
									<td>
										<div class="form-group">
											<input type="text" class="form-control input-md text-right descuento" id="descuento" name="descuento" value="0" />
										</div>
									</td>
								</tr>
								<tr>
									<th width="200">Total: </th>
									<td class="text-right"> $ <span id="total"> 0.00 </span> pesos</td>
								</tr>
								<tr>
									<th width="200">
										<label class="control-label"><strong>Metodo de Pago: </strong></label>
									</th>
									<td>
										<div class="form-group">
											<select name="metodo" class="form-control">
												<option>Efectivo</option>
												<option>Tarjeta Debido/Credito</option>
												<option>Oxxo</option>
												<option>Paypal</option>
												<option>TEF</option>
												<option>Credito</option>
											</select>
										</div>
									</td>
								</tr>
								<tr>
									<th width="200">
										<label class="control-label"><strong>Pago Con: </strong></label>
									</th>
									<td>
										<div class="form-group">
											<input type="text" class="form-control input-md text-right" id="pagocon" name="pagocon" value="" />
										</div>
									</td>
								</tr>
								<tr>
									<th width="200">Cambio: </th>
									<td width="300" class="text-right"> $ <span id="cambio"> 0.00 </span> pesos</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="line line-dashed line-lg pull-in"></div>
					<button type="submit" id="finalizar" class="btn btn-md btn-success btn-block"><i class="fa fa-check icon"></i> Finalizar Venta</button>
					<a href="admin.php?m=pventa" class="btn btn-sm btn-danger btn-block"><i class="fa fa-times icon"></i> Cancelar</a>
				</div>
			</section>
		</div>
		<div class="col-md-8">
			<section class="panel panel-default">
				<header class="panel-heading">
					<i class="fa fa-shopping-cart icon"></i> Agregar Articulo
				</header>
				<div class="panel-body">
					<div class="alert alert-warning m-t" style="display:none;" id="errorArticulo">
						<i class="fa fa-warning"></i> Favor de seleccionar un articulo
					</div>
					<div class="row m-b">
						<div class="col-md-12">
							<a href="#" class="btn btn-success btn-sm m-b agregarArticulo"> <i class="fa fa-plus"></i> Registrar Articulo</a>
							<select class="form-control input-md" id="articulo" style="width:100%;">
								<option></option>
							</select>
						</div>
					</div>
					<div class="table-responsive">
						<table class="table table-striped" id="productos">
							<tr>
								<th>Articulo</th>
								<th width="120">Precio U.</th>
								<th width="100">Cantidad</th>
								<th width="120">Total</th>
								<th width="80"></th>
							</tr>
						</table>
					</div>

				</div>

			</section>
			<!-- <form class="bs-example form-horizontal" action="" method="post"> -->
				<div class="row">
					<div class="col-md-12">
						<section class="panel panel-default">
							<header class="panel-heading">
								<i class="fa fa-archive icon"></i> Datos de Entrega
							</header>
							<div class="panel-body">
								<div class="alert alert-warning m-t" style="display:none;" id="errorNombre">
									<i class="fa fa-warning"></i> Favor de introducir un nombre.
								</div>
								<div class="alert alert-warning m-t" style="display:none;" id="errorDir">
									<i class="fa fa-warning"></i> Favor de introducir una direccion.
								</div>
								<div class="alert alert-warning m-t" style="display:none;" id="errorFecha">
									<i class="fa fa-warning"></i> Favor de introducir una fecha.
								</div>
								<div class="alert alert-warning m-t" style="display:none;" id="errorHora">
									<i class="fa fa-warning"></i> Favor de introducir una Hora.
								</div>
								<div class="alert alert-warning m-t" style="display:none;" id="errorMensaje">
									<i class="fa fa-warning"></i> Favor de introducir un mensaje.
								</div>
								<div class="col-md-6">
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
									<label class="col-md-4 control-label"><strong>Comentarios</strong></label>
									<div class="col-md-8"><textarea style="height:100px;" class="form-control" name="comen" id="comen"></textarea></div>
								</div>
								</div>
								<div class="col-md-6">
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
							</div>
							</div>
						</section>

					</div>
				</div>
			<!-- </form> -->
		</div>

	</div>
</form>

<div class="modal fade" id="modal-clientes">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<h3 class="m-t-none m-b">Agregar Cliente</h3>
						<form role="form" action="" method="post" id="formCliente">
							<div class="form-group">
								<div class="row">
									<label class="col-md-4 control-label"><strong>Nombre</strong></label>
									<div class="col-md-8"><input type="text" class="form-control" name="nombre"></div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<label class="col-md-4 control-label"><strong>Tel&eacute;fono</strong></label>
									<div class="col-md-8"><input type="text" class="form-control" name="telefono" ></div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<label class="col-md-4 control-label"><strong>Correo</strong></label>
									<div class="col-md-8"><input type="text" class="form-control" name="correo"></div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<label class="col-md-4 control-label"><strong>Direcci&oacute;n</strong></label>
									<div class="col-md-8"><input type="text" class="form-control" name="direccion"></div>
								</div>
							</div>
							<div class="checkbox m-t-lg">
								<a class="btn btn-sm btn-default m-t-n-xs" id="cancelar"> <i class="fa fa-times"></i> <strong>Cancelar</strong></a>
								<button type="submit" class="btn btn-sm pull-right btn-success m-t-n-xs" id="submitCliente"> <i class="fa fa-check"></i> <strong>Agregar Cliente</strong></button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>

<div class="modal fade" id="modal-articulo">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<h3 class="m-t-none m-b">Agregar Articulo</h3>
						<form role="form" action="" class="bs-example form-horizontal" method="post" id="formArticulo">
							<div class="form-group">
								<label class="col-md-3 control-label">Nombre del Articulo</label>
								<div class="col-md-9"><input type="text" name="articulo_nombre" class="form-control" placeholder=""></div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Precio</label>
								<div class="col-md-9">
									<div class="input-group">
										<span class="input-group-addon">$</span>
										<input type="text" name="articulo_precio" class="form-control">
										<span class="input-group-addon"> pesos </span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Descripci&oacute;n</label>
								<div class="col-md-9"><textarea class="form-control" name="articulo_descripcion" style="height:150px;" placeholder=""></textarea></div>
							</div>
							<div class="checkbox m-t-lg">
								<a class="btn btn-sm btn-default m-t-n-xs" id="cancelar2"> <i class="fa fa-times"></i> <strong>Cancelar</strong></a>
								<button type="submit" class="btn btn-sm pull-right btn-success m-t-n-xs" id="submitArticulo"> <i class="fa fa-check"></i> <strong>Agregar Articulo</strong></button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>


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
								<a class="btn btn-sm btn-default m-t-n-xs" id="cancelar3"> <i class="fa fa-times"></i> <strong>Cancelar</strong></a>
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
function actualizarSaldos(){
	var total = 0;
	$(".totalArticulo").each(function(){
		total += parseFloat( $(this).html() );
	});

	$("#subtotal").html(total);
	$("#subtotalOculto").val(total);

	if ( $("#descuento").val() != "0"){
		var descuento 	= $("#descuento").val();
		var total2 		= $("#subtotalOculto").val();
		//var result = (descuento / 100) * total2;

		$("#total").html( total2 - descuento );
	} else {
		$("#total").html(total);
	}

}

function actualizarTabla(id){
	var cuantos = $("#productos tr").length;

	$.ajax({
  		dataType: "json",
  		url: "php/ajax/articulo.php?q="+id,
  		success: function(articulo){
  			var nuevaFila = '<tr>'+
                        '<td>'+articulo[0].articulo+
                        	'<input type="hidden" name="idarticulo[]" value="'+id+'">'+
                        '</td>'+
						'<td class="text-right v-middle"><input type="text" name="precio[]" id="precio_'+id+'" data-id="'+id+'" value="'+articulo[0].precio+'" class="form-control precioArticulo text-right"></td>'+
						'<td class="text-right v-middle"><input type="text" name="cantidad[]" value="1" id="cantidad_'+id+'" data-id="'+id+'" class="form-control cantidad text-right"></td>'+
						'<td class="text-right v-middle">$ <span class="totalArticulo" id="total_'+id+'">'+articulo[0].precio+'</span></td>'+
                        '<td class="text-right"><a href="#" class="btn btn-sm btn-danger clsEliminarFila"> <i class="fa fa-times"></i> </a></td>'+
                    '</tr>';
    		$('table#productos tr:last').after(nuevaFila);
    		actualizarSaldos();
  		}
	});
}
	$(function(){

		/* articulo */
		$(".agregarArticulo").click(function(){
			$("#modal-articulo").modal("show");
		});

		$("#cancelar2").click(function(){
			$("#modal-articulo").modal("hide");
		});

		$("#submitArticulo").click(function(e){
			e.preventDefault();

			var datos = $("#formArticulo").serialize();

			$.ajax({
				method: "post",
		  		data: datos,
		  		url: "php/ajax/post.articulo.php",
		  		success: function(x){

		  			//	$("#cliente").attr('value',x.oid);
		  			//	$("#cliente").val(x.nombre);

		  				$("#modal-articulo").modal("hide");
		  			//}
		  		}
			});
		});

		/* cliente */
		$(".agregarCliente").click(function(){
			$("#modal-clientes").modal("show");
		});

		$("#cancelar").click(function(){
			$("#modal-clientes").modal("hide");
		});

		$("#submitCliente").click(function(e){
			e.preventDefault();

			var datos = $("#formCliente").serialize();

			$.ajax({
				method: "post",
		  		data: datos,
		  		url: "php/ajax/post.cliente.php",
		  		success: function(x){

		  			//	$("#cliente").attr('value',x.oid);
		  			//	$("#cliente").val(x.nombre);

		  				$("#modal-clientes").modal("hide");
		  			//}
		  		}
			});
		});

		$("#cliente").select2({
  			ajax: {
    			url: 'php/ajax/clientes.php',
    			dataType: "json",
    			data: function( params ){
	    			return {
	    				q: params.term
	    			};
	    		},
	    		processResults: function (data, params) {
	      			return {
        				results: data,
        			};
	    		},
	    		cache: true
  			},
  			placeholder: "Selecciona un cliente...",
		});

		var $articulo = $("#articulo").select2({
  			ajax: {
    			url: 'php/ajax/articulos.php',
    			dataType: "json",
    			data: function( params ){
	    			return {
	    				q: params.term
	    			};
	    		},
	    		processResults: function (data, params) {
	      			return {
        				results: data,
        			};
	    		},
	    		cache: true
  			},
  			placeholder: "Selecciona un articulo...",
		});

		$articulo.on("select2:select", function (e) {
			actualizarTabla(e.params.data.id)
			$articulo.val(null).trigger("change");
		});

		$(document).on('click','.clsEliminarFila',function(){
			var objFila = $(this).parents().get(1);
			$(objFila).remove();
			actualizarSaldos();
		});

		/* actualizar precio */
		$(document).on('keyup', '.precioArticulo', function(){
			var este = $(this).val();
			var id   = $(this).data("id");
			var cantidad = $("#cantidad_"+id).val();

			$("#total_"+ id).html( este * cantidad );

			actualizarSaldos();
		});

		/* descuento */
		$(document).on('keyup','.descuento',function(){
			var descuento 	= $(this).val()
			var total 		= $("#subtotalOculto").val();

			//var result = (descuento / 100) * total;

			$("#total").html( total - descuento);
		});

		$(document).on('keyup','.cantidad',function(){
			var este 	= $(this).val()
			var id 		= $(this).data("id");
			var precio 	= $("#precio_"+id).val();

			$("#total_"+id).html( parseFloat(este) * parseFloat(precio) );
			actualizarSaldos();
		});

		$(document).on('keyup', '#pagocon', function(){
			var total = $("#total").html();
			var pago  = $(this).val();

			var resta = parseFloat(pago) - parseFloat(total);
			$("#cambio").html(resta);
		});

		$("#finalizar").click(function(e){
			e.preventDefault();
			//alert($("#productos tr").length);

			if ($("#pagocon").val() == ""){
				// mensaje de que selecicone cliente
				// return false;
				if ($("#cliente").val() != ""){
				if($("#e_nombre").val() != ""){
					if($("#e_direccion").val() != ""){
						if($("#e_fechaentrega").val() != ""){
							if($("#e_horaentrega").val() != ""){
								if($("#e_mensaje").val() != ""){
									if($("#productos tr").length > 1){
										$("#formaVenta").submit();
									}else{$("#errorArticulo").show(); $("#errorMensaje").hide();}
								}else{$("#errorMensaje").show(); $("#errorHora").hide();}
							}else{$("#errorHora").show(); $("#errorFecha").hide(); $("#errorDir").hide();}
						}else{$("#errorFecha").show(); $("#errorDir").hide();}
					}else{$("#errorDir").show(); $("#errorNombre").hide();}
				}else{$("#errorNombre").show(); $("#errorCliente").hide();}
			}else{$("#errorCliente").show();}


			} else {
				$("#formaVenta").submit();
			}


		});

	});

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

		$("#cancelar3").click(function(){
			$("#modal-datos").modal("hide");
		});

	});

</script>
