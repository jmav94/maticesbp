<?php
if ( isset($_GET['daterange']) ){
	$buscar = mysql_real_escape_string($_GET['daterange']);
	$date = explode(" - ", $buscar);
} else {
	$buscar = $fechaHoy." - ".$fechaHoy;
}
?>
<section class="panel panel-default pos-rlt clearfix">

	<header class="panel-heading"> <i class="fa fa-calendar"></i> Cuentas por cobrar</header>
	
	<div class="row wrapper">
		<div class="col-xs-12 col-md-3 m-b-xs">
			<form id="reportesForm" action="" method="get">
				<input type="hidden" value="porcobrar" name="m">
				<div class="input-group m-b">
					<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					<input type="text" id="daterange" class="form-control btn-sm" name="daterange" value="<?php echo $buscar; ?>" />
				</div>
			</form>
		</div>
	</div>

	<div class="table-responsive">
		<table class="table table-striped b-t b-light">
			<thead>
				<tr>
					<th width="100"># Orden</th>
					<th width="150"> Fecha de pago </th>
					<th>Comentarios</th>
					<th>Cliente</th>
					<th width="100"> </th>
				</tr>

			<tbody>

<?php
			if ( isset($_GET['daterange']) ){
				$query = "SELECT 
					ventas_creditos.comentarios,
					ventas_creditos.fecha,
					ventas_creditos.idventa,
					clientes.nombre
					FROM ventas_creditos 
					JOIN ventas ON ventas.idventas=ventas_creditos.idventa
					JOIN clientes ON clientes.idclientes=ventas.idcliente
					WHERE ventas_creditos.fecha BETWEEN '".$date[0]."' AND '".$date[1]."'
					ORDER BY ventas_creditos.idcreditos DESC";
				$url = "admin.php?m=porcobrar&buscar=".$buscar;
				#LEFT JOIN clientes ON clientes.idclientes=ventas.idcliente
			} else {

				$query = "SELECT 
					ventas_creditos.comentarios,
					ventas_creditos.fecha,
					ventas_creditos.idventa,
					clientes.nombre
					FROM ventas_creditos
					JOIN ventas ON ventas.idventas=ventas_creditos.idventa
					JOIN clientes ON clientes.idclientes=ventas.idcliente
					WHERE ventas_creditos.fecha='".$fechaHoy."'
					ORDER BY ventas_creditos.idcreditos DESC";
				$url = "admin.php?m=porcobrar";
				
			}

			

##### PAGINADOR #####
$rows_per_page = 30;

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
</script>