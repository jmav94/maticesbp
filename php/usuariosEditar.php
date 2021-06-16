<?php

$id = mysql_real_escape_string($_GET['id']);

if ( isset($_POST['nombre']) ){

	$nombre 	    = mysql_real_escape_string($_POST['nombre']);
	$privilegio 	= mysql_real_escape_string($_POST['privilegio']);
    $correo         = mysql_real_escape_string($_POST['correo']);

    if (!empty($_POST['password'])){
    	$password   = mysql_real_escape_string($_POST['password']);
    	$sql 		= ",password='".$password."'";
    } else {
    	$sql 		= "";
    }
	

	if ( mysql_query("UPDATE usuarios SET nombre='".$nombre."',privilegio='".$privilegio."',email='".$correo."'".$sql." WHERE idusuarios='".$id."'") ){
		$errorMsg = '<div class="alert alert-success">
				<i class="fa fa-check"></i> Usuario agregado correctamente.
			</div>';
	} else {
		$errorMsg = '<div class="alert alert-danger">
			<i class="fa fa-times"></i> Error, intenta nuevamente.
		</div>';

	}
}

$data = mysql_fetch_object(mysql_query("SELECT * FROM usuarios WHERE idusuarios='".$id."' LIMIT 1"));
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
								<div class="col-md-9"><input type="text" name="nombre" class="form-control" value="<?php echo $data->nombre; ?>"></div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
                           		<label class="col-md-3 control-label">Privilegio</label>
                           		<div class="col-md-9">
                           			<select class="form-control" name="privilegio">
                           				<option value="2" <?php if ($data->privilegio == 2) echo "selected"; ?>>Usuario</option>
                           				<option value="1" <?php if ($data->privilegio == 1) echo "selected"; ?>>Administrador</option>
                           			</select>
                           		</div>
                           </div>
						</div>	
					</div>
					<div class="row">
						<div class="col-md-6" >
							<div class="form-group">
								<label class="col-md-3 control-label">Correo</label>
								<div class="col-md-9"><input type="email" name="correo" class="form-control" value="<?php echo $data->email; ?>"></div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="col-md-3 control-label">Password</label>
							    <div class="col-md-9"><input type="text" name="password" class="form-control" placeholder="teclea para cambiar"></div>
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
