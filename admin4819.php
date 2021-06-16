<?php
@session_start();

if ( !isset($_SESSION['userId']) ){
	header("Location: index.php");
	die;
}

include 'db.php';

$fechaHoy = date("Y-m-d");

if ( isset($_GET['m']) ){
	switch($_GET['m']) {

		/* clientes */
		case "clientes":
			$paginaPHP = "php/clientes.php";
		break;
		case "clientesAgregar":
			$paginaPHP = "php/clientesAgregar.php";
		break;
		case "clientesEditar":
			$paginaPHP = "php/clientesEditar.php";
		break;
		case "clientePagos":
			$paginaPHP = "php/clientePagos.php";
		break;

		/* inventario */
		case "inventario":
			$paginaPHP = "php/inventario.php";
		break;
		case "inventarioAgregar":
			$paginaPHP = "php/inventarioAgregar.php";
		break;
		case "inventarioEditar":
			$paginaPHP = "php/inventarioEditar.php";
		break;

		/* punto de venta */
		case "pventa":
			$paginaPHP = "php/pventa.php";
		break;
		case "pventaAgregar":
			$paginaPHP = "php/pventaAgregar.php";
		break;
		case "pventaEditar":
			$paginaPHP = "php/pventaEditar.php";
		break;
		case "pventaVer":
			$paginaPHP = "php/pventaVer.php";
		break;

		/* servicios */
		case "servicios":
			$paginaPHP = "php/servicios.php";
		break;
		case "serviciosAgregar":
			$paginaPHP = "php/serviciosAgregar.php";
		break;
		case "serviciosEditar":
			$paginaPHP = "php/serviciosEditar.php";
		break;
		case "serviciosVer":
			$paginaPHP = "php/serviciosVer.php";
		break;

		/* pedidos */
		case "pedidos":
			$paginaPHP = "php/pedidos.php";
		break;

		/* ingresos */
		case "ingresos":
			$paginaPHP = "php/ingresos.php";
		break;

		/* reportes */
		case "reportes":
			$paginaPHP = "php/reportes.php";
		break;

		/* cuentas por cobrar */
		case "porcobrar":
			$paginaPHP = "php/porcobrar.php";
		break;

		/* cuentas por pagar */
		case "porpagar":
			$paginaPHP = "php/porpagar.php";
		break;
		case "porpagarEditar":
			$paginaPHP = "php/porpagarEditar.php";
		break;

		/* proveedores */
		case "proveedores":
			$paginaPHP = "php/proveedores.php";
		break;
		case "proveedoresAgregar":
			$paginaPHP = "php/proveedoresAgregar.php";
		break;
		case "proveedoresEditar":
			$paginaPHP = "php/proveedoresEditar.php";
		break;

		/* usuarios */
		case "usuarios":
			$paginaPHP = "php/usuarios.php";
		break;
		case "usuariosAgregar":
			$paginaPHP = "php/usuariosAgregar.php";
		break;
		case "usuariosEditar":
			$paginaPHP = "php/usuariosEditar.php";
		break;
	}
} else {
		$paginaPHP = "php/index.php";
}

$errorMsg = "";

?>
<!DOCTYPE html>
<html lang="en" class="app">
<head> <meta charset="utf-8" />
<title>POS v1.0</title>
<meta name="description" content="" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<link rel="stylesheet" href="css/app.v1.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/select2.min.css">
<link rel="stylesheet" type="text/css" href="css/datepicker.css">
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/1/daterangepicker-bs3.css" />
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css" />


