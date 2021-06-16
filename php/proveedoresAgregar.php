<?php

if ( isset($_POST['nombre']) ){

	$nombre 	    = mysql_real_escape_string($_POST['nombre']);
	$correo 	    = mysql_real_escape_string($_POST['correo']);
	$domicilio  	= mysql_real_escape_string($_POST['domicilio']);
	$telefono  		= mysql_real_escape_string($_POST['telefono']);
	$cp  		    = mysql_real_escape_string($_POST['cp']);
    $colonia  	    = mysql_real_escape_string($_POST['colonia']);
    $rfc  	    	= mysql_real_escape_string($_POST['rfc']);
    $contacto     	= mysql_real_escape_string($_POST['contacto']);
    $estado         = mysql_real_escape_string($_POST['estado']);
	$pais  	        = mysql_real_escape_string($_POST['pais']);


	if ( mysql_query("INSERT INTO proveedores SET 
		nombre='".$nombre."',
		correo='".$correo."',
		domicilio='".$domicilio."',
		colonia='".$colonia."',
		codigopostal='".$cp."',
		telefono='".$telefono."',
		rfc='".$rfc."',
		estado='".$estado."',
		pais='".$pais."',
		contacto='".$contacto."'
		")){
		$errorMsg = '<div class="alert alert-success">
				<i class="fa fa-check"></i> Proveedor agregado correctamente.
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
				<i class="fa fa-user icon"></i> Agregar Proveedor
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
										<div class="col-md-6"><input type="text" name="pais" class="form-control" value=""></div>
									</div>	
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Contacto</label>
								<div class="col-md-9"><input type="text" name="contacto" class="form-control" value=""></div>
							</div>							
						</div>	
						<div class="col-md-6">
							<div class="form-group">
								<label class="col-lg-3 col-md-3 control-label">Telefono</label>
								<div class="col-lg-9 col-md-9 "><input type="text" name="telefono" class="form-control" placeholder=""></div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">C.P.</label>
								<div class="col-md-9"><input type="text" name="cp" class="form-control" placeholder=""></div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Colonia</label>
								<div class="col-md-9"><input type="text" name="colonia" class="form-control" placeholder=""></div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">RFC</label>
								<div class="col-md-9"><input type="text" name="rfc" class="form-control" value=""></div>
							</div>	
						</div>	
					</div>

					<div class="line line-dashed line-lg pull-in"></div>
					<div class="form-group text-right">
						<div class="col-lg-12"> 
							<button type="submit" class="btn btn-md btn-success"><i class="fa fa-check icon"></i> Agregar</button>
							<a href="admin.php?m=proveedores" class="btn btn-md btn-danger"><i class="fa fa-times icon"></i> Cancelar</a>
						</div>
					</div>
				</form>
			</div>
		</section>
