<?php
include_once "_core.php";
include('num2letras.php');

include('facturacion_funcion_imprimir.php');
//errores
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

function initial()
{
  $title="Editar Pedido";
  $_PAGE = array();
  $_PAGE ['title'] = $title;
  $_PAGE ['links'] = null;
  $_PAGE ['links'] .= '<link href="css/typeahead.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/select2/select2-bootstrap.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/bootstrap-checkbox/bootstrap-checkbox.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link rel="stylesheet" type="text/css" href="css/plugins/perfect-scrollbar/perfect-scrollbar.css">';
  $_PAGE ['links'] .= '<link rel="stylesheet" type="text/css" href="css/util_co.css">';
  $_PAGE ['links'] .= '<link rel="stylesheet" type="text/css" href="css/main_co.css">';

  include_once "header.php";
  //include_once "main_menu.php";

  $id_usuario=$_SESSION["id_usuario"];
  $fecha_actual=date("Y-m-d");
  //permiso del script
  $id_user=$_SESSION["id_usuario"];
  $admin=$_SESSION["admin"];
  $uri = $_SERVER['SCRIPT_NAME'];
  $filename=get_name_script($uri);
  $links=permission_usr($id_user, $filename);
  $id_sucursal=$_SESSION['id_sucursal'];

  //impuestos
  $id_cotizacion = $_REQUEST["id_pedido"];
  $sql_cot = _query("SELECT * FROM pedido WHERE id_pedido='$id_cotizacion' and finalizada=0");
  $dat_cot = _fetch_array($sql_cot);
  $id_cliente = $dat_cot["id_cliente"];
  $fecha = $dat_cot["fecha"];
  $vigencia = $dat_cot["fecha_entrega"];

  $sql_iva="SELECT iva,monto_retencion1,monto_retencion10,monto_percepcion FROM sucursal WHERE id_sucursal='$id_sucursal'";
  $result_IVA=_query($sql_iva);
  $row_IVA=_fetch_array($result_IVA);
  $iva=$row_IVA['iva']/100;
  $monto_retencion1=$row_IVA['monto_retencion1'];
  $monto_retencion10=$row_IVA['monto_retencion10'];
  $monto_percepcion=$row_IVA['monto_percepcion'];
  //caja
  //SELECT * FROM apertura_caja WHERE vigente = 1 AND id_sucursal = '$id_sucursal' AND id_empleado = '$id_user'

  //array de tipo_pagos

  $select_depa = "<select class='form-control  select_depa' id='select_depa'>";
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
      if($dat_cot['id_departamento']==$id_departamento)
      {
        $select_depa.= " selected ";
      }
      $select_depa.=">".$descripcion."</option>";
    }
  }
  $select_depa.='</select>';

  $fecha_pedido = date("d-m-Y");
  $total = "";
  $lugar_entrega = "";
  $cliente = "";
  $empleado = "";
  $sucursal = "";
  $departamento = "";
  $municipio = "";
  $id_cliente = "";

  $id_cliente_bd=$dat_cot['id_cliente'];

  $hidden = "text";
  $hidden1 = "hidden";

  $select_muni = "<select class='form-control  select_muni' id='select_muni'>";
  if($dat_cot['id_municipio']!=0)
  {
    $sqlmu = _query("SELECT * FROM municipio WHERE id_departamento_municipio=$dat_cot[id_departamento]");

    while ($romu = _fetch_array($sqlmu))
    {
      $select_muni.= "<option value='".$romu['id_municipio']."'";
      if($dat_cot['id_municipio']==$romu['id_municipio'])
      {
        $select_muni.= " selected ";
      }
      $select_muni.=">".$romu['nombre_municipio']."</option>";
    }
  }
  else
  {
    $select_muni.= "<option value=''>Primero seleccione un departamento</option>";
  }
  $select_muni.= '</select>';

  ?>
  <div class="gray-bg">
    <div class="wrapper wrapper-content  animated fadeInRight">
      <a style="display:none;" target="_blank" id="redir" href="#"></a>
      <div class="row">
        <div class="col-lg-12">
          <div class="ibox">
            <?php  if ($links!='NOT' || $admin=='1') { ?>
              <input type='hidden' name='urlprocess' id='urlprocess' value="<?php echo $filename; ?>">
              <input type="hidden" name="process" id="process" value="edit">
              <div class="ibox-content">
                <section>
                  <div class="panel">
                    <input type='hidden' name='caja' id='caja' value='<?php echo $caja; ?>'>
                    <input type='hidden' name='porc_iva' id='porc_iva' value='<?php echo $iva; ?>'>
                    <input type='hidden' name='monto_retencion1' id='monto_retencion1' value='100'>
                    <input type='hidden' name='monto_retencion10' id='monto_retencion10' value='100'>
                    <input type='hidden' name='monto_percepcion' id='monto_percepcion' value='100'>
                    <input type='hidden' name='porc_retencion1' id='porc_retencion1' value=0>
                    <input type='hidden' name='porc_retencion10' id='porc_retencion10' value=0>
                    <input type='hidden' name='porc_percepcion' id='porc_percepcion' value=0>
                    <input type='hidden' name='porcentaje_descuento' id='porcentaje_descuento' value=0>

                    <div class="widget-content">
                      <div class="row">
                        <div class="col-lg-3">
                          <div class='form-group has-info single-line'>
                            <label>Cliente&nbsp;</label>

                            <select class="form-control sel usage" name="id_cliente" id="id_cliente">
                              <?php

                              $sqlcli=_query("SELECT * FROM cliente ORDER BY nombre");
                              while ($row_cli = _fetch_array($sqlcli))
                              {
                                echo "<option value='".$row_cli["id_cliente"]."'";
                                if($id_cliente_bd != "")
                                {
                                  if ($row_cli["id_cliente"] == $id_cliente_bd)
                                  {
                                    echo " selected ";
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
                                echo "<option ";
                                if ($id_usuario == $dat_cot['id_empleado'])
                                {
                                  echo " selected ";
                                }
                                echo "value=' $id_usuario ' >".MAYU($nombre)."</option>";
                              }
                              ?>
                            </select>
                          </div>
                        </div>
                        <div class='col-lg-2'>
                          <div class='form-group has-info single-line'>
                            <label>Fecha</label>
                            <input type='text' readonly class='form-control text-center' value='<?php echo ED($dat_cot['fecha']); ?>' id='fecha1' name='fecha1'></div>
                          </div>
                          <div class='col-lg-2'>
                            <div class='form-group has-info single-line'>
                              <label>Fecha entrega</label>
                              <input type='text' readonly class='form-control text-center' value='<?php echo ED($dat_cot['fecha_entrega']); ?>' id='fecha_entrega' name='fecha_entrega'></div>
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
                                    echo "<option ";
                                    if($row['id_ubicacion'] == $dat_cot['origen'])
                                    {
                                      echo "  selected  ";
                                    }

                                    echo "value='".$row["id_ubicacion"]."'>".MAYU(utf8_decode($row["descripcion"]))."</option>";
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
                                <input type="text" value="<?= $dat_cot['transporte'] ?>" id="transporte" name="transporte" size="20" class="direccion form-control" placeholder="Observacion">
                              </div>
                            </div>
                            <div class="col-lg-3">
                              <div class='form-group has-info single-line'>
                                <label>Dirección</label>
                                <input type="text" value="<?= $dat_cot['lugar_entrega'] ?>" id="direccion" name="direccion" size="20" class="direccion form-control" placeholder="Dirección">
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
      													<textarea style='width:100%' id="comentario" name="comentario" rows="2" cols="80"> <?=$dat_cot['observaciones'] ?></textarea>
      												</div>
      											</div>
      										</div>
                          <div class="row">
                            <div class="col-md-6">
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
                            <div class="col-md-2">
                              <label>Boton para editar</label>
                              <button type="submit" style='width:100%'  id="editari" name="editari" class="btn btn-warning pull-right"><i class="fa fa-save"></i> EDITAR PEDIDO</button>
                            </div>
                            <div class="col-md-1">
                              <label>Salir</label>
                              <a class="btn btn-danger pull-right" style='width:100%'  href="admin_pedido_pendiente.php" id='salir'><i class="fa fa-mail-reply"></i> Salir</a>
                            </div>
                            <div class="col-md-3">
                              <label>Este boton finaliza el pedido</label>
                              <button type="submit" style='width:100%' id="submit1" name="submit1" class="btn btn-primary pull-right"><i class="fa fa-check"></i> Finalizar PEDIDO</button>
                            </div>

                          </div><br>
                        </div>
                        <!-- fin buscador Superior -->
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
                                <div class="">
                                  <table>
                                    <tbody id="mostrardatos">
                                      <?php
                                      $sql = _query("SELECT p.id_categoria, p.id_producto, p.descripcion, cd.precio_venta, cd.cantidad, cd.id_presentacion, cd.subtotal,cd.unidad FROM pedido_detalle as cd JOIN producto as p ON cd.id_prod_serv=p.id_producto WHERE id_pedido='$id_cotizacion'");
                                      $filas = 0;
                                      while ($row = _fetch_array($sql))
                                      {
                                        $id_producto = $row["id_producto"];
                                        $id_categoria = $row["id_categoria"];
                                        $id_presentacion = $row["id_presentacion"];
                                        $descripcion = $row["descripcion"];
                                        $precio_venta = $row["precio_venta"];
                                        $cantidad = round($row["cantidad"],0);
                                        $subtotal = $row["subtotal"];
                                        $up = $row["unidad"];
                                        $sql_aux_a = _query("SELECT presentacion.nombre, presentacion_producto.descripcion,presentacion_producto.id_pp as id_presentacion,presentacion_producto.unidad, presentacion_producto.precio
                                          FROM presentacion_producto
                                          JOIN presentacion ON presentacion.id_presentacion=presentacion_producto.id_presentacion
                                          WHERE presentacion_producto.id_producto='$id_producto'
                                          AND presentacion_producto.activo=1
                                          ORDER BY presentacion_producto.unidad ASC");
                                          $sql_exis = _query("SELECT sum(stock_ubicacion.cantidad) as stock FROM stock_ubicacion WHERE id_producto = '$id_producto' AND id_ubicacion= $dat_cot[origen]");
                                          $datos_exis = _fetch_array($sql_exis);
                                          $stock = round($datos_exis["stock"],0);
                                          if(!($stock > 0))
                                          {
                                            $stock = 0;
                                          }

                                          $sql_res_esto=_fetch_array(_query("SELECT SUM(pedido_detalle.cantidad) as reservado FROM pedido JOIN pedido_detalle ON pedido_detalle.id_pedido=pedido.id_pedido WHERE pedido_detalle.id_prod_serv=$id_producto AND pedido.id_pedido=$id_cotizacion"));
                                          $reservado=$sql_res_esto['reservado'];

                                          $stock = $stock + $reservado;
                                          $select = "<select class='form-control sel'>";
                                          $unidad = 0;
                                          $descripcionp = "";
                                          while($row_a = _fetch_array($sql_aux_a))
                                          {
                                            $select .= "<option value='".$row_a["id_presentacion"]."'";
                                            if($id_presentacion == $row_a["id_presentacion"])
                                            {
                                              $select.= " selected ";
                                              $descripcionp = $row_a["descripcion"];
                                              $precio=$row_a['precio'];
                                              $unidad=$row_a['unidad'];
                                            }
                                            $select .= ">".$row_a["nombre"]."</option>";

                                          }
                                          $select .= "</select>";

                                          $sql_e=_fetch_array(_query("SELECT exento FROM producto WHERE id_producto=$id_producto"));
                                          $exento=$sql_e['exento'];

                                          $select_rank="<select class='sel_r precio_r form-control'>";

                                          $preciosArray = _getPrecios($id_presentacion, 0);
                                          $xc=0;
                                          foreach ($preciosArray as $key => $value) {
                                            // code...
                                            if($value>0)
                                            {
                                              $select_rank.="<option value='$value'";
                                              if ($precio_venta==$value) {
                                                $select_rank.=" selected ";
                                                $preciop=$value;
                                                $xc = 1;
                                              }
                                              $select_rank.=">$value</option>";
                                            }
                                          }
                                          if ($xc==0){
                                            // code...
                                            $select_rank.="<option value='$precio_venta'";
                                            $select_rank.=" selected ";
                                            $preciop=$precio_venta;
                                            $xc = 1;
                                            $select_rank.=">$precio_venta</option>";
                                          }
                                          $select_rank.="</select>";

                                          $exent2 = "<input type='hidden' id='exento' name='exento' value='".$exento."'>";

                                          $cantidades = "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='txt_box decimal2 ".$id_categoria." cant' id='cant' name='cant' value='".round($cantidad/$up,0)."' style='width:60px;'></div></td>";
                                          $tr_add = '';
                                          $tr_add .= "<tr  class='row100 head' id='".$filas."'>";
                                          $tr_add .= "<td hidden class='cell100 column10 text-success id_pps'><input type='hidden' id='unidades' name='unidades' value='".$unidad."'>".$id_producto."</td>";
                                          $tr_add .= "<td class='cell100 column30 text-success'>".$descripcion." ".$exent2."</td>";
                                          $tr_add .= "<td class='cell100 column10 text-success' id='cant_stock'>".$stock."</td>";
                                          $tr_add .= $cantidades;
                                          $tr_add .= "<td class='cell100 column10 text-success preccs'>".$select."</td>";
                                          $tr_add .= "<td class='cell100 column10 text-success rank_s'>".$select_rank."</td>";
                                          $tr_add .= "<td class='cell100 column10 text-success'><input type'text' id='precio_venta' class='form-control pp' value='".$precio_venta."'></td>";
                                          $tr_add .= "<td class='cell100 column10'><input type='hidden'  id='subtotal_fin' name='subtotal_fin' value='0.00'><input type='text'  class='decimal txt_box form-control subt' id='subtotal_mostrar' name='subtotal_mostrar'  value='".number_format($subtotal,4,".","")."' readOnly></td>";
                                          $tr_add .= '<td class="cell100 column10 Delete text-center"><input id="delprod" type="button" class="btn btn-danger fa"  value="&#xf1f8;"></td>';
                                          $tr_add .= '</tr>';
                                          $filas ++;
                                          echo $tr_add;
                                        }
                                        ?>
                                      </tbody>
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
                        </div>
                      </section>
                      <input type="hidden" id="id_pedido" name="id_pedido" value="<?php echo $_REQUEST['id_pedido']; ?>" />

                      <input type='hidden' name='totalfactura' id='totalfactura' value='0'>
                      <input type='hidden' name='filas' id='filas' value='<?php echo $filas; ?>'>
                    </div>
                  </div>
                  <!--<div class='ibox float-e-margins' -->
                </div>
                <!--div class='col-lg-12'-->
              </div>
              <!--div class='row'-->

              <!--div class='wrapper wrapper-content  animated fadeInRight'-->
              <?php
              include_once ("footer.php");
              echo "<script src='js/plugins/arrowtable/arrow-table.js'></script>";
              echo "<script src='js/plugins/bootstrap-checkbox/bootstrap-checkbox.js'></script>";
              echo '<script src="js/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
              <script src="js/funciones/main.js"></script>';
              echo "<script src='js/funciones/util.js'></script>";
              echo "<script src='js/funciones/pedido.js?id=".rand(0,9999)."'></script>";
            } //permiso del script
            else
            {
              echo "<br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div></div></div></div></div>";
              include_once ("footer.php");
            }
          }

          function pedido()
          {
            $comentario = "";
            if(isset($_REQUEST['comentario']))
            {
              $comentario=$_REQUEST['comentario'];
            }
            $id_pedido = $_REQUEST['id_pedido'];
            $datped = _fetch_array(_query("SELECT * FROM pedido where id_pedido=$id_pedido"));
            finalizar($id_pedido);
            // facturacion
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
            $numero_doc=$datped['numero_doc'];

            $table='pedido';
            $form_data = array(
              'id_cliente' => $id_cliente,
              //'fecha' => $fecha,
              'numero_doc' => $numero_doc,
              'total' => $total_compras,
              'id_usuario' => $id_usuario,
              'id_empleado' => $id_vendedor,
              'fecha_factura' => date("Y-m-d"),
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
            $insertarM = _update($table,$form_data,"id_pedido = $id_pedido");
            _delete("pedido_detalle","id_pedido = $id_pedido");
            $id_factura=$id_pedido;

            $concepto="PEDIDO PRODUCTO";
            $table='movimiento_producto';
            $form_data = array(
              'id_sucursal' => $id_sucursal,
              'correlativo' => $numero_doc,
              'concepto' => "FINALIZACION DE PEDIDO",
              'total' => $total_compras,
              'tipo' => 'SALIDA',
              'proceso' => 'PEF',
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
                }
                else{
                  _rollback();
                  $xdatos['typeinfo']='Error';
                  $xdatos['msg']='Registro de Inventario no pudo ser Actualizado !';
                }
                echo json_encode($xdatos);
              }

              function editar()
              {
                $comentario = "";
                if(isset($_REQUEST['comentario']))
                {
                  $comentario=$_REQUEST['comentario'];
                }
                $id_pedido = $_REQUEST['id_pedido'];
                $datped = _fetch_array(_query("SELECT * FROM pedido where id_pedido=$id_pedido"));
                edit($id_pedido);
                // facturacion
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
                $numero_doc=$datped['numero_doc'];

                $table='pedido';
                $form_data = array(
                  'id_cliente' => $id_cliente,
                  //'fecha' => $fecha,
                  'numero_doc' => $numero_doc,
                  'total' => $total_compras,
                  'id_usuario' => $id_usuario,
                  'id_empleado' => $id_vendedor,
                  'fecha_factura' => "0000-00-00",
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
                $insertarM = _update($table,$form_data,"id_pedido = $id_pedido");
                _delete("pedido_detalle","id_pedido = $id_pedido");
                $id_factura=$id_pedido;

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
                    }
                    else{
                      _rollback();
                      $xdatos['typeinfo']='Error';
                      $xdatos['msg']='Registro de Inventario no pudo ser Actualizado !';
                    }
                    echo json_encode($xdatos);
                  }

                  function edit($id_pedido) {
                    $id_sucursal = $_SESSION["id_sucursal"];

                    _begin();

                    $id_factura= $id_pedido;
                    $sel=_fetch_array(_query("SELECT movimiento_producto.id_movimiento,pedido.total FROM pedido JOIN movimiento_producto ON movimiento_producto.id_traslado=pedido.id_pedido WHERE movimiento_producto.id_traslado=$id_factura AND movimiento_producto.proceso='ped'"));
                    $id_sucursal=$_SESSION['id_sucursal'];
                    $id_movimiento = $sel["id_movimiento"];


                    $table = 'movimiento_producto';
                    $form_data = array (
                        'id_traslado' => 0,
                      );
                    $where_clause = "id_movimiento='".$id_movimiento."'";
                    $update = _update ( $table, $form_data, $where_clause );


                    $id_mov=$id_movimiento;
                    $total=$sel['total'];
                    $up=0;
                    $up2=0;
                    $i=0;
                    $an=0;
                    $table="movimiento_stock_ubicacion";
                    $form_data = array(
                      'anulada' => 1,
                    );
                    $where_clause="id_mov_prod='".$id_movimiento."'";
                    $update=_update($table,$form_data,$where_clause);

                    if ($update) {
                      # code...
                    }
                    else {
                      # code...
                      $up=1;
                    }


                    $table="movimiento_producto_pendiente";
                    $where_clause="id_movimiento='".$id_movimiento."'";
                    $delete=_delete($table,$where_clause);

                    $sql_mp=_query("SELECT * FROM movimiento_producto_detalle WHERE id_movimiento=$id_movimiento ");
                    $num_r_m=_num_rows($sql_mp);
                    if ($num_r_m!=0) {
                      # code...
                      $sql_des=_fetch_array(_query("SELECT id_ubicacion FROM ubicacion WHERE id_sucursal=$id_sucursal AND bodega=0"));

                      $destino = $sql_des;
                      $fecha = date("Y-m-d");
                      $total_compras = $total;
                      $concepto="CARGA PARA EDITAR PEDIDO";
                      $hora=date("H:i:s");
                      $fecha_movimiento = date("Y-m-d");
                      $id_empleado=$_SESSION["id_usuario"];

                      $sql_num = _query("SELECT ii FROM correlativo WHERE id_sucursal='$id_sucursal'");
                      $datos_num = _fetch_array($sql_num);
                      $ult = $datos_num["ii"]+1;
                      $numero_doc=str_pad($ult,7,"0",STR_PAD_LEFT).'_II';
                      $tipo_entrada_salida='AJUSTE DE PEDIDO';

                      $z=1;

                      /*actualizar los correlativos de II*/
                      $corr=1;
                      $table="correlativo";
                      $form_data = array(
                        'ii' =>$ult
                      );
                      $where_clause_c="id_sucursal='".$id_sucursal."'";
                      $up_corr=_update($table,$form_data,$where_clause_c);
                      if ($up_corr) {
                        # code...
                      }
                      else {
                        $corr=0;
                      }
                      if ($concepto=='')
                      {
                        $concepto='ENTRADA DE INVENTARIO';
                      }
                      $table='movimiento_producto';
                      $form_data = array(
                        'id_sucursal' => $id_sucursal,
                        'correlativo' => $numero_doc,
                        'concepto' => $concepto,
                        'total' => $total_compras,
                        'tipo' => 'ENTRADA',
                        'proceso' => 'II',
                        'referencia' => $numero_doc,
                        'id_empleado' => $id_empleado,
                        'fecha' => $fecha,
                        'hora' => $hora,
                        'id_suc_origen' => $id_sucursal,
                        'id_suc_destino' => $id_sucursal,
                        'id_proveedor' => 0,
                      );
                      $insert_mov =_insert($table,$form_data);
                      $id_movimiento=_insert_id();

                      $j = 1 ;
                      $k = 1 ;
                      $l = 1 ;
                      $m = 1 ;

                      while($row_mov=_fetch_array($sql_mp))
                      {
                        $id_producto=$row_mov['id_producto'];
                        $precio_compra=$row_mov['costo'];
                        $precio_venta=$row_mov['precio'];
                        $cantidad=$row_mov['cantidad'];
                        $fecha_caduca="";
                        $id_presentacion=$row_mov['id_presentacion'];


                        $sql2="SELECT stock FROM stock WHERE id_producto='$id_producto' AND id_sucursal='$id_sucursal'";
                        $stock2=_query($sql2);
                        $row2=_fetch_array($stock2);
                        $nrow2=_num_rows($stock2);
                        if ($nrow2>0)
                        {
                          $existencias=$row2['stock'];
                        }
                        else
                        {
                          $existencias=0;
                        }

                        $sql_lot = _query("SELECT MAX(numero) AS ultimo FROM lote WHERE id_producto='$id_producto'");
                        $datos_lot = _fetch_array($sql_lot);
                        $lote = $datos_lot["ultimo"]+1;
                        $table1= 'movimiento_producto_detalle';
                        $cant_total=$cantidad+$existencias;
                        $form_data1 = array(
                          'id_movimiento'=>$id_movimiento,
                          'id_producto' => $id_producto,
                          'cantidad' => $cantidad,
                          'costo' => $precio_compra,
                          'precio' => $precio_venta,
                          'stock_anterior'=>$existencias,
                          'stock_actual'=>$cant_total,
                          'lote' => $lote,
                          'id_presentacion' => $id_presentacion,
                          'fecha' => $fecha,
                          'hora' => $hora,

                        );
                        $insert_mov_det = _insert($table1,$form_data1);
                        if(!$insert_mov_det)
                        {
                          $j = 0;
                        }

                      }

                      if($insert_mov &&$corr &&$z && $j && $k && $l && $m)
                      {

                      }
                      else
                      {
                        $up=1;
                        $up2=1;
                        $an=1;
                      }
                    }
                    $id_movimiento=$id_mov;
                    $sql_su=_query("SELECT movimiento_stock_ubicacion.id_producto,id_origen,id_destino,movimiento_stock_ubicacion.cantidad,movimiento_stock_ubicacion.id_presentacion FROM movimiento_stock_ubicacion WHERE id_mov_prod=$id_movimiento");
                    while ($row=_fetch_array($sql_su)) {
                      # code...
                      $id_producto=$row['id_producto'];
                      $id_origen=$row['id_origen'];
                      $id_destino=$row['id_destino'];
                      $cantidad=$row['cantidad'];
                      $id_presentacion=$row['id_presentacion'];

                      $sql_s=_query("SELECT cantidad AS stock_origen FROM stock_ubicacion WHERE id_producto=$id_producto  AND id_su=$id_origen");
                      $rw=_fetch_array($sql_s);
                      $stock_origen=$rw['stock_origen'];
                      $stock_origen=$stock_origen+$cantidad;

                        # code...
                        $table="stock_ubicacion";
                        $form_data = array(
                          'cantidad' => $stock_origen,
                        );
                        $where_clause="id_su='".$id_origen."'";
                        $update=_update($table,$form_data,$where_clause);

                        if ($update) {
                          # code...
                        }
                        else {
                          # code...
                          $up2=1;
                        }
                        $sql_stock=_fetch_array(_query("SELECT id_stock,stock FROM stock WHERE id_producto='".$id_producto."' AND id_sucursal=$_SESSION[id_sucursal]"));
                        $sql_stock_anterior=$sql_stock['stock'];
                        $stock_nuevo=$sql_stock_anterior+$cantidad;
                        $id_stock=$sql_stock['id_stock'];


                        $table="stock";
                        $form_data = array(
                          'stock' => $stock_nuevo,
                        );
                        $where_clause="id_stock='".$id_stock."'";

                        $update=_update($table,$form_data,$where_clause);
                        if ($update) {
                          # code...
                        }
                        else {
                          # code...
                          $up=1;
                        }
                      $sql_lot = _query("SELECT MAX(numero) AS ultimo FROM lote WHERE id_producto='$id_producto'");
                      $datos_lot = _fetch_array($sql_lot);
                      $lote = $datos_lot["ultimo"]+1;



                      $sql_lote = _query("SELECT MAX(lote.vencimiento) as vence FROM lote WHERE lote.id_producto='$id_producto'");
                      $datos_lote = _fetch_array($sql_lote);
                      $fecha_caduca = $datos_lote["vence"];

                      $sql_costo = _query("SELECT costo FROM presentacion_producto WHERE id_presentacion=$id_presentacion");
                      $datos_costo = _fetch_array($sql_costo);
                      $precio = $datos_costo["costo"];

                      $estado='VIGENTE';
                      $table_perece='lote';
                      $form_data_perece = array(
                        'id_producto' => $id_producto,
                        'referencia' => $id_movimiento,
                        'numero' => $lote,
                        'fecha_entrada' => date("Y-m-d"),
                        'vencimiento'=>$fecha_caduca,
                        'precio' => $precio,
                        'cantidad' => $cantidad,
                        'estado'=>$estado,
                        'id_sucursal' => $_SESSION['id_sucursal'],
                        'id_presentacion' => $id_presentacion,
                      );
                      $insert_lote = _insert($table_perece,$form_data_perece );

                    }
                    if($i==0)
                    {
                      if ($up==0&&$up2==0&&$an==0)
                      {
                        _commit();
                      }
                      else
                      {
                        _rollback();
                      }
                   }
                   else {
                     _rollback();
                   }
                  }

              function finalizar($id_pedido) {
              	$id_sucursal = $_SESSION["id_sucursal"];

              	_begin();
              	$table = 'pedido';
              	$form_data = array (
              			'finalizada' => 1,
              		);
              	$where_clause = "id_pedido='".$id_pedido."' AND id_sucursal='$id_sucursal'";
              	$update = _update ( $table, $form_data, $where_clause );

              	$id_factura= $id_pedido;
              	$sel=_fetch_array(_query("SELECT movimiento_producto.id_movimiento,pedido.total FROM pedido JOIN movimiento_producto ON movimiento_producto.id_traslado=pedido.id_pedido WHERE movimiento_producto.id_traslado=$id_factura AND movimiento_producto.proceso='ped'"));
              	$id_sucursal=$_SESSION['id_sucursal'];
              	$id_movimiento = $sel["id_movimiento"];
              	$id_mov=$id_movimiento;
              	$total=$sel['total'];
              	$up=0;
              	$up2=0;
              	$i=0;
              	$an=0;
              	$table="movimiento_stock_ubicacion";
              	$form_data = array(
              		'anulada' => 1,
              	);
              	$where_clause="id_mov_prod='".$id_movimiento."'";
              	$update=_update($table,$form_data,$where_clause);

              	if ($update) {
              		# code...
              	}
              	else {
              		# code...
              		$up=1;
              	}


              	$table="movimiento_producto_pendiente";
              	$where_clause="id_movimiento='".$id_movimiento."'";
              	$delete=_delete($table,$where_clause);

              	$sql_mp=_query("SELECT * FROM movimiento_producto_detalle WHERE id_movimiento=$id_movimiento ");
              	$num_r_m=_num_rows($sql_mp);
              	if ($num_r_m!=0) {
              		# code...
              		$sql_des=_fetch_array(_query("SELECT id_ubicacion FROM ubicacion WHERE id_sucursal=$id_sucursal AND bodega=0"));

              		$destino = $sql_des;
              		$fecha = date("Y-m-d");
              		$total_compras = $total;
              		$concepto="CARGA PARA FINALIZAR PEDIDO";
              		$hora=date("H:i:s");
              		$fecha_movimiento = date("Y-m-d");
              		$id_empleado=$_SESSION["id_usuario"];

              		$sql_num = _query("SELECT ii FROM correlativo WHERE id_sucursal='$id_sucursal'");
              		$datos_num = _fetch_array($sql_num);
              		$ult = $datos_num["ii"]+1;
              		$numero_doc=str_pad($ult,7,"0",STR_PAD_LEFT).'_II';
              		$tipo_entrada_salida='AJUSTE DE PEDIDO';

              		$z=1;

              		/*actualizar los correlativos de II*/
              		$corr=1;
              		$table="correlativo";
              		$form_data = array(
              			'ii' =>$ult
              		);
              		$where_clause_c="id_sucursal='".$id_sucursal."'";
              		$up_corr=_update($table,$form_data,$where_clause_c);
              		if ($up_corr) {
              			# code...
              		}
              		else {
              			$corr=0;
              		}
              		if ($concepto=='')
              		{
              			$concepto='ENTRADA DE INVENTARIO';
              		}
              		$table='movimiento_producto';
              		$form_data = array(
              			'id_sucursal' => $id_sucursal,
              			'correlativo' => $numero_doc,
              			'concepto' => $concepto,
              			'total' => $total_compras,
              			'tipo' => 'ENTRADA',
              			'proceso' => 'II',
              			'referencia' => $numero_doc,
              			'id_empleado' => $id_empleado,
              			'fecha' => $fecha,
              			'hora' => $hora,
              			'id_suc_origen' => $id_sucursal,
              			'id_suc_destino' => $id_sucursal,
              			'id_proveedor' => 0,
              		);
              		$insert_mov =_insert($table,$form_data);
              		$id_movimiento=_insert_id();

              		$j = 1 ;
              		$k = 1 ;
              		$l = 1 ;
              		$m = 1 ;

              		while($row_mov=_fetch_array($sql_mp))
              		{
              			$id_producto=$row_mov['id_producto'];
              			$precio_compra=$row_mov['costo'];
              			$precio_venta=$row_mov['precio'];
              			$cantidad=$row_mov['cantidad'];
              			$fecha_caduca="";
              			$id_presentacion=$row_mov['id_presentacion'];


              			$sql2="SELECT stock FROM stock WHERE id_producto='$id_producto' AND id_sucursal='$id_sucursal'";
              			$stock2=_query($sql2);
              			$row2=_fetch_array($stock2);
              			$nrow2=_num_rows($stock2);
              			if ($nrow2>0)
              			{
              				$existencias=$row2['stock'];
              			}
              			else
              			{
              				$existencias=0;
              			}

              			$sql_lot = _query("SELECT MAX(numero) AS ultimo FROM lote WHERE id_producto='$id_producto'");
              			$datos_lot = _fetch_array($sql_lot);
              			$lote = $datos_lot["ultimo"]+1;
              			$table1= 'movimiento_producto_detalle';
              			$cant_total=$cantidad+$existencias;
              			$form_data1 = array(
              				'id_movimiento'=>$id_movimiento,
              				'id_producto' => $id_producto,
              				'cantidad' => $cantidad,
              				'costo' => $precio_compra,
              				'precio' => $precio_venta,
              				'stock_anterior'=>$existencias,
              				'stock_actual'=>$cant_total,
              				'lote' => $lote,
              				'id_presentacion' => $id_presentacion,
              				'fecha' => $fecha,
              				'hora' => $hora,

              			);
              			$insert_mov_det = _insert($table1,$form_data1);
              			if(!$insert_mov_det)
              			{
              				$j = 0;
              			}

              		}

              		if($insert_mov &&$corr &&$z && $j && $k && $l && $m)
              		{

              		}
              		else
              		{
              			$up=1;
              			$up2=1;
              			$an=1;
              		}
              	}
              	$id_movimiento=$id_mov;
              	$sql_su=_query("SELECT movimiento_stock_ubicacion.id_producto,id_origen,id_destino,movimiento_stock_ubicacion.cantidad,movimiento_stock_ubicacion.id_presentacion FROM movimiento_stock_ubicacion WHERE id_mov_prod=$id_movimiento");
              	while ($row=_fetch_array($sql_su)) {
              		# code...
              		$id_producto=$row['id_producto'];
              		$id_origen=$row['id_origen'];
              		$id_destino=$row['id_destino'];
              		$cantidad=$row['cantidad'];
              		$id_presentacion=$row['id_presentacion'];

              		$sql_s=_query("SELECT cantidad AS stock_origen FROM stock_ubicacion WHERE id_producto=$id_producto  AND id_su=$id_origen");
              		$rw=_fetch_array($sql_s);
              		$stock_origen=$rw['stock_origen'];
              		$stock_origen=$stock_origen+$cantidad;

              			# code...
              			$table="stock_ubicacion";
              			$form_data = array(
              				'cantidad' => $stock_origen,
              			);
              			$where_clause="id_su='".$id_origen."'";
              			$update=_update($table,$form_data,$where_clause);

              			if ($update) {
              				# code...
              			}
              			else {
              				# code...
              				$up2=1;
              			}
              			$sql_stock=_fetch_array(_query("SELECT id_stock,stock FROM stock WHERE id_producto='".$id_producto."' AND id_sucursal=$_SESSION[id_sucursal]"));
              			$sql_stock_anterior=$sql_stock['stock'];
              			$stock_nuevo=$sql_stock_anterior+$cantidad;
              			$id_stock=$sql_stock['id_stock'];


              			$table="stock";
              			$form_data = array(
              				'stock' => $stock_nuevo,
              			);
              			$where_clause="id_stock='".$id_stock."'";

              			$update=_update($table,$form_data,$where_clause);
              			if ($update) {
              				# code...
              			}
              			else {
              				# code...
              				$up=1;
              			}
              		$sql_lot = _query("SELECT MAX(numero) AS ultimo FROM lote WHERE id_producto='$id_producto'");
              		$datos_lot = _fetch_array($sql_lot);
              		$lote = $datos_lot["ultimo"]+1;



              		$sql_lote = _query("SELECT MAX(lote.vencimiento) as vence FROM lote WHERE lote.id_producto='$id_producto'");
              		$datos_lote = _fetch_array($sql_lote);
              		$fecha_caduca = $datos_lote["vence"];

              		$sql_costo = _query("SELECT costo FROM presentacion_producto WHERE id_presentacion=$id_presentacion");
              		$datos_costo = _fetch_array($sql_costo);
              		$precio = $datos_costo["costo"];

              		$estado='VIGENTE';
              		$table_perece='lote';
              		$form_data_perece = array(
              			'id_producto' => $id_producto,
              			'referencia' => $id_movimiento,
              			'numero' => $lote,
              			'fecha_entrada' => date("Y-m-d"),
              			'vencimiento'=>$fecha_caduca,
              			'precio' => $precio,
              			'cantidad' => $cantidad,
              			'estado'=>$estado,
              			'id_sucursal' => $_SESSION['id_sucursal'],
              			'id_presentacion' => $id_presentacion,
              		);
              		$insert_lote = _insert($table_perece,$form_data_perece );

              	}
              	if($i==0)
              	{
              		if ($up==0&&$up2==0&&$an==0)
              		{
              			_commit();
              		}
              		else
              		{
              			_rollback();
              		}
               }
               else {
              	 _rollback();
               }
              }

              function consultar_stock()
              {
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
                  $sql_aux = _query("SELECT id_presentacion, id_producto FROM presentacion_producto WHERE barcode='$id_producto' AND activo='1'");
                  if(_num_rows($sql_aux)>0)
                  {
                    $dats_aux = _fetch_array($sql_aux);
                    $id_producto = $dats_aux["id_producto"];
                    $id_presentacione = $dats_aux["id_presentacion"];
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
                AND u.bodega=0
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

                    $sql_res_esto=_fetch_array(_query("SELECT SUM(factura_detalle.cantidad) as reservado FROM factura JOIN factura_detalle ON factura_detalle.id_factura=factura.id_factura WHERE factura_detalle.id_prod_serv=$id_producto AND factura.id_factura=$id_factura"));
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
                    $sql_p=_query("SELECT presentacion.nombre, presentacion_producto.descripcion,presentacion_producto.id_presentacion,presentacion_producto.unidad,presentacion_producto.precio
                      FROM presentacion_producto
                      JOIN presentacion ON presentacion.id_presentacion=presentacion_producto.presentacion
                      WHERE presentacion_producto.id_producto='$id_producto'
                      AND presentacion_producto.activo=1
                      AND presentacion_producto.id_sucursal=$id_sucursal
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
                          $xc=0;
                          $sql_rank=_query("SELECT presentacion_producto_precio.id_prepd,
                            presentacion_producto_precio.desde,presentacion_producto_precio.hasta,
                            presentacion_producto_precio.precio
                            FROM presentacion_producto_precio
                            WHERE presentacion_producto_precio.id_presentacion='$id_press'
                            AND presentacion_producto_precio.id_sucursal='$id_sucursal'
                            AND presentacion_producto_precio.precio!=0
                            ORDER BY presentacion_producto_precio.desde ASC $limit");
                            while ($rowr=_fetch_array($sql_rank))
                            {
                              # code...
                              $select_rank.="<option value='$rowr[precio]'";
                              if($xc==0)
                              {
                                $select_rank.=" selected ";
                                $preciop=$rowr['precio'];
                                $xc = 1;
                              }
                              $select_rank.=">$rowr[precio]</option>";
                            }
                            $select_rank.="<option value='0.0'>0.0</option>";
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
                    $id_sucursal=$_SESSION['id_sucursal'];
                    $id_presentacion =$_REQUEST['id_presentacion'];

                    $sql=_fetch_array(_query("SELECT * FROM presentacion_producto WHERE id_presentacion=$id_presentacion"));
                    $precio=$sql['precio'];
                    $unidad=$sql['unidad'];
                    $descripcion=$sql['descripcion'];
                    $id_producto=$sql['id_producto'];
                    $sql_e=_fetch_array(_query("SELECT exento FROM producto WHERE id_producto=$id_producto"));
                    $exento=$sql_e['exento'];

                    $select_rank="<select class='sel_r precio_r form-control'>";
                    $xc=0;
                    $id_sucursal = $_SESSION['id_sucursal'];

                    $id_usuario=$_SESSION["id_usuario"];
                    $r_precios=_fetch_array(_query("SELECT precios FROM usuario WHERE id_usuario=$id_usuario"));
                    $precios=$r_precios['precios'];
                    $limit=" LIMIT ".$precios;


                    $sql_rank=_query("SELECT id_prepd,desde,hasta,precio
                      FROM presentacion_producto_precio
                      WHERE id_presentacion=$id_presentacion
                      AND id_sucursal=$id_sucursal
                      AND precio>0
                      ORDER BY precio DESC
                      $limit");

                      while ($rowr=_fetch_array($sql_rank))
                      {
                        # code...
                        $select_rank.="<option value='$rowr[precio]'";
                        if(!$xc)
                        {
                          $select_rank.=" selected ";
                          $precio=$rowr['precio'];
                          $xc=1;
                        }
                        $select_rank.=">$rowr[precio]</option>";
                      }
                      if (_num_rows($sql_rank)==0) {
                        # code...
                        $select_rank.="<option value='$precio'";
                        $select_rank.="selected";
                        $select_rank.=">$precio</option>";
                      }
                      $select_rank.="<option value='0.0'>0.0</option>";
                      $select_rank.="</select>";

                      $des = "<input type='text' id='ss' class='txt_box form-control' value='".$descripcion."' readonly>";
                      $xdatos['precio']=$precio;

                      if ($exento==1) {
                        # code...
                        $xdatos['preciop_s_iva']=$precio;
                      }
                      else {
                        # code...
                        $sqkl=_fetch_array(_query("SELECT iva FROM sucursal WHERE id_sucursal=$id_sucursal"));
                        $iva=$sqkl['iva']/100;
                        $iva=1+$iva;
                        $xdatos['preciop_s_iva']= round(($precio/$iva),8,PHP_ROUND_HALF_DOWN);
                      }
                      $xdatos['unidad']=$unidad;
                      $xdatos['descripcion']=$des;
                      $xdatos['descripcion']=$des;
                      $xdatos['select_rank']=$select_rank;
                      echo json_encode($xdatos);
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
                      $cadena_salida= "Son: ".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.";
                      echo $cadena_salida;
                    }

                    function agregar_cliente()
                    {
                      //$id_cliente=$_POST["id_cliente"];
                      $nombre=$_POST["nombress"];
                      $apellido=$_POST["apellidos"];
                      $dui=$_POST["dui"];
                      $tel1=$_POST["tel1"];
                      $tel2=$_POST["tel2"];

                      $sql_result=_query("SELECT * FROM cliente WHERE nombre='$nombre'");
                      $numrows=_num_rows($sql_result);
                      $row_update=_fetch_array($sql_result);
                      $id_cliente=$row_update["id_cliente"];
                      $name_cliente=$row_update["nombre"];


                      //'id_cliente' => $id_cliente,
                      $table = 'cliente';
                      $form_data = array(
                        'nombre' => $nombre,
                        'apellido' => $apellido,
                        'dui' => $dui,
                        'telefono1' => $tel1,
                        'telefono2' => $tel2,
                      );

                      if ($numrows == 0 && trim($nombre)!='') {
                        $insertar = _insert($table, $form_data);
                        $id_cliente=_insert_id();
                        if ($insertar) {
                          $xdatos['typeinfo']='Success';
                          $xdatos['msg']='Registro insertado con exito!';
                          $xdatos['process']='insert';
                          $xdatos['id_client']=  $id_cliente;
                        } else {
                          $xdatos['typeinfo']='Error';
                          $xdatos['msg']='Registro no insertado !';
                        }
                      } else {
                        $xdatos['typeinfo']='Error';
                        $xdatos['msg']='Registro no insertado !';
                      }
                      echo json_encode($xdatos);
                    }
                    //functions to load
                    if (!isset($_REQUEST['process'])) {
                      initial();
                    }
                    //else {
                    if (isset($_REQUEST['process'])) {
                      switch ($_REQUEST['process']) {
                        case 'insert':
                        insertar();
                        break;
                        case 'consultar_stock':
                        consultar_stock();
                        break;
                        case 'total_texto':
                        total_texto();
                        break;
                        case 'getpresentacion':
                        getpresentacion();
                        break;
                        case 'agregar_cliente':
                        agregar_cliente();
                        break;
                        case 'pedido':
                        pedido();
                        break;
                        case 'editar':
                        editar();
                        break;
                      }
                    }
                    ?>
