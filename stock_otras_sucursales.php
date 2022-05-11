<?php
include_once "_core.php";

function initial() {
	// Page setup
	$id_user=$_SESSION["id_usuario"];

	$_PAGE = array ();
	$_PAGE ['title'] = 'Stock en global (Desface 5 minutos)';
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

	$a = uniqid();
	$b = uniqid();
	//permiso del script
	if ($links!='NOT' || $admin=='1' ){
?>
        <div class="wrapper wrapper-content  animated fadeInRight">
            <div class="row">

                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5><?php echo $_PAGE ['title']; ?></h5>
                        </div>
                        <div class="ibox-content">
													<div class="row">
														<div class="col-lg-12">
															<label>Producto</label>
															<input type="text" class="form-control external <?=$a ?>" id="external" name="external" value="">
														</div>

														<div class="col-lg-12">
															<table class="table table-hover table-striped">
																<thead>
																	<th class="col-lg-2">Marca</th>
																	<th class="col-lg-7">Producto</th>
																	<th class="col-lg-2">Existencias</th>
																	<th class="col-lg-1">Precio</th>
																</thead>
																<tbody class="extern <?=$b ?>">

																</tbody>
															</table>
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

	$(document).on('keyup', '.<?=$a ?>', function(event) {
		var q= $(this).val();
			$('.<?=$b ?>').html="buscando...";
		var id_suc = $(".selectable_suc").val();
		if (q.length>1) {
			$.ajax({
				url: 'http://dg.apps-oss.com/syn_server/force.php',
				type: 'POST',
				dataType: 'json',
				data: {hash: 'd681824931f81f6578e63fd7e35095af',q: q,id_sucursal:'<?=$_SESSION['id_sucursal']?>',process:"stock_g"},
				success: function(datax) {
					$('.<?=$b ?>').html(datax.data);
				}
			})
		}
	});

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
