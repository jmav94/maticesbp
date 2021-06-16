<?php

if ( isset($_POST['nombre']) ){

	$nombre 	    = mysql_real_escape_string($_POST['nombre']);
	$privilegio 	= mysql_real_escape_string($_POST['privilegio']);
    $correo         = mysql_real_escape_string($_POST['correo']);
	$password 	    = mysql_real_escape_string($_POST['password']);

	if ( mysql_query("INSERT INTO usuarios SET nombre='".$nombre."',privilegio='".$privilegio."',email='".$correo."',password='".$password."'") ){
		$errorMsg = '<div class="alert alert-success">
				<i class="fa fa-check"></i> Usuario agregado correctamente.
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
				<i class="fa fa-user"></i> Agregar Usuario
			</header>
			<div class="panel-body">
				<form class="bs-example form-horizontal" action="" method="post">
					<?php echo $errorMsg; ?>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="col-md-3 control-label">Nombre</label>
								<div class="col-md-9"><input type="text" name="nombre" class="form-control"></div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
                           		<label class="col-md-3 control-label">Privilegio</label>
                           		<div class="col-md-9">
                           			<select class="form-control" name="privilegio">
                           				<option value="2">Usuario</option>
                           				<option value="1">Administrador</option>
                           			</select>
                           		</div>
                           </div>
						</div>	
					</div>
					<div class="row">
						<div class="col-md-6" >
							<div class="form-group">
								<label class="col-md-3 control-label">Correo</label>
								<div class="col-md-9"><input type="email" name="correo" class="form-control"></div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="col-md-3 control-label">Password</label>
							    <div class="col-md-9"><input type="text" name="password" class="form-control"></div>
							</div>
						</div>
					</div>
					<div class="line line-dashed line-lg pull-in"></div>
					<div class="form-group text-right">
						<div class="col-lg-12"> 
							<button type="submit" class="btn btn-md btn-success"><i class="fa fa-check icon"></i> Agregar</button>
							<a href="admin.php?m=usuarios" class="btn btn-md btn-danger"><i class="fa fa-times icon"></i> Cancelar</a>
						</div>
					</div>
				</form>
			</div>
		</section>
