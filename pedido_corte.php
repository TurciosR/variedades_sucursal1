<?php
include_once "_core.php";
function initial()
{
	$title = "Corte Pedidos";
	$_PAGE = array ();
	$_PAGE ['title'] = $title;
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/chosen/chosen.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/jQueryUI/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/jqGrid/ui.jqgrid.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user, $filename);
	//permiso del script

	$hoy = date("Y-m-d");
	$sqlp = _fetch_array(_query("SELECT SUM(pedido.total) as monto FROM pedido WHERE pedido.finalizada = 1 AND pedido.fecha_factura = '$hoy'"));
	?>
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row">
			<div class="col-lg-12">
				<div class="ibox">
					<?php if ($links!='NOT' || $admin=='1' ){ ?>
						<div class="ibox-title">
							<h5><?php echo $title; ?></h5>
						</div>
						<div class="ibox-content">
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group has-info single-line">
										<label>FECHA</label>
										<input class="form-control datepick" readonly style="width:25%" type="text" id="fecha_date" name="fecha_date" value="<?=$hoy ?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-3">
									<div class="form-group has-info single-line">
										<label>Monto Pedidos Finalizados</label>
										<input style="text-align:right" type="text" value = '<?= round($sqlp['monto'],2) ?>' readonly placeholder="" class="form-control" id="monto" name="monto">
									</div>
								</div>
								<div class="col-lg-2">
									<div class="form-group has-info single-line">
										<label>Efectivo</label>
										<input style="text-align:right" type="text" value = '' placeholder="" class="form-control" id="efectivo" name="efectivo">
									</div>
								</div>
								<div class="col-lg-2">
									<div class="form-group has-info single-line">
										<label>Abonos Previos</label>
										<input  style="text-align:right" type="text" value = '' class="form-control" id="abonos" name="abonos">
									</div>
								</div>
								<div class="col-lg-2">
									<div class="form-group has-info single-line">
										<label>Diferencia</label>
										<input readonly style="text-align:right" type="text" value = '' placeholder="" class="form-control" id="diferencia" name="diferencia">
									</div>
								</div>
								<div class="col-lg-3">
									<div class="form-group has-info single-line">
										<label>Guardar</label>
										<button class='btn btn-primary form-control' type="button" id="submit1" name="submit1">Guardar</button>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-9">
									<div class="form-group has-info single-line">
										<label>Comentario</label>
										<input  type="text" value='' placeholder="Comentario" class="form-control " id="comentario" name="comentario">
									</div>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group has-info single-line">
										<label>REGISTROS ANTERIORES</label>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-lg-3">
									<div class="form-group has-info single-line">
										<label>Inicio</label>
										<input readonly type="text" value='<?=$hoy ?>' placeholder="" class="form-control datepick" id="fecha_inicio" name="fecha_inicio">
									</div>
								</div>
								<div class="col-lg-3">
									<div class="form-group has-info single-line">
										<label>Inicio</label>
										<input readonly type="text" value='<?=$hoy ?>' placeholder="" class="form-control datepick" id="fecha_fin" name="fecha_fin">
									</div>
								</div>
								<div class="col-lg-3">
									<div class="form-group has-info single-line">
										<label>Buscar</label>
										<button class='btn btn-primary form-control' type="button" id="buscar" name="Buscar">Buscar</button>
									</div>
								</div>
							</div>

							<div class="row">
								<div class='col-lg-12'>
									<table class="table table-striped table-bordered table-hover" id="editable2">
										<thead>
											<tr>
												<th class="col-lg-1">Id</th>
												<th class="col-lg-1">Fecha Corte</th>
												<th class="col-lg-1">Monto</th>
												<th class="col-lg-1">Efectivo</th>
												<th class="col-lg-1">Abonos</th>
												<th class="col-lg-1">Diferencia</th>

												<th class="col-lg-1">Comentario</th>
											</tr>
										</thead>
										<tbody></tbody>
									</table>
									<input type="hidden" name="autosave" id="autosave" value="false-0">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
<?php
		include_once ("footer.php");
		?>
		<script type="text/javascript">
		$(document).ready(function() {
			generar();
			$("#efectivo").numeric({negative:false,decimalPlaces:2});
			$("#diferencia").numeric({negative:false,decimalPlaces:2});
			$("#abonos").numeric({negative:false,decimalPlaces:2});
		});

		$(document).on('change', '#fecha_date', function(event) {
			var fecha = $(this).val();

			$.ajax({
				url: 'pedido_corte.php',
				type: 'POST',
				dataType: 'json',
				data: {
					process: 'getmoney',
					fecha: fecha
				},
				success: function(xdatos)
				{
					$('#monto').val(xdatos.monto);
				}
			});

		});

		$(document).on('keyup', '#efectivo,#abonos', function(event) {
			var monto  = parseFloat($("#monto").val());
			var efectivo =  parseFloat($("#efectivo").val());
			var abonos =  parseFloat($("#abonos").val());
			if(isNaN(efectivo))
			{
				efectivo=0;
			}

			if(isNaN(abonos))
			{
				abonos=0;
			}
			console.log(abonos);
			diferencia = efectivo + abonos - monto ;
			diferencia =  diferencia.toFixed(2);
			$("#diferencia").val(diferencia);
		});

		$(document).on('click', '#buscar', function(event) {
			/* Act on the event */
			generar();
		});

		$(document).on('click', '#submit1', function(event) {
			$(this).attr("disabled","disabled");
			a = $(this);

			var monto  = parseFloat($("#monto").val());
			var efectivo =  $("#efectivo").val();
			var diferencia = $("#diferencia").val();
			var abonos = $("#abonos").val();
			var comentario = $("#comentario").val();
			var fecha = $("#fecha_date").val();

			if(efectivo!="")
			{
				$.ajax({
					url: 'pedido_corte.php',
					type: 'POST',
					dataType: 'json',
					data: {
						process: 'insert',
						monto:monto,
						efectivo:efectivo,
						abonos:abonos,
						diferencia:diferencia,
						comentario:comentario,
						fecha:fecha,

					},
					success: function(datax)
					{
						display_notify(datax.typeinfo,datax.msg);
						location.href = "pedido_corte.php";
					}
				});

			}
			else {
				a.removeAttr('disabled');
				display_notify("Error","Digite el efectivo por favor");
			}

		});

		function generar() {
		  fechai = $("#fecha_inicio").val();
		  fechaf = $("#fecha_fin").val();
		  dataTable = $('#editable2').DataTable().destroy()
		  dataTable = $('#editable2').DataTable({
		    "pageLength": 50,
		    "order": [
		      [0, 'desc']
		    ],
		    "processing": true,
		    "serverSide": true,
		    "ajax": {
		      url: "pedido_corte_dt.php?fechai="+fechai+"&fechaf="+fechaf, // json datasource
		      error: function() { // error handling
		        $(".editable2-error").html("");
		        $("#editable2").append('<tbody class="editable2_grid-error"><tr><th colspan="9">No se encontró información segun busqueda </th></tr></tbody>');
		        $("#editable2_processing").css("display", "none");
		        $(".editable2-error").remove();
		      }
		    },
		    "language": {
		      "url": "js/Spanish.json"
		    },
		  });
		  dataTable.ajax.reload();

		}
		</script>
		<?php
	} //permiso del script
	else {
		echo "<br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div></div></div></div></div>";
		include_once ("footer.php");
	}
}
function insertar()
{
	$efectivo=$_POST["efectivo"];
	$diferencia=$_POST["diferencia"];
	$monto=$_POST["monto"];
	$abonos=$_POST["abonos"];
	$comentario = $_POST['comentario'];
	$fecha = $_REQUEST['fecha'];

	$table = 'corte_pedido';
	$form_data = array(
		'efectivo' => $efectivo,
		'diferencia' => $diferencia,
		'monto' => $monto,
		'id_usuario' => $_SESSION['id_usuario'],
		'id_sucursal' => $_SESSION['id_sucursal'],
		'fecha' => $fecha,
		'hora' => date('H:i:s'),
		'abonos' => $abonos,
		'comentario' => $comentario,
	);
	$insertar = _insert($table,$form_data );
	if($insertar)
	{
		$xdatos['typeinfo']='Success';
		$xdatos['msg']='Registro ingresado con exito!';
		$xdatos['process']='insert';
	}
	else
	{
		$xdatos['typeinfo']='Error';
		$xdatos['msg']='Registro no pudo ser ingresado!';
	}

	echo json_encode($xdatos);
}

function getmoney()
{
	$fecha = $_REQUEST['fecha'];

	$sqlp = _fetch_array(_query("SELECT SUM(pedido.total) as monto FROM pedido WHERE pedido.finalizada = 1 AND pedido.fecha_factura = '$fecha'"));
	$monto = round($sqlp['monto'],2);

	$xdatos['monto']=$monto;
	echo json_encode($xdatos);
}

if(!isset($_POST['process']))
{
	initial();
}
else
{
	if(isset($_POST['process']))
	{
		switch ($_POST['process'])
		{
			case 'insert':
				insertar();
				break;
			case 'getmoney':
				getmoney();
				break;
		}
	}
}
?>
