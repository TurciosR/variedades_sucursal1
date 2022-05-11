<?php
include_once "_core.php";
include ('num2letras.php');

function initial() {
	// Page setup
	$id_user=$_SESSION["id_usuario"];

	$_PAGE = array ();
	$_PAGE ['title'] = 'Reporte ventas_por proveedor';
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
        <div class="wrapper wrapper-content  animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>Reporte ventas por proveedor</h5>
                        </div>
                        <div class="ibox-content">
                  				  <input type="hidden" name="process" id="process" value="reposicion">
														<div class="row">
															<div class="col-md-4">
																<div class="form-group">
																	<label>Año:</label>
																	<select style="width:100%" class="" id="year" name="year">
																		<?php
                                    $array_years = array(
                                      2018,
                                      2019,
                                      2020,
                                      2021,
                                      2022,
                                      2023,
                                      2024,
                                      2025,
                                      2026,
                                      2027,
                                      2028,
                                      2029,
                                    );
																		foreach ($array_years as $key => $value) {
																		?>
																		<option  <?php if($value==date("Y")){echo "selected";} ?>  value="<?= $value ?>"><?=$value?></option>
																		<?php
																		}
																		 ?>
																	</select>
																</div>
															</div>

  															<div class="col-md-4">
  																<div class="form-group">
  																	<label>Mas vendidos por: </label>
  																	<select style="width:100%" class="" id="por" name="por">
  																		<?php
                                      $array_years = array(
                                        "Cantidad",
                                        "Total"
                                      );
  																		foreach ($array_years as $key => $value) {
  																		?>
  																		<option value="<?= $value ?>"><?=$value?></option>
  																		<?php
  																		}
  																		 ?>
  																	</select>
  																</div>
  															</div>
                                <div class="col-md-4">
  																<div class="form-group">
  																	<label>Mostrar productos por Proveedor: </label>
  																	<select style="width:100%" class="" id="limit" name="limit">
  																		<?php
                                      $array_years = array(
                                        10,
                                        15,
                                        20,
                                        25,
                                      );
  																		foreach ($array_years as $key => $value) {
  																		?>
  																		<option value="<?= $value ?>"><?=$value?></option>
  																		<?php
  																		}
  																		 ?>
  																	</select>
  																</div>
  															</div>
														</div>
                            <div class="row">
                              <div class="col-md-12">
                                <div class="form-group">
                                  <input type="hidden" name="id_sucursal" id="id_sucursal">
                                  <input type="submit" id="submit1" name="submit1" value="PDF" class="btn btn-primary m-t-n-xs" />
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

$("#year").select2();
$("#por").select2();
$("#limit").select2();
$("#submit1").click(function()
{
		var year = $("#year").val();
    var por = $("#por").val();
    var limit = $("#limit").val();
		var cadena = "reporte_ventas_por_proveedor.php?year="+year+"&por="+por+'&limit='+limit;
		window.open(cadena, '', '');

})


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
