<?php
$date = ['',''];
if ( isset($_GET['daterange']) ){
	$buscar = mysql_real_escape_string($_GET['daterange']);
	$date = explode(" - ", $buscar);
} elseif (isset($_GET['estado']) && $_GET['estado'] == "0"){
	$buscar = date("2000-01-01")." - ".date("Y-m-d");
	$date = explode(" - ", $buscar);
	$tipo = "Pendientes";
}
	else {
	$tipo = "Liquidadas";
	$buscar = date("Y-m-01")." - ".date("Y-m-d");
	$date = explode(" - ", $buscar);
	}


?>
<section class="panel panel-default pos-rlt clearfix">

	<header class="panel-heading"> <i class="fa fa-bar-chart-o"></i> Reportes de Ventas <?php echo $tipo; ?></header>

	<div class="row wrapper">
		<div class="col-md-3">
			<form id="reportesForm" action="" method="get">
				<input type="hidden" value="reportes" name="m">
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
					<th width="80" class="text-center"> # </th>
					<th width="120"> Fecha </th>
					<th width="80"> Hora </th>
					<th>Cliente</th>
					<th width="140" class="text-right">Total</th>
					<th width="140" class="text-right">Pagado</th>
					<th width="140" class="text-center">Metodo</th>
					<th width="140" class="text-center">Estatus</th>
				</tr>

			<tbody>

<?php
			if ( isset($_GET['daterange']) ){
				$buscar = mysql_real_escape_string($_GET['daterange']);
				$date = explode(" - ", $buscar);

				$query = mysql_query("SELECT
					ventas.idventas,
					ventas.fecha,
					ventas.hora,
					clientes.nombre,
					( (SELECT SUM(total) FROM ventas_articulos WHERE idventa=ventas.idventas) - ventas.descuento) as total,
					(SELECT SUM(cantidad) FROM ventas_pagos WHERE idventa=ventas.idventas) as pagos,
					ventas_pagos.metodo
					FROM ventas
					LEFT JOIN clientes ON clientes.idclientes=ventas.idcliente
					LEFT JOIN ventas_pagos ON ventas_pagos.idventa=ventas.idventas
					WHERE ventas.fecha BETWEEN '".$date[0]."' AND '".$date[1]."'
					GROUP BY ventas.idventas
					ORDER BY ventas.idventas DESC") or die(mysql_error());

			} else {
				$query = mysql_query("SELECT
					ventas.idventas,
					ventas.fecha,
					ventas.hora,
					clientes.nombre,
					( (SELECT SUM(total) FROM ventas_articulos WHERE idventa=ventas.idventas) - ventas.descuento) as total,
					(SELECT SUM(cantidad) FROM ventas_pagos WHERE idventa=ventas.idventas) as pagos,
					ventas_pagos.metodo
					FROM ventas
					LEFT JOIN clientes ON clientes.idclientes=ventas.idcliente
					LEFT JOIN ventas_pagos ON ventas_pagos.idventa=ventas.idventas
					WHERE ventas.fecha BETWEEN '".$date[0]."' AND '".$date[1]."'
					GROUP BY ventas.idventas
					ORDER BY ventas.idventas DESC");
				

			}



			$suma = 0;
			$total = 0;
			$total2 = 0;

			while($q = mysql_fetch_object($query)){

				$total 	 = $q->total; //- $q->descuento;
				$suma 	+= $q->pagos;
				$total2 += $total;

				if ( @$_GET['estado'] == 1){
					if ($q->pagos >= $total){

						echo '<tr>
							<td class="text-center">'.$q->idventas.'</td>
							<td>'.$q->fecha.'</td>
							<td>'.$q->hora.'</td>
							<td>'.$q->nombre.'</td>
							<td class="text-right">$ '.$total.' pesos </td>
							<td class="text-right">$ '.$q->pagos.' pesos</td>
							<td class="text-center">'.$q->metodo.'</td>
							<td class="text-center"><label class="label label-success"> liquidado</label></td>
						</tr>';
					}
				} else {
					if ($q->pagos < $total){
						echo '<tr>
							<td class="text-center">'.$q->idventas.'</td>
							<td>'.$q->fecha.'</td>
							<td>'.$q->hora.'</td>
							<td>'.$q->nombre.'</td>
							<td class="text-right">$ '.$total.' pesos </td>
							<td class="text-right">$ '.$q->pagos.' pesos</td>
							<td class="text-center">'.$q->metodo.'</td>
							<td class="text-center"><label class="label label-warning"> pendiente</label></td>
						</tr>';
					}
				}

			}
?>
		    </tbody>
			</thead>
		</table>
	</div>
	<footer class="panel-footer">
		<div class="row">
			<div class="col-sm-12 text-right text-center-xs">
				<strong>Total Venta: $ <?php echo $total2; ?> pesos</strong> | <strong>Total Pagado: $ <?php echo $suma; ?> pesos</strong>
			</div>
		</div>
	</footer>
</section>
<script type="text/javascript">
$(function(){
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
