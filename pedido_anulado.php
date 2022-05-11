<?php
include_once "_core.php";
function initial()
{
	$title = "Reportes pedidos anulados";
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
										<label>Pedidos anulados Por Rango de fechas</label>
									</div>
								</div>
							</div>
							<div class="row">
								<form class="" target="_blank" action="pedido_anulado_pdf.php" method="get">
									<div class="col-lg-3">
										<div class="form-group has-info single-line">
											<label>Fecha Inicio</label>
											<input style="text-align:center" type="text" value = '<?=date("Y-m-01") ?>' readonly placeholder="" class="form-control datepick" id="ini" name="ini">
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group has-info single-line">
											<label>Fecha Fin</label>
											<input style="text-align:center" type="text" value = '<?=date("Y-m-d") ?>' readonly placeholder="" class="form-control datepick" id="fin" name="fin">
										</div>
									</div>

									<div class="col-lg-3">
										<div class="form-group has-info single-line">
											<label>Generar</label>
											<input type="submit"  class='form-control btn btn-primary' name="" value="PDF">
										</div>
									</div>
								</form>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
<?php
		include_once ("footer.php");
		?>
		 <!-- aca ban los scripts -->
		<?php
	} //permiso del script
	else {
		echo "<br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div></div></div></div></div>";
		include_once ("footer.php");
	}
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
				break;
		}
	}
}
?>
