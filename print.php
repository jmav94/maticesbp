<?php 

include 'db.php';

$id = mysql_real_escape_string($_GET['id']);

    $query = "SELECT 
    ventas.idventas,
    ventas.fecha,
    ventas.descuento,
    clientes.nombre,
    clientes.telefono,
    destinatarios.nombre as dnombre,
    destinatarios.direccion as ddireccion,
    destinatarios.fechaentrega as dfechaentrega,
    destinatarios.horaentrega as dhoraentrega,
    destinatarios.colonia as dcolonia,
    destinatarios.codigopostal as dcp,
    destinatarios.referencia as dref,
    destinatarios.comen
    FROM ventas 
    JOIN clientes ON clientes.idclientes=ventas.idcliente
    LEFT JOIN destinatarios ON destinatarios.idventa='".$id."'
    WHERE ventas.idventas='".$id."' LIMIT 1";

$query = mysql_query($query) or die(mysql_error());
$data = mysql_fetch_object($query);

?>
<!DOCTYPE html>
<html>
<head>
    <title>IMPRIMIR ORDEN: <?php echo $id; ?></title>
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
                <div class="panel-heading">ORDEN</div>
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
                            <div style="width:500px;float:left;" class="under"> &nbsp;<?php if ($data->dcolonia <> "") {
                                echo $data->ddireccion." <strong>COL. :</strong> ".$data->dcolonia." <strong>CP. :</strong> ".$data->dcp;
                            }  ?></div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="fuentechica top">
                            <label style="width:200px;float:left;">REFERENCIA :</label>
                            <div style="width:500px;float:left;" class="under"> &nbsp;<?php echo $data->dref;
                            ?></div>
                        </div>

                        <div class="clearfix"></div>
                        
                        <div class="fuentechica top">
                            <label style="width:200px;float:left;">FECHA/HORA DE ENTREGA :</label>
                            <div style="width:500px;float:left;" class="under"> &nbsp;<?php echo $data->dfechaentrega." / ".$data->dhoraentrega; ?></div>
                        </div>
                        <div class="clearfix"></div>
                        
                        <div class="fuentechica top">
                            <label style="width:200px;float:left;">COMENTARIOS :</label>
                            <div style="width:500px;float:left;" class="under"> &nbsp;<?php echo $data->comen; ?></div>
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
                            ventas_articulos.idarticulo,
                            ventas_articulos.precio,
                            ventas_articulos.cantidad,
                            ventas_articulos.total,
                            articulos.articulo
                            FROM ventas_articulos 
                            JOIN articulos ON articulos.idarticulos=ventas_articulos.idarticulo
                            WHERE ventas_articulos.idventa='".$data->idventas."' 
                            ORDER BY ventas_articulos.idva ASC");
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