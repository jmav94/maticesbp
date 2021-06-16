<?php

$id = mysql_real_escape_string($_GET['id']);

if ( isset($_POST['nombre']) ){

	$nombre 		    = mysql_real_escape_string($_POST['nombre']);
	$precio  			= mysql_real_escape_string($_POST['precio']);
	$stock  	     	= mysql_real_escape_string($_POST['stock']);
	$alerta      		= mysql_real_escape_string($_POST['alerta']);
	$descripcion  	    = mysql_real_escape_string($_POST['descripcion']);
	$observaciones  	= mysql_real_escape_string($_POST['observaciones']);
	$componentes 		= @$_POST['componente'];
	$cantidad 			= @$_POST['cantidad'];
	
	if (!empty($_FILES)) {
     
	    $tempFile = $_FILES['file']['tmp_name'];
	    $filename = $_FILES['file']['name'];
	    $targetPath = "images/articulos/";
	    $targetFile =  $targetPath.$filename;
	 
	    if ( move_uploaded_file($tempFile,$targetFile) ) {
	    	$sql = "imagen='".$filename."',";
	    } else {
	    	$sql = "";
	    }
	}

	if ( mysql_query("UPDATE articulos SET ".$sql."articulo='".$nombre."',precio='".$precio."',stock='".$stock."',alerta='".$alerta."',descripcion='".$descripcion."',observaciones='".$observaciones."' WHERE idarticulos='".$id."'") ){

		if ( count($componentes) ){
			$sql = array();
			for($x = 0; $x < count($componentes); $x++){
				$sql[] = "('".$id."','".mysql_real_escape_string($componentes[$x])."','".mysql_real_escape_string($cantidad[$x])."')";
			}

			mysql_query("INSERT INTO articulos_componentes(idarticulo,componente,cantidad) VALUES ".implode(",", $sql));
		}

		$errorMsg = '<div class="alert alert-success">
				<i class="fa fa-check"></i> Articulo editado correctamente.
			</div>';
	} else {
		$errorMsg = '<div class="alert alert-danger">
			<i class="fa fa-times"></i> Error, intenta nuevamente.
		</div>';
	}

}

$data = mysql_fetch_object(mysql_query("SELECT * FROM articulos WHERE idarticulos='".$id."' LIMIT 1"));
?>
<section class="panel panel-default">
	<header class="panel-heading bg-light">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#home" data-toggle="tab"><i class="fa fa-plus"></i> Agregar articulo</a></li>
			<li><a href="#compo" data-toggle="tab">Componentes</a></li> 
		</ul>
	</header>
	<div class="panel-body">
		<form class="bs-example form-horizontal" action="" method="post" enctype="multipart/form-data">
			<div class="tab-content">
				<div class="tab-pane active" id="home">
					<?php echo $errorMsg; ?>
					<div class="col-md-3">
						<div class="form-group">
							<label class="">Imagen del Articulo</label><br>
<?php
					if ($data->imagen != ""){
						echo '<img src="images/articulos/'.$data->imagen.'" class="img-responsive thumbnail">';
					}
?>
							
							<input type="file" name="file" class="form-control" placeholder="">
						</div>
					</div>
					<div class="col-md-9">
						<div class="form-group">
							<label class="col-md-3 control-label">Nombre del Articulo</label>
							<div class="col-md-9"><input type="text" name="nombre" class="form-control" placeholder="" value="<?php echo $data->articulo; ?>"></div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-6 control-label">Precio</label>
									<div class="col-md-6">
										<div class="input-group m-b">
											<span class="input-group-addon">$</span>
											<input type="text" name="precio" class="form-control" value="<?php echo $data->precio; ?>">
											<span class="input-group-addon"> pesos </span>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label class="col-md-6 control-label">Stock</label>
											<div class="col-md-6"><input type="text" name="stock" class="form-control" placeholder="" value="<?php echo $data->stock; ?>"></div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label class="col-md-6 control-label">Alerta</label>
											<div class="col-md-6"><input type="text" name="alerta" class="form-control" value="<?php echo $data->alerta; ?>"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">Descripcion</label>
							<div class="col-md-9"><textarea class="form-control" name="descripcion" style="height:150px;" placeholder=""><?php echo $data->descripcion; ?></textarea></div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">Observaciones</label>
							<div class="col-md-9"><textarea class="form-control" name="observaciones" style="height:150px;" placeholder=""><?php echo $data->observaciones; ?></textarea></div>
						</div>
					</div>
				</div>
				<div class="tab-pane" id="compo">
					<div class="row">
						<div class="col-md-12">
							<a id="agregar" class="btn btn-sm btn-success"> <i class="fa fa-plus"></i> Agregar Componente</a>
							<br><br>
						</div>
					</div>
					<div class="table-responsive">
						<table id="imagenes" class="table table-bordered">	
							<tr>	
								<th>Nombre del Componente</th>
								<th width="120">Cantidad</th>
								<th width="50"></th>
							</tr>
<?php
					if (isset($_GET['del'])){
						$del = mysql_real_escape_string($_GET['del']);
						mysql_query("DELETE FROM articulos_componentes WHERE idcomponentes='".$del."'");
					}

					$query = mysql_query("SELECT * FROM articulos_componentes WHERE idarticulo='".$id."' ORDER BY idcomponentes ASC") or die(mysql_error());
					while($q = mysql_fetch_object($query)){
						echo '<tr>
							<td class="v-middle">'.$q->componente.'</td>
							<td class="v-middle text-center">'.$q->cantidad.'</td>
							<td>
								<a href="admin.php?m=inventarioEditar&id='.$id.'&del='.$q->idcomponentes.'#compo" class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i></a>
							</td>
						</tr>';
					}
?>
						</table>
					</div>
				</div>
			</div>

			<div class="col-md-12">
			<div class="clearfix"></div>
			<div class="line line-dashed line-lg pull-in"></div>
			
				<div class="form-group text-right">
					<button type="submit" class="btn btn-md btn-success"><i class="fa fa-check icon"></i> Agregar</button>
					<a href="admin.php?m=inventario" class="btn btn-md btn-danger"><i class="fa fa-times icon"></i> Cancelar</a>
				</div>
			</div>
		</form>
	</div>
</section>

<script type="text/javascript">
$(function(){
	$("#agregar").click(function(){
		var nuevaFila = '<tr>'+
								'<td class="v-middle">'+
									'<input type="text" name="componente[]" class="form-control">'+
								'</td>'+
								'<td class="v-middle">'+
									'<input type="text" name="cantidad[]" class="form-control">'+
								'</td>'+
                                '<td class="v-middle text-center" width="50px"><button class="btn btn-sm btn-danger clsEliminarFila"> <i class="fa fa-trash-o"></i></button></td>'+
                            '</tr>';
        $('table#imagenes tr:last').after(nuevaFila);
		return false;
	});

    $(document).on('click','.clsEliminarFila',function(){
		var objFila = $(this).parents().get(1);
		$(objFila).remove();
	});
});
</script>