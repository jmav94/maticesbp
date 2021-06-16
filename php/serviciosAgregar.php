<?php
 
if ( isset($_POST['cliente']) ){
	
	$fecha 	  = date("Y-m-d");
	$hora 	  = date("H:i:s");
	$cliente  = mysql_real_escape_string($_POST['cliente']);
	$pagocon  = mysql_real_escape_string($_POST['pagocon']);
	$metodo   = mysql_real_escape_string($_POST['metodo']);
	$descuento = mysql_real_escape_string($_POST['descuento']);

	# agregamos la venta
	mysql_query("INSERT INTO servicios SET fecha='".$fecha."',hora='".$hora."',idcliente='".$cliente."',descuento='".$descuento."',estatus='Pendiente',idusuario='".$_SESSION['userId']."'") or die(mysql_error());
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
	mysql_query("INSERT INTO servicios_articulos(idservicio,idarticulo,precio,cantidad,total) VALUES ".implode(",", $sql));

	if (!empty($_POST['pagocon'])){
		
		if ( $pagocon >= $Total){
			$pagototal = $Total;
		} else {
			$pagototal = $pagocon;
		}

		mysql_query("INSERT INTO servicios_pagos SET idservicio='".$idventa."',fecha='".$fecha."',hora='".$hora."',cantidad='".$pagototal."',metodo='".$metodo."'");

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

	echo "<script>top.location.href='admin.php?m=serviciosVer&id=".$idventa."';</script>";

}
?>
<form class="bs-example form-horizontal" action="" method="post">
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
										<label class="control-label"><strong>Descuento: (%) </strong></label>
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
					<div class="row">
						<div class="col-md-12">
							<a href="#" class="btn btn-success btn-sm m-b agregarServicio"> <i class="fa fa-plus"></i> Agregar Servicio</a>
						</div>
					</div>
					<div class="row m-b">
						<div class="col-md-12" >
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
								<th></th>
							</tr>
						</table>						
					</div>
				</div>
			</section>
		</div>
	</div>
</form>

<div class="modal fade" id="modal-servicios">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<h3 class="m-t-none m-b">Agregar Servicio</h3>
						<form role="form" action="" class="form-horizontal" method="post" id="formServicio">
							<div class="form-group">
								<label class="col-md-3 control-label">Nombre del Servicio</label>
								<div class="col-md-9"><input type="text" name="servicio" id="servicio" class="form-control" placeholder=""></div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Precio</label>
								<div class="col-md-9">
									<div class="input-group m-b">
										<span class="input-group-addon">$</span>
										<input type="text" name="precio" id="precio" class="form-control">
										<span class="input-group-addon"> pesos </span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Descripcion</label>
								<div class="col-md-9"><textarea class="form-control" id="descripcion" name="descripcion" style="height:150px;" placeholder=""></textarea></div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Observaciones</label>
								<div class="col-md-9"><textarea class="form-control" id="observaciones" name="observaciones" style="height:150px;" placeholder=""></textarea></div>
							</div>
							<div class="checkbox m-t-lg">
								<a class="btn btn-sm btn-default m-t-n-xs" id="cancelar2"> <i class="fa fa-times"></i> <strong>Cancelar</strong></a>
								<button type="submit" class="btn btn-sm pull-right btn-success m-t-n-xs" id="submitServicio"> <i class="fa fa-check"></i> <strong>Agregar Servicio</strong></button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>

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


<script>
function actualizarSaldos(){
	var total = 0;
	$(".totalArticulo").each(function(){
		total += parseInt( $(this).html() );
	});

	$("#subtotal").html(total);
	$("#subtotalOculto").val(total);

	if ( $("#descuento").val() != "0"){
		console.log("hay descuento");
		var descuento 	= $("#descuento").val();
		var total2 		= $("#subtotalOculto").val();
		var result = (descuento / 100) * total2;
		
		$("#total").html( total2 - result);
	} else {
		$("#total").html(total);	
	}
	
}

function actualizarTabla(id){
	var cuantos = $("#productos tr").length;
	var next 	= (cuantos+1);

	$.ajax({
  		dataType: "json",
  		url: "php/ajax/articulo.php?q="+id,
  		success: function(articulo){
  			var nuevaFila = '<tr>'+
                        '<td>'+articulo[0].articulo+
                        	'<input type="hidden" name="idarticulo[]" value="'+id+'">'+
                        	'<input type="hidden" name="precio[]" value="'+articulo[0].precio+'">'+
                        '</td>'+
						'<td class="text-right v-middle">$ <span class="precioArticulo">'+articulo[0].precio+'</span></td>'+
						'<td class="text-right v-middle"><input type="text" name="cantidad[]" value="1" data-precio="'+articulo[0].precio+'" data-oid="'+next+'" class="form-control cantidad text-right"></td>'+
						'<td class="text-right v-middle">$ <span class="totalArticulo" id="total_'+next+'">'+articulo[0].precio+'</span></td>'+
                        '<td class="text-right"><a href="#" class="btn btn-sm btn-danger clsEliminarFila"> <i class="fa fa-trash-o"></i> </a></td>'+
                    '</tr>';
    		$('table#productos tr:last').after(nuevaFila);
    		actualizarSaldos();
  		}
	});
}
	$(function(){

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

		$(".agregarServicio").click(function(){
			$("#modal-servicios").modal("show");
		});

		$("#cancelar2").click(function(){
			$("#modal-servicios").modal("hide");
		});

		$("#submitServicio").click(function(e){
			e.preventDefault();

			var datos = $("#formServicio").serialize();

			$.ajax({
				method: "post",
		  		data: datos,
		  		url: "php/ajax/post.servicio.php",
		  		success: function(x){

		  			$("#servicio").val("");
		  			$("#precio").val("");
		  			$("#descripcion").val("");
		  			$("#observaciones").val("");
		  				
		  			$("#modal-servicios").modal("hide");
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

		/* descuento */ 
		$(document).on('keyup','.descuento',function(){
			var descuento 	= $(this).val()
			var total 		= $("#subtotalOculto").val();

			var result = (descuento / 100) * total;
			
			$("#total").html( total - result);
		});

		$(document).on('keyup','.cantidad',function(){
			var este = $(this).val()
			var precio = $(this).data("precio");
			var ide = $(this).data("oid");
			var nuevo = parseInt(este) * parseInt(precio);
			
			$("#total_"+ide).html(nuevo);
			actualizarSaldos();
		});

		$(document).on('keyup', '#pagocon', function(){
			var total = $("#total").html();
			var pago  = $(this).val();

			var resta = parseInt(pago) - parseInt(total);
			$("#cambio").html(resta);
		});

		$("#finalizar").click(function(e){
			e.preventDefault();

			if ($("#pagocon").val() == ""){
				// mensaje de que selecicone cliente
				// return false;

				if ( $("#cliente").val() == ""){
					$("#errorCliente").show();
				} else {
					$(".form-horizontal").submit();
				}
				
			} else {
				$(".form-horizontal").submit();
			}

		});
		
	});
</script>