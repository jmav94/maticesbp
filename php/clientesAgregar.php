<?php

if ( isset($_POST['nombre']) ){

	$nombre 	    = mysql_real_escape_string($_POST['nombre']);
	$correo 	    = mysql_real_escape_string($_POST['correo']);
	$estado          = mysql_real_escape_string($_POST['estado']);
	$pais  	        = mysql_real_escape_string($_POST['pais']);
	$domicilio  	= mysql_real_escape_string($_POST['domicilio']);
	$telefono  		= mysql_real_escape_string($_POST['telefono']);
	$colonia  	    = mysql_real_escape_string($_POST['colonia']);
	$cp  		    = mysql_real_escape_string($_POST['cp']);
	$celular  	    = mysql_real_escape_string($_POST['celular']);
	$rfc  	    	= mysql_real_escape_string($_POST['rfc']);
	$contacto     	= mysql_real_escape_string($_POST['contacto']);
	$comentarios   	= mysql_real_escape_string($_POST['comentarios']);

	if ( mysql_query("INSERT INTO clientes SET 
		fecharegistro='".date("Y-m-d")."',
		nombre='".$nombre."',
		correo='".$correo."',
		estado='".$estado."',
		pais='".$pais."',
		domicilio='".$domicilio."',
		colonia='".$colonia."',
		codigopostal='".$cp."',
		telefono='".$telefono."',
		celular='".$celular."',
		rfc='".$rfc."',
		contacto='".$contacto."',
		comentarios='".$comentarios."'
		")){
		$errorMsg = '<div class="alert alert-success">
				<i class="fa fa-check"></i> Cliente agregado correctamente.
			</div>';
	} else {
		$errorMsg = '<div class="alert alert-danger">
			<i class="fa fa-times"></i> Error, intenta nuevamente.
		</div>';

	}
}

?>
		<section class="panel panel-default">
			<header class="panel-heading">
				<i class="fa fa-user icon"></i> Agregar Cliente
			</header>
			<div class="panel-body">
				<form class="bs-example form-horizontal" action="" method="post">
					<?php echo $errorMsg; ?>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="col-lg-3 col-md-3 control-label">Nombre / Razon Social</label>
								<div class="col-lg-9 col-md-9"><input type="text" name="nombre" class="form-control" placeholder=""></div>
							</div>
							<div class="form-group">
								<label class="col-lg-3 col-md-3 control-label">Correo</label>
								<div class="col-lg-9 col-md-9"><input type="text" name="correo" class="form-control" placeholder=""></div>
							</div>
							<div class="form-group">
								<label class="col-lg-3 col-md-3 control-label">Domicilio</label>
								<div class="col-lg-9 col-md-9"><input type="text" name="domicilio" class="form-control" placeholder=""></div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Colonia</label>
								<div class="col-md-9"><input type="text" name="colonia" class="form-control" placeholder=""></div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">RFC</label>
								<div class="col-md-9"><input type="text" name="rfc" class="form-control" value=""></div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Comentarios</label>
								<div class="col-md-9"><textarea name="comentarios" class="form-control" rows="10"></textarea></div>
							</div>
						</div>	
						<div class="col-md-6">
							<div class="form-group">
								<label class="col-lg-3 col-md-3 control-label">Telefono</label>
								<div class="col-lg-9 col-md-9 "><input type="text" name="telefono" class="form-control" placeholder=""></div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="col-md-6 control-label">Estado</label>
										<div class="col-md-6"><input type="text" name="estado" class="form-control" value=""></div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="col-md-6 control-label">País</label>
										<div class="col-md-6"><input type="text" name="pais" class="form-control" value="Mexico"></div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">C.P.</label>
								<div class="col-md-9"><input type="text" name="cp" class="form-control" placeholder=""></div>
							</div>
							<div class="form-group">
								<label class="col-lg-3 col-md-3 control-label">Celular</label>
								<div class="col-lg-9  col-md-9 "><input type="text" name="celular" class="form-control" placeholder=""></div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Contacto</label>
								<div class="col-md-9"><input type="text" name="contacto" class="form-control" value=""></div>
							</div>
						</div>	
					</div>

					<div class="line line-dashed line-lg pull-in"></div>
					<div class="form-group text-right">
						<div class="col-lg-12"> 
							<button type="submit" class="btn btn-md btn-success"><i class="fa fa-check icon"></i> Agregar</button>
							<a href="admin.php?m=clientes" class="btn btn-md btn-danger"><i class="fa fa-times icon"></i> Cancelar</a>
						</div>
					</div>
				</form>
			</div>
		</section>
