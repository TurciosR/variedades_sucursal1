<?php
include_once "_core.php";
$query = $_REQUEST['query'];
$id_sucursal=$_SESSION['id_sucursal'];
$id_origen = $_REQUEST['id_origen'];
$sql0="SELECT producto.id_producto as id, descripcion, barcode
				FROM producto
				JOIN stock_ubicacion on stock_ubicacion.id_producto=producto.id_producto
				WHERE barcode='$query' AND stock_ubicacion.id_sucursal='$id_sucursal' AND stock_ubicacion.id_ubicacion=$id_origen AND stock_ubicacion.cantidad>0";
$result = _query($sql0);
if(_num_rows($result)==0)
{
	$sql = "SELECT producto.id_producto as id, descripcion, barcode
					FROM producto
					JOIN stock_ubicacion on stock_ubicacion.id_producto=producto.id_producto
					WHERE descripcion LIKE '%$query%' AND stock_ubicacion.id_sucursal='$id_sucursal'  AND stock_ubicacion.id_ubicacion=$id_origen  AND stock_ubicacion.cantidad>0 limit 100";
	$result = _query($sql);
}

if (_num_rows($result)==0) {
	# code...
	echo json_encode ("");
}
else {
$array_prod[] = array();
$i=0;
while ($row1 = _fetch_array($result))
{
	if($row1['barcode']==""){
	$barcod=" ";
	}
	else{
	$barcod=" [".$row1['barcode']."] ";
	}
	$array_prod[$i] = array('producto'=>$row1['id']."|".$barcod.$row1['descripcion']);
	$i++;
}
	echo json_encode ($array_prod);
}

?>
