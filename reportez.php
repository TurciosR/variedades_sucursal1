<?php
error_reporting(E_ERROR | E_PARSE);
include ("_core.php");
// Page setup
function initial() {


	$_PAGE = array ();
	$_PAGE ['title'] = 'Reporte Ticket del Dia';
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/chosen/chosen.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
	include_once "header.php";
	include_once "main_menu.php";
	$id_sucursal=$_SESSION['id_sucursal'];
	$id_user = $_SESSION["id_usuario"];
	date_default_timezone_set('America/El_Salvador');
	$fecha_actual = date("Y-m-d");
	$hora_actual = date("H:i:s");
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	//permiso del script
	if ($links!='NOT' || $admin=='1' ){
		?>

		<div class="wrapper wrapper-content  animated fadeInRight">
			<div class="row" id="row1">
				<div class="col-lg-12">
					<div class="ibox float-e-margins">
						<div class="ibox-content">

              <header>
                <h4>Corte Z por dia</h4>
              </header>
              <section>
                <form class="" target="_blank" action="reportez_pdf.php" method="get">
                  <div class="row">
                    <div class="col-lg-6">
                      <div class="form-group has-info single-line">
                        <label>Fecha Inicio</label>
                        <input type="text"  class="form-control datepick" id="fini" name="fini" value="<?php echo date('Y-m-d');?>">
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="form-group has-info single-line">
                        <label>Fecha Fin</label>
                        <input type="text"  class="form-control datepick" id="ffin" name="ffin" value="<?php echo date('Y-m-d');?>">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <button type="submit"   class="btn btn-primary m-t-n-xs pull-right"><i class="fa fa-file-pdf-o"></i> Imprimir PDF</button>
												<button type="button"  id="print"  class="btn btn-primary m-t-n-xs "><i class="fa fa-print"></i> Imprimir Ticket</button>
                      </div>
                    </div>
                  </div>

									<div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
												<label>Seleccione el primer y ultimo dia del mes en el rango de fechas y de click en imprimir para imprimir el Gran Z</label>
												<br>
												<button type="button"  id="print_gz"  class="btn btn-primary m-t-n-xs "><i class="fa fa-print"></i> Imprimir Gran Z</button>
                      </div>
                    </div>
                  </div>
                </form>
              </section>
							<!--Show Modal Popups View & Delete -->
							<div class='modal fade' id='viewModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
								<div class='modal-dialog'>
									<div class='modal-content'></div><!-- /.modal-content -->
								</div><!-- /.modal-dialog -->
							</div><!-- /.modal -->
							<div class='modal fade' id='deleteModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
								<div class='modal-dialog'>
									<div class='modal-content modal-sm'></div><!-- /.modal-content -->
								</div><!-- /.modal-dialog -->
							</div><!-- /.modal -->
						</div><!--div class='ibox-content'-->
					</div><!--<div class='ibox float-e-margins' -->
					</div> <!--div class='col-lg-12'-->
				</div> <!--div class='row'-->
			</div><!--div class='wrapper wrapper-content  animated fadeInRight'-->
			<?php
			include("footer.php");
			echo" <script type='text/javascript' src='js/funciones/tike_dia.js'></script>";
			?>
			<script type="text/javascript">
			$(document).on('click', '#print', function(event) {
				event.preventDefault();
				fini = $("#fini").val();
				ffin = $("#ffin").val();
				$.ajax({
					url: 'reportez.php',
					type: 'POST',
					dataType: 'json',
					data: {process: 'imprimir',fini: fini, ffin: ffin},
					success: function(datos) {
						var sist_ope = datos.sist_ope;
						var dir_print=datos.dir_print;
						var shared_printer_win=datos.shared_printer_win;
						var shared_printer_pos=datos.shared_printer_pos;

						console.log(datos.movimiento);

						if (sist_ope == 'win') {
							$.post("http://"+dir_print+"printvalewin1.php", {
								datosvale: datos.movimiento,
								shared_printer_win:shared_printer_win,
								shared_printer_pos:shared_printer_pos,
							})
						} else {
							$.post("http://"+dir_print+"printvale1.php", {
								datosvale: datos.movimiento
							});
						}

					}
				});

			});
			$(document).on('click', '#print_gz', function(event) {
				event.preventDefault();
				fini = $("#fini").val();
				ffin = $("#ffin").val();
				$.ajax({
					url: 'reportez.php',
					type: 'POST',
					dataType: 'json',
					data: {process: 'imprimir_gz',fini: fini, ffin: ffin},
					success: function(datos) {
						var sist_ope = datos.sist_ope;
						var dir_print=datos.dir_print;
						var shared_printer_win=datos.shared_printer_win;
						var shared_printer_pos=datos.shared_printer_pos;

						console.log(datos.movimiento);

						if (sist_ope == 'win') {
							$.post("http://"+dir_print+"printvalewin1.php", {
								datosvale: datos.movimiento,
								shared_printer_win:shared_printer_win,
								shared_printer_pos:shared_printer_pos,
							})
						} else {
							$.post("http://"+dir_print+"printvale1.php", {
								datosvale: datos.movimiento
							});
						}

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
	function  imprimir(){
		$id_sucursal=$_SESSION['id_sucursal'];
		//directorio de script impresion cliente
		$sql_dir_print="SELECT *  FROM config_dir WHERE id_sucursal='$id_sucursal'";
		//$sql_dir_print="SELECT * FROM `config_dir` WHERE `id_sucursal`=1 ";
		$result_dir_print=_query($sql_dir_print);
		$row0=_fetch_array($result_dir_print);
		$dir_print=$row0['dir_print_script'];
		$shared_printer_win=$row0['shared_printer_matrix'];
		$shared_printer_pos=$row0['shared_printer_pos'];

		$info_mov=data();
		$info = $_SERVER['HTTP_USER_AGENT'];
		if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
		else
		$so_cliente='lin';
		$nreg_encode['shared_printer_win'] =$shared_printer_win;
		$nreg_encode['shared_printer_pos'] =$shared_printer_pos;
		$nreg_encode['dir_print'] =$dir_print;
		$nreg_encode['movimiento'] =$info_mov;
		$nreg_encode['sist_ope'] =$so_cliente;
		echo json_encode($nreg_encode);
	}

	function  imprimir_gz(){
		$id_sucursal=$_SESSION['id_sucursal'];
		//directorio de script impresion cliente
		$sql_dir_print="SELECT *  FROM config_dir WHERE id_sucursal='$id_sucursal'";
		//$sql_dir_print="SELECT * FROM `config_dir` WHERE `id_sucursal`=1 ";
		$result_dir_print=_query($sql_dir_print);
		$row0=_fetch_array($result_dir_print);
		$dir_print=$row0['dir_print_script'];
		$shared_printer_win=$row0['shared_printer_matrix'];
		$shared_printer_pos=$row0['shared_printer_pos'];

		$info_mov=data_gz();
		$info = $_SERVER['HTTP_USER_AGENT'];
		if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
		else
		$so_cliente='lin';
		$nreg_encode['shared_printer_win'] =$shared_printer_win;
		$nreg_encode['shared_printer_pos'] =$shared_printer_pos;
		$nreg_encode['dir_print'] =$dir_print;
		$nreg_encode['movimiento'] =$info_mov;
		$nreg_encode['sist_ope'] =$so_cliente;
		echo json_encode($nreg_encode);
	}


	function data()
	{
		$infoo="";
		$fecha_inicio= $_REQUEST['fini'];
		$fecha_fin= $_REQUEST['ffin'];
		$fini1 = $_REQUEST["fini"];
		$fin1 = $_REQUEST["ffin"];
		$fecha_ini= ed($fecha_inicio);
		$fecha_fina=ed($fecha_fin);
		$sql_empresa=_query("SELECT * FROM sucursal where id_sucursal=$_SESSION[id_sucursal]");
		$array_empresa=_fetch_array($sql_empresa);
		$nombre_empresa=$array_empresa['descripcion'];
		$telefono=$array_empresa['telefono1'];
		$logo_empresa=$array_empresa['logo'];

		$id_user=$_SESSION["id_usuario"];
		$id_sucursal=$_SESSION['id_sucursal'];

		$sql_cabezera = _query("SELECT * FROM sucursal WHERE id_sucursal = '$id_sucursal'");
		$cue = _num_rows($sql_cabezera);
		$row_cabe = _fetch_array($sql_cabezera);

		$nite=$row_cabe['nit'];
		$nrce=$row_cabe['nrc'];
		$empresa1=$row_cabe['descripcion'];
		$giro1=$row_cabe['giro'];
		$direccion = $row_cabe['direccion'];


		$aSz = array(
		  'fecha' => 17,
		  'sucursal' => 8,
		  'inicio' => 12,
		  'fin' =>12,
		  'ex' =>15,
		  'giv' =>14,
		  'ret' => 10,
		  'total' => 14,
		  'totalgen' => 15,
		);


		$fecha_impresion = date("d-m-Y")." ".hora(date("H:i:s"));
		$fk = $fini1;

		$tAry = array(
		  'te' => 0,
		  'tg' => 0,
		  'tt' => 0,
		  'tret' => 0,
		  'fe' => 0,
		  'fg' => 0,
		  'ft' => 0,
		  'fret' => 0,
		  'ce' => 0,
		  'cg' => 0,
		  'ct' => 0,
		  'cret' => 0,
		  'ttg' => 0,
		);

		while(strtotime($fk) <= strtotime($fin1))
		{
		  $fk = ($fk);
		  $sql_efectivo = _query("SELECT * FROM factura WHERE fecha = '$fk' AND finalizada=1 AND anulada=0 AND id_sucursal = '$id_sucursal'");
		  $cuenta = _num_rows($sql_efectivo);
		  $sql_min_max=_query("
		  SELECT MIN(CAST(num_fact_impresa as UNSIGNED)) as minimo, MAX(CAST(num_fact_impresa as UNSIGNED)) as maximo FROM factura WHERE fecha = '$fk' AND numero_doc LIKE '%TIK%' AND id_sucursal = $id_sucursal UNION ALL
		  SELECT MIN(CAST(num_fact_impresa as UNSIGNED)) as minimo, MAX(CAST(num_fact_impresa as UNSIGNED)) as maximo FROM factura WHERE fecha = '$fk' AND numero_doc LIKE '%COF%' AND id_sucursal = $id_sucursal UNION ALL
		  SELECT MIN(CAST(num_fact_impresa as UNSIGNED)) as minimo, MAX(CAST(num_fact_impresa as UNSIGNED)) as maximo FROM factura WHERE fecha = '$fk' AND numero_doc LIKE '%CCF%' AND id_sucursal = $id_sucursal");
		  $cuenta_min_max = _num_rows($sql_min_max);
		  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		  $total_tike = 0;
		  $total_factura = 0;
		  $total_credito_fiscal = 0;
		  $tike_min = 0;
		  $tike_max = 0;
		  $factura_min = 0;
		  $factura_max = 0;
		  $credito_fiscal_min = 0;
		  $credito_fiscal_max = 0;

		  $tret = 0;
		  $fret = 0;
		  $cret = 0;

		  $exent_t = array(
		    'TIK' => 0,
		    'COF' => 0,
		    'CCF' => 0
		  );
		  if($cuenta > 0)
		  {
		      while ($row_corte = _fetch_array($sql_efectivo))
		      {
		          $id_factura = $row_corte["id_factura"];
		          $anulada = $row_corte["anulada"];
		          $subtotal = $row_corte["subtotal"];
		          $suma = $row_corte["sumas"];
		          $iva = $row_corte["iva"];
		          $total = $row_corte["total"];
		          $numero_doc = $row_corte["numero_doc"];
		          $ax = explode("_", $numero_doc);
		          $numero_co = $ax[0];
		          $alias_tipodoc = $ax[1];
		          $total = $row_corte["total"];


		          if($alias_tipodoc == 'TIK')
		          {
		            $exent_t[$alias_tipodoc]+=$row_corte['venta_exenta'];
		            $total_tike += $total;
		            $tret +=$row_corte['retencion'];

		          }
		          else if($alias_tipodoc == 'COF')
		          {
		            $exent_t[$alias_tipodoc]+=$row_corte['venta_exenta'];
		            $total_factura += $total;
		            $fret +=$row_corte['retencion'];
		          }
		          else if($alias_tipodoc == 'CCF')
		          {
		            $exent_t[$alias_tipodoc]+=$row_corte['venta_exenta'];
		            $total_credito_fiscal += $total;
		            $cret +=$row_corte['retencion'];
		          }




		      }
		  }

		  if($cuenta_min_max)
		  {
		      $i = 1;
		      while ($row_min_max = _fetch_array($sql_min_max))
		      {
		          if($i == 1)
		          {
		            $tike_min = $row_min_max["minimo"];
		            $tike_max = $row_min_max["maximo"];
		              list($minimo_num,$ads) = explode("_", $tike_min);
		              list($maximo_num,$ads) = explode("_", $tike_max);
		          }
		          if($i == 2)
		          {
		              $factura_min = $row_min_max["minimo"];
		              $factura_max = $row_min_max["maximo"];
		          }
		          if($i == 3)
		          {
		              $credito_fiscal_min = $row_min_max["minimo"];
		              $credito_fiscal_max = $row_min_max["maximo"];
		          }
		          $i += 1;
		      }
		  }




		  $total_general = $total_tike + $total_factura + $total_credito_fiscal - $tret -  $fret -  $cret;

		  $sql_fa = _query("SELECT * FROM factura WHERE fecha = '$fk' AND tipo_documento!='TIK'  AND anulada=1 AND num_fact_impresa>0 AND id_sucursal = '$id_sucursal'");
		  $fk = ED($fk);

		  if($total_general!=0)
		  {
		    $sqlcc = _fetch_array(_query("SELECT MAX(caja) as caja FROM factura WHERE fecha='".MD($fk)."'"));

		    $sql_caja = _query("SELECT * FROM caja WHERE id_caja='$sqlcc[caja]'");

		    $direccio=divtextlin($direccion,35);
		    $empress= divtextlin($empresa1, 35);
		    $giros = divtextlin($giro1, 35);

				foreach ($empress as $nnona)
			 {
				 $infoo.=$nnona."\n";
			 }
			 foreach ($direccio as $nnona)
			 {
				 $infoo.=$nnona."\n";
			 }
			 foreach ($giros as $nnon)
			 {

				 $infoo.=$nnon."\n";
			 }
			 $infoo.="NIT: ".$nite."  NRC: ".$nrce."\n";

		    if(_num_rows($sql_caja)>0)
		    {

		      $dats_caja = _fetch_array($sql_caja);
		      $fehca = ED($dats_caja["fecha"]);
		      $resolucion = $dats_caja["resolucion"];
		      $serie = $dats_caja["serie"];
		      $desde = $dats_caja["desde"];
		      $hasta = $dats_caja["hasta"];
		      $nombre_c = $dats_caja["nombre"];

					$resolucion1 = divtextlin($resolucion, 35);
					foreach ($resolucion1 as $nnon)
	 			 {
	 				 $infoo.=$nnon."\n";
	 			 }

					$infoo.="DEL: ".$desde." AL: ".$hasta."\n";
					$infoo.="SERIE: ".$serie."\n";
					$infoo.="FECHA RESOLUCION: ".$fehca."\n";
		    }

				$sql_corte_z_dia="SELECT c.caja,c.turno,c.hora_corte,c.tipo_corte,e.nombre,e.usuario
				FROM controlcaja AS c
				JOIN usuario AS e ON(e.id_usuario=c.id_empleado)
				WHERE c.tipo_corte='Z' AND c.fecha_corte ='".MD($fk)."' ORDER BY id_corte DESC LIMIT 1  ";

				$result_corte=_query($sql_corte_z_dia);
				$nrow_corte = _num_rows($result_corte);

				if ($nrow_corte > 0)
				{
					$row_corte = _fetch_array($result_corte);
					$nombre_emp = mb_strtoupper($row_corte['usuario']);
					$hora= $row_corte["hora_corte"];
					$caja = $row_corte["caja"];
					$turno = $row_corte["turno"];
					$infoo.="FECHA: ".$fk."  HORA:".hora($hora)."\n";
					$infoo.="EMPLEADO: ".$nombre_emp."\n";
					$infoo.="CAJA : ".$caja. "  TURNO: ".$turno."\n";

				}
				else
				{
					$infoo.="FECHA: ".$fk;
				}
				//$infoo.="TIQUETE #".utf8_decode(intval($tike_max+1))."\n";
				$infoo.="CORTE Z"."\n";
				//$infoo.="FECHA: ".$fk."\n\n";

				$infoo.="VENTAS CON TIQUETE"."\n";

				$infoo.=str_pad("No. INICIAL:",20 ," " , STR_PAD_RIGHT).str_pad(intval($tike_min),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("No. FINAL:",20 ," " , STR_PAD_RIGHT).str_pad(intval($tike_max),20 ," " , STR_PAD_LEFT)."\n";

				$infoo.=str_pad("VENTAS EXENTAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($exent_t['TIK'], 2),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("VENTAS GRAVADAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($total_tike-$exent_t['TIK'], 2),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("VENTAS NO SUJETAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format(0,2),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("RETENCION:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($tret, 2),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("TOTAL:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($total_tike-$tret, 2),20 ," " , STR_PAD_LEFT)."\n\n";

				$infoo.="VENTAS CON FACTURA"."\n";

				$infoo.=str_pad("No. INICIAL:",20 ," " , STR_PAD_RIGHT).str_pad(intval($factura_min),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("No. FINAL:",20 ," " , STR_PAD_RIGHT).str_pad(intval($factura_max),20 ," " , STR_PAD_LEFT)."\n";

				$infoo.=str_pad("VENTAS EXENTAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($exent_t['COF'], 2),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("VENTAS GRAVADAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($total_factura-$exent_t['COF'], 2),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("VENTAS NO SUJETAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format(0,2),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("RETENCION:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($fret, 2),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("TOTAL:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($total_factura-$fret, 2),20 ," " , STR_PAD_LEFT)."\n\n";

				$infoo.="VENTAS CON CREDITO FISCAL"."\n";

				$infoo.=str_pad("No. INICIAL:",20 ," " , STR_PAD_RIGHT).str_pad(intval($credito_fiscal_min),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("No. FINAL:",20 ," " , STR_PAD_RIGHT).str_pad(intval($credito_fiscal_max),20 ," " , STR_PAD_LEFT)."\n";

				$infoo.=str_pad("VENTAS EXENTAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($exent_t['CCF'], 2),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("VENTAS GRAVADAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($total_credito_fiscal-$exent_t['CCF'], 2),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("VENTAS NO SUJETAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format(0,2),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("RETENCION:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($cret, 2),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("TOTAL:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($total_credito_fiscal-$cret, 2),20 ," " , STR_PAD_LEFT)."\n\n";

				$infoo.=str_pad("TOTAL GENERAL:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($total_general, 2),20 ," " , STR_PAD_LEFT)."\n\n";

				$sql_ven_tar = _query("SELECT tipo_documento,num_fact_impresa,total FROM factura WHERE tipo_documento IN('DEV','NC') AND fecha = '".MD($fk)."' AND finalizada=1 AND anulada=0");
				if(_num_rows($sql_ven_tar)>0)
				{
					$infoo.="\nDEVOLUCIONES SOBRE VENTAS DEL DIA"."\n";
					$tottar=0;
					while($row_ven_tar = _fetch_array($sql_ven_tar))
					{

						$tipo_dt=str_pad($row_ven_tar['tipo_documento'],10," ",STR_PAD_RIGHT);
						$num_nfi=str_pad($row_ven_tar['num_fact_impresa'],10," ",STR_PAD_LEFT);
						$tor_tfi=str_pad("$ ".number_format($row_ven_tar['total'],2,".",""),20," ",STR_PAD_LEFT);
						$infoo.="".$tipo_dt."".$num_nfi."".$tor_tfi."\n";
						$tottar = $tottar + $row_ven_tar['total'];
					}
					$tipo_dt=str_pad("TOTAL",10," ",STR_PAD_RIGHT);
					$num_nfi=str_pad("",10," ",STR_PAD_LEFT);
					$tor_tfi=str_pad("$ ".number_format($tottar,2,".",""),20," ",STR_PAD_LEFT);
					$infoo.="".$tipo_dt."".$num_nfi."".$tor_tfi."\n";
				}

		  }

		  $tAry['te']+=$exent_t['TIK'];
		  $tAry['fe']+=$exent_t['COF'];
		  $tAry['ce']+=$exent_t['CCF'];

		  $tAry['tg']+=$total_tike-$exent_t['TIK'];
		  $tAry['fg']+=$total_factura-$exent_t['COF'];
		  $tAry['cg']+=$total_credito_fiscal-$exent_t['CCF'];

		  $tAry['tt']+=$total_tike-$tret;
		  $tAry['ft']+=$total_factura-$fret;
		  $tAry['ct']+=$total_credito_fiscal-$cret;

		  $tAry['tret']+=$tret;
		  $tAry['fret']+=$fret;
		  $tAry['cret']+=$cret;

		  $tAry['ttg']+=$total_general;

		  $fk = sumar_dias($fk,1);

		  $fk = MD($fk);
		}
		return  $xdatos['data']=$infoo;
	}

	function data_gz()
	{
		$infoo="";
		$fecha_inicio= $_REQUEST['fini'];
		$fecha_fin= $_REQUEST['ffin'];
		$fini1 = $_REQUEST["fini"];
		$fin1 = $_REQUEST["ffin"];
		$fecha_ini= ed($fecha_inicio);
		$fecha_fina=ed($fecha_fin);
		$sql_empresa=_query("SELECT * FROM sucursal where id_sucursal=$_SESSION[id_sucursal]");
		$array_empresa=_fetch_array($sql_empresa);
		$nombre_empresa=$array_empresa['descripcion'];
		$telefono=$array_empresa['telefono1'];
		$logo_empresa=$array_empresa['logo'];

		$id_user=$_SESSION["id_usuario"];
		$id_sucursal=$_SESSION['id_sucursal'];

		$sql_cabezera = _query("SELECT * FROM sucursal WHERE id_sucursal = '$id_sucursal'");
		$cue = _num_rows($sql_cabezera);
		$row_cabe = _fetch_array($sql_cabezera);

		$nite=$row_cabe['nit'];
		$nrce=$row_cabe['nrc'];
		$empresa1=$row_cabe['descripcion'];
		$giro1=$row_cabe['giro'];
		$direccion = $row_cabe['direccion'];


		$aSz = array(
			'fecha' => 17,
			'sucursal' => 8,
			'inicio' => 12,
			'fin' =>12,
			'ex' =>15,
			'giv' =>14,
			'ret' => 10,
			'total' => 14,
			'totalgen' => 15,
		);


		$fecha_impresion = date("d-m-Y")." ".hora(date("H:i:s"));
		$fk = $fini1;

		$tAry = array(
			'te' => 0,
			'tg' => 0,
			'tt' => 0,
			'tret' => 0,
			'fe' => 0,
			'fg' => 0,
			'ft' => 0,
			'fret' => 0,
			'ce' => 0,
			'cg' => 0,
			'ct' => 0,
			'cret' => 0,
			'ttg' => 0,
		);
		$one_p=0;

		while(strtotime($fk) <= strtotime($fin1))
		{
			$fk = ($fk);
			$sql_efectivo = _query("SELECT * FROM factura WHERE fecha = '$fk' AND finalizada=1 AND anulada=0 AND id_sucursal = '$id_sucursal'");
			$cuenta = _num_rows($sql_efectivo);
			$sql_min_max=_query("
			SELECT MIN(CAST(num_fact_impresa as UNSIGNED)) as minimo, MAX(CAST(num_fact_impresa as UNSIGNED)) as maximo FROM factura WHERE fecha = '$fk' AND numero_doc LIKE '%TIK%' AND id_sucursal = $id_sucursal UNION ALL
			SELECT MIN(CAST(num_fact_impresa as UNSIGNED)) as minimo, MAX(CAST(num_fact_impresa as UNSIGNED)) as maximo FROM factura WHERE fecha = '$fk' AND numero_doc LIKE '%COF%' AND id_sucursal = $id_sucursal UNION ALL
			SELECT MIN(CAST(num_fact_impresa as UNSIGNED)) as minimo, MAX(CAST(num_fact_impresa as UNSIGNED)) as maximo FROM factura WHERE fecha = '$fk' AND numero_doc LIKE '%CCF%' AND id_sucursal = $id_sucursal");
			$cuenta_min_max = _num_rows($sql_min_max);
			///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$total_tike = 0;
			$total_factura = 0;
			$total_credito_fiscal = 0;
			$tike_min = 0;
			$tike_max = 0;
			$factura_min = 0;
			$factura_max = 0;
			$credito_fiscal_min = 0;
			$credito_fiscal_max = 0;

			$tret = 0;
			$fret = 0;
			$cret = 0;

			$exent_t = array(
				'TIK' => 0,
				'COF' => 0,
				'CCF' => 0
			);
			if($cuenta > 0)
			{
					while ($row_corte = _fetch_array($sql_efectivo))
					{
							$id_factura = $row_corte["id_factura"];
							$anulada = $row_corte["anulada"];
							$subtotal = $row_corte["subtotal"];
							$suma = $row_corte["sumas"];
							$iva = $row_corte["iva"];
							$total = $row_corte["total"];
							$numero_doc = $row_corte["numero_doc"];
							$ax = explode("_", $numero_doc);
							$numero_co = $ax[0];
							$alias_tipodoc = $ax[1];
							$total = $row_corte["total"];


							if($alias_tipodoc == 'TIK')
							{
								$exent_t[$alias_tipodoc]+=$row_corte['venta_exenta'];
								$total_tike += $total;
								$tret +=$row_corte['retencion'];

							}
							else if($alias_tipodoc == 'COF')
							{
								$exent_t[$alias_tipodoc]+=$row_corte['venta_exenta'];
								$total_factura += $total;
								$fret +=$row_corte['retencion'];
							}
							else if($alias_tipodoc == 'CCF')
							{
								$exent_t[$alias_tipodoc]+=$row_corte['venta_exenta'];
								$total_credito_fiscal += $total;
								$cret +=$row_corte['retencion'];
							}




					}
			}

			if($cuenta_min_max)
			{
					$i = 1;
					while ($row_min_max = _fetch_array($sql_min_max))
					{
							if($i == 1)
							{
								$tike_min = $row_min_max["minimo"];
								$tike_max = $row_min_max["maximo"];
									list($minimo_num,$ads) = explode("_", $tike_min);
									list($maximo_num,$ads) = explode("_", $tike_max);
							}
							if($i == 2)
							{
									$factura_min = $row_min_max["minimo"];
									$factura_max = $row_min_max["maximo"];
							}
							if($i == 3)
							{
									$credito_fiscal_min = $row_min_max["minimo"];
									$credito_fiscal_max = $row_min_max["maximo"];
							}
							$i += 1;
					}
			}




			$total_general = $total_tike + $total_factura + $total_credito_fiscal - $tret -  $fret -  $cret;

			$sql_fa = _query("SELECT * FROM factura WHERE fecha = '$fk' AND tipo_documento!='TIK'  AND anulada=1 AND num_fact_impresa>0 AND id_sucursal = '$id_sucursal'");
			$fk = ED($fk);

			if($total_general!=0)
			{
				$sqlcc = _fetch_array(_query("SELECT MAX(caja) as caja FROM factura WHERE fecha='".MD($fk)."'"));

				$sql_caja = _query("SELECT * FROM caja WHERE id_caja='$sqlcc[caja]'");

				$direccio=divtextlin($direccion,35);
				$empress= divtextlin($empresa1, 35);
				$giros = divtextlin($giro1, 35);

				if($one_p==0)
				{
						foreach ($empress as $nnona)
					 {
						 $infoo.=$nnona."\n";
					 }
					 foreach ($direccio as $nnona)
					 {
						 $infoo.=$nnona."\n";
					 }
					 foreach ($giros as $nnon)
					 {

						 $infoo.=$nnon."\n";
					 }
					 $infoo.="NIT: ".$nite."  NRC: ".$nrce."\n";


					if(_num_rows($sql_caja)>0)
					{

						$dats_caja = _fetch_array($sql_caja);
						$fehca = ED($dats_caja["fecha"]);
						$resolucion = $dats_caja["resolucion"];
						$serie = $dats_caja["serie"];
						$desde = $dats_caja["desde"];
						$hasta = $dats_caja["hasta"];
						$nombre_c = $dats_caja["nombre"];

						$resolucion1 = divtextlin($resolucion, 35);
						foreach ($resolucion1 as $nnon)
					 {
						 $infoo.=$nnon."\n";
					 }

						$infoo.="DEL: ".$desde." AL: ".$hasta."\n";
						$infoo.="SERIE: ".$serie."\n";
						$infoo.="FECHA RESOLUCION: ".$fehca."\n";
					}
					$one_p=1;

					$infoo.="CORTE Z MENSUAL"."\n";
				}
				/*

				$infoo.="VENTAS CON TIQUETE"."\n";

				$infoo.=str_pad("No. INICIAL:",20 ," " , STR_PAD_RIGHT).str_pad(intval($tike_min),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("No. FINAL:",20 ," " , STR_PAD_RIGHT).str_pad(intval($tike_max),20 ," " , STR_PAD_LEFT)."\n";

				$infoo.=str_pad("VENTAS EXENTAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($exent_t['TIK'], 2),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("VENTAS GRAVADAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($total_tike-$exent_t['TIK'], 2),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("VENTAS NO SUJETAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format(0,2),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("RETENCION:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($tret, 2),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("TOTAL:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($total_tike-$tret, 2),20 ," " , STR_PAD_LEFT)."\n\n";

				$infoo.="VENTAS CON FACTURA"."\n";

				$infoo.=str_pad("No. INICIAL:",20 ," " , STR_PAD_RIGHT).str_pad(intval($factura_min),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("No. FINAL:",20 ," " , STR_PAD_RIGHT).str_pad(intval($factura_max),20 ," " , STR_PAD_LEFT)."\n";

				$infoo.=str_pad("VENTAS EXENTAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($exent_t['COF'], 2),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("VENTAS GRAVADAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($total_factura-$exent_t['COF'], 2),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("VENTAS NO SUJETAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format(0,2),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("RETENCION:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($fret, 2),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("TOTAL:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($total_factura-$fret, 2),20 ," " , STR_PAD_LEFT)."\n\n";

				$infoo.="VENTAS CON CREDITO FISCAL"."\n";

				$infoo.=str_pad("No. INICIAL:",20 ," " , STR_PAD_RIGHT).str_pad(intval($credito_fiscal_min),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("No. FINAL:",20 ," " , STR_PAD_RIGHT).str_pad(intval($credito_fiscal_max),20 ," " , STR_PAD_LEFT)."\n";

				$infoo.=str_pad("VENTAS EXENTAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($exent_t['CCF'], 2),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("VENTAS GRAVADAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($total_credito_fiscal-$exent_t['CCF'], 2),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("VENTAS NO SUJETAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format(0,2),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("RETENCION:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($cret, 2),20 ," " , STR_PAD_LEFT)."\n";
				$infoo.=str_pad("TOTAL:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($total_credito_fiscal-$cret, 2),20 ," " , STR_PAD_LEFT)."\n\n";

				$infoo.=str_pad("TOTAL GENERAL:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($total_general, 2),20 ," " , STR_PAD_LEFT)."\n\n";
				*/
			}


			$tAry['te']+=$exent_t['TIK'];
			$tAry['fe']+=$exent_t['COF'];
			$tAry['ce']+=$exent_t['CCF'];

			$tAry['tg']+=$total_tike-$exent_t['TIK'];
			$tAry['fg']+=$total_factura-$exent_t['COF'];
			$tAry['cg']+=$total_credito_fiscal-$exent_t['CCF'];

			$tAry['tt']+=$total_tike-$tret;
			$tAry['ft']+=$total_factura-$fret;
			$tAry['ct']+=$total_credito_fiscal-$cret;

			$tAry['tret']+=$tret;
			$tAry['fret']+=$fret;
			$tAry['cret']+=$cret;

			$tAry['ttg']+=$total_general;

			$fk = sumar_dias($fk,1);

			$fk = MD($fk);
		}

		$sql_min_max=_query("
		SELECT MIN(CAST(num_fact_impresa as UNSIGNED)) as minimo, MAX(CAST(num_fact_impresa as UNSIGNED)) as maximo FROM factura WHERE fecha between '$fecha_inicio' AND '$fecha_fin' AND numero_doc LIKE '%TIK%' AND id_sucursal = $id_sucursal UNION ALL
		SELECT MIN(CAST(num_fact_impresa as UNSIGNED)) as minimo, MAX(CAST(num_fact_impresa as UNSIGNED)) as maximo FROM factura WHERE fecha between '$fecha_inicio' AND '$fecha_fin' AND numero_doc LIKE '%COF%' AND id_sucursal = $id_sucursal UNION ALL
		SELECT MIN(CAST(num_fact_impresa as UNSIGNED)) as minimo, MAX(CAST(num_fact_impresa as UNSIGNED)) as maximo FROM factura WHERE fecha between '$fecha_inicio' AND '$fecha_fin' AND numero_doc LIKE '%CCF%' AND id_sucursal = $id_sucursal");
		$cuenta_min_max = _num_rows($sql_min_max);

		if($cuenta_min_max)
		{
				$i = 1;
				while ($row_min_max = _fetch_array($sql_min_max))
				{
						if($i == 1)
						{
							$tike_min = $row_min_max["minimo"];
							$tike_max = $row_min_max["maximo"];
								list($minimo_num,$ads) = explode("_", $tike_min);
								list($maximo_num,$ads) = explode("_", $tike_max);
						}
						if($i == 2)
						{
								$factura_min = $row_min_max["minimo"];
								$factura_max = $row_min_max["maximo"];
						}
						if($i == 3)
						{
								$credito_fiscal_min = $row_min_max["minimo"];
								$credito_fiscal_max = $row_min_max["maximo"];
								$mi = explode("_", $credito_fiscal_min);
								$minimo_num = $mi[0];
								$max = explode("_", $credito_fiscal_max);
								$maximo_num = $max[0];
						}
						$i += 1;
				}
		}


		$infoo.="DEL".ED($fecha_inicio)." AL ".ED($fecha_fin)."\n\n";

		$infoo.="VENTAS CON TIQUETE"."\n";

		$infoo.=str_pad("No. INICIAL:",20 ," " , STR_PAD_RIGHT).str_pad(intval($tike_min),20 ," " , STR_PAD_LEFT)."\n";
		$infoo.=str_pad("No. FINAL:",20 ," " , STR_PAD_RIGHT).str_pad(intval($tike_max),20 ," " , STR_PAD_LEFT)."\n";

		$infoo.=str_pad("VENTAS EXENTAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($tAry['te'], 2),20 ," " , STR_PAD_LEFT)."\n";
		$infoo.=str_pad("VENTAS GRAVADAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($tAry['tg'], 2),20 ," " , STR_PAD_LEFT)."\n";
		$infoo.=str_pad("VENTAS NO SUJETAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format(0,2),20 ," " , STR_PAD_LEFT)."\n";
		$infoo.=str_pad("RETENCION:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format(	$tAry['tret'], 2),20 ," " , STR_PAD_LEFT)."\n";
		$infoo.=str_pad("TOTAL:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($tAry['tt'], 2),20 ," " , STR_PAD_LEFT)."\n\n";

		$infoo.="VENTAS CON FACTURA"."\n";

		$infoo.=str_pad("No. INICIAL:",20 ," " , STR_PAD_RIGHT).str_pad(intval($factura_min),20 ," " , STR_PAD_LEFT)."\n";
		$infoo.=str_pad("No. FINAL:",20 ," " , STR_PAD_RIGHT).str_pad(intval($factura_max),20 ," " , STR_PAD_LEFT)."\n";

		$infoo.=str_pad("VENTAS EXENTAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($tAry['fe'], 2),20 ," " , STR_PAD_LEFT)."\n";
		$infoo.=str_pad("VENTAS GRAVADAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($tAry['fg'], 2),20 ," " , STR_PAD_LEFT)."\n";
		$infoo.=str_pad("VENTAS NO SUJETAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format(0,2),20 ," " , STR_PAD_LEFT)."\n";
		$infoo.=str_pad("RETENCION:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format(	$tAry['fret'], 2),20 ," " , STR_PAD_LEFT)."\n";
		$infoo.=str_pad("TOTAL:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($tAry['ft'], 2),20 ," " , STR_PAD_LEFT)."\n\n";

		$infoo.="VENTAS CON CREDITO FISCAL"."\n";

		$infoo.=str_pad("No. INICIAL:",20 ," " , STR_PAD_RIGHT).str_pad(intval($credito_fiscal_min),20 ," " , STR_PAD_LEFT)."\n";
		$infoo.=str_pad("No. FINAL:",20 ," " , STR_PAD_RIGHT).str_pad(intval($credito_fiscal_max),20 ," " , STR_PAD_LEFT)."\n";

		$infoo.=str_pad("VENTAS EXENTAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($tAry['ce'], 2),20 ," " , STR_PAD_LEFT)."\n";
		$infoo.=str_pad("VENTAS GRAVADAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($tAry['cg'], 2),20 ," " , STR_PAD_LEFT)."\n";
		$infoo.=str_pad("VENTAS NO SUJETAS:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format(0,2),20 ," " , STR_PAD_LEFT)."\n";
		$infoo.=str_pad("RETENCION:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format(	$tAry['cret'], 2),20 ," " , STR_PAD_LEFT)."\n";
		$infoo.=str_pad("TOTAL:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($tAry['ct'], 2),20 ," " , STR_PAD_LEFT)."\n\n";

		$infoo.=str_pad("TOTAL GENERAL:",20 ," " , STR_PAD_RIGHT).str_pad("$ ".number_format($tAry['ttg'], 2),20 ," " , STR_PAD_LEFT)."\n\n";



		$sql_ven_tar = _query("SELECT tipo_documento,num_fact_impresa,total FROM factura WHERE tipo_documento IN('DEV','NC') AND fecha between '".$fecha_inicio."' AND '".$fecha_fin."' AND finalizada=1 AND anulada=0");
		if(_num_rows($sql_ven_tar)>0)
		{
			$infoo.="\nDEVOLUCIONES SOBRE VENTAS"."\n";
			$tottar=0;
			while($row_ven_tar = _fetch_array($sql_ven_tar))
			{

				$tipo_dt=str_pad($row_ven_tar['tipo_documento'],10," ",STR_PAD_RIGHT);
				$num_nfi=str_pad($row_ven_tar['num_fact_impresa'],10," ",STR_PAD_LEFT);
				$tor_tfi=str_pad("$ ".number_format($row_ven_tar['total'],2,".",""),20," ",STR_PAD_LEFT);
				$infoo.="".$tipo_dt."".$num_nfi."".$tor_tfi."\n";
				$tottar = $tottar + $row_ven_tar['total'];
			}
			$tipo_dt=str_pad("TOTAL",10," ",STR_PAD_RIGHT);
			$num_nfi=str_pad("",10," ",STR_PAD_LEFT);
			$tor_tfi=str_pad("$ ".number_format($tottar,2,".",""),20," ",STR_PAD_LEFT);
			$infoo.="".$tipo_dt."".$num_nfi."".$tor_tfi."\n";
		}
		return  $xdatos['data']=$infoo;
	}


	if(!isset($_POST['process'])){
		initial();
	}
	else
	{
		if(isset($_POST['process']))
		{
			switch ($_POST['process'])
			{case 'imprimir':
				imprimir();
				break;
			case 'imprimir_gz':
				imprimir_gz();
				break;
        default:
        break;
			}
		}
	}
	?>
