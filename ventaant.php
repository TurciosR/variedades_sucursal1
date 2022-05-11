<?php
include_once "_core.php";
include('num2letras.php');
include('facturacion_funcion_imprimir.php');
function initial()
{
  //$id_factura=$_REQUEST["id_factura"];
  $title="Venta Anterior";
  $_PAGE = array();
  $_PAGE ['title'] = $title;
  $_PAGE ['links'] = null;
  $_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/typeahead.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/select2/select2-bootstrap.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/sweetalert/sweetalert.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/bootstrap-checkbox/bootstrap-checkbox.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link rel="stylesheet" type="text/css" href="css/plugins/perfect-scrollbar/perfect-scrollbar.css">';
  $_PAGE ['links'] .= '<link rel="stylesheet" type="text/css" href="css/util.css">';
  $_PAGE ['links'] .= '<link rel="stylesheet" type="text/css" href="css/main.css">';
  include_once "header.php";
  //include_once "main_menu.php";
  date_default_timezone_set('America/El_Salvador');
  $fecha_actual = date('Y-m-d');

  $id_sucursal=$_SESSION['id_sucursal'];
  //permiso del script
  $id_user=$_SESSION["id_usuario"];
  $sql_apertura = _query("SELECT * FROM apertura_caja WHERE vigente = 1 AND id_sucursal = '$id_sucursal' AND fecha='$fecha_actual'");
  $cuenta = _num_rows($sql_apertura);

  $turno_vigente=0;
  if ($cuenta>0) {
    $row_apertura = _fetch_array($sql_apertura);
    $id_apertura = $row_apertura["id_apertura"];
    $turno = $row_apertura["turno"];
    $caja = $row_apertura["caja"];
    $fecha_apertura = $row_apertura["fecha"];
    $hora_apertura = $row_apertura["hora"];
    $turno_vigente = $row_apertura["vigente"];
  }

  //impuestos
  $sql_iva="SELECT iva,monto_retencion1,monto_retencion10,monto_percepcion FROM sucursal WHERE id_sucursal='$id_sucursal'";
  $result_IVA=_query($sql_iva);
  $row_IVA=_fetch_array($result_IVA);
  $iva=$row_IVA['iva']/100;
  $monto_retencion1=$row_IVA['monto_retencion1'];
  $monto_retencion10=$row_IVA['monto_retencion10'];
  $monto_percepcion=$row_IVA['monto_percepcion'];
  /////////////////////////////////////////////////////
  $admin=$_SESSION["admin"];
  $uri = $_SERVER['SCRIPT_NAME'];
  $filename=get_name_script($uri);
  $links=permission_usr($id_user, $filename);
  $id_usuario=$id_user;

  $fecha_actual=date("Y-m-d");
  //array clientes

  //clientes

  //factura
  ?>

  <style media="screen">
    .sweet-alert i
    {
      color:#FF0000;
    }

    #inventable tr td
    {
      font-size: 12px;
    }

    #inventable input
    {
      height: 28px;
      font-size: 12px;
      margin: 0px;

    }
    .Delete>input,a
    {
      height: 28px;
    }

  </style>

  <div class="gray-bg">
    <div class="wrapper wrapper-content  animated fadeInRight">
      <div class="row">
        <div class="col-lg-12">
          <div class="ibox ">
            <?php
            //permiso del script
            if ($links!='NOT' || $admin=='1') {
              if ($turno_vigente==1){
                ?>
                <div class="ibox-content">
                    <div class="row focuss"><br>
                      <div class="form-group col-md-4">
                        <div id="a">
                          <label>Buscar Producto (Código)</label>
                            <input type="text" id="codigo" name="codigo" style="width:100% !important" class="form-control usage" placeholder="Ingrese Código de producto" style="border-radius:0px">
                        </div>
                        <div hidden id="b">
                          <label id='buscar_habilitado'>Buscar Producto (Descripción)</label>
                          <div id="scrollable-dropdown-menu">
                            <input type="text" id="producto_buscar" name="producto_buscar" style="width:100% !important" class="form-control usage typeahead" placeholder="Ingrese la Descripción de producto" data-provide="typeahead" style="border-radius:0px">
                          </div>
                        </div>
                      </div>
                      <div class='col-md-2'><br>
                        <button type="button" id="change"  name="change" class="btn btn-sm btn-success pull-right usage form-control"><i class="fa fa-exchange"></i> F3 Descripción</button>
                      </div>
                      <div class="col-md-2">
                        <label>Fecha de facturacion</label>
                        <input class="form-control" readonly style='text-align:center' id="fecha" value="<?php echo $fecha_actual; ?>">
                      </div>
                      <div class="col-md-2">
                        <label>Numero Factura Impresa</label>
                        <input class="form-control" style='text-align:center' id="numero_factura_impresa" value="">
                      </div>
                      <div class="col-md-2"><br>
                        <a class="btn btn-danger btn-sm pull-right" style="margin-left:3%;" href="dashboard.php" id='salir'><i class="fa fa-mail-reply"></i> F4 Salir</a>
                        <button type="button" id="preventa" style="margin-left:3%;" name="preventa" class="btn btn-sm btn-success pull-right usage"><i class="fa fa-save"></i> F8 Guardar</button>
                      </div>
                    </div>
                    <div class="row">

                      <div style="display:none"  class="form-group col-md-2">
                        <div class="form-group has-info">

                          <input type="hidden" class="form-control" id="num_ref" name="num_ref" value="">

                        </div>
                      </div>
                      <div style="display:none" class="col-md-3 form-group">

                        <!--
                        <input type="text" name="n_ref" id="n_ref" class="form-control usage" style="border-radius:0px;">
                      -->
                      <select class="form-control select_r" name="n_ref" id="n_ref">
                        <option value="0">Seleccione</option>
                        <?php
                        $fecha_actual = date("Y-m-d");
                        $sql="SELECT cliente.nombre, factura.total,numero_ref FROM factura LEFT JOIN cliente ON cliente.id_cliente=factura.id_cliente WHERE numero_ref!=0 AND fecha='".date("Y-m-d")."' AND finalizada!=1 AND factura.id_sucursal=$id_sucursal";
                        $result=_query($sql);
                        $cuenta = _num_rows($result);
                        echo _error();
                        if($cuenta > 0)
                        {
                          while ($row = _fetch_array($result))
                          {
                            echo "<option value='".$row['numero_ref']."'>";
                            echo "".$row['numero_ref']." | ";
                            echo "".utf8_decode(Mayu(utf8_decode($row['nombre'])))." | ";
                            echo "$".number_format($row['total'],2)."";
                            echo "</option>";


                          }
                        }
                         ?>
                      </select>
                      </div>

                      <div hidden  class="form-group col-md-2">
                        <div class="form-group has-info">
                          <label>Seleccione Vendedor</label>
                          <input type="text" name="vendedor" id="vendedor" value="">
                          <!--
                          <select class="form-control select usage" name="vendedor" id="vendedor">
                            <option value="">Seleccione</option>
                          -->
                            <?php
                          /*  $sqlemp=_query("SELECT id_empleado, nombre FROM empleado WHERE id_sucursal='$id_sucursal' AND id_tipo_empleado=2");
                            while($row_emp = _fetch_array($sqlemp))
                            {
                              echo "<option value='".$row_emp["id_empleado"]."'>".$row_emp["nombre"]."</option>";
                            }*/
                            ?>
                          </select>
                        </div>
                      </div>

                    <div id='form_datos_cliente' class="form-group col-md-3">
                      <div class="form-group has-info">
                        <label>Cliente&nbsp;</label>
                        <select class="form-control select usage" name="id_cliente" id="id_cliente">
                          <option value="">Seleccione</option>
                          <?php
                          $sqlcli=_query("SELECT * FROM cliente WHERE id_sucursal='$id_sucursal' ORDER BY nombre");
                          while($row_cli = _fetch_array($sqlcli))
                          {
                            echo "<option value='".$row_cli["id_cliente"]."'";
                            if($row_cli["id_cliente"] == 1)
                            {
                              echo " selected ";
                            }
                            echo ">".$row_cli["nombre"]."</option>";
                          }
                          ?>
                        </select>
                      </div>
                    </div>
                    <div  class="form-group col-md-2">
                      <div class="form-group has-info">
                        <label>Tipo Impresi&oacuten</label>
                        <select name='tipo_impresion' id='tipo_impresion' class='select form-control usage'>
                          <option value='CCF' selected>CREDITO FISCAL</option>
                        </select>
                      </div>
                    </div>
                    <div style="display:none" class="col-md-2">
                      <div class="form-group has-info">
                        <label>Seleccione tipo de pago</label><br>
                        <select name='con_pago' id='con_pago' class='select form-control usage'>
                          <option value='0' selected>Contado</option>
                          <option value='1' >Credito</option>
                        </select>
                      </div>
                    </div>

                    </div>
                  <!--load datables estructure html-->
                  <header>
                    <section>
                      <input type='hidden' name='porc_iva' id='porc_iva' value='<?php echo $iva; ?>'>
                      <input type='hidden' name='monto_retencion1' id='monto_retencion1' value='<?php echo $monto_retencion1 ?>'>
                      <input type='hidden' name='monto_retencion10' id='monto_retencion10' value='<?php echo $monto_retencion10 ?>'>
                      <input type='hidden' name='monto_percepcion' id='monto_percepcion' value='100'>
                      <input type='hidden' name='porc_retencion1' id='porc_retencion1' value=0>
                      <input type='hidden' name='porc_retencion10' id='porc_retencion10' value=0>
                      <input type='hidden' name='porc_percepcion' id='porc_percepcion' value=0>
                      <input type='hidden' name='porcentaje_descuento' id='porcentaje_descuento' value=0>

                      <div class="">
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
                                        <th hidden class='success  cell100 column10'>Descripción</th>
                                        <th class='success  cell100 column10'>Precios</th>
                                        <th class='success  cell100 column10'>$</th>
                                        <th class='success  cell100 column10'>Subtotal</th>
                                        <th class='success  cell100 column10'>Acci&oacute;n</th>
                                      </tr>
                                    </thead>
                                  </table>
                                </div>
                                <div class="table100-body js-pscroll">
                                  <table>
                                    <tbody id="inventable"></tbody>
                                  </table>
                                </div>
                                <div class="table101-body">
                                  <table>
                                    <tbody>
                                      <tr>

                                      </tr>
                                      <tr>
                                        <td class='cell100 column50 text-bluegrey'  id='totaltexto'>&nbsp;</td>
                                        <td class='cell100 column15 leftt  text-bluegrey ' >CANT. PROD:</td>
                                        <td class='cell100 column10 text-right text-danger' id='totcant'>0.00</td>
                                        <td class="cell100 column10  leftt text-bluegrey ">TOTALES $:</td>
                                        <td class='cell100 column15 text-right text-green' id='total_gravado'>0.00</td>

                                      </tr>
                                      <tr >
                                        <td class="cell100 column15 leftt text-bluegrey ">GRAVADO $:</td>
                                        <td  class="cell100 column10 text-right text-green" id='total_gravado_sin_iva'>0.00</td>
                                        <td class="cell100 column15  leftt  text-bluegrey ">IVA  $:</td>
                                        <td class="cell100 column10 text-right text-green " id='total_iva'>0.00</td>
                                        <td class="cell100 column15  leftt text-bluegrey ">SUBTOTAL  $:</td>
                                        <td class="cell100 column10 text-right  text-green" id='total_gravado_iva'>0.00</td>
                                        <td class="cell100 column15 leftt  text-bluegrey ">VENTA EXENTA $:</td>
                                        <td class="cell100 column10  text-right text-green" id='total_exenta'>0.00</td>
                                      </tr>
                                      <tr >
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
                          <div hidden class="col-md-3">
                            <div class="wrap-table1001">
                              <div class="table100 ver1 m-b-10">
                                <div class="table100-head">
                                  <table id="inventable1">
                                    <thead>
                                      <tr class="row100 head">
                                        <th class="success cell100 column100 text-center">PAGO Y CAMBIO</th>
                                        </tr>
                                    </thead>
                                  </table>
                                </div>
                                <div class="table101-body">
                                  <table>
                                    <tbody>
                                      <tr>
                                        <td class='cell100 column70 text-success'>CORRELATIVO:</td>
                                        <td class='cell100 column30'><input type="text" id="corr_in" class="txt_box2"  value="" readOnly></td>
                                      </tr>
                                      <tr>
                                        <td class='cell100 column70 text-success'>TOTAL: $</td>
                                        <td class='cell100 column30'><input type="text" id="tot_fdo" class="txt_box2"   value="" readOnly></td>
                                      </tr>
                                      <tr>
                                        <td class='cell100 column70 text-success'>NUM. DOCUMENTO: </td>
                                        <td class='cell100 column30'><input type="text" id="numdoc" class="txt_box2"   value="" readOnly></td>
                                      </tr>
                                      <tr>
                                        <td class='cell100 column70 text-success'>CLIENTE: </td>
                                        <td class='cell100 column30'><input type="text" id="nomcli" class="txt_box2"  value="" readOnly></td>
                                      </tr>
									  <tr>
                                        <td class='cell100 column70 text-success'>DIRECCION: </td>
                                        <td class='cell100 column30'><input type="text" id="dircli" class="txt_box2"  value="" readOnly></td>
                                      </tr>
                                      <tr>
                                        <td class='cell100 column70 text-success'>NIT: </td>
                                        <td class='cell100 column30'><input type="text" id="nitcli" class="txt_box2"    value="" readOnly></td>
                                      </tr>
                                      <tr>
                                        <td class='cell100 column70 text-success'>NRC: </td>
                                        <td class='cell100 column30'><input type="text" id="nrccli" class="txt_box2"   value="" readOnly></td>
                                      </tr>
                                      <tr>
                                        <td class='cell100 column70 text-success'>EFECTIVO: $</td>
                                        <td class='cell100 column30'> <input type="text" id="efectivov" class="txt_box2"   value=""> </td>
                                      </tr>
                                      <tr>
                                        <td class='cell100 column70 text-success'>CAMBIO: $</td>
                                        <td class='cell100 column30'><input type="text" id="cambiov" class="txt_box2"   value="" readOnly></td>
                                      </tr>

                                    </tbody>
                                  </table>
                                </div>

                              </div>
                            </div>
                          </div>
                        </div>
                        <?php

                        echo "<input type='hidden' name='id_empleado' id='id_empleado' >";
                        echo "<input type='hidden' name='numero_doc' id='numero_doc' >";
                        echo "<input type='hidden' name='id_factura' id='id_factura' >";
                        echo "<input type='hidden' name='urlprocess' id='urlprocess' value='$filename'>"; ?>
                        <input type='hidden' name='totalfactura' id='totalfactura' value='0'>
                        <input type="hidden" id="imprimiendo" name="imprimiendo" value="0">

                        <input type='hidden' name='id_apertura' id='id_apertura' value='<?php echo $id_apertura; ?>'>
                        <input type='hidden' name='turno' id='turno' value='<?php echo $turno; ?>'>
                        <input type='hidden' name='filas' id='filas' value='0'>
                        <input type='hidden' name='caja' id='caja' value='<?php echo $caja; ?>'>
                      </div>
                      <!--div class="table-responsive m-t"-->
                    </section>

                  </div>
                  <!--div class='ibox-content'-->
                  <!-- Modal -->
                  <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content modal-md">
                        <div class="modal-header">
                          <h4 class="modal-title" id="myModalLabel">Pago y Cambio</h4>
                        </div>
                        <div class="modal-body">
                          <div class="wrapper wrapper-content  animated fadeInRight">
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label><h5 class='text-navy'>Numero factura Interno:</h5></label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group" id='fact_num'></div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label><h5 class='text-navy'>Facturado $:</h5></label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <input type="text" id="facturado" name="facturado" value=0 class="form-control decimal" readonly>
                                </div>
                              </div>
                            </div>

                            <div class="row" id='fact_cf'>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label><strong><h5 class='text-danger'>Num. Factura/ Credito Fiscal/ Nota de Envio: </h5></strong></label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <input type="text" id='num_doc_fact' name='num_doc_fact' value='' class="form-control">
                                </div>
                              </div>
                            </div>
                            <div class="row" id='ccf'>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label><strong><h5 class='text-navy'>Nombre de Cliente Credito Fiscal: </h5></strong></label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <input type="text" id='nombreape' name='nombreape' value='' class="form-control">
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label>NIT Cliente</label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <input type='text' placeholder='NIT Cliente' class='form-control' id='nit' name='nit' value=''>
                                </div>
                              </div>


                              <div class="col-md-6">
                                <div class="form-group">
                                  <label>Registro Cliente(NRC)</label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <input type='text' placeholder='Registro (NRC) Cliente' class='form-control' id='nrc' name='nrc' value=''>
                                </div>
                              </div>


                            </div>

                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label>Efectivo $</label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <input type="text" id="efectivo" name="efectivo" value="" class="form-control decimal" autofocus>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label>Cambio $</label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <input type="text" id="cambio" name="cambio" value=0 placeholder="cambio" class="form-control decimal" readonly>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-primary" id="btnPrintFact">Imprimir</button>
                          <button type="button" class="btn btn-warning" id="btnEsc">Salir</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-container">
                    <div class="modal fade" id="clienteModal" tabindex="-2" role="dialog" aria-labelledby="myModalCliente" aria-hidden="true">
                      <div class="modal-dialog model-sm">
                        <div class="modal-content"> </div>
                      </div>
                    </div>
                    <div class='modal fade' id='busqueda' style="overflow:hidden;" role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                      <div class='modal-dialog modal-lg'>
                        <div class='modal-content modal-lg'></div><!-- /.modal-content -->
                      </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                  </div>
                </div>
                <!--<div class='ibox float-e-margins' -->
              </div>
              <!--div class='col-lg-12'-->
            <!--div class='row'-->
            <!--div class='wrapper wrapper-content  animated fadeInRight'-->

            <?php
          }   //apertura de caja
          else {
            echo "<br><br><div class='alert alert-warning'><h3 class='text-danger'> No Hay Apertura de Caja vigente para este turno!!! </h3></div></div></div></div></div>";
            include_once("footer.php");
          }  //apertura de caja
          include_once("footer.php");
          echo "<script src='js/funciones/ventaant.js'></script>";
          echo "<script src='js/plugins/arrowtable/arrow-table.js'></script>";
          echo "<script src='js/plugins/bootstrap-checkbox/bootstrap-checkbox.js'></script>";
          echo '<script src="js/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
          <script src="js/funciones/main.js"></script>';
          echo "<script src='js/funciones/util.js'></script>";
          ?>
          <?php
        } //permiso del script
        else {
          echo "<br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div></div></div></div></div>";
          include_once("footer.php");
        }
      }

          function total_texto()
          {
            $total=$_REQUEST['total'];
            list($entero, $decimal)=explode('.', $total);
            $enteros_txt=num2letras($entero);
            $decimales_txt=num2letras($decimal);

            if ($entero>1) {
              $dolar=" dolares";
            } else {
              $dolar=" dolar";
            }
            $cadena_salida= "Son: <strong>".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.</strong>";
            echo $cadena_salida;
          }

          //functions to load
          if (!isset($_REQUEST['process'])) {
            initial();
          }

          function numero_tiquete($ult_doc, $tipo)
          {
            $ult_doc=trim($ult_doc);
            $len_ult_valor=strlen($ult_doc);
            $long_num_fact=10;
            $long_increment=$long_num_fact-$len_ult_valor;
            $valor_txt="";
            if ($len_ult_valor<$long_num_fact) {
              for ($j=0;$j<$long_increment;$j++) {
                $valor_txt.="0";
              }
            } else {
              $valor_txt="";
            }
            $valor_txt=$valor_txt.$ult_doc."_".$tipo;
            return $valor_txt;
          }

          function insertar_preventa()
          {
            //date_default_timezone_set('America/El_Salvador');
            $fecha_movimiento = $_REQUEST['fecha_movimiento'];
            $numero_factuar_impresa = "";
            $id_cliente=$_POST['id_cliente'];
            $id_factura=$_POST['id_factura'];

            $id_vendedor=$_POST['id_vendedor'];
            $cuantos = $_POST['cuantos'];
            $array_json=$_POST['json_arr'];
            $fecha=date("Y-m-d");
            //  IMPUESTOS
            $total_percepcion= $_POST['total_percepcion'];

            $subtotal=$_POST['subtotal'];
            $sumas=$_POST['sumas'];
            $suma_gravada=$_POST['suma_gravada'];
            $iva= $_POST['iva'];
            $retencion= $_POST['retencion'];
            $venta_exenta= $_POST['venta_exenta'];
            $total_menos_retencion=$_POST['total'];
            $total = $retencion+$_POST['total'];
            $numero_factura_impresa = $_REQUEST['numero_factura_impresa'];

            $id_empleado=$_SESSION["id_usuario"];
            if ($id_vendedor == "") {
              $id_vendedor = $id_empleado;
            }
            $id_sucursal=$_SESSION["id_sucursal"];
            $fecha_actual = date('Y-m-d');
            $tipoprodserv = "PRODUCTO";
            $credito=0;

            $turno="";
            $caja="";
            $sql_apertura = _query("SELECT * FROM apertura_caja WHERE id_sucursal = '$id_sucursal' AND fecha='$fecha_movimiento'");
            $cuenta = _num_rows($sql_apertura);
            $id_apertura=0;
            $turno_vigente=0;
            if ($cuenta>0) {
              $row_apertura = _fetch_array($sql_apertura);
              $id_apertura = $row_apertura["id_apertura"];
              $turno = $row_apertura["turno"];
              $caja = $row_apertura["caja"];
            }

            $tipo_documento=$_POST['tipo_impresion'];
            $tipo_impresion=$tipo_documento;

            $insertar_fact=false;
            $insertar_fact_dett=true;
            $insertar_numdoc =false;

            $hora=date("H:i:s");
            $xdatos['typeinfo']='';
            $xdatos['msg']='';
            $xdatos['process']='';

            _begin();

            $a=1;
            $b=1;
            $c=1;
            $z=1;
            $j = 1 ;
            $k = 1 ;
            $l = 1 ;
            $tipo_entrada_salida='';

            $sql="select * from correlativo WHERE id_sucursal=$id_sucursal";
            $result= _query($sql);
            $rows=_fetch_array($result);
            $nrows=_num_rows($result);
            $ult_ccf=$rows['ccf']+1;
            $ult_cof=$rows['cof']+1;

            $numero_doc="";
            $num_fact_impresa=$numero_factura_impresa;

            $table_numdoc="correlativo";
            $data_numdoc="";

            if ($tipo_impresion =='COF') {
              $tipo_entrada_salida='FACTURA CONSUMIDOR';
              $data_numdoc = array(
                'cof' => $ult_cof
              );
              $numero_doc=numero_tiquete($ult_cof, $tipo_impresion);
            }
            if ($tipo_impresion =='TIK') {
              $sql_corre = _query("SELECT * FROM caja WHERE id_caja = '$caja'");
              $row_corre = _fetch_array($sql_corre);
              $correlativo_dispo = $row_corre["correlativo_dispo"];
              $tipo_entrada_salida='TICKET';
              $data_numdoc = array(
                'correlativo_dispo' => $correlativo_dispo+1,
              );
              $num_fact_impresa=$correlativo_dispo;
              $numero_doc=numero_tiquete($correlativo_dispo, $tipo_impresion);
            }
            if ($tipo_impresion =='CCF') {
              $tipo_entrada_salida='CREDITO FISCAL';
              $data_numdoc = array(
                'ccf' => $ult_ccf
              );
              $numero_doc=numero_tiquete($ult_ccf, $tipo_impresion);
            }

            if ($tipo_impresion != "TIK") {
              $where_clause_n=" WHERE id_sucursal='$id_sucursal'";
              $insertar_numdoc = _update($table_numdoc, $data_numdoc, $where_clause_n);
            } else {
              $tab = 'caja';
              $where_clause_c=" WHERE id_caja='$caja'";
              $insertar_numdoc = _update($tab, $data_numdoc, $where_clause_c);
            }

            $abono=0;
            $saldo=0;

            $serie="";
            $ultimo=0;

            if ($tipo_impresion == "TIK") {
              # code...
              $sql_corre = _query("SELECT * FROM caja WHERE id_caja = '$caja'");
              $row_corre = _fetch_array($sql_corre);
              $serie = $row_corre["serie"];
            } elseif ($tipo_impresion == "COF") {
              # code...
              $swl =_fetch_array(_query("SELECT * FROM sucursal where id_sucursal=$id_sucursal "));
              $serie=$swl['serie_cof'];
              $sql_ult=_query("SELECT MAX(CONVERT(num_fact_impresa,UNSIGNED INTEGER)) as ultimo FROM factura WHERE id_sucursal=$id_sucursal AND tipo_documento='COF' ");

              $num_rows_ul=_num_rows($sql_ult);
              if ($num_rows_ul>0) {
                # code...
                $ul=_fetch_array($sql_ult);
                $ultimo=$ul['ultimo'];
              }
            } else {
              # code...
              $swl =_fetch_array(_query("SELECT * FROM sucursal where id_sucursal=$id_sucursal "));
              $serie=$swl['serie_ccf'];

              $sql_ult=_query("SELECT MAX(CONVERT(num_fact_impresa,UNSIGNED INTEGER)) as ultimo FROM factura WHERE id_sucursal=$id_sucursal AND tipo_documento='CCF' ");

              $num_rows_ul=_num_rows($sql_ult);
              if ($num_rows_ul>0) {
                # code...
                $ul=_fetch_array($sql_ult);
                $ultimo=$ul['ultimo'];
              }
            }

            if ($credito==1) {
              $saldo=$total_menos_retencion;
            }
            $id_fact="";
            if (true) {
              # code...
              $table_fact= 'factura';
              $form_data_fact = array(
                'id_cliente' => $id_cliente,
                'fecha' => $fecha_movimiento,
                'numero_doc' => $numero_doc,
                'subtotal' => $subtotal,
                'sumas'=>$sumas,
                'suma_gravado'=>$suma_gravada,
                'iva' =>$iva,
                'retencion'=>$retencion,
                'venta_exenta'=>$venta_exenta,
                'total_menos_retencion'=>$total_menos_retencion,
                'total' => $total,
                'id_usuario'=>$id_empleado,
                'id_empleado' => $id_vendedor,
                'id_sucursal' => $id_sucursal,
                'tipo' => "CREDITO FISCAL",
                'serie' => $serie,
                'num_fact_impresa' => $num_fact_impresa,
                'hora' => $hora,
                'finalizada' => '1',
                'abono'=>$abono,
                'saldo' => $saldo,
                'tipo_documento' => $tipo_documento,
                'id_apertura' => $id_apertura,
                'id_apertura_pagada' => $id_apertura,
                'caja' => $caja,
                'credito' => $credito,
                'turno' => $turno,
                'serie_e ' => 'posin',
              );
              $insertar_fact = _insert($table_fact, $form_data_fact);
              $id_fact= _insert_id();

              if (!$insertar_fact) {
                # code...
                $b=0;
              }
            } else {
              # code...
              $table_fact= 'factura';
              $form_data_fact = array(
                'id_cliente' => $id_cliente,
                'fecha' => $fecha_movimiento,
                'numero_doc' => $numero_doc,
                'subtotal' => $subtotal,
                'sumas'=>$sumas,
                'suma_gravado'=>$suma_gravada,
                'iva' =>$iva,
                'retencion'=>$retencion,
                'venta_exenta'=>$venta_exenta,
                'total_menos_retencion'=>$total_menos_retencion,
                'total' => $total,/*
                'id_usuario'=>$id_empleado,
                'id_empleado' => $id_vendedor,*/
                'id_sucursal' => $id_sucursal,
                'tipo' => "CREDITO FISCAL",
                'serie' => $serie,
                'num_fact_impresa' => $num_fact_impresa,
                'hora' => $hora,
                'finalizada' => '1',
                'abono'=>$abono,
                'saldo' => $saldo,
                'tipo_documento' => $tipo_documento,
                'id_apertura' => $id_apertura,
                'id_apertura_pagada' => $id_apertura,
                'caja' => $caja,
                'credito' => $credito,
                'turno' => $turno,
              );
              $whereclause="id_factura='".$id_factura."'";
              $insertar_fact = _update($table_fact, $form_data_fact, $whereclause);
              $id_fact= $id_factura;

              if (!$insertar_fact) {
                # code...
                $b=0;
              }
              $table="factura_detalle";
              $where_clause="id_factura='".$id_fact."'";
              $delete=_delete($table, $where_clause);
              if (!$delete) {
                # code...
                $b=0;
              }
            }

            $cre=1;
            if ($credito==1) {
              $table="credito";
              $form_data = array(
                'id_cliente' => $id_cliente,
                'fecha' => $fecha_movimiento,
                'tipo_doc' => $tipo_documento,
                'numero_doc' => $numero_doc,
                'id_factura' => $id_fact,
                'dias' =>  '30',
                'total' => $total_menos_retencion,
                'abono' => 0,
                'saldo' => $total_menos_retencion,
                'finalizada' => 0,
                'id_sucursal' => $id_sucursal,
              );
              $insert=_insert($table, $form_data);
              if ($insert) {
                # code...
              } else {
                # code...
                $cre=0;
              }
            }

            if ($cuantos>0) {
              $array = json_decode($array_json, true);
              foreach ($array as $fila) {
                $id_producto=$fila['id'];
                $unidades=$fila['unidades'];
                $subtotal=$fila['subtotal'];
                $cantidad=$fila['cantidad'];
                $id_presentacion=$fila['id_presentacion'];
                $cantidad_real=$cantidad*$unidades;
                $exento=$fila['exento'];
                $precio_venta=$fila['precio'];

                $sql_costo=_fetch_array(_query("SELECT costo FROM presentacion_producto WHERE id_presentacion = $id_presentacion"));
                $precio_compra=$sql_costo['costo'];
                $table_fact_det= 'factura_detalle';
                $data_fact_det = array(
                  'id_factura' => $id_fact,
                  'id_prod_serv' => $id_producto,
                  'cantidad' => $cantidad_real,
                  'precio_venta' => $precio_venta,
                  'subtotal' => $subtotal,
                  'tipo_prod_serv' => $tipoprodserv,
                  'id_empleado' => $id_empleado,
                  'id_sucursal' => $id_sucursal,
                  'fecha' => $fecha_movimiento,
                  'id_presentacion'=> $id_presentacion,
                  'exento' => $exento,
                );
                $insertar_fact_det = _insert($table_fact_det, $data_fact_det);
                if (!$insertar_fact_det) {
                  # code...
                  $c=0;
                }


              } //foreach ($array as $fila){



              if ($a&&$b&&$c&&$z&&$k&&$j&&$l&&$cre) {
                _commit(); // transaction is committed
                $xdatos['typeinfo']='Success';
                $xdatos['msg']='Registro  Numero: <strong>'.$numero_doc.'</strong>  Guardado con Exito !';
                $xdatos['numdoc']=$numero_doc;
                $xdatos['id_factura']=$id_fact;
                $xdatos['ultimo']=$ultimo+1;
              } else {
                _rollback(); // transaction rolls back
                $xdatos['typeinfo']='Error';
                $xdatos['msg']='Registro no pudo ser ingresado!'.$a."-".$b."-".$c."-".$z."-".$k."-".$j."-".$l;
              }
            }
            echo json_encode($xdatos);
          }


          //else {
          if (isset($_REQUEST['process'])) {
            switch ($_REQUEST['process']) {
              case 'formEdit':
                initial();
                break;
                case 'total_texto':
                total_texto();
                break;
                case 'insert_preventa':
                insertar_preventa();
                break;
              }

              //}
            }
            ?>
