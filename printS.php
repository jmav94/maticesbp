<?php 

include 'db.php';

$id = mysql_real_escape_string($_GET['id']);

    $query = "SELECT 
    servicios.idservicios,
    servicios.fecha,
    servicios.descuento,
    clientes.nombre,
    clientes.telefono,
    servicios_destinatarios.nombre as dnombre,
    servicios_destinatarios.direccion as ddireccion,
    servicios_destinatarios.fechaentrega as dfechaentrega,
    servicios_destinatarios.horaentrega as dhoraentrega,
    servicios_destinatarios.colonia as dcolonia,
    servicios_destinatarios.codigopostal as dcp
    FROM servicios 
    JOIN clientes ON clientes.idclientes=servicios.idcliente
    LEFT JOIN servicios_destinatarios ON servicios_destinatarios.idservicio='".$id."'
    WHERE servicios.idservicios='".$id."' LIMIT 1";

$query = mysql_query($query) or die(mysql_error());
$data = mysql_fetch_object($query);

?>
<!DOCTYPE html>
<html>
<head>
    <title>IMPRIMIR SERVICIO: <?php echo $id; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="css/app.v1.css" type="text/css" />
    <style type="text/css">
    .fuentechica{
        font-size: 12px;
    }
    label{
        font-weight: bold;
    }
    .under{
        border-bottom: 1px solid #DDD;
    }
    </style>
</head>
<body onload="window.print();">
<div class="row">
        <div class="col-xs-4">
            <img src="images/logomatices.jpg" style="width:200px;">
        </div>
        <div class="col-xs-2"></div>
        <div class="col-xs-3 text-center">
            <div class="panel panel-default">
                <div class="panel-heading">SERVICIO</div>
                <div class="panel-body">
                    <?php echo $id; ?>
                </div>
            </div>
        </div>
        <div class="col-xs-3 text-center">
            <div class="panel panel-default">
                <div class="panel-heading">FECHA</div>
                <div class="panel-body">
                    <?php echo $data->fecha; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12">
            <form class="form-horizontal">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <div class="fuentechica">
                            <label style="width:200px;float:left;">NOMBRE DEL CLIENTE:</label>
                            <div style="width:500px;float:left;" class="under">&nbsp;<?php echo $data->nombre; ?></div>
                        </div>
                        <div class="clearfix"></div>
                        
                        <div class="fuentechica top">
                            <label style="width:200px;float:left;">TELEFONO :</label>
                            <div style="width:500px;float:left;margin-right:5px;" class="under">&nbsp;<?php echo $data->telefono; ?></div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="fuentechica top">
                            <label style="width:200px;float:left;">NOMBRE DESTINATARIO :</label>
                            <div style="width:500px;float:left;margin-right:5px;" class="under">&nbsp;<?php echo $data->dnombre; ?></div>
                        </div>

                        
                        <div class="clearfix"></div>
                        <div class="fuentechica top">
                            <label style="width:200px;float:left;">DIRECCION DE ENTREGA :</label>
                            <div style="width:500px;float:left;" class="under"> &nbsp;<?php echo $data->ddireccion." <strong>COL. :</strong> ".$data->dcolonia." <strong>CP. :</strong> ".$data->dcp; ?></div>
                        </div>

                        <div class="clearfix"></div>
                        
                        <div class="fuentechica top">
                            <label style="width:200px;float:left;">FECHA/HORA DE ENTREGA :</label>
                            <div style="width:500px;float:left;" class="under"> &nbsp;<?php echo $data->dfechaentrega." / ".$data->dhoraentrega; ?></div>
                        </div>
                    </div>
                </div>
            </form>
                
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center fuentechica">CONCEPTO</th>
                        <th class="text-center fuentechica" width="70">CANT.</th>
                        <th class="text-center fuentechica" width="70">P.U.</th>
                        <th class="text-center fuentechica" width="80">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
<?php
                $subtotal = 0;
                $query = mysql_query("SELECT 
                            servicios_articulos.idarticulo,
                            servicios_articulos.precio,
                            servicios_articulos.cantidad,
                            servicios_articulos.total,
                            articulos.articulo
                            FROM servicios_articulos 
                            JOIN articulos ON articulos.idarticulos=servicios_articulos.idarticulo
                            WHERE servicios_articulos.idservicio='".$data->idservicios."' 
                            ORDER BY servicios_articulos.idse ASC");
                while($q = mysql_fetch_object($query)){
                    $subtotal += ($q->precio*$q->cantidad);
                    echo '<tr>
                                <td>'.$q->articulo.'</td>
                                <td class="text-center v-middle">'.$q->cantidad.'</td>
                                <td class="text-right v-middle">'.$q->precio.'</td>
                                <td class="text-right v-middle">$ '.($q->precio*$q->cantidad).'</td>
                            </tr>';
                }
?>
                    <tr> <td colspan="3" class="text-right"><strong>SUBTOTAL</strong></td> <td class="text-right">$ <?php echo $subtotal; ?></td> </tr>
                <?php
                if (!empty($data->descuento)){

                    $subtotal = $subtotal - $data->descuento;
                    echo '<tr> <td colspan="3" class="text-right no-border"><strong>DESCUENTO</strong></td> <td class="text-right">$ '.$data->descuento.'</td> </tr>';
                }
                ?>
                    <tr> <td colspan="3" class="text-right no-border"><strong>TOTAL</strong></td> <td class="text-right"><strong>$ <?php echo $subtotal; ?></strong></td> </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>