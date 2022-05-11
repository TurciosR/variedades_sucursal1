<?php
include ("_core.php");
// Page setup
$title='Administrar Pedidos';
$_PAGE = array();
$_PAGE ['title'] = $title;
$_PAGE ['links'] = null;
$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
include_once "header.php";
include_once "main_menu.php";

//permiso del script
$id_user=$_SESSION["id_usuario"];
$admin=$_SESSION["admin"];
$uri = $_SERVER['SCRIPT_NAME'];
$filename=get_name_script($uri);
$links=permission_usr($id_user,$filename);

$fechahoy=date("d-m-Y");
$fechaanterior=ED(restar_dias($fechahoy,30));
?>

<div class="wrapper wrapper-content  animated fadeInRight">
	<div class="row" id="row1">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<?php
				$filename='facturacion.php';
				$link=permission_usr($id_user,$filename);

				if ($links!='NOT' || $admin=='1' ){
					echo"
					<div class='ibox-title'> Pedidos de la app";
					echo "</div>";
					?>
					<div class="ibox-content">
						<!--load datables estructure html-->
						<div hidden class="row">
							<div class="input-group">
								<div class="col-md-4">
									<div class="form-group">
										<label>Fecha Inicio</label>
										<input type="text" placeholder="Fecha Inicio" class="datepick form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo  MD($fechaanterior);?>">
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label>Fecha Fin</label>
										<input type="text" placeholder="Fecha Fin" class="datepick form-control" id="fecha_fin" name="fecha_fin" value="<?php echo MD($fechahoy);?>">
									</div>
								</div>
								<div class="col-md-4">

									<div class="form-group">
										<div><label>Buscar</label> </div>
										<button type="button" id="btnMostrar" name="btnMostrar" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>

									</div>
								</div>
							</div>

						</div>
						<section>
							<table class="table table-striped table-bordered table-hover" id="editable2">
								<thead>
									<tr>
										<th class="col-lg-1">Id </th>
										<th class="col-lg-3">Cliente</th>
										<th class="col-lg-1">Depto/Municipio</th>
										<th class="col-lg-1">Empleado</th>
										<th class="col-lg-1">Pedido</th>
										<th class="col-lg-1">Fecha Entrega </th>
										<th class="col-lg-3">Lugar Entrega</th>
										<th class="col-lg-2">Total</th>
										<th class="col-lg-1">Acci&oacute;n</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
							<input type="hidden" name="autosave" id="autosave" value="false-0">
						</section>
						<!--Show Modal Popups View & Delete -->
						<div class='modal fade' id='viewModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
							<div class='modal-dialog'>
								<div class='modal-content modal-sm'></div><!-- /.modal-content -->
							</div><!-- /.modal-dialog -->
						</div><!-- /.modal -->
						<div class='modal fade' id='deleteModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
							<div class='modal-dialog'>
								<div class='modal-content modal-sm'></div><!-- /.modal-content -->
							</div><!-- /.modal-dialog -->
						</div><!-- /.modal -->
						<!--Show Modal Popups View & Delete -->
						<div class='modal fade' id='viewModalFact' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
							<div class='modal-dialog'>
								<div class='modal-content modal-md'></div><!-- /.modal-content -->
							</div><!-- /.modal-dialog -->
						</div><!-- /.modal -->

					</div><!--div class='ibox-content'-->
				</div><!--<div class='ibox float-e-margins' -->
				</div> <!--div class='col-lg-12'-->
			</div> <!--div class='row'-->
		</div><!--div class='wrapper wrapper-content  animated fadeInRight'-->
		<?php
		include("footer.php");
		?>
		<script type="text/javascript">

		$(document).on('hidden.bs.modal', function(e) {
			var target = $(e.target);
			target.removeData('bs.modal').find(".modal-content").html('');
		});
		$(document).on("click", "#btnDelete", function(event) {
			$(this).attr('disabled', 'disabled');
			deleted();
		});

		function deleted()
		{
		  var id_pedido = $('#id_pedido').val();
		  var dataString = 'process=deleted'+'&id_pedido='+id_pedido;
		  $.ajax({
		    type: "POST",
		    url: "anular_pedido_app.php",
		    data: dataString,
		    dataType: 'json',
		    success: function(datax) {
		      display_notify(datax.typeinfo, datax.msg);
		      if (datax.typeinfo != "Error")
		      {
		        setInterval("location.reload();", 1000);
		        $('#btncerr').click();
		      }
		    }
		  });
		}

		$(document).ready(function() {
			  generar();
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
				"autoWidth": false,
				"ajax": {
					url: "admin_pedido_app_dt.php?fechai="+fechai+"&fechaf="+fechaf, // json datasource
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
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}

	?>
