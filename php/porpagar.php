<?php
if ( isset($_GET['daterange']) ){
	$buscar = mysql_real_escape_string($_GET['daterange']);
	$date = explode(" - ", $buscar);
} else {
	$buscar = date("Y-m-d")." - ".date("Y-m-d");
}

if ( isset($_GET['estado']) && $_GET['estado'] == "0"){
	$tipo = "Pendientes";
} else {
	$tipo = "Liquidadas";
}

if ( isset($_POST['idproveedor']) ){

	$idproveedor 	= mysql_real_escape_string($_POST['idproveedor']);
	$cantidad 		= mysql_real_escape_string($_POST['cantidad']);
	$abono 			= mysql_real_escape_string($_POST['abono']);
	$comentarios 	= mysql_real_escape_string($_POST['comentarios']);

	mysql_query("INSERT INTO cuentas SET fecha='".date("Y-m-d")."',idproveedor='".$idproveedor."',importe='".$cantidad."',comentarios='".$comentarios."'");

	if ($abono != "0"){
		$ref = mysql_insert_id();

		mysql_query("INSERT INTO cuentas_pagos SET fecha='".date("Y-m-d")."',idcuenta='".$ref."',cantidad='".$abono."'");
	}
}
?>
<section class="panel panel-default pos-rlt clearfix">

	<header class="panel-heading"> <i class="fa fa-usd"></i> Cuentas por pagar</header>
	
	<div class="row wrapper">
		<div class="col-md-9">
			<a href="#" id="agregar" class="btn btn-md btn-success"><i class="fa fa-plus"></i> Agregar Cuenta</a>
		</div>
		<div class="col-md-3">
			<form id="reportesForm" action="" method="get">
				<input type="hidden" value="porpagar" name="m">
				<input type="hidden" value="<?php echo @$_GET['estado']; ?>" name="estado">
				<div class="input-group m-b">
					<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					<input type="text" id="daterange" class="form-control btn-sm" name="daterange" value="<?php echo $buscar; ?>" />
				</div>
			</form>
		</div>
		<div class="col-md-8"></div>
	</div>

	<div class="table-responsive">
		<table class="table table-striped b-t b-light">
			<thead>
				<tr>
					<th width="120"> Fecha </th>
					<th>Proveedor</th>
					<th width="140" class="text-right">Total</th>
					<th width="140" class="text-right">Pagado</th>
					<th width="120" class="text-center">Estatus</th>
					<th width="120"></th>
				</tr>

			<tbody>

<?php
			if ( isset($_GET['borrar']) ){
				$borrar = mysql_real_escape_string($_GET['borrar']);
				mysql_query("DELETE FROM cuentas WHERE idcuentas='".$borrar."'");
				mysql_query("DELETE FROM cuentas_pagos WHERE idcuenta='".$borrar."'");
			}

			if ( isset($_GET['daterange']) ){
				$buscar = mysql_real_escape_string($_GET['daterange']);
				$date = explode(" - ", $buscar);
				
				$query = mysql_query("SELECT 
					(SELECT SUM(cantidad) FROM cuentas_pagos WHERE idcuenta=cuentas.idcuentas) as abono,
					cuentas.*,
					proveedores.nombre
					FROM cuentas
					JOIN proveedores ON proveedores.idproveedores=cuentas.idproveedor
					WHERE cuentas.fecha BETWEEN '".$date[0]."' AND '".$date[1]."'
					ORDER BY cuentas.idcuentas DESC");
			} else {
				$query = mysql_query("SELECT 
					(SELECT SUM(cantidad) FROM cuentas_pagos WHERE idcuenta=cuentas.idcuentas) as abono,
					cuentas.*,
					proveedores.nombre
					FROM cuentas
					JOIN proveedores ON proveedores.idproveedores=cuentas.idproveedor
					WHERE cuentas.fecha=CURDATE()
					ORDER BY cuentas.idcuentas DESC");
			}


			$abono = 0;
			$total = 0;
			while($q = mysql_fetch_object($query)){

				if ($q->abono >= $q->importe){
					$estado = '<label class="label label-success"> liquidado</label>';
				} else {
					$estado = '<label class="label label-warning"> pendiente</label>';
				}

				echo '<tr>
						<td>'.$q->fecha.'</td>
						<td>'.$q->nombre.'</td>
						<td class="text-right">$ '.$q->importe.' pesos </td>
						<td class="text-right">$ '.$q->abono.' pesos</td>
						<td class="text-center">'.$estado.'</td>
						<td class="text-right">
							<a href="admin.php?m=porpagarEditar&id='.$q->idcuentas.'" class="btn btn-sm btn-default m-r"> <i class="fa fa-eye"></i> </a>
							<a href="admin.php?m=porpagar&borrar='.$q->idcuentas.'" class="btn btn-sm btn-danger"> <i class="fa fa-trash-o"></i> </a>
						</td>
					</tr>';
				
				$abono += $q->abono;
				$total += $q->importe;
			}
?>
		    </tbody>
			</thead>
		</table>
	</div>
	<footer class="panel-footer">
		<div class="row">
			<div class="col-sm-12 text-right text-center-xs">
				<strong>Total Adeudo: $ <?php echo $total; ?> pesos</strong> | <strong>Total Abonado: $ <?php echo $abono; ?> pesos</strong>
			</div>
		</div>
	</footer>
</section>

<div class="modal fade" id="modal-agregar">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<h3 class="m-t-none m-b">Agregar Cuenta por Pagar</h3>
						<form role="form" action="" method="post">
							<div class="form-group">
								<label class="control-label"><strong>Proveedor</strong></label>
								<select name="idproveedor" class="form-control">
<?php
					$query = mysql_query("SELECT * FROM proveedores ORDER BY nombre ASC");
					while($q = mysql_fetch_object($query)){
						echo '<option value="'.$q->idproveedores.'">'.$q->nombre.'</option>';
					}
?>
								</select>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label"><strong>Cantidad</strong></label>
										<div class="input-group m-b">
											<span class="input-group-addon">$</span>
											<input type="text" name="cantidad" class="form-control">
											<span class="input-group-addon"> pesos </span>
										</div>
									</div>		
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label"><strong>Abono</strong></label>
										<div class="input-group m-b">
											<span class="input-group-addon">$</span>
											<input type="text" name="abono" value="0" class="form-control">
											<span class="input-group-addon"> pesos </span>
										</div>
									</div>		
								</div>
							</div>
							
							<div class="form-group">
								<label><strong>Comentarios</strong></label>
								<textarea class="form-control" name="comentarios" style="height:150px;"></textarea>
							</div>
							<div class="checkbox m-t-lg">
								<a class="btn btn-md btn-default m-t-n-xs" id="cancelar"> <i class="fa fa-times"></i> <strong>Cancelar</strong></a>
								<button type="submit" class="btn btn-md pull-right btn-success m-t-n-xs"> <i class="fa fa-check"></i> <strong>Agregar cuenta</strong></button>
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

	$("#agregar").click(function(){
		$("#modal-agregar").modal("show");
	});

	$("#cancelar").click(function(){
		$("#modal-agregar").modal("hide");
	});
	
	$('input[name="daterange"]').daterangepicker({
        	format: 'YYYY-MM-DD',
        	locale: {
            	applyLabel: 'Buscar',
        	    cancelLabel: 'Cancelar',
    	        fromLabel: 'De',
	            toLabel: 'A',
            	customRangeLabel: 'Custom',
        	    daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vie','Sa'],
    	        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        	}
    	});
    	$('#daterange').on('apply.daterangepicker', function(ev, picker) {
  			$("#reportesForm").submit();
		});
});
</script>