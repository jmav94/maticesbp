<?php
if ( isset($_GET['daterange']) ){
	$buscar = mysql_real_escape_string($_GET['daterange']);
	$date = explode(" - ", $buscar);
} else {
	$buscar = $fechaHoy." - ".$fechaHoy;
}
?>
<section class="panel panel-default pos-rlt clearfix">

	<header class="panel-heading"> <i class="fa fa-archive"></i> Listado de Pedidos</header>

	<div class="row wrapper">
		<div class="col-xs-12 col-md-3 m-b-xs">
			<form id="reportesForm" action="" method="get">
				<input type="hidden" value="pedidos" name="m">
				<div class="input-group m-b">
					<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					<input type="text" id="daterange" class="form-control btn-sm" name="daterange" value="<?php echo $buscar; ?>" />
				</div>
			</form>
		</div>
	</div>

	<div class="table-responsive">
		<table id="p" class="table table-striped b-t b-light">
			<thead>
				<tr>
					<th width="80" class="text-center"> # </th>
					<th width="150"> Fecha de Entrega </th>
					<th width="150"> Hora de Entrega</th>
					<th>Cliente</th>
					<th>Para</th>
					<th>Direccion</th>
					<th>Articulos</th>
					<th width="140" class="text-center">Estatus</th>
					<th width="140" class="text-center">Pedido</th>
					<th width="100"> </th>
				</tr>

			<tbody>

<?php
			if ( isset($_GET['daterange']) ){
				$query = "SELECT
					ventas.idventas,
					ventas.estatus as pedido,
					ventas.fecha,
					ventas.hora,
					clientes.nombre,
					destinatarios.direccion as direccion2,
					destinatarios.nombre as nombre2,
					destinatarios.colonia as colonia2,
					destinatarios.codigopostal as codigopostal2,
					destinatarios.fechaentrega,
					destinatarios.horaentrega,
					( (SELECT SUM(total) FROM ventas_articulos WHERE idventa=ventas.idventas) - ventas.descuento) as total,
					(SELECT cantidad FROM ventas_articulos WHERE idventa=ventas.idventas LIMIT 1) as cantidad1,
					(SELECT SUM(cantidad) FROM ventas_pagos WHERE idventa=ventas.idventas) as pagos,
					(SELECT estado FROM ventas_estados WHERE idventa=ventas.idventas ORDER BY idestados DESC LIMIT 1) as estatus
					FROM ventas
					LEFT JOIN clientes ON clientes.idclientes=ventas.idcliente
					JOIN destinatarios ON destinatarios.idventa=ventas.idventas
					WHERE destinatarios.fechaentrega BETWEEN '".$date[0]."' AND '".$date[1]."'
					ORDER BY ventas.idventas DESC";
				$url = "admin.php?m=pedidos&buscar=".$buscar;
			} else {

				$query = "SELECT
					ventas.idventas,
					ventas.estatus as pedido,
					ventas.fecha,
					ventas.hora,
					clientes.nombre,
					destinatarios.direccion as direccion2,
					destinatarios.nombre as nombre2,
					destinatarios.colonia as colonia2,
					destinatarios.codigopostal as codigopostal2,
					destinatarios.fechaentrega,
					destinatarios.horaentrega,
					( (SELECT SUM(total) FROM ventas_articulos WHERE idventa=ventas.idventas) - ventas.descuento) as total,
					(SELECT cantidad FROM ventas_articulos WHERE idventa=ventas.idventas LIMIT 1) as cantidad1,
					(SELECT SUM(cantidad) FROM ventas_pagos WHERE idventa=ventas.idventas) as pagos,
					(SELECT estado FROM ventas_estados WHERE idventa=ventas.idventas ORDER BY idestados DESC LIMIT 1) as estatus
					FROM ventas
					LEFT JOIN clientes ON clientes.idclientes=ventas.idcliente
					JOIN destinatarios ON destinatarios.idventa=ventas.idventas
					WHERE destinatarios.fechaentrega='".$fechaHoy."'
					ORDER BY ventas.idventas DESC";
				$url = "admin.php?m=pedidos";

			}



##### PAGINADOR #####
$rows_per_page = 100;

if(isset($_GET['pag']))
	$page = mysql_real_escape_string($_GET['pag']);
else if (@$_GET['pag'] == "0")
	$page = 1;
else
	$page = 1;

$num_rows 		= mysql_num_rows(mysql_query($query));
$lastpage		= ceil($num_rows / $rows_per_page);
$page     = (int)$page;
if($page > $lastpage){
	$page = $lastpage;
}
if($page < 1){
	$page = 1;
}
$limit 		= 'LIMIT '. ($page -1) * $rows_per_page . ',' .$rows_per_page;
$query  .=" $limit";

$query = mysql_query($query) or die(mysql_error());
##### PAGINADOR #####
		$c =0;
			while($q = mysql_fetch_object($query)){
					$c = $c + 1;
				if($q->pedido == "Pendiente"){
					$estado = '<label class="label label-warning">Pendiente</strong>';
				} else if ($q->pedido == "En Proceso"){
					$estado = '<label class="label label-warning">En Proceso</strong>';
				} else if ($q->pedido  == "En Ruta"){
					$estado = '<label class="label label-info">En Ruta</strong>';
				} else if ($q->pedido  == "Entregado"){
					$estado = '<label class="label label-success">Entregado</strong>';
				} else if ($q->pedido  == "No Entregado"){
					$estado = '<label class="label label-danger">No Entregado</strong>';
				}

				echo '<tr>
					<td class="text-center v-middle">'.$q->idventas.'</td>
					<td class="v-middle">'.@$q->fechaentrega.'<br><i name="calendario"class="fa fa-calendar" id="calendario'.$c.'"></i></td>
					<td class="v-middle">'.@$q->horaentrega.'</td>
					<td class="v-middle">'.$q->nombre.'</td>
					<td class="v-middle">'.$q->nombre2.'</td>
					<td>'.$q->direccion2.'<br><strong>Colonia:</strong> '.$q->colonia2.'<br><strong>CP:</strong> '.$q->codigopostal2.'</td>';

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
				echo '<td class="v-middle">'.$articulo.'<strong>cantidad:</strong> '.$q->cantidad1.' </td>';
				if ($q->pagos >= $q->total){
					echo '<td class="text-center v-middle"><label class="label label-success"> liquidado</label></td>';
				} else {
					echo '<td class="text-center v-middle"><label class="label label-warning"> pendiente</label></td>';
				}
				echo '<td class="text-center v-middle">'.$estado.'</td>
					<td class="text-right v-middle">
						<a href="admin.php?m=pventaVer&id='.$q->idventas.'" class="btn btn-sm btn-info"> <i class="fa fa-archive"></i> </a>
					</td>
				</tr>';
			}
?>
		    </tbody>
			</thead>
		</table>
	</div>
	<footer class="panel-footer">
		<div class="row">
			<div class="col-sm-12 text-right text-center-xs">
				<ul class="pagination pagination-sm m-t-none m-b-none">
<?php

	if($num_rows != 0){
		$nextpage = $page + 1;
		$prevpage = $page - 1;

		if ($page == 1) {
			echo '<li class="disabled"><a href="#"><i class="fa fa-chevron-left"></i></a></li>';
			
			echo '<li class="active"><a href="">1</a></li>';
			
			for($i= $page+1; $i<= $lastpage ; $i++){
				echo '<li><a href="'.$url.'&pag='.$i.'">'.$i.'</a></li> ';
			}

			if($lastpage >$page ){
				echo '<li><a href="'.$url.'&pag='.$nextpage.'"><i class="fa fa-chevron-right"></i></a></li>';
			}else{	
				echo '<li class="disabled"><a href="#"><i class="fa fa-chevron-right"></i></a></li>';
			}
		} else {
			echo '<li><a href="'.$url.'&pag='.$prevpage.'"><i class="fa fa-chevron-left"></i></a></li>';
			
			for($i= 1; $i<= $lastpage ; $i++){
				if($page == $i){
					echo '<li class="active"><a href="#">'.$i.'</a></li>';
				} else {
					echo '<li><a href="'.$url.'&pag='.$i.'">'.$i.'</a></li> ';
				}
			}
         
			if($lastpage >$page ){
				echo '<li><a href="'.$url.'&pag='.$nextpage.'"><i class="fa fa-chevron-right"></i></a></li>';
			} else {
				echo '<li class="disabled"><a href="#"><i class="fa fa-chevron-right"></i></a></li>';
			}
		}
	} 
?>
				</ul>
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



$(function () {
	var filas = $('#p tr').length;
	for (var i = 0; i < filas; i++) {
		$('#calendario' + i).datepicker();
	}  });
</script>
