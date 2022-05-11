<?php
 header("Access-Control-Allow-Origin: *");
 header('Access-Control-Allow-Methods: GET, POST');
include ("_core.php");

function initial(){
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$id_sucursal=$_SESSION['id_sucursal'];
	date_default_timezone_set('America/El_Salvador');
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	//permiso del script

  $a = uniqid();
  $b = uniqid();
  $d = uniqid();
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Ofertas Disponibles</h4>
</div>
<div class="modal-body">
	<!--div class="wrapper wrapper-content  animated fadeInRight"-->
	<div class="row" id="row1">
		<!--div class="col-lg-12"-->
		<?php

		?>
    <div class="row">
      <div class="col-lg-12">
        <button id="<?=$a ?>" style="height:auto" class=' btn-info form-control' type="button" name="button">
          EN LA COMPRA DE COCINA COCINA ACERO INOXIDABLE 3 QUEMADORES C/CHISP CH-333BBGS A $26.00 la segunda a $19.00
        </button>
      </div>
    </div>
    <br>
	</div>
		<!--/div-->
		<!--/div-->
</div>
<div class="modal-footer">
  <button id="<?=$b ?>" type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
</div>

<script type="text/javascript">

$(document).on('click', '#<?=$a?>', function(event) {

  $.ajax({
    url: 'modal_ofertas.php',
    type: 'POST',
    dataType: 'json',
    data: {process: 'oferta1'},
    success: function(xdatos){
      $("#inventable").append(xdatos.lista);

      $(".cant").numeric({
        negative: false,
        decimal: false
      });
      $(".86").numeric({
        negative: false,
        decimalPlaces: 4
      });

      $(".sel").select2();
      $(".sel_r").select2();
      totales();

      $("#<?=$b?>").click();
    }
  });

});
</script>

<!--/modal-footer -->

<?php

}