<script src="js/app.v1.js"></script>
<!--[if lt IE 9]>
<script src="js/ie/html5shiv.js"></script>
<script src="js/ie/respond.min.js"></script>
<script src="js/ie/excanvas.js"></script>
<![endif]-->
<style type="text/css">
.datepicker-input{z-index:1151 !important;}
td,th { white-space: nowrap; }
.table-responsive{
	width: 100%;
    margin-bottom: 15px;
    overflow-x: auto;
    overflow-y: hidden;
}
</style>
</head>
<body class="">
	<section class="vbox">
		<header class=" dk header navbar navbar-fixed-top-xs" style="background:orange;">
			<div class="navbar-header aside-md">
				<a class="btn btn-link visible-xs" data-toggle="class:nav-off-screen,open" data-target="#nav,html"> <i class="fa fa-bars"></i> </a>
				<a href="#" class="navbar-brand" data-toggle="fullscreen" style="color:white;">POS V1.0</a>
				<a class="btn btn-link visible-xs" data-toggle="dropdown" data-target=".nav-user"> <i class="fa fa-cog"></i> </a>
			</div>
			<ul class="nav navbar-nav navbar-right m-n hidden-xs nav-user">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:white;"> <span class="thumb-sm avatar pull-left"> <img src="images/avatar_default.jpg"> </span > <?php echo $_SESSION['userNm']; ?> <b class="caret"></b> </a>
					<ul class="dropdown-menu animated fadeInRight"> <span class="arrow top"></span>
						<li class="divider"></li>
						<li> <a href="cerrar.php">Salir</a> </li>
					</ul>
				</li>
			</ul>
		</header>
		<section>
			<section class="hbox stretch">
				<!-- .aside -->
				<aside class="bg-black lter aside-md hidden-print hidden-xs <?php if ((@$_GET['m'] == "pventaAgregar") || (@$_GET['m'] == "pventaEditar") || (@$_GET['m'] == "serviciosAgregar") || (@$_GET['m'] == "serviciosEditar") || (@$_GET['m'] == "pventa") ) echo "nav-xs"; ?>" id="nav">
					<section class="vbox">
						<section class="w-f scrollable">
							<div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333">
								<!-- nav -->
								<nav class="nav-primary hidden-xs">
									<ul class="nav">
										<li><a href="admin.php"> <i class="fa fa-home"></i> <span>Inicio</span> </a> </li>
										<li><a href="admin.php?m=clientes"> <i class="fa fa-users"></i> <span>Clientes</span> </a> </li>
										<li><a href="admin.php?m=pventaAgregar"> <i class="fa fa-barcode"></i> <span>POS</span> </a> </li>
										<li><a href="admin.php?m=inventario"><i class="fa fa-tag"></i><span>Inventario</span></a></li>
										<li><a href="admin.php?m=pventa"><i class="fa fa-shopping-cart"></i><span>Historial de Venta</span></a></li>
										<li><a href="admin.php?m=pedidos"><i class="fa fa-archive"></i><span>Pedidos</span></a></li>
										<li><a href="admin.php?m=servicios"><i class="fa fa-user"></i><span>Servicios</span></a></li>
										<li> <a href="admin.php?m=porcobrar"> <i class="fa fa-calendar"></i> <span>Cuentas por Cobrar</span> </a> </li>
										<li class="">
											<a href="#webpage" class=""><i class="fa fa-bar-chart-o icon"></i> <span class="pull-right"> <i class="fa fa-angle-down text"></i> <i class="fa fa-angle-up text-active"></i> </span> <span>Reportes</span> </a>
											<ul class="nav lt" style="display: none;">
												<li> <a href="admin.php?m=ingresos"> <i class="fa fa-usd"></i> <span>Ventas</span> </a> </li>
												<li> <a href="admin.php?m=reportes&estado=1"> <i class="fa fa-check"></i> <span>Liquidados</span> </a> </li>
												<li> <a href="admin.php?m=reportes&estado=0"> <i class="fa fa-exclamation"></i> <span>Pendientes</span> </a> </li>
											</ul>
										</li>
										<li><a href="admin.php?m=porpagar"><i class="fa fa-usd"></i><span>Cuentas por pagar</span></a></li>
										<li class="">
											<a href="#webpage" class=""><i class="fa fa-cog icon"></i> <span class="pull-right"> <i class="fa fa-angle-down text"></i> <i class="fa fa-angle-up text-active"></i> </span> <span>Config</span> </a>
											<ul class="nav lt" style="display: none;">
												<li> <a href="admin.php?m=proveedores"> <i class="fa fa-users"></i> <span>Proveedores</span> </a> </li>
												<li> <a href="admin.php?m=usuarios"> <i class="fa fa-users"></i> <span>Usuarios</span> </a> </li>
											</ul>
										</li>
									</ul>
								</nav>
								<!-- / nav -->
							</div>
						</section>
					</section>
				</aside>
				<!-- /.aside -->
				<section id="content">
					<section class="vbox">
						<!--<header class="header bg-white b-b b-light"> <p>Layout with black color</p> </header>-->
						<section class="scrollable wrapper w-f">
							<?php include $paginaPHP; ?>
						</section>
						<footer class="footer bg-white b-t b-light text-right">
							<p><a href="http://rosef.mx" target="_blank">wakecode.mx &copy; 2018</a></p>
						</footer>
					</section>
					<a href="#" class="hide nav-off-screen-block" data-toggle="class:nav-off-screen, open" data-target="#nav,html"></a>
				</section>
			</section>
		</section>
	</section>
	<!-- Bootstrap -->
	<!-- App -->
	<script type="text/javascript" src="js/select2.min.js"></script>
	<script type="text/javascript" src="js/bootstrap-datepicker.js"></script>
	<!-- daterangepicker -->
	<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/2.9.0/moment.min.js"></script>
	<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/1/daterangepicker.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
	<script src="js/app.plugin.js"></script>
</body>
</html>
