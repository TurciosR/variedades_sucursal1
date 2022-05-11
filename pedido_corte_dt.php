<?php
include ("_core.php");
$requestData= $_REQUEST;
require('ssp.customized.class.php' );
$table = 'corte_pedido';

$inicio=$_REQUEST['fechai'];
$fin=$_REQUEST['fechaf'];


$primaryKey = 'id_corte';

$sql_details = array(
	'user' => $username,
	'pass' => $password,
	'db'   => $dbname,
	'host' => $hostname
);
$joinQuery=" FROM corte_pedido";

$extraWhere="   corte_pedido.fecha BETWEEN '$inicio' AND '$fin'";

$columns = array(
	array( 'db' => 'id_corte',  'dt' => 0, 'field' => 'id_corte'),
	array( 'db' => 'corte_pedido.fecha',  'dt' => 1, 'field' => 'fecha'),
	array( 'db' => 'corte_pedido.monto',  'dt' => 2, 'field' => 'monto'),
	array( 'db' => 'corte_pedido.efectivo',  'dt' => 3, 'field' => 'efectivo'),
	array( 'db' => 'corte_pedido.abonos',  'dt' => 4, 'field' => 'abonos'),
	array( 'db' => 'corte_pedido.diferencia',  'dt' => 5, 'field' => 'diferencia'),
	array( 'db' => 'corte_pedido.comentario',  'dt' => 6, 'field' => 'comentario'),
);
echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);
?>
