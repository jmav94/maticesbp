<?php
@session_start();

if ( isset($_SESSION['userId']) ){
	header("Location: admin.php");
	die;
}

$errorMsg = "";

if ( isset($_POST['user']) ){
	include 'db.php';

	$user = ($_POST['user']);
	$pass = ($_POST['pass']);

	$consulta = "SELECT * FROM usuarios WHERE email='".$user."' AND password='".$pass."' LIMIT 1";
	if ( mysql_num_rows( mysql_query($consulta) )){

		$data 				= mysql_fetch_object(mysql_query($consulta));

		$_SESSION['userId'] = $data->idusuarios;
		$_SESSION['userNm'] = $data->email;
		$_SESSION['userPv'] = $data->privilegio;

		header("Location: admin.php");
	} else {
		$errorMsg = "<div class='row'>
						<div class='wrapper'>
							<div class='alert alert-danger'> <i class='fa fa-times'></i> <b>Usuario o contrase&ntilde;a incorrecta.</b></div>	
						</div>
					</div>";
	}
}

?>

<!DOCTYPE html>
<html lang="en" class="bg-dark">
<head> 
		<meta charset="utf-8" />
		<title>ROSEF POS</title>
		<meta name="description" content="app, web app, responsive, admin dashboard, admin, flat, flat ui, ui kit, off screen nav" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
		<link rel="stylesheet" href="css/app.v1.css" type="text/css" />
		<!--[if lt IE 9]>
		<script src="js/ie/html5shiv.js"></script>
		<script src="js/ie/respond.min.js"></script>
		<script src="js/ie/excanvas.js"></script>
		<![endif]-->
</head>

<body class="">
	<section id="content" class="m-t-lg wrapper-md animated fadeInUp">
		<div class="container aside-xxl">
			<section class="panel panel-default bg-white m-t-lg">
				<header class="panel-heading text-center">
					<strong>POS V1.0</strong>
				</header>
				<form action="" class="panel-body wrapper-lg" method="post">
					<div class="form-group">
						<input type="text" name="user" placeholder="Usuario" class="form-control input-lg">
					</div>
					<div class="form-group">
						<input type="password" name="pass" id="inputPassword" placeholder="Contrase&ntilde;a" class="form-control input-lg">
					</div>
					<button type="submit" class="btn btn-block btn-primary">Entrar</button>

					<?php echo $errorMsg; ?>
				</form>
			</section>
		</div>
	</section>
	<!-- footer -->
	<footer id="footer">
		<div class="text-center padder">
			<p> <small><a href="http://rosef.mx">ROSEF</a></small> </p>
		</div>
	</footer>
	<!-- / footer -->

	<!-- Bootstrap -->
	<!-- App -->
	<script src="js/app.v1.js"></script>
	<script src="js/app.plugin.js"></script>
</body>
</html>