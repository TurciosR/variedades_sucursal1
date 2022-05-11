<?php
include ("_core.php");
function initial()
{
	include ("_core.php");
	$admin = $_SESSION["admin"];
		$id_pedido = $_REQUEST['id_pedido'];
		$id_sucur = $_SESSION['id_sucursal'];
		$id_user=$_SESSION["id_usuario"];
		$sql="SELECT pedido.*,cliente.nombre FROM pedido_app as pedido left join cliente on cliente.id_cliente=pedido.id_cliente WHERE  pedido.id_pedido='$id_pedido' ORDER BY 'PENDIENTE'";
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
							 FROM pedido_detalle_app as pedido_detalle
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
	$table = 'pedido_app';
	$form_data = array (
			'finalizada' => 2,
			'anulada' => 1,
		);
	$where_clause = "id_pedido='".$id_pedido."' AND id_sucursal='$id_sucursal'";
	$update = _update ( $table, $form_data, $where_clause );

	if ($update)
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
