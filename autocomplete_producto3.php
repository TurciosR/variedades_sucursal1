<?php
include_once "_core.php";
$query = $_REQUEST['query'];
$id_sucursal=$_SESSION['id_sucursal'];
$sql0="SELECT producto.id_producto as id, descripcion, barcode
				FROM producto
				WHERE barcode='$query' ";
$result = _query($sql0);
if(_num_rows($result)==0)
{
	$sql = "SELECT producto.id_producto as id, descripcion, barcode
					FROM producto
					WHERE descripcion LIKE '%$query%' limit 500";
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
	$array_prod[$i] = array('producto'=>"".$row1['id'].""."|".$barcod.$row1['descripcion']);
	$i++;
}
	echo json_encode ($array_prod);
}

?>
