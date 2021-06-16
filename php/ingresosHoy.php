<?php
//Variable query especificando que en este reporte se usaran consultas largas
$query = mysql_query("SET SQL_BIG_SELECTS=1");
?>
<section class="panel panel-default pos-rlt clearfix">

	<header class="panel-heading"> <i class="fa fa-usd"></i> Reporte de Ingresos del dia de Hoy</header>
	<div class="table-responsive">
		<table class="table table-striped b-t b-light">
			<thead>
				<tr>
					<th>#</th>
					<th width="120"> Fecha </th>
					<th width="80"> Hora </th>
					<th>Cliente</th>
					<th>Total</th>
					<th>Estatus</th>
					<th>Metodo</th>
					<th width="180" class="text-left">Pagado</th>
				</tr>

			<tbody>

<?php
		$query = mysql_query("SELECT
				ventas.idventas,
				ventas.fecha,
				ventas.hora,
				clientes.nombre,
				( (SELECT SUM(total) FROM ventas_articulos WHERE idventa=ventas.idventas) - ventas.descuento) as total,
				(SELECT SUM(cantidad) FROM ventas_pagos WHERE idventa=ventas.idventas) as pagos,
			 ventas_pagos.metodo,
				ventas.estatus as pedido,
				destinatarios.fechaentrega
				FROM ventas
				LEFT JOIN clientes ON clientes.idclientes=ventas.idcliente
				LEFT JOIN destinatarios ON destinatarios.idventa=ventas.idventas
				LEFT JOIN ventas_pagos ON ventas_pagos.idventa=ventas.idventas
				WHERE ventas_pagos.fecha=CURDATE()
				GROUP BY ventas.idventas
				ORDER BY ventas.idventas DESC");
				$url = "admin.php?m=ingresos";
			
			$tventas = 0;
			$tpagos = 0;

			while($q = mysql_fetch_object($query)){
						echo '<tr>
						<td>'.$q->idventas.'</td>
							<td>'.$q->fecha.'</td>
							<td>'.$q->hora.'</td>
							<td>'.$q->nombre.'</td>
							<td>$ '.$q->total.'</td>';
							if ($q->pagos >= $q->total){
								echo '<td><label class="label label-success"> liquidado</label></td>';
							} else {
								echo '<td><label class="label label-warning"> pendiente</label></td>';
							}
							echo '<td class="text-center">'.$q->metodo.'</td>';
							echo '<td class="text-right">$ '.$q->pagos.' pesos</td>
						</tr>';
						$tventas += $q->total;
						$tpagos += $q->pagos;

			}

/*##### PAGINADOR #####
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
				##### PAGINADOR #####*/
?>
		    </tbody>
			</thead>
		</table>
	</div>
	<footer class="panel-footer">
		<div class="row">
			<div class="col-sm-7 text-right text-center-xs">
				<strong>Total Vendido: $ <?php echo $tventas; ?> pesos</strong></strong>
			</div>
			<div class="col-sm-5 text-right text-center-xs">
				<strong>Total: $ <?php echo $tpagos ?> pesos</strong></strong>`
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12 text-right text-center-xs">
				<ul class="pagination pagination-sm m-t-none m-b-none">
					
				</ul>
			</div>
		</div>
	</footer>
</section>