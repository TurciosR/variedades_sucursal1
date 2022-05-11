<?php
include ("_core.php");
function initial()
{
	include ("_core.php");
	$admin = $_SESSION["admin"];
		$id_pedido = $_REQUEST['id_pedido'];
		$id_sucur = $_SESSION['id_sucursal'];
		$id_user=$_SESSION["id_usuario"];
		$sql="SELECT pedido.*,cliente.nombre FROM pedido left join cliente on cliente.id_cliente=pedido.id_cliente WHERE  pedido.id_pedido='$id_pedido' ORDER BY 'PENDIENTE'";
		$result = _query($sql);
		$row = _fetch_array($result);
		$cliente = $row["nombre"];
		$fecha = $row["fecha"];
		$fecha2 = $row["fecha_entrega"];
		$lugar = $row["lugar_entrega"];
		$total = $row["total"];
		$uri = $_SERVER['SCRIPT_NAME'];
		$filename=get_name_script($uri);
		$links="yes";//permission_usr($id_user,$filename);
	?>
	<?php if($links!='NOT' || $admin == '1' ){ ?>
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title">DETALLES DE PEDIDO</h4>
	</div>
	<div class="modal-body">
		<div class="wrapper wrapper-content  animated fadeInRight">
			<div class="row">
				<div class="col-lg-4">
					<div class="form-group">
						<label>Cliente:</label>
						<input type="text" name="fecha" value="<?php echo $cliente; ?>" class="form-control" readOnly>
					</div>
				</div>

				<div class="col-lg-4">
					<div class="form-group">
						<label>Fecha:</label>
						<input type="text" name="fecha" value="<?php echo $fecha; ?>" class="form-control" readOnly>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label>Fecha de entrega:</label>
						<input type="text" name="fecha" value="<?php echo $fecha2; ?>" class="form-control" readOnly>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<table class="table table-hover table-bordered" id="tabla_modal">
					<thead>
						<tr>
							<th>Cantidad</th>
							<th>Nombre</th>
							<th>Presentación</th>
							<th>Descripción</th>
							</tr>
					</thead>
						<tbody>
						<?php
						 $sql_prese=_query("SELECT producto.id_producto,pedido_detalle.unidad, producto.descripcion AS producto, presentacion.nombre,presentacion_producto.id_pp as id_presentacion ,presentacion_producto.descripcion, presentacion_producto.unidad ,pedido_detalle.id_pedido_detalle,pedido_detalle.precio_venta, pedido_detalle.cantidad,pedido_detalle.cantidad as cantidad_enviar, pedido_detalle.subtotal, stock.stock
							 FROM pedido_detalle
							 JOIN producto ON (pedido_detalle.id_prod_serv=producto.id_producto)
							 JOIN presentacion_producto ON (pedido_detalle.id_presentacion=presentacion_producto.id_pp)
							 JOIN presentacion ON (presentacion_producto.id_presentacion=presentacion.id_presentacion)
							 JOIN stock ON (pedido_detalle.id_prod_serv=stock.id_producto)
							 WHERE pedido_detalle.id_pedido='$id_pedido'");

							$i = 1;
							$cant = 0;
							$enviado = 0;
							while ($filas = _fetch_array($sql_prese))
							{	$cant += $filas['cantidad'];
								$enviado += $filas['cantidad_enviar'];
								 $id_presentacion=$filas['id_presentacion'];
									echo "<tr>";
									echo "<td class='text-right'>".number_format($filas['cantidad']/$filas['unidad'],0)."</td>";
									echo "<td>".$filas['producto']."</td>";
									echo "<td>".$filas['nombre']."</td>";
									echo "<td>".$filas['descripcion']."</td>";
									echo "</tr>";
								$i++;
							}
						?>
						</tbody>

					</table>
				</div>
			</div>
		</div>
	</div>
	<?php
	echo "<input type='hidden' nombre='id_pedido' id='id_pedido' value='$id_pedido'>";
	?>
	<div class="modal-footer">
			<button type="button" class="btn btn-danger" id="btnDelete">Anular</button>
			<button type="button" class="btn btn-info" data-dismiss="modal">Cerrar</button>
	</div><!--/modal-footer -->
	<?php
	} //permiso del script
		else {
			$mensaje="No tiene permiso para este modulo.";
			echo "
			<div calss='modal-header'>
				<div class='alert alert-warning'><h5 class='text-success'>$mensaje</h5></div>
			</div>
			<div class='modal-footer'>
				<button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Cerrar</button>
			</div>";
	}//permiso del script
}
function anular() {
	$id_pedido = $_POST['id_pedido'];
	$id_sucursal = $_SESSION["id_sucursal"];

	_begin();
	$table = 'pedido';
	$form_data = array (
			'finalizada' => 2,
			'anulada' => 1,
		);
	$where_clause = "id_pedido='".$id_pedido."' AND id_sucursal='$id_sucursal'";
	$update = _update ( $table, $form_data, $where_clause );

	$id_factura= $id_pedido;
	$sel=_fetch_array(_query("SELECT movimiento_producto.id_movimiento,pedido.total FROM pedido JOIN movimiento_producto ON movimiento_producto.id_traslado=pedido.id_pedido WHERE movimiento_producto.id_traslado=$id_factura AND movimiento_producto.proceso='ped'"));
	$id_sucursal=$_SESSION['id_sucursal'];
	$id_movimiento = $sel["id_movimiento"];
	$id_mov=$id_movimiento;
	$total=$sel['total'];
	$up=0;
	$up2=0;
	$i=0;
	$an=0;
	$table="movimiento_stock_ubicacion";
	$form_data = array(
		'anulada' => 1,
	);
	$where_clause="id_mov_prod='".$id_movimiento."'";
	$update=_update($table,$form_data,$where_clause);

	if ($update) {
		# code...
	}
	else {
		# code...
		$up=1;
	}


	$table="movimiento_producto_pendiente";
	$where_clause="id_movimiento='".$id_movimiento."'";
	$delete=_delete($table,$where_clause);

	$sql_mp=_query("SELECT * FROM movimiento_producto_detalle WHERE id_movimiento=$id_movimiento ");
	$num_r_m=_num_rows($sql_mp);
	if ($num_r_m!=0) {
		# code...
		$sql_des=_fetch_array(_query("SELECT id_ubicacion FROM ubicacion WHERE id_sucursal=$id_sucursal AND bodega=0"));

		$destino = $sql_des;
		$fecha = date("Y-m-d");
		$total_compras = $total;
		$concepto="CARGA DE INVENTARIO";
		$hora=date("H:i:s");
		$fecha_movimiento = date("Y-m-d");
		$id_empleado=$_SESSION["id_usuario"];

		$sql_num = _query("SELECT ii FROM correlativo WHERE id_sucursal='$id_sucursal'");
		$datos_num = _fetch_array($sql_num);
		$ult = $datos_num["ii"]+1;
		$numero_doc=str_pad($ult,7,"0",STR_PAD_LEFT).'_II';
		$tipo_entrada_salida='ENTRADA DE INVENTARIO';


		$z=1;

		/*actualizar los correlativos de II*/
		$corr=1;
		$table="correlativo";
		$form_data = array(
			'ii' =>$ult
		);
		$where_clause_c="id_sucursal='".$id_sucursal."'";
		$up_corr=_update($table,$form_data,$where_clause_c);
		if ($up_corr) {
			# code...
		}
		else {
			$corr=0;
		}
		if ($concepto=='')
		{
			$concepto='ENTRADA DE INVENTARIO';
		}
		$table='movimiento_producto';
		$form_data = array(
			'id_sucursal' => $id_sucursal,
			'correlativo' => $numero_doc,
			'concepto' => $concepto,
			'total' => $total_compras,
			'tipo' => 'ENTRADA',
			'proceso' => 'II',
			'referencia' => $numero_doc,
			'id_empleado' => $id_empleado,
			'fecha' => $fecha,
			'hora' => $hora,
			'id_suc_origen' => $id_sucursal,
			'id_suc_destino' => $id_sucursal,
			'id_proveedor' => 0,
		);
		$insert_mov =_insert($table,$form_data);
		$id_movimiento=_insert_id();

		$j = 1 ;
		$k = 1 ;
		$l = 1 ;
		$m = 1 ;

		while($row_mov=_fetch_array($sql_mp))
		{
			$id_producto=$row_mov['id_producto'];
			$precio_compra=$row_mov['costo'];
			$precio_venta=$row_mov['precio'];
			$cantidad=$row_mov['cantidad'];
			$fecha_caduca="";
			$id_presentacion=$row_mov['id_presentacion'];


			$sql2="SELECT stock FROM stock WHERE id_producto='$id_producto' AND id_sucursal='$id_sucursal'";
			$stock2=_query($sql2);
			$row2=_fetch_array($stock2);
			$nrow2=_num_rows($stock2);
			if ($nrow2>0)
			{
				$existencias=$row2['stock'];
			}
			else
			{
				$existencias=0;
			}

			$sql_lot = _query("SELECT MAX(numero) AS ultimo FROM lote WHERE id_producto='$id_producto'");
			$datos_lot = _fetch_array($sql_lot);
			$lote = $datos_lot["ultimo"]+1;
			$table1= 'movimiento_producto_detalle';
			$cant_total=$cantidad+$existencias;
			$form_data1 = array(
				'id_movimiento'=>$id_movimiento,
				'id_producto' => $id_producto,
				'cantidad' => $cantidad,
				'costo' => $precio_compra,
				'precio' => $precio_venta,
				'stock_anterior'=>$existencias,
				'stock_actual'=>$cant_total,
				'lote' => $lote,
				'id_presentacion' => $id_presentacion,
				'fecha' => $fecha,
				'hora' => $hora,

			);
			$insert_mov_det = _insert($table1,$form_data1);
			if(!$insert_mov_det)
			{
				$j = 0;
			}

		}

		if($insert_mov &&$corr &&$z && $j && $k && $l && $m)
		{

		}
		else
		{
			$up=1;
			$up2=1;
			$an=1;
		}
	}
	$id_movimiento=$id_mov;
	$sql_su=_query("SELECT movimiento_stock_ubicacion.id_producto,id_origen,id_destino,movimiento_stock_ubicacion.cantidad,movimiento_stock_ubicacion.id_presentacion FROM movimiento_stock_ubicacion WHERE id_mov_prod=$id_movimiento");
	while ($row=_fetch_array($sql_su)) {
		# code...
		$id_producto=$row['id_producto'];
		$id_origen=$row['id_origen'];
		$id_destino=$row['id_destino'];
		$cantidad=$row['cantidad'];
		$id_presentacion=$row['id_presentacion'];

		$sql_s=_query("SELECT cantidad AS stock_origen FROM stock_ubicacion WHERE id_producto=$id_producto  AND id_su=$id_origen");
		$rw=_fetch_array($sql_s);
		$stock_origen=$rw['stock_origen'];
		$stock_origen=$stock_origen+$cantidad;

			# code...
			$table="stock_ubicacion";
			$form_data = array(
				'cantidad' => $stock_origen,
			);
			$where_clause="id_su='".$id_origen."'";
			$update=_update($table,$form_data,$where_clause);

			if ($update) {
				# code...
			}
			else {
				# code...
				$up2=1;
			}
			$sql_stock=_fetch_array(_query("SELECT id_stock,stock FROM stock WHERE id_producto='".$id_producto."' AND id_sucursal=$_SESSION[id_sucursal]"));
			$sql_stock_anterior=$sql_stock['stock'];
			$stock_nuevo=$sql_stock_anterior+$cantidad;
			$id_stock=$sql_stock['id_stock'];


			$table="stock";
			$form_data = array(
				'stock' => $stock_nuevo,
			);
			$where_clause="id_stock='".$id_stock."'";

			$update=_update($table,$form_data,$where_clause);
			if ($update) {
				# code...
			}
			else {
				# code...
				$up=1;
			}
		$sql_lot = _query("SELECT MAX(numero) AS ultimo FROM lote WHERE id_producto='$id_producto'");
		$datos_lot = _fetch_array($sql_lot);
		$lote = $datos_lot["ultimo"]+1;



		$sql_lote = _query("SELECT MAX(lote.vencimiento) as vence FROM lote WHERE lote.id_producto='$id_producto'");
		$datos_lote = _fetch_array($sql_lote);
		$fecha_caduca = $datos_lote["vence"];

		$sql_costo = _query("SELECT costo FROM presentacion_producto WHERE id_presentacion=$id_presentacion");
		$datos_costo = _fetch_array($sql_costo);
		$precio = $datos_costo["costo"];

		$estado='VIGENTE';
		$table_perece='lote';
		$form_data_perece = array(
			'id_producto' => $id_producto,
			'referencia' => $id_movimiento,
			'numero' => $lote,
			'fecha_entrada' => date("Y-m-d"),
			'vencimiento'=>$fecha_caduca,
			'precio' => $precio,
			'cantidad' => $cantidad,
			'estado'=>$estado,
			'id_sucursal' => $_SESSION['id_sucursal'],
			'id_presentacion' => $id_presentacion,
		);
		$insert_lote = _insert($table_perece,$form_data_perece );

	}
	if($i==0)
	{
		if ($up==0&&$up2==0&&$an==0)
		{
			_commit();
			$xdatos['typeinfo']='Success';
			$xdatos['msg']='Registro ingresado correctamente!';
			$xdatos['process']='insert';
		}
		else
		{
			_rollback();
			$xdatos['typeinfo']='Error';
			$xdatos['msg']='Registro no pudo ser ingresado!';
			$xdatos['process']='none';
		}
 }
 else {
	 _rollback();
	 $xdatos['typeinfo']='Error';
	 $xdatos['msg']='Stock insuficiente para realizar anulación!'.$stock_destino;
	 $xdatos['process']='none';
 }
echo json_encode($xdatos);
}
if (! isset ( $_REQUEST ['process'] )) {
	initial();
} else {
	if (isset ( $_REQUEST ['process'] )) {
		switch ($_REQUEST ['process']) {
			case 'formAnular' :
				initial();
				break;
				case 'deleted' :
				anular();
				break;
			}
		}
	}

	?>