function oferta1()
{
  $id_sucursal = $_SESSION['id_sucursal'];
  $array = array(
    0 => array('id_producto' => 3271, 'id_presentacion' => 3224,'cantidad'=>"",'exento' => 0,'id_categoria'=>19,'precio_venta'=>26, 'subtotal'=>0),
    1 => array('id_producto' => 3271, 'id_presentacion' => 3224,'cantidad'=>"",'exento' => 0,'id_categoria'=>19,'precio_venta'=>19, 'subtotal'=>0),
  );

  $lista="";
  foreach ($array as $key => $row) {
    $cantidad=$row['cantidad'];
    $precio_venta=$row['precio_venta'];
    $subtotal=$row['subtotal'];
    $categoria=$row['id_categoria'];
    $id_producto=$row['id_producto'];
    $id_pre = $row["id_presentacion"];
    $exento=$row['exento'];

    $sql_ss=_query("SELECT presentacion.nombre, presentacion_producto.descripcion,presentacion_producto.id_pp as id_presentacion,presentacion_producto.unidad,presentacion_producto.precio FROM presentacion_producto JOIN presentacion ON presentacion.id_presentacion=presentacion_producto.id_presentacion WHERE presentacion_producto.id_producto='$id_producto' AND presentacion_producto.activo=1 ");
    //echo "SELECT presentacion.nombre, presentacion_producto.descripcion,presentacion_producto.id_presentacion,presentacion_producto.unidad,presentacion_producto.precio FROM presentacion_producto JOIN presentacion ON presentacion.id_presentacion=presentacion_producto.presentacion WHERE presentacion_producto.id_producto='$id_producto' AND presentacion_producto.activo=1";
    $y = 0;
    $unidadp = 0;
    $preciop = 0;
    $select_rank="<select class='sel_r form-control'>";
    $select="<select class='sel form-control'>";
    $pe=0;
    while ($rowx=_fetch_array($sql_ss)) {
      # code...
      if ($y==0) {
        # code...
        $unidadp=$rowx['unidad'];
        $preciop=$rowx['precio'];
        $descripcionp=$rowx['descripcion'];

        $preciosArray = _getPrecios($id_pre, 0);
        $xc=0;
        $select_rank.="<option value='$precio_venta'";
        $select_rank.=">$precio_venta</option>";
        $preciop=$precio_venta;
        foreach ($preciosArray as $key => $value) {
          // code...
          if ($value>0) {
            // code...
            $select_rank.="<option value='$value'";
            $select_rank.=">$value</option>";
          }
        }
        //$select_rank.="<option value='0.0'>0.0</option>";
        $select_rank.="</select>";

      }
      $select.="<option value='$rowx[id_presentacion]'";
      if ($id_pre == $rowx["id_presentacion"]) {
        $select.="selected";
      }
      $select.=">$rowx[nombre]</option>";
      $y=$y+1;
    }
    $select.="</select>";
    $sql_cc = _query("SELECT * FROM presentacion_producto WHERE id_pp = '$id_pre'");
    $roq = _fetch_array($sql_cc);
    $unidadq=$roq['unidad'];

    $descripcionq=$roq['descripcion'];
    $cc = $cantidad / $unidadq;

    $descripcion="COCINA ACERO INOXIDABLE 3 QUEMADORES C/CHISP CH-333BBGS";

    $sql_s = _fetch_array(_query("SELECT p.id_sucursal,SUM(su.cantidad) as stock FROM producto AS p JOIN stock_ubicacion as su ON su.id_producto=p.id_producto JOIN ubicacion as u ON u.id_ubicacion=su.id_ubicacion  WHERE  p.id_producto ='$id_producto' AND u.bodega=0 AND su.id_sucursal=$id_sucursal"));
    $stock_r=$sql_s['stock'];

    $hoy=date("Y-m-d");
    $sql_res_pre=_fetch_array(_query("SELECT SUM(factura_detalle.cantidad) as reserva FROM factura JOIN factura_detalle ON factura_detalle.id_factura=factura.id_factura WHERE factura_detalle.id_prod_serv=$id_producto AND factura.id_sucursal=$id_sucursal AND factura.fecha = '$hoy' AND factura.finalizada=0 "));
    $reserva=$sql_res_pre['reserva'];

    $reservado=0;


    $existencias=$stock_r+$reservado-$reserva;

    $descprod=$descripcion;
    //$ubica=ubicacionn($id_posicion);
    $ubicacion="";

    $sqkl=_fetch_array(_query("SELECT iva FROM sucursal WHERE id_sucursal=$id_sucursal"));
    $iva=$sqkl['iva']/100;
    $iva=1+$iva;

    $descripcion.=$ubicacion;
    $lista.= "<tr class='row100 head'>";
    $lista.= "<td hidden class='cell100 column10 text-success id_pps'><input type='hidden' id='unidades' name='unidades' value='" . $unidadq . "'>".$id_producto."</td>";
    $lista.= "<td class='cell100 column30 text-success'>".$descripcion."<input type='hidden' id='exento' name='exento' value='".$exento."'>"."</td>";


    $lista.= "<td class='cell100 column10 text-success' id='cant_stock'>".$existencias."</td>";
    $lista.= "<td class='cell100 column10 text-success'><input type='text'  class='form-control decimal $categoria cant' id='cant' name='cant' value='' style='width:60px;'></td>";

    $lista.= "<td class='cell100 column10 text-success preccs'>".$select."</td>";
    $lista.= "<td hidden class='cell100 column10 text-success descp'>"."<input type'text' id='dsd' value='" . $descripcionp. "' class='form-control' readonly>"."</td>";
    $lista.= "<td class='cell100 column10 text-success rank_s'>".  $select_rank . "</td>";
    $lista.= "<td class='cell100 column10 text-success'><input type='hidden'  id='precio_venta_inicial' readonly name='precio_venta_inicial' value='".$precio_venta."' ><input type='hidden'  id='precio_sin_iva' name='precio_sin_iva' value='" . round(($precio_venta/$iva), 8, PHP_ROUND_HALF_DOWN) . "'><input type='text'  class='form-control decimal' id='precio_venta' name='precio_venta' value='".$precio_venta."' ></td>";

    $lista.= "<td class='ccell100 column10'>"."<input type='hidden'  id='subtotal_fin' name='subtotal_fin' value='".$subtotal."'>" . "<input type='text'  class='decimal form-control' id='subtotal_mostrar' name='subtotal_mostrar'  value='" . round($subtotal, 2) . "'readOnly>"."</td>";
    $lista.= "<td class='cell100 column10 Delete text-center'><input id='delprod' type='button' class='btn btn-danger fa'  value='&#xf1f8;'>". '<a data-toggle="modal" href="ver_imagen.php?id_producto='.$id_producto.'"  data-target="#viewProd" data-refresh="true" class="btn btn-primary btnViw fa"><i class="fa fa-eye"></i></a>'."</td>";
    $lista.= "</tr>";
  }

  $xdatos['lista']=$lista;

  echo json_encode($xdatos);

}

if (! isset ( $_REQUEST ['process'] )) {
	initial();
} else {
	if (isset ( $_REQUEST ['process'] )) {
		switch ($_REQUEST ['process']) {
      case 'oferta1' :
        oferta1();
        break;
			case 'formDelete' :
				initial();
				break;
		}
	}
}

?>
