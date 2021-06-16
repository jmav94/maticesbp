<?php

$id = mysql_real_escape_string($_GET['id']);

if ( isset($_POST['pagocon']) ){
	
	$fecha 	  = date("Y-m-d");
	$cliente  = mysql_real_escape_string($_POST['cliente']);
	$pagocon  = mysql_real_escape_string($_POST['pagocon']);

	mysql_query("UPDATE ventas SET idcliente='".$cliente."',pagocon='".$pagocon."' WHERE idventas='".$id."'") or die(mysql_error());
	mysql_query("DELETE FROM ventas_articulos WHERE idventa='".$id."'");

	$idarticulo = $_POST['idarticulo'];
	$precio 	= $_POST['precio'];
	$cantidad 	= $_POST['cantidad'];
	$sql 		= array();

	$Total = "";
	for ($i=0; $i < count($idarticulo); $i++) { 
		$total = $cantidad[$i] * $precio[$i];
		$sql[] = "(".$id.",'".mysql_real_escape_string($idarticulo[$i])."','".mysql_real_escape_string($precio[$i])."','".mysql_real_escape_string($cantidad[$i])."','".mysql_real_escape_string($total)."')";
		$Total += $total;
	}

	mysql_query("INSERT INTO ventas_articulos(idventa,idarticulo,precio,cantidad,total) VALUES ".implode(",", $sql));
	
	#if ( !empty($_POST['anticipo']) ){
	#	mysql_query("INSERT INTO pagos SET ordenId='".$ordenId."',fecha='".$fecha."',descripcion='Anticipo',cantidad='".$anticipo."',metodopago='".$metodopago."'");
	#}

	$cambio = $pagocon - $Total;

	$errorMsg = '<div class="col-md-12">
				<div class="alert alert-success">
					<i class="fa fa-check"></i> Venta actalizada: <strong>'.$id.'</strong> agregada, cambio para el cliente <strong>$ '.$cambio.' pesos</strong>
				</div>
			</div>';

}
$data    = mysql_fetch_object(mysql_query("SELECT * FROM ventas WHERE idventas='".$id."' LIMIT 1"));
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
					<select class="form-control" name="cliente" id="cliente" style="width:100%;">
						<option></option>
					</select>

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
					<i class="fa fa-usd icon"></i> Informacion de Venta
				</header>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-12">
							<table class="table table-striped">
								<tr>
									<th width="200">Total: </th>
									<td class="text-right"> $ <span id="total"> 0.00 </span> pesos</td>
								</tr>
								<tr>
									<th width="200">Pago Con: </th>
									<td>
										<div class="form-group">
											<input type="text" class="form-control input-md text-right" id="pagocon" name="pagocon" value="<?php echo $data->pagocon; ?>" />
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
					<button type="submit" id="finalizar" class="btn btn-md btn-success btn-block"><i class="fa fa-check icon"></i> Modificar Venta</button>
					<a href="admin.php?m=ordenes" class="btn btn-sm btn-danger btn-block"><i class="fa fa-times icon"></i> Cancelar</a>
				</div>
			</section>
		</div>
		<div class="col-md-8">
			<section class="panel panel-default">
				<header class="panel-heading">
					<i class="fa fa-shopping-cart icon"></i> Agregar Articulo
				</header>
				<div class="panel-body">
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
<?php
						$next  = 1; 
						$query = mysql_query("SELECT * 
							FROM ventas_articulos 
							JOIN articulos ON articulos.idarticulos=ventas_articulos.idarticulo
							WHERE idventa='".$data->idventas."' 
							ORDER BY idva ASC");
						while($q = mysql_fetch_object($query)){
							echo '<tr>
                        		<td>
                        			'.$q->articulo.'
                        			<input type="hidden" name="idarticulo[]" value="'.$q->idarticulo.'">
                        			<input type="hidden" name="precio[]" value="'.$q->precio.'">
                        		</td>
								<td class="text-right v-middle">
									$ <span class="precioArticulo">'.$q->precio.'</span>
								</td>
								<td class="text-right v-middle">
									<input type="text" name="cantidad[]" value="1" data-precio="'.$q->precio.'" data-oid="'.$next.'" class="form-control cantidad text-right">
								</td>
								<td class="text-right v-middle">$ <span class="totalArticulo" id="total_'.$next.'">'.$q->precio.'</span></td>
                        		<td class="text-right"><a href="#" class="btn btn-sm btn-danger clsEliminarFila"> <i class="fa fa-trash-o"></i> </a></td>
                    		</tr>';
                    		$next++;
						}
?>
						</table>						
					</div>
				</div>
			</section>
		</div>
	</div>
</form>
		


<script>
function actualizarSaldos(){
	var total = 0;
	$(".totalArticulo").each(function(){
		total += parseInt( $(this).html() );
	});
	$("#total").html(total);
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

function actualizarCambio(){
	var total = $("#total").html();
	var pago  = $("#pagocon").val();

	var resta = parseInt(pago) - parseInt(total);
	$("#cambio").html(resta);
}
	$(function(){

		actualizarSaldos();
		actualizarCambio();

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
				
				//return false;
			} else {
				$(".form-horizontal").submit();
			}

		});
		
	});
</script>