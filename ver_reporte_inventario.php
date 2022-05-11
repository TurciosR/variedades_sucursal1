<?php
include_once "_core.php";
include ('num2letras.php');
include ('facturacion_funcion_imprimir.php');

function initial() {
	// Page setup
	$id_user=$_SESSION["id_usuario"];

	$_PAGE = array ();
	$_PAGE ['title'] = 'Reporte Inventario';
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
  $_PAGE ['links'] .= '<link href="css/plugins/fileinput/fileinput.css" media="all" rel="stylesheet" type="text/css"/>';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user, $filename);


	//permiso del script
	if ($links!='NOT' || $admin=='1' ){
?>
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-2">
                </div>
            </div>
        <div class="wrapper wrapper-content  animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>Reporte Inventario</h5>
                        </div>
                        <div class="ibox-content">
                  				  <input type="hidden" name="process" id="process" value="inventario">
														<div class="row">
															<div class="col-md-3">
																<div class="form-group">
																	<label>UBICACION</label>
																	<select class="slec form-control" id="ubi" name="ubi">
																		<?php
																		$data  = _query("SELECT * FROM ubicacion");
																		while($row=_fetch_array($data))
																		{
																			?>
																			<option value="<?=$row['id_ubicacion']?>"><?=$row['descripcion'] ?></option>
																			<?php
																		}
																		 ?>
																	</select>
																</div>
															</div>
															<div class="col-md-3">
																<div class="form-group">
																	<label style="width:100%">Mostrar Solo</label>
																	<label style="vertical-align: center; display: block; margin:0px; padding:0px;" for="fragil">
																		<input style="vertical-align: middle;position: relative; margin:0px; padding:0px;" type="checkbox" id="fragil" name="fragil" value="">
																		Frágil
																	</label>
																</div>
															</div>
														</div>
                            <div class="row">
                              <div class="col-md-12">
                                <div class="form-group">
                                  <input type="hidden" name="id_sucursal" id="id_sucursal">
                                  <input type="submit" id="submit1" name="submit1" value="PDF" class="btn btn-primary m-t-n-xs" />
                                  <input type="submit" id="submit2" name="submit1" value="EXCEL" class="btn btn-primary m-t-n-xs" />
                                </div>
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
$(".slec").select2();
$("#submit2").click(function()
{
	f= $("#fragil").prop("checked") ? 1 : 0;
	id_ubicacion = $("#ubi").val();
	var cadena = "reporte_inventario_xls.php?ubicacion="+id_ubicacion+"&f="+f;
	window.open(cadena, '', '');
});

$("#submit1").click(function()
{
	f= $("#fragil").prop("checked") ? 1 : 0;
	id_ubicacion = $("#ubi").val();
	var cadena = "reporte_inventario_pdf.php?ubicacion="+id_ubicacion+"&f="+f;
	window.open(cadena, '', '');
});

</script>
<?php
		} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}


if(!isset($_POST['process'])){
	initial();
}
else
{
if(isset($_POST['process'])){
switch ($_POST['process']) {
	case 'edit':
		//insertar_empresa();
    editar();
		break;
	}
}
}
?>
