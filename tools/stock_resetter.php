<?php
/**
 * This file is part of the OpenPyme1.
 * 
 * (c) Open Solution Systems <operaciones@tumundolaboral.com.sv>
 * 
 * For the full copyright and license information, please refere to LICENSE file
 * that has been distributed with this source code.
 */

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('display_errors', 1);
error_reporting( E_ERROR | E_PARSE );

include('../_conexion.php');


$info = [];

function view(){
    global $info;
    loadInfo();
?>
<html>
    <head>
        <title>Reiniciar Stock</title>
        <link href="../css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <h2>Poner el Stock de una ubicación específica a cero.</h2>
       <div class="row">
        <div class="col-lg-6">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID SUCURSAL</th>
                    <th>ID UBICACION</th>
                    <th>DESCRIPCION</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($info['ubicaciones'] AS $key => $ubicacion){
                        echo "<tr>";
                        echo "<td>$ubicacion[id_sucursal]</td>";
                        echo "<td>$ubicacion[id_ubicacion]</td>";
                        echo "<td>$ubicacion[descripcion]</td>";
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
       </div><!--col-lg-6-->
       <div><!--row-->

       <div class="col-lg-6"><!--col-lg-6-->
       <div class="form-group">
            <label>ID de ubicacion</label>
            <input class="form-control" style="width: 10em;">
            <button class="btn btn-primary" style="margin-top: 1.5em;">
            Reiniciar Stock
            </button>
       </div>
       </div><!--Col-lg-6--->
       
        </div><!--row-->
        
    </body>
</html>

<?php
}

function loadInfo(){
    global $info;

    $info['ubicaciones'] = _query(
        "SELECT id_sucursal, id_ubicacion, descripcion
        FROM ubicacion"
    );
}

function stockReset(){
    $errorControl = false;
    $id_sucursal = 4;

    $stocksUbicacion = _query(
        "SELECT id_su, id_producto, cantidad
        FROM stock_ubicacion
        WHERE id_ubicacion = 2
        AND id_sucursal=$id_sucursal"
    );
    echo "SELECT id_su, id_producto, cantidad
    FROM stock_ubicacion
    WHERE id_ubicacion = 2
    AND id_sucursal=$id_sucursal";

    _begin();
    $count = 1;
    while($stockUb = _fetch_array($stocksUbicacion)){
        echo "$count) ";
        $count += 1;
        $stockConsolidado = _fetch_array(_query(
            "SELECT id_stock, stock FROM stock WHERE
            id_producto=$stockUb[id_producto] AND id_sucursal=$id_sucursal"
        ));

        if($stockUb['cantidad'] == 0){
            echo "<hr>Stock de Ubicacion: 0 -> pasamos al sigueinte<hr>";
            continue;
        }

        $nuevoConsolidado = ($stockConsolidado['stock']-$stockUb['cantidad']);

        echo "ID PRODUCTO: $stockUb[id_producto] (".$stockConsolidado['stock'] ." - $stockUb[cantidad] ):  ->";
        echo  $nuevoConsolidado;

        $toUpdateStockUb = array(
            "cantidad" => 0
        );
        _update('stock_ubicacion', $toUpdateStockUb, "WHERE id_su =$stockUb[id_su] AND id_sucursal=$id_sucursal");
        if(_affected_rows() > 0){

            $toUpdateStock = array(
                "stock" => $nuevoConsolidado
            );
            _update('stock', $toUpdateStock, "WHERE id_stock =$stockConsolidado[id_stock] AND id_sucursal=$id_sucursal");
            if(_affected_rows() > 0){
                echo "---> [OK]<br>";
            }else{
                echo "---> [ERROR CONSOLIDADO] <br>";
                $errorControl = true;
            }
        }else{
            echo "---> [ERROR STOCK UBICACION]<br>";
            $errorControl = true;
        }
    }

    if($errorControl == false){
        //Commitear porque todo bien todo correcto.
        _commit();
        echo "<br> TODO BIEN, TODO CORRECTO";
    }else{
        //Dar rollback porque ubo un error.
        _rollback();
        echo "<br> ERROR. ROLLBACK EXECUTED";
    }

}

if(isset($_REQUEST['action'])){

    switch($_REQUEST['action']){
        case 'reset':
            stockReset();
            break;
    }
}else{
    view();
}


?>