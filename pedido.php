<?php
include_once "_core.php";

function initial() {

	$title='Pedidos';
	$_PAGE = array();
	$_PAGE ['title'] = $title;
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';

	$_PAGE ['links'] .= '<link href="css/typeahead.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/sweetalert/sweetalert.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

	$_PAGE ['links'] .= '<link rel="stylesheet" type="text/css" href="css/main_co.css">';
	$_PAGE ['links'] .= '<link rel="stylesheet" type="text/css" href="css/util_co.css">';
	include_once "header.php";
	//include_once "main_menu.php";
	$id_sucursal=$_SESSION["id_sucursal"];
	$sql="SELECT * FROM producto";

	$result=_query($sql);
	$count=_num_rows($result);
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	//permiso del script
	$fecha_actual=date("d-m-Y");

	if (false)
	{

	}
	else
	{

		$fecha_pedido = date("d-m-Y");
		$total = "";
		$lugar_entrega = "";
		$cliente = "";
		$empleado = "";
		$sucursal = "";
		$departamento = "";
		$municipio = "";
		$id_cliente = "";
		$hidden = "text";
		$hidden1 = "hidden";

		$select_depa = "<select class='form-control select_depa' id='select_depa'>";
		$sql_depa = _query("SELECT * FROM departamento");
		$cuenta = _num_rows($sql_depa);
		if($cuenta > 0)
		{
			$select_depa .= "<option value=''>Seleccione</option>";
			while ($row_depa = _fetch_array($sql_depa))
			{
				$id_departamento = $row_depa["id_departamento"];
				$descripcion = $row_depa["nombre_departamento"];
				$select_depa.= "<option value='".$id_departamento."'";
				$select_depa.=">".$descripcion."</option>";
			}
		}
		$select_depa.='</select>';

		$select_muni = "<select class='form-control select_muni' id='select_muni'>";
		$select_muni.= "<option value=''>Primero seleccione un departamento</option>";
		$select_muni.= '</select>';
	}
	if ($links!='NOT' || $admin=='1' ){

		?>
		<div id="page-wrapper" style="margin:0px;" class="gray-bg">
			<a style="display:none;" target="_blank" id="redir" href="#">a</a>
			<div class="wrapper wrapper-content  animated fadeInRight">
				<div class="row">
					<div class="col-lg-12">
						<!--Primero si e si es inv. inicial ,factura de compra, compra caja chica, traslado de otra sucursal; luego Registrar No. de Factura , lote, proveedor -->
						<div class="ibox ">
							<div class="ibox-content">
								<div class="row">
									<div class="col-lg-3">
										<div class='form-group has-info single-line'>
											<label>Cliente&nbsp;</label>

											<select class="form-control selc usage" name="id_cliente" id="id_cliente">
												<?php
												$id_cliente_bd=0;
												$sqlcli=_query("SELECT * FROM cliente  ORDER BY nombre");
												while ($row_cli = _fetch_array($sqlcli))
												{
													echo "<option value='".$row_cli["id_cliente"]."'";
													if($id_cliente_bd != "")
													{
														if ($row_cli["id_cliente"] == $id_cliente_bd)
														{
															echo " selected ";
														}
														else
														{
															if ($row_cli["id_cliente"] == 1)
															{
																echo " selected ";
															}
														}
													}

													echo ">".$row_cli["nombre"]."</option>";
												} ?>
											</select>
										</div>
									</div>
									<div class='col-lg-3'>
										<div class='form-group has-info single-line'>
											<label>Vendedor</label>
											<select class="form-control select" id="vendedor" name="vendedor" style="width:100%">
												<option value="">Seleccione</option>
												<?php
												$sql_us=_query("SELECT id_usuario,nombre FROM usuario");
												while ($row_us=_fetch_array($sql_us))
												{
													$id_usuario = $row_us['id_usuario'];
													$nombre = $row_us['nombre'];
													echo "<option value=' $id_usuario ' >".MAYU($nombre)."</option>";
												}
												?>
											</select>
										</div>
									</div>
									<div class='col-lg-2'>
										<div class='form-group has-info single-line'>
											<label>Fecha</label>
											<input type='text' readonly class='form-control text-center' value='<?php echo $fecha_pedido ?>' id='fecha1' name='fecha1'></div>
										</div>
										<div class='col-lg-2'>
											<div class='form-group has-info single-line'>
												<label>Fecha entrega</label>
												<input type='text' readonly class='form-control text-center' value='<?php echo date("d-m-Y") ?>' id='fecha_entrega' name='fecha_entrega'></div>
											</div>
											<div class="col-lg-2">
												<div class='form-group has-info single-line'>
													<label>Origen</label>
													<select name='origen' id="origen" class="form-control select">
														<?php
														$id_sucursal=$_SESSION['id_sucursal'];
														$sql = _query("SELECT * FROM ubicacion WHERE id_sucursal='$id_sucursal' ORDER BY descripcion ASC");
														while($row = _fetch_array($sql))
														{
															// code...
															echo "<option value='".$row["id_ubicacion"]."'>".MAYU(utf8_decode($row["descripcion"]))."</option>";
														}
														?>
													</select>
												</div>
											</div>
										</div>
										<div class="row caja_datos">
											<div class="col-lg-3">
												<div class='form-group has-info single-line'>
													<label>Observacion</label>
													<input type="text" id="transporte" name="transporte" size="20" class="direccion form-control" placeholder="Observacion">
												</div>
											</div>
											<div class="col-lg-3">
												<div class='form-group has-info single-line'>
													<label>Dirección</label>
													<input type="text" id="direccion" name="direccion" size="20" class="direccion form-control" placeholder="Dirección" value="<?php echo $lugar_entrega; ?>">
												</div>
											</div>
											<div class='col-lg-3'>
												<div class='form-group has-info single-line'>
													<label>Departamento</label>
													<div class="depa">
														<?php
														echo $select_depa;
														?>
													</div>
												</div>
											</div>
											<div class='col-lg-3'>
												<div class='form-group has-info single-line'>
													<label>Municipio</label>
													<div class="muni">
														<?php
														echo $select_muni;
														?>
													</div>
												</div>
											</div>
										</div>
										<div class='row'>
											<div class="col-lg-12">
												<div class='form-group has-info single-line'>
													<label>Comentario</label>
													<textarea style='width:100%' id="comentario" name="comentario" rows="2" cols="80"></textarea>
												</div>
											</div>
										</div>
										<div class="row" id='buscador'>
											<div class="col-lg-6">
												<div id="a">
													<label>Buscar Producto (Código)</label>
														<input type="text" id="codigo" name="codigo" style="width:100% !important" class="form-control usage" placeholder="Ingrese Código de producto" style="border-radius:0px">
												</div>
												<div hidden id="b">
													<label id='buscar_habilitado'>Buscar Producto (Descripción)</label>
													<div id="scrollable-dropdown-menu">
														<input type="text" id="producto_buscar" name="producto_buscar" style="width:100% !important" class=" form-control usage typeahead" placeholder="Ingrese la Descripción de producto" data-provide="typeahead" style="border-radius:0px">
													</div>
												</div>

											</div>
											<div class="col-lg-6">
												<br>
												<a class="btn btn-danger pull-right" style="margin-left:1%;" href="dashboard.php" id='salir'><i class="fa fa-mail-reply"></i> F4 Salir</a>
												<input type="submit" id="submit1" name="submit1" value="Guardar e Imprimir" class="btn btn-primary  pull-right"  style="margin-left:5px;"/>
												<input type="hidden" id="id_pedido" name="id_pedido" value="" />
											</div>
										</div>
										<div class="ibox">
											<div class="row">
												<div class="ibox-content">
													<!--load datables estructure html-->


													<div class="row">
														<div class="col-md-12">
															<div class="wrap-table1001">
																<div class="table100 ver1 m-b-10">
																	<div class="table100-head">
																		<table id="inventable1">
																			<thead>
																				<tr class="row100 head">
																					<th hidden class="success cell100 column10">Id</th>
																					<th class='success  cell100 column30'>Descripci&oacute;n</th>
																					<th class='success  cell100 column10'>Stock</th>
																					<th class='success  cell100 column10'>Cantidad</th>
																					<th class='success  cell100 column10'>Presentación</th>
																					<th class='success  cell100 column10'>Precio</th>
																					<th class='success  cell100 column10'>$</th>
																					<th class='success  cell100 column10'>Subtotal</th>
																					<th class='success  cell100 column10'>Acci&oacute;n</th>
																				</tr>
																			</thead>
																		</table>
																	</div>
																	<div class="" >
																		<table>
																			<tbody id="mostrardatos"></tbody>
																		</table>
																	</div>
																	<div class="table101-body">
																		<table>
																			<tbody>
																				<tr class='red'>
																					<td class="cell100 column100">&nbsp;</td>
																				</tr>
																				<tr>
																					<td class='cell100 column50 text-bluegrey'  id='totaltexto'>&nbsp;</td>
																					<td class='cell100 column15 leftt  text-bluegrey ' >CANT. PROD:</td>
																					<td class='cell100 column10 text-right text-danger' id='totcant'>0.00</td>
																					<td class="cell100 column10  leftt text-bluegrey ">TOTALES $:</td>
																					<td class='cell100 column15 text-right text-green' id='total_gravado'>0.00</td>

																				</tr>
																				<tr hidden>
																					<td class="cell100 column15 leftt text-bluegrey ">SUMAS (SIN IVA) $:</td>
																					<td  class="cell100 column10 text-right text-green" id='total_gravado_sin_iva'>0.00</td>
																					<td class="cell100 column15  leftt  text-bluegrey ">IVA  $:</td>
																					<td class="cell100 column10 text-right text-green " id='total_iva'>0.00</td>
																					<td class="cell100 column15  leftt text-bluegrey ">SUBTOTAL  $:</td>
																					<td class="cell100 column10 text-right  text-green" id='total_gravado_iva'>0.00</td>
																					<td class="cell100 column15 leftt  text-bluegrey ">VENTA EXENTA $:</td>
																					<td class="cell100 column10  text-right text-green" id='total_exenta'>0.00</td>
																				</tr>
																				<tr hidden>
																					<td class="cell100 column15 leftt text-bluegrey ">PERCEPCION $:</td>
																					<td class="cell100 column10 text-right  text-green"  id='total_percepcion'>0.00</td>
																					<td class="cell100 column15  leftt  text-bluegrey ">RETENCION $:</td>
																					<td class="cell100 column10 text-right text-green" id='total_retencion'>0.00</td>
																					<td class="cell100 column15 leftt text-bluegrey ">DESCUENTO $:</td>
																					<td class="cell100 column10  text-right text-green"  id='total_final'>0.00</td>
																					<td class="cell100 column15 leftt  text-bluegrey">A PAGAR $:</td>
																					<td class="cell100 column10  text-right text-green"  id='monto_pago'>0.00</td>
																				</tr>
																			</tbody>
																		</table>
																	</div>
																</div>
															</div>
														</div>
													</div>

													<section>
														<input type="hidden" name="autosave" id="autosave" value="false-0">
													</section>

													<input type="hidden" name="process" id="process" value="insert"><br>
													<div>
													</div>
												</div>
												<input type="hidden" id="filas" name="filas" value="0">
											</div>
										</div>
										<div class='modal fade' id='viewProd' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
											<div class='modal-dialog'>
												<div class='modal-content'></div><!-- /.modal-content -->
											</div><!-- /.modal-dialog -->
										</div><!-- /.modal -->
									</div><!--div class='ibox-content'-->
								</div>
							</div>
						</div>
					</div>

					<?php
					include_once ("footer.php");

					// echo "<script src='js/plugins/typehead/bootstrap3-typeahead.js'></script>";4
					$a = rand(0,999);
					echo "<script src='js/funciones/pedido.js?$a'></script>";
				} //permiso del script
				else {
					echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
				}
			}

			function traerdatos()
			{
				$start = !empty($_POST['page'])?$_POST['page']:0;
				$limit =$_POST['records'];
				$sortBy = $_POST['sortBy'];
				$producto_buscar = $_POST['producto_buscar'];
				$origen = $_POST['origen'];

				$sqlJoined="SELECT pr.id_producto,pr.descripcion, pr.barcode FROM
				producto AS pr";
				//  $sqlParcial=get_sql($keywords, $id_color, $estilo, $talla, $barcode, $limite);
				$sqlParcial= get_sql($start,$limit,$producto_buscar,$origen,$sortBy);
				$groupBy="";
				$limitSQL= " ";
				$sql_final= $sqlJoined." ".$sqlParcial." ".$groupBy." ".$limitSQL;
				$query = _query($sql_final);

				echo _error();
				$num_rows = _num_rows($query);
				$filas=0;
				if ($num_rows > 0)
				{
					while ($row = _fetch_array($query))
					{
						$id_producto = $row['id_producto'];
						$sql_existencia = _query("SELECT sum(cantidad) as existencia FROM stock_ubicacion WHERE id_producto='$id_producto' AND stock_ubicacion.id_ubicacion='$origen'");
						$dt_existencia = _fetch_array($sql_existencia);
						$existencia = round($dt_existencia["existencia"]);
						$descripcion=$row["descripcion"];
						$barcode = $row['barcode'];
						$sql_p=_query("SELECT presentacion.nombre, prp.descripcion,prp.id_pp as id_presentacion,prp.unidad,prp.costo,prp.precio
							FROM presentacion_producto AS prp
							JOIN presentacion ON presentacion.id_presentacion=prp.id_presentacion
							WHERE prp.id_producto=$id_producto
							AND prp.activo=1");
							$i=0;
							$unidadp=0;
							$costop=0;
							$preciop=0;
							$descripcionp="";
							$select="<select class='sel'>";
							while ($row=_fetch_array($sql_p))
							{
								if ($i==0)
								{
									$unidadp=$row['unidad'];
									$costop=$row['costo'];
									$preciop=$row['precio'];
									$descripcionp=$row['descripcion'];
								}
								$select.="<option value='".$row["id_presentacion"]."'>".$row["nombre"]." (".$row["unidad"].")</option>";
								$i=$i+1;
							}
							$select.="</select>";
							$input = "<input type='text' class='cant form-control numeric' style='width:100%;'>";
							?>
							<tr>
								<td class='col-lg-5'> <input type='hidden' class='id_producto' name='' value='<?php echo $id_producto ?>'> <input type='hidden' class="unidad" value='<?php echo $unidadp; ?>'><?php echo $descripcion; ?></td>
								<td class='col-lg-1 text-center'><?php echo $select; ?></td>
								<td class='col-lg-1 text-center descp'><?php echo $descripcionp; ?></td>
								<td style="display:none;" class='col-lg-1 text-center precio_compra'><?php echo $costop; ?></td>
								<td class='text-center precio_venta'><?php echo $preciop; ?></td>
								<td class='col-lg-1 text-center exis'><?php echo $existencia; ?></td>
								<td class='col-lg-1 text-center'><?php echo $input; ?></td>
								<td class='col-lg-1 text-center subtotal'><?php echo "0.0000" ?></td>
								<td class='col-lg-2 text-center'>
									<a data-toggle='modal' href='ver_imagen.php?id_producto=<?php echo $id_producto; ?>'  data-target='#viewProd' data-refresh='true' class="btn btn-primary btnViw"><i class="fa fa-eye"></i></a>
									<button class="btn btn-danger btnDelete"> <i class="fa fa-trash"></i> </button>
								</td>
							</tr>
							<?php
							$filas+=1;
						}
					}
				}

				function get_sql($start,$limit,$producto_buscar,$origen,$sortBy)
				{
					$andSQL='';
					$id_sucursal= $_SESSION['id_sucursal'];
					$whereSQL=" WHERE
					";
					$andSQL.= "pr.id_producto = '$producto_buscar'";
					$orderBy="";
					$sql_parcial=$whereSQL.$andSQL.$orderBy;
					return $sql_parcial;
				}

				function pedido()
				{
					//- hacer edicion de entradas
					// facturacion
					$comentario = "";
					if(isset($_REQUEST['comentario']))
					{
						$comentario=$_REQUEST['comentario'];
					}
					$cuantos = $_POST['cuantos'];
					$stringdatos = $_POST['stringdatos'];
					$fecha_movimiento= $_POST['fecha_movimiento'];
					$fecha_entrega = $_POST['fecha_entrega'];

					$total_compras = round($_POST['total_compras'],2);

					$id_sucursal=$_SESSION["id_sucursal"];
					$id_usuario=$_SESSION['id_usuario'];

					$departamento = $_POST["select_depa"];
					$municipio = $_POST["select_muni"];
					$direccion = $_POST["direccion"];
					$id_cliente = $_POST["id_cliente"];
					$origen = $_POST['origen'];
					$transporte = $_POST["transporte"];

					$id_vendedor = $_POST['id_vendedor'];


					$insertar1=false;
					$insertar2=false;
					$insertarM=false;
					$fecha=date("Y-m-d");
					$hora=date("H:i:s");
					_begin();


					$n=10;
					$numero_doc="P".date("dmyHis");

					$table='pedido';
					$form_data = array(
						'id_cliente' => $id_cliente,
						'fecha' => $fecha,
						'total' => $total_compras,
						'numero_doc' => $numero_doc,
						'id_usuario' => $id_usuario,
						'id_empleado' => $id_vendedor,
						// 'fecha_factura' => $fecha,
						'fecha_entrega' => MD($fecha_entrega),
						'lugar_entrega' => $direccion,
						'id_departamento' => $departamento,
						'id_municipio' => $municipio,
						'id_sucursal' => $id_sucursal,
						'transporte' => $transporte,
						'hora_pedido' => $hora,
						'origen' => $origen,
						'observaciones' => $comentario,
					);
					$insertarM = _insert($table,$form_data);
					$id_factura=_insert_id();

					$concepto="PEDIDO PRODUCTO";
					$table='movimiento_producto';
					$form_data = array(
						'id_sucursal' => $id_sucursal,
						'correlativo' => $numero_doc,
						'concepto' => "PEDIDO PRODUCTO",
						'total' => $total_compras,
						'tipo' => 'SALIDA',
						'proceso' => 'PED',
						'referencia' => $numero_doc,
						'id_empleado' => $id_usuario,
						'fecha' => $fecha,
						'hora' => $hora,
						'id_suc_origen' => $id_sucursal,
						// 'id_suc_destino' => $id_suc_destino,
						'id_proveedor' => 0,
						'id_traslado' => $id_factura,
					);
					$insert_mov =_insert($table,$form_data);

					echo _error();
					$id_movimiento=_insert_id();

					if ($cuantos>0)
					{
						$listadatos=explode('#',$stringdatos);
						for ($i=0;$i<$cuantos ;$i++)
						{
							list($id_producto,$precio_venta,$cantidad,$subtotal,$unidad,$id_presentacion)=explode('|',$listadatos[$i]);

							$id_producto;
							$cantidad=$cantidad*$unidad;
							$a_transferir=$cantidad;
							$sql_get_p=_fetch_array(_query("SELECT presentacion_producto.id_presentacion as presentacion,presentacion_producto.id_server,producto.id_server as id_server_prod FROM presentacion_producto JOIN producto ON presentacion_producto.id_producto=producto.id_producto WHERE id_pp=$id_presentacion"));
							$presentacion=$sql_get_p['presentacion'];
							$id_server_presen=$sql_get_p['id_server'];
							$id_server_prod=$sql_get_p['id_server_prod'];

							$subtotal = $precio_venta * $cantidad;

							$table1= 'pedido_detalle';
							$form_data1 = array(
								'id_prod_serv' => $id_producto,
								'cantidad' => $cantidad,
								'precio_venta' =>$precio_venta,
								'subtotal' =>  $subtotal,
								'id_presentacion' => $id_presentacion,
								'id_empleado' => $id_usuario,
								'unidad' => $unidad,
								'id_pedido' => $id_factura,
							);


							if ($cantidad>0)
							{
								$insertar1 = _insert($table1,$form_data1 );
								echo _error();
							}

							$sql=_query("SELECT * FROM stock_ubicacion WHERE stock_ubicacion.id_producto=$id_producto AND stock_ubicacion.id_ubicacion=$origen AND stock_ubicacion.cantidad!=0 ORDER BY id_posicion DESC ,id_estante DESC ");

							while ($rowsu=_fetch_array($sql))
							{
								# code...

								$id_su1=$rowsu['id_su'];
								$stock_anterior=$rowsu['cantidad'];

								if ($a_transferir!=0) {
									# code...

									$transfiriendo=0;
									$nuevo_stock=$stock_anterior-$a_transferir;
									if ($nuevo_stock<0) {
										# code...
										$transfiriendo=$stock_anterior;
										$a_transferir=$a_transferir-$stock_anterior;
										$nuevo_stock=0;
									}
									else
									{
										if ($nuevo_stock>0) {
											# code...
											$transfiriendo=$a_transferir;
											$a_transferir=0;
											$nuevo_stock=$stock_anterior-$transfiriendo;
										}
										else {
											# code...
											$transfiriendo=$stock_anterior;
											$a_transferir=0;
											$nuevo_stock=0;

										}
									}

									$table="stock_ubicacion";
									$form_data = array(
										'cantidad' => $nuevo_stock,
									);
									$where_clause="id_su='".$id_su1."'";
									$update=_update($table,$form_data,$where_clause);
									if ($update) {
										# code...
									}
									else {
										$up=0;
									}

									/*actualizando el stock del local de venta*/
									$sql1a=_fetch_array(_query("SELECT ubicacion.id_ubicacion FROM ubicacion WHERE id_sucursal=$id_sucursal AND bodega=0"));
									$id_ubicaciona=$sql1a['id_ubicacion'];
									$sql2a=_fetch_array(_query("SELECT SUM(stock_ubicacion.cantidad) as stock FROM stock_ubicacion WHERE id_producto=$id_producto AND stock_ubicacion.id_ubicacion=$id_ubicaciona"));
									$table='stock';
									$form_data = array(
										'stock_local' => $sql2a['stock'],
									);
									$where_clause="id_producto='".$id_producto."' AND id_sucursal=$id_sucursal";
									$updatea=_update($table,$form_data,$where_clause);
									/*finalizando we*/

									$table="movimiento_stock_ubicacion";
									$form_data = array(
										'id_producto' => $id_producto,
										'id_origen' => $id_su1,
										'id_destino'=> 0,
										'cantidad' => $transfiriendo,
										'fecha' => $fecha,
										'hora' => $hora,
										'anulada' => 0,
										'afecta' => 0,
										'id_sucursal' => $id_sucursal,
										'id_presentacion'=> $id_presentacion,
										'id_mov_prod' => $id_movimiento,
									);

									$insert_mss =_insert($table,$form_data);

									if ($insert_mss) {
										# code...
									}
									else {
										# code...
										$z=0;
									}

								}

							}

							$sql2="SELECT stock FROM stock WHERE id_producto='$id_producto' AND id_sucursal='$id_sucursal'";
							$stock2=_query($sql2);
							$nrow2=_num_rows($stock2);
							if ($nrow2>0)
							{
								$row2=_fetch_array($stock2);
								$existencias=$row2['stock'];
							}
							else
							{
								$existencias=0;
							}

							/*significa que no hay suficientes unidades en el stock_ubicacion para realizar el descargo*/
							if ($a_transferir>0) {
								/*verificamos si se desconto algo de stock_ubicacion*/

								if($a_transferir!=$cantidad)
								{/*si entra aca significa que se descontaron algunas unidades de stock_ubicacion y hay que descontarlas de stock y lote*/
									/*se insertara la diferencia entre el stock_ubicacion y la cantidad a descontar en la tabla de movimientos pendientes*/
									$table1= 'movimiento_producto_detalle';
									$cant_total=$existencias-($cantidad-$a_transferir);
									$form_data1 = array(
										'id_movimiento'=>$id_movimiento,
										'id_producto' => $id_producto,
										'cantidad' => ($cantidad-$a_transferir),
										'costo' => $precio_compra,
										'precio' => $precio_venta,
										'stock_anterior'=>$existencias,
										'stock_actual'=>$cant_total,
										'lote' => 0,
										'id_presentacion' => $id_presentacion,
										'fecha' =>  $fecha,
										'hora' => $hora
									);
									$insert_mov_det = _insert($table1,$form_data1);
									if(!$insert_mov_det)
									{
										$j = 0;
									}


									$table2= 'stock';
									if($nrow2==0)
									{
										$form_data2 = array(
											'id_producto' => $id_producto,
											'stock' => 0,
											'costo_unitario'=>round(($precio_compra/$unidades),2),
											'precio_unitario'=>round(($precio_venta/$unidades),2),
											'create_date'=>$fecha_movimiento,
											'update_date'=>$fecha_movimiento,
											'id_sucursal' => $id_sucursal
										);
										$insert_stock = _insert($table2,$form_data2 );
									}
									else
									{
										$form_data2 = array(
											'id_producto' => $id_producto,
											'stock' => $cant_total,
											'costo_unitario'=>round(($precio_compra/$unidades),2),
											'precio_unitario'=>round(($precio_venta/$unidades),2),
											'update_date'=>$fecha_movimiento,
											'id_sucursal' => $id_sucursal
										);
										$where_clause="WHERE id_producto='$id_producto' and id_sucursal='$id_sucursal'";
										$insert_stock = _update($table2,$form_data2, $where_clause );
									}
									if(!$insert_stock)
									{
										$k = 0;
									}

									/*arreglando problema con lotes de nuevo*/
									$cantidad_a_descontar=($cantidad-$a_transferir);
									$sql=_query("SELECT id_lote, id_producto, fecha_entrada, vencimiento, cantidad
										FROM lote
										WHERE id_producto='$id_producto'
										AND id_sucursal='$id_sucursal'
										AND cantidad>0
										AND estado='VIGENTE'
										ORDER BY vencimiento");


										$contar=_num_rows($sql);
										$insert=1;
										if ($contar>0) {
											# code...
											while ($row=_fetch_array($sql)) {
												# code...
												$entrada_lote=$row['cantidad'];
												if ($cantidad_a_descontar>0) {
													# code...
													if ($entrada_lote==0) {
														$table='lote';
														$form_dat_lote=$arrayName = array(
															'estado' => 'FINALIZADO',
														);
														$where = " WHERE id_lote='$row[id_lote]'";
														$insert=_update($table,$form_dat_lote,$where);
													} else {
														if (($entrada_lote-$cantidad_a_descontar)>0) {
															# code...
															$table='lote';
															$form_dat_lote=$arrayName = array(
																'cantidad'=>($entrada_lote-$cantidad_a_descontar),
																'estado' => 'VIGENTE',
															);
															$cantidad_a_descontar=0;

															$where = " WHERE id_lote='$row[id_lote]'";
															$insert=_update($table,$form_dat_lote,$where);
														} else {
															# code...
															if (($entrada_lote-$cantidad_a_descontar)==0) {
																# code...
																$table='lote';
																$form_dat_lote=$arrayName = array(
																	'cantidad'=>($entrada_lote-$cantidad_a_descontar),
																	'estado' => 'FINALIZADO',
																);
																$cantidad_a_descontar=0;

																$where = " WHERE id_lote='$row[id_lote]'";
																$insert=_update($table,$form_dat_lote,$where);
															}
															else
															{
																$table='lote';
																$form_dat_lote=$arrayName = array(
																	'cantidad'=>0,
																	'estado' => 'FINALIZADO',
																);
																$cantidad_a_descontar=$cantidad_a_descontar-$entrada_lote;
																$where = " WHERE id_lote='$row[id_lote]'";
																$insert=_update($table,$form_dat_lote,$where);
															}
														}
													}
												}
											}
										}
										/*fin arreglar problema con lotes*/
										if(!$insert)
										{
											$l = 0;
										}

										$table1= 'movimiento_producto_pendiente';
										$cant_total=$existencias-$cantidad;
										$form_data1 = array(
											'id_movimiento'=>$id_movimiento,
											'id_producto' => $id_producto,
											'id_presentacion' => $id_presentacion,
											'cantidad' => $a_transferir,
											'costo' => $precio_compra,
											'precio' => $precio_venta,
											'fecha' =>  $fecha,
											'hora' => $hora,
											'id_sucursal' => $id_sucursal
										);
										$insert_mov_det = _insert($table1,$form_data1);
										if(!$insert_mov_det)
										{
											$j = 0;
										}

									}
									else
									{/*significa que no hay nada en stock_ubicacion y no se puede descontar de stock_ubicacion ni de stock*/
										/*se insertara todo en la tabla de movimientos pendientes*/

										$table1= 'movimiento_producto_pendiente';
										$cant_total=$existencias-$cantidad;
										$form_data1 = array(
											'id_movimiento'=>$id_movimiento,
											'id_producto' => $id_producto,
											'id_presentacion' => $id_presentacion,
											'cantidad' => $cantidad,
											'costo' => $precio_compra,
											'precio' => $precio_venta,
											'fecha' =>  $fecha,
											'hora' => $hora,
											'id_sucursal' => $id_sucursal
										);
										$insert_mov_det = _insert($table1,$form_data1);
										if(!$insert_mov_det)
										{
											$j = 0;
										}
									}
								}

								else {

									$table1= 'movimiento_producto_detalle';
									$cant_total=$existencias-$cantidad;
									$form_data1 = array(
										'id_movimiento'=>$id_movimiento,
										'id_producto' => $id_producto,
										'cantidad' => $cantidad,
										// 'costo' => $precio_venta,
										'precio' => $precio_venta,
										'stock_anterior'=>$existencias,
										'stock_actual'=>$cant_total,
										'lote' => 0,
										'id_presentacion' => $id_presentacion,
										'fecha' =>  $fecha,
										'hora' => $hora
									);
									$insert_mov_det = _insert($table1,$form_data1);
									if(!$insert_mov_det)
									{
										$j = 0;
									}


									$table2= 'stock';
									if($nrow2==0)
									{
										$cant_total=$cantidad;
										$form_data2 = array(
											'id_producto' => $id_producto,
											'stock' => $cant_total,
											// 'costo_unitario'=>round(($precio_compra/$unidades),2),
											'precio_unitario'=>round(($precio_venta/$unidad),2),
											'create_date'=>$fecha_movimiento,
											'update_date'=>$fecha_movimiento,
											'id_sucursal' => $id_sucursal
										);
										$insert_stock = _insert($table2,$form_data2 );
									}
									else
									{
										$cant_total=$existencias-$cantidad;
										$form_data2 = array(
											'id_producto' => $id_producto,
											'stock' => $cant_total,
											// 'costo_unitario'=>round(($precio_compra/$unidades),2),
											'precio_unitario'=>round(($precio_venta/$unidad),2),
											'update_date'=>$fecha_movimiento,
											'id_sucursal' => $id_sucursal
										);
										$where_clause="WHERE id_producto='$id_producto' and id_sucursal='$id_sucursal'";
										$insert_stock = _update($table2,$form_data2, $where_clause );
									}
									if(!$insert_stock)
									{
										$k = 0;
									}

									/*arreglando problema con lotes de nuevo*/
									$cantidad_a_descontar=$cantidad;
									$sql=_query("SELECT id_lote, id_producto, fecha_entrada, vencimiento, cantidad
										FROM lote
										WHERE id_producto='$id_producto'
										AND id_sucursal='$id_sucursal'
										AND cantidad>0
										AND estado='VIGENTE'
										ORDER BY vencimiento");


										$contar=_num_rows($sql);
										$insert=1;
										if ($contar>0) {
											# code...
											while ($row=_fetch_array($sql)) {
												# code...
												$entrada_lote=$row['cantidad'];
												if ($cantidad_a_descontar>0) {
													# code...
													if ($entrada_lote==0) {
														$table='lote';
														$form_dat_lote=$arrayName = array(
															'estado' => 'FINALIZADO',
														);
														$where = " WHERE id_lote='$row[id_lote]'";
														$insert=_update($table,$form_dat_lote,$where);
													} else {
														if (($entrada_lote-$cantidad_a_descontar)>0) {
															# code...
															$table='lote';
															$form_dat_lote=$arrayName = array(
																'cantidad'=>($entrada_lote-$cantidad_a_descontar),
																'estado' => 'VIGENTE',
															);
															$cantidad_a_descontar=0;

															$where = " WHERE id_lote='$row[id_lote]'";
															$insert=_update($table,$form_dat_lote,$where);
														} else {
															# code...
															if (($entrada_lote-$cantidad_a_descontar)==0) {
																# code...
																$table='lote';
																$form_dat_lote=$arrayName = array(
																	'cantidad'=>($entrada_lote-$cantidad_a_descontar),
																	'estado' => 'FINALIZADO',
																);
																$cantidad_a_descontar=0;

																$where = " WHERE id_lote='$row[id_lote]'";
																$insert=_update($table,$form_dat_lote,$where);
															}
															else
															{
																$table='lote';
																$form_dat_lote=$arrayName = array(
																	'cantidad'=>0,
																	'estado' => 'FINALIZADO',
																);
																$cantidad_a_descontar=$cantidad_a_descontar-$entrada_lote;
																$where = " WHERE id_lote='$row[id_lote]'";
																$insert=_update($table,$form_dat_lote,$where);
															}
														}
													}
												}
											}
										}
										/*fin arreglar problema con lotes*/
										if(!$insert)
										{
											$l = 0;
										}

									}

								}//for
							}//if
							// echo $insertarM."\n";
							// echo $insertar1;
							if ($insertar1 && $insertarM){
								_commit();
								$xdatos['typeinfo']='Success';
								$xdatos['msg']='Registro de Inventario Actualizado !';
								$xdatos['process']='insert';
								$xdatos['id_pedido']=$id_factura;
							}
							else{
								_rollback();
								$xdatos['typeinfo']='Error';
								$xdatos['msg']='Registro de Inventario no pudo ser Actualizado !';
							}
							echo json_encode($xdatos);
						}

						function consultar_stock()
						{
							$id_origen = $_REQUEST['id_origen'];
							$tipo = $_POST['tipo'];
							$id_producto = $_REQUEST['id_producto'];
							$id_usuario=$_SESSION["id_usuario"];
							$r_precios=_fetch_array(_query("SELECT precios FROM usuario WHERE id_usuario=$id_usuario"));
							$precios=$r_precios['precios'];
							$limit="LIMIT ".$precios;
							$id_sucursal=$_SESSION['id_sucursal'];
							$id_factura=$_REQUEST['id_factura'];
							$precio=0;
							$id_presentacione = 0;
							$categoria="";
							if($tipo == "D")
							{
								$clause = "p.id_producto = '$id_producto'";
							}
							else
							{
								$sql_aux = _query("SELECT id_pp as id_presentacion, id_producto FROM presentacion_producto WHERE barcode='$id_producto' AND activo='1'");
								if(_num_rows($sql_aux)>0)
								{
									$dats_aux = _fetch_array($sql_aux);
									$id_producto = $dats_aux["id_producto"];
									//$id_presentacione = $dats_aux["id_presentacion"];
									$clause = "p.id_producto = '$id_producto'";
								}
								else
								{
									$clause = "p.barcode = '$id_producto'";
								}
							}
							$sql1 = "SELECT p.id_producto,p.id_categoria, p.barcode, p.descripcion, p.estado, p.perecedero, p.exento, p.id_categoria, p.id_sucursal,SUM(su.cantidad) as stock
							FROM producto AS p
							JOIN stock_ubicacion as su ON su.id_producto=p.id_producto
							JOIN ubicacion as u ON u.id_ubicacion=su.id_ubicacion
							WHERE $clause
							AND u.id_ubicacion=$id_origen
							AND su.id_sucursal=$id_sucursal";
							$stock1=_query($sql1);
							$row1=_fetch_array($stock1);
							$nrow1=_num_rows($stock1);
							if ($nrow1>0)
							{
								if($row1["descripcion"] != "" && $row1["descripcion"] != null)
								{
									$id_productov = $row1['id_producto'];
									$id_producto = $row1['id_producto'];
									$sql_exis = _query("SELECT stock FROM stock WHERE id_producto = '$id_productov'");
									$datos_exis = _fetch_array($sql_exis);
									$stockv = $datos_exis["stock"];
									if(!($stockv > 0))
									{
										$stockv = 0;
									}
									$hoy=date("Y-m-d");
									$perecedero=$row1['perecedero'];
									$barcode = $row1["barcode"];
									$descripcion = $row1["descripcion"];
									$estado = $row1["estado"];
									$perecedero = $row1["perecedero"];
									$exento = $row1["exento"];
									$categoria=$row1['id_categoria'];
									$sql_res_pre=_fetch_array(_query("SELECT SUM(factura_detalle.cantidad) as reserva FROM factura JOIN factura_detalle ON factura_detalle.id_factura=factura.id_factura WHERE factura_detalle.id_prod_serv=$id_producto AND factura.id_sucursal=$id_sucursal AND factura.fecha = '$hoy' AND factura.finalizada=0 "));
									$reserva=$sql_res_pre['reserva'];
									$sql_res_esto=_fetch_array(_query("SELECT SUM(pedido_detalle.cantidad) as reservado FROM pedido JOIN pedido_detalle ON pedido_detalle.id_pedido=pedido.id_pedido WHERE pedido_detalle.id_prod_serv=$id_producto AND pedido.id_pedido=$id_factura"));
									$reservado=$sql_res_esto['reservado'];
									$stock= $row1["stock"]-$reserva+$reservado;
									if($stock<0)
									{
										$stock=0;
									}

									$i=0;
									$unidadp=0;
									$preciop=0;
									$descripcionp=0;
									$select_rank="<select class='sel_r form-control'>";
									$anda = "";
									if($id_presentacione > 0)
									{
										$anda = "AND presentacion_producto.id_presentacion = '$id_presentacione'";
									}
									$sql_p=_query("SELECT presentacion.nombre, presentacion_producto.descripcion,presentacion_producto.id_pp as id_presentacion,presentacion_producto.unidad,presentacion_producto.precio
										FROM presentacion_producto
										JOIN presentacion ON presentacion.id_presentacion=presentacion_producto.id_presentacion
										WHERE presentacion_producto.id_producto='$id_producto'
										AND presentacion_producto.activo=1
										$anda
										ORDER BY presentacion_producto.unidad ASC");
										$select="<select class='sel form-control'>";
										while ($row=_fetch_array($sql_p))
										{
											if ($i==0)
											{
												$id_press=$row["id_presentacion"];
												$unidadp=$row['unidad'];
												$preciop=$row['precio'];
												$descripcionp=$row['descripcion'];
												$preciosArray = _getPrecios($id_press, $precios);
												$xc=0;
												foreach ($preciosArray as $key => $value) {
													if ($value>0)
													{
														$select_rank.="<option value='$value'";
														if ($xc==0) {
															$select_rank.=" selected ";
															$preciop=$value;
															$xc = 1;
														}
														$select_rank.=">$value</option>";
													}
												}
												//$select_rank.="<option value='0.0'>0.0</option>";
												$select_rank.="</select>";
											}
											$select.="<option value='".$row["id_presentacion"]."'";
											if($id_presentacione == $row["id_presentacion"])
											{
												$select.=" selected ";
											}
											$select.=">$row[nombre]</option>";
											$i=$i+1;
										}


										$select.="</select>";
										$xdatos['perecedero']=$perecedero;
										$xdatos['descripcion']= $descripcion;
										$xdatos['id_producto']= $id_productov;
										$xdatos['select']= $select;
										$xdatos['select_rank']= $select_rank;
										$xdatos['stock']= $stock;
										$xdatos['preciop']= $preciop;

										$sql_e=_fetch_array(_query("SELECT exento FROM producto WHERE id_producto=$id_producto"));
										$exento=$sql_e['exento'];
										if ($exento==1) {
											# code...
											$xdatos['preciop_s_iva']=$preciop;
										}
										else {
											# code...
											$sqkl=_fetch_array(_query("SELECT iva FROM sucursal WHERE id_sucursal=$id_sucursal"));
											$iva=$sqkl['iva']/100;
											$iva=1+$iva;
											$xdatos['preciop_s_iva']= round(($preciop/$iva),8,PHP_ROUND_HALF_DOWN);
										}
										$xdatos['unidadp']= $unidadp;
										$xdatos['descripcionp']= $descripcionp;
										$xdatos['exento']=$exento;
										$xdatos['categoria']=$categoria;
										$xdatos['typeinfo']="Success";

										echo json_encode($xdatos); //Return the JSON Array
									}
									else
									{
										$xdatos['typeinfo']="Error";
										$xdatos['msg']="El codigo ingresado no pertenece a nungun producto";
										echo json_encode($xdatos); //Return the JSON Array
									}
								}
							}

							function getpresentacion()
							{
								$id_presentacion =$_REQUEST['id_presentacion'];
								$sql=_fetch_array(_query("SELECT * FROM `presentacion_producto` WHERE id_pp=$id_presentacion"));
								$id_producto=$sql['id_producto'];
								$precio=$sql['precio'];
								$unidad=$sql['unidad'];
								$descripcion=$sql['descripcion'];

								$costo=0;
								if ($sql['costo']==0) {
									# code...
									$sql_max=_query("SELECT MAX(id_movimiento_producto) as id FROM movimiento_producto WHERE id_producto=$id_producto AND salida=0 AND entrada>0 AND precio_compra>0 AND id_presentacion=$sql[id_presentacion] ");
									$_f_a=_fetch_array($sql_max);
									$id=$_f_a['id'];
									if ($id!=null) {
										# code...
										$sql_costo=_fetch_array(_query("SELECT * FROM movimiento_producto WHERE id_movimiento_producto=$id"));
										$costo=$sql_costo['precio_compra'];
									}

								}
								else {
									# code...
									$costo=$sql['costo'];
								}


								$xdatos['precio']=$precio;
								$xdatos['unidad']=$unidad;
								$xdatos['descripcion']=$descripcion;
								$xdatos['costo']=$costo;

								echo json_encode($xdatos);
							}

							function datos_cliente()
							{
								$id_cliente = $_POST['id_cliente'];
								$sql_cliente = _query("SELECT * FROM cliente WHERE id_cliente = '$id_cliente'");
								$row = _fetch_array($sql_cliente);

								$direccion = $row["direccion"];
								$departamento = $row["depto"];
								$municipio = $row["municipio"];

								$select_depa = "<select class='form-control select_depa' id='select_depa'>";
								$sql_depa = _query("SELECT * FROM departamento");
								$cuenta = _num_rows($sql_depa);
								$xi=0;
								if($cuenta > 0)
								{
									while ($row_depa = _fetch_array($sql_depa))
									{
										$id_departamento = $row_depa["id_departamento"];
										$descripcion = $row_depa["nombre_departamento"];
										$select_depa.= "<option value='".$id_departamento."'";
										if($id_departamento == $departamento)
										{
											$select_depa.= " selected";
											$xi=1;
										}
										$select_depa.=">".$descripcion."</option>";
									}
								}
								if($xi==0)
								{
									$select_depa.="<option selected value=''>Seleccione</option>";
								}
								$select_depa.='</select>';

								$select_muni = "<select class='form-control select_muni' id='select_muni'>";
								$sql_muni = _query("SELECT * FROM municipio WHERE id_departamento_municipio = '$departamento'");
								$cuenta_muni = _num_rows($sql_muni);
								if($cuenta_muni > 0)
								{
									while ($row_muni = _fetch_array($sql_muni))
									{
										$id_municipio = $row_muni["id_municipio"];
										$descripcion = $row_muni["nombre_municipio"];
										$select_muni.= "<option value='".$id_municipio."'";
										if($id_municipio == $municipio)
										{
											$select_muni.= " selected";
										}
										$select_muni.=">".$descripcion."</option>";
									}
								}
								$select_muni.='</select>';

								$xdatos['direccion']=$direccion;
								$xdatos['select_depa']=$select_depa;
								$xdatos['select_muni']=$select_muni;

								echo json_encode($xdatos);
							}

							function municipio()
							{
								$id_departamento = $_POST["id_departamento"];
								$option = "<option value=''>Seleccione</option>";
								$sql_mun = _query("SELECT * FROM municipio WHERE id_departamento_municipio='$id_departamento'");
								while($mun_dt=_fetch_array($sql_mun))
								{
									$option .= "<option value='".$mun_dt["id_municipio"]."'>".$mun_dt["nombre_municipio"]."</option>";
								}
								echo $option;
							}

							//functions to load
							if(!isset($_REQUEST['process'])){
								initial();
							}
							//else {
							if (isset($_REQUEST['process'])) {


								switch ($_REQUEST['process']) {
									case 'insert':
									insertar();
									break;
									case 'traerdatos':
									traerdatos();
									break;
									case 'pedido':
									pedido();
									break;
									case 'consultar_stock':
									consultar_stock();
									break;
									case 'getpresentacion':
									getpresentacion();
									break;
									case 'datos_cliente':
									datos_cliente();
									break;
									case 'municipio':
									municipio();
									break;
								}

								//}
							}
							?>
