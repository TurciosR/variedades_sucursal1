<?php
	include ("_core.php");
	/*
	SELECT movimientos.id_movimiento,movimientos.fecha,movimientos.hora,usuario.nombre,movimientos.concepto,movimientos.total,SUM(movimiento_producto.entrada) as entrada,SUM(mp.salida) AS salida FROM movimientos JOIN usuario ON usuario.id_usuario=movimientos.id_usuario JOIN movimiento_producto ON movimiento_producto.id_movimiento=movimientos.id_movimiento JOIN movimiento_producto as mp ON mp.id_movimiento=movimientos.id_movimiento GROUP BY movimiento_producto.id_movimiento
	*/
	$requestData= $_REQUEST;
	$fechai= $_REQUEST['fechai'];
	$fechaf= $_REQUEST['fechaf'];

	require('ssp.customized.class.php' );
	// DB table to use
	$table = 'pedido';
	// Table's primary key
	$primaryKey = 'id_pedido';

	// MySQL server connection information
	$sql_details = array(
  'user' => $username,
  'pass' => $password,
  'db'   => $dbname,
  'host' => $hostname
  );

	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$id_sucursal = $_SESSION["id_sucursal"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);


	$joinQuery = "FROM pedido as m LEFT JOIN cliente as c ON m.id_cliente = c.id_cliente LEFT JOIN usuario ON m.id_empleado=usuario.id_usuario LEFT join ubicacion on ubicacion.id_ubicacion=m.origen";
	$extraWhere=" m.finalizada=0 ";
	//$extraWhere = " m.fecha BETWEEN '$fechai' AND '$fechaf'";
	$columns = array(
	array( 'db' => 'm.id_pedido', 'dt' => 0, 'field' => 'id_pedido' ),
	array( 'db' => 'c.nombre', 'dt' => 1, 'field' => 'nombre' ),
	array( 'db' => 'm.id_pedido', 'dt' => 2, 'formatter' => function( $tipo, $row )
	{
		$sql_suc=_fetch_array(_query("SELECT municipio.nombre_municipio,departamento.nombre_departamento FROM pedido left JOIN departamento ON departamento.id_departamento=pedido.id_departamento LEFT JOIN municipio ON municipio.id_municipio=pedido.id_municipio LEFT JOIN usuario ON pedido.id_empleado=usuario.id_usuario WHERE pedido.id_pedido='$tipo'"));

		return $sql_suc['nombre_municipio']."/".$sql_suc['nombre_departamento'];

	},'field' => 'id_pedido' ),
	array( 'db' => 'usuario.nombre', 'dt' => 3,'as' => 'name', 'field' => 'name' ),
	array( 'db' => 'IF(m.id_pedido > 1600, IF(MOD(m.id_pedido,1600) = 0, 1600, MOD(m.id_pedido,1600)),m.id_pedido)', 'dt' => 4,'as' => 'id_num', 'field' => 'id_num' ),
	/*array( 'db' => 'm.id_pedido', 'dt' => 4,'formatter' => function( $tipo, $row )
	{
		if($tipo>1600)
		{
			if($tipo % 1600 == 0)
			{
				$tipo = 1600;
			}
			else {
				$tipo =  $tipo % 1600;
			}

		}
		return str_pad($tipo,8,0,STR_PAD_LEFT);
	},'field' => 'id_pedido' ),*/
	array( 'db' => 'm.fecha_entrega', 'dt' => 5, 'field' => 'fecha_entrega' ),

	array( 'db' => 'if(m.lugar_entrega="",c.direccion,m.lugar_entrega)', 'dt' => 6, 'as' => 'en', 'field' => 'en' ),
	array( 'db' => 'm.total', 'dt' => 7, 'formatter' => function( $tipo, $row )
	{
		return "$".number_format($tipo,2);
	},'field' => 'total' ),

	array( 'db' => 'm.id_pedido', 'dt' => 8, 'formatter' => function( $id_movimiento, $row ){
		$menudrop="<div class='btn-group'>
			<a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
			<ul class='dropdown-menu dropdown-primary'>";
			include ("_core.php");
			$id_user=$_SESSION["id_usuario"];
			$id_sucursal=1;
			$admin=$_SESSION["admin"];

      $sql_pedido = _query("SELECT * FROM pedido WHERE id_pedido = '$id_movimiento'");
      $row_p = _fetch_array($sql_pedido);
      $finalizada = $row_p["finalizada"];

			$filename='ver_pedido.php';
			$link=permission_usr($id_user,$filename);
			if ($link!='NOT' || $admin=='1' )
				$menudrop.="<li><a data-toggle='modal' href='$filename?id_pedido=$id_movimiento'  data-target='#viewModalFact' data-refresh='true'><i class=\"fa fa-check\"></i> Ver detalle</a></li>";
      if($finalizada == 0)
      {
        $filename='anular_pedido.php';
				$link=permission_usr($id_user,$filename);
        if ($link!='NOT' || $admin=='1' )
				$menudrop.="<li><a data-toggle='modal' href='$filename?id_pedido=$id_movimiento'  data-target='#viewModalFact' data-refresh='true'><i class=\"fa fa-ban\"></i> Anular</a></li>";
      }

			if($finalizada == 0)
      {
        $filename='editar_pedido.php';
				$link=permission_usr($id_user,$filename);
        if ($link!='NOT' || $admin=='1' )
				{
					$menudrop.= "<li><a href='$filename?id_pedido=".$id_movimiento."' ><i class=\"fa fa-edit\"></i> Editar y Finalizar</a></li>";
				}
			}

				$filename='pedido_pdf.php';
				$link=permission_usr($id_user,$filename);
				if ($link!='NOT' || $admin=='1' )
					$menudrop.="<li><a href='$filename?id_pedido=$id_movimiento'target='_blank' ><i class=\"fa fa-print\"></i> Imprimir Formato</a></li>";
									$filename='pedido_pdf_b.php';
					$menudrop.="<li><a href='$filename?id_pedido=$id_movimiento'target='_blank' ><i class=\"fa fa-spinner\"></i> Imprimir Detalle</a></li>";
  			$menudrop.="</ul>

  		</div>";
  		return $menudrop;},
  		'field' => 'id_pedido' ),

	);
	//echo json_encode(
	//SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy, $having )
	echo json_encode(
		SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
	);
?>
