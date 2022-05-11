<?php
error_reporting(E_ERROR | E_PARSE);
require("_core.php");
require("num2letras.php");
require('fpdf/fpdf.php');


$pdf=new fPDF('L','mm', 'Letter');
$pdf->SetMargins(10,5);
$pdf->SetTopMargin(2);
$pdf->SetLeftMargin(10);
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true,1);
$pdf->AddFont("latin","","latin.php");
$id_sucursal = $_SESSION["id_sucursal"];
$sql_empresa = "SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'";

$resultado_emp=_query($sql_empresa);
$row_emp=_fetch_array($resultado_emp);
$tel1 = $row_emp['telefono'];
$nit = $row_emp['nit'];
$nrc = $row_emp['nrc'];
$razonsocial = $row_emp['razonsocial'];
$descripcion = utf8_decode($row_emp['descripcion']);
$giro = $row_emp['giro'];
$telefonos="TEL. ".$tel1;

    $id_producto = $_REQUEST["id_producto"];
    $fini = $_REQUEST["fini"];
    $fin = $_REQUEST["fin"];
    $logo = "img/logo_sys.jpg";
    $impress = "Impreso: ".date("d/m/Y");
    $title = $descripcion;
    $titulo = "KARDEX DE PRODUCTO";
    if($fini!="" && $fin!="")
    {
        list($a,$m,$d) = explode("-", $fini);
        list($a1,$m1,$d1) = explode("-", $fin);
        if($a ==$a1)
        {
            if($m==$m1)
            {
                $fech="DEL $d AL $d1 DE ".meses($m)." DE $a";
            }
            else
            {
                $fech="DEL $d DE ".meses($m)." AL $d1 DE ".meses($m1)." DE $a";
            }
        }
        else
        {
            $fech="DEL $d DE ".meses($m)." DEL $a AL $d1 DE ".meses($m1)." DE $a1";
        }
    }
            $sql = "SELECT * FROM movimiento_producto_detalle as md, movimiento_producto as m
                    WHERE md.id_movimiento=m.id_movimiento
                    AND m.id_sucursal='$id_sucursal'
                    AND md.id_producto='$id_producto'
                    AND m.tipo!='ASIGNACION'
                    AND m.tipo!='TRANSFERENCIA'
                    AND CAST(m.fecha AS DATE) BETWEEN '$fini' AND '$fin' ORDER BY md.fecha,md.hora ASC";

    $pdf->AddPage();
    $pdf->SetFont('Latin','',10);
    $pdf->Image($logo,9,4,50,18);
    //$pdf->Image($logob,160,4,50,15);
    $set_x = 0;
    $set_y = 6;

    //Encabezado General
    $pdf->SetFont('Latin','',12);
    $pdf->SetXY($set_x, $set_y);
    $pdf->MultiCell(280,6,$title,0,'C',0);
    $pdf->SetFont('Latin','',10);
    $pdf->SetXY($set_x, $set_y+5);
    $pdf->Cell(280,6,$telefonos,0,1,'C');
    $pdf->SetXY($set_x, $set_y+10);
    $pdf->Cell(280,6,"NIT: ".$nit.", NRC: ".$nrc,0,1,'C');
    $pdf->SetXY($set_x, $set_y+15);
    $pdf->Cell(280,6,utf8_decode($titulo),0,1,'C');
    $pdf->SetXY($set_x, $set_y+20);
    $pdf->Cell(280,6,$fech,0,1,'C');

    $sql_aux = _query("SELECT descripcion FROM producto  WHERE id_producto='$id_producto'");
    $dats_aux = _fetch_array($sql_aux);
    $pdf->SetXY($set_x+4, $set_y+27);
    $pdf->Cell(100,5,"PRODUCTO: ".utf8_decode($dats_aux["descripcion"]),0,1,'L',0);

    $set_y = 40;
    $set_x = 4;


    $pdf->SetFont('Latin','',8);
    $pdf->SetXY($set_x, $set_y);
    $pdf->Cell(18,10,"FECHA",1,1,'C',0);
    $pdf->SetXY($set_x+18, $set_y);
    $pdf->Cell(18,10,"TIPO DOC",1,1,'C',0);
    $pdf->SetXY($set_x+36, $set_y);
    $pdf->Cell(18,10,"NUM. DOC",1,1,'C',0);
    $pdf->SetXY($set_x+54, $set_y);
    $pdf->Cell(54,5,"ENTRADA",1,1,'C',0);
    $pdf->SetXY($set_x+54, $set_y+5);
    $pdf->Cell(18,5,"CANTIDAD",1,1,'C',0);
    $pdf->SetXY($set_x+72, $set_y+5);
    $pdf->Cell(18,5,"COSTO",1,1,'C',0);
    $pdf->SetXY($set_x+90, $set_y+5);
    $pdf->Cell(18,5,"SUBTOTAL",1,1,'C',0);
    $pdf->SetXY($set_x+108, $set_y);
    $pdf->Cell(54,5,"SALIDA",1,1,'C',0);
    $pdf->SetXY($set_x+108, $set_y+5);
    $pdf->Cell(18,5,"CANTIDAD",1,1,'C',0);
    $pdf->SetXY($set_x+126, $set_y+5);
    $pdf->Cell(18,5,"COSTO",1,1,'C',0);
    $pdf->SetXY($set_x+144, $set_y+5);
    $pdf->Cell(18,5,"SUBTOTAL",1,1,'C',0);
    $pdf->SetXY($set_x+162, $set_y);
    $pdf->Cell(54,5,"SALDO",1,1,'C',0);
    $pdf->SetXY($set_x+162, $set_y+5);
    $pdf->Cell(18,5,"CANTIDAD",1,1,'C',0);
    $pdf->SetXY($set_x+180, $set_y+5);
    $pdf->Cell(18,5,"COSTO",1,1,'C',0);
    $pdf->SetXY($set_x+198, $set_y+5);
    $pdf->Cell(18,5,"SUBTOTAL",1,1,'C',0);
    $pdf->SetXY($set_x+216, $set_y);
    $pdf->Cell(56,10,"PROVEEDOR",1,1,'C',0);
    //$pdf->SetTextColor(0,0,0);
    $set_y = 50;
    $page = 0;
    $j=0;
    $mm = 0;
    $i = 0;
    $result = _query($sql);
    if(_num_rows($result)>0)
    {
        $entrada = 0;
        $salida = 0;
        $init = 1;
        while($row = _fetch_array($result))
        {
            if($page==0)
                $salto = 29;
            else
                $salto = 36;
            if($j>=$salto)
            {
                $page++;
                $pdf->AddPage();
                $set_y = 10;
                $set_x = 4;
                //$pdf->SetFillColor(195, 195, 195);
                //$pdf->SetTextColor(255,255,255);
                $pdf->SetFont('Latin','',8);
                $pdf->SetXY($set_x, $set_y);
                $pdf->Cell(18,10,"FECHA",1,1,'C',0);
                $pdf->SetXY($set_x+18, $set_y);
                $pdf->Cell(18,10,"TIPO DOC",1,1,'C',0);
                $pdf->SetXY($set_x+36, $set_y);
                $pdf->Cell(18,10,"NUM. DOC",1,1,'C',0);
                $pdf->SetXY($set_x+54, $set_y);
                $pdf->Cell(54,5,"ENTRADA",1,1,'C',0);
                $pdf->SetXY($set_x+54, $set_y+5);
                $pdf->Cell(18,5,"CANTIDAD",1,1,'C',0);
                $pdf->SetXY($set_x+72, $set_y+5);
                $pdf->Cell(18,5,"COSTO",1,1,'C',0);
                $pdf->SetXY($set_x+90, $set_y+5);
                $pdf->Cell(18,5,"SUBTOTAL",1,1,'C',0);
                $pdf->SetXY($set_x+108, $set_y);
                $pdf->Cell(54,5,"SALIDA",1,1,'C',0);
                $pdf->SetXY($set_x+108, $set_y+5);
                $pdf->Cell(18,5,"CANTIDAD",1,1,'C',0);
                $pdf->SetXY($set_x+126, $set_y+5);
                $pdf->Cell(18,5,"COSTO",1,1,'C',0);
                $pdf->SetXY($set_x+144, $set_y+5);
                $pdf->Cell(18,5,"SUBTOTAL",1,1,'C',0);
                $pdf->SetXY($set_x+162, $set_y);
                $pdf->Cell(54,5,"SALDO",1,1,'C',0);
                $pdf->SetXY($set_x+162, $set_y+5);
                $pdf->Cell(18,5,"CANTIDAD",1,1,'C',0);
                $pdf->SetXY($set_x+180, $set_y+5);
                $pdf->Cell(18,5,"COSTO",1,1,'C',0);
                $pdf->SetXY($set_x+198, $set_y+5);
                $pdf->Cell(18,5,"SUBTOTAL",1,1,'C',0);
                $pdf->SetXY($set_x+216, $set_y);
                $pdf->Cell(56,10,"PROVEEDOR",1,1,'C',0);
                $j=0;
                $set_y = 20;
                $i=0;
                $mm=0;
                $pdf->SetFont('Latin','',8);
            }
            $fechadoc = ED($row["fecha"]);
            if($row["tipo"] == "ENTRADA" || $row["proceso"] =="TRR")
            {
              $csal = -1;
              $centr = $row["cantidad"];
              $entrada += $centr;
            }
            else if($row["tipo"] == "SALIDA" || $row["proceso"] =="TRE")
            {
              $centr = -1;
              $csal = $row["cantidad"];
              $salida += $csal;
            }
            if($row["tipo"] == "AJUSTE" && $row['id_presentacion']!=0)
            {
              $csal = -1;
              $centr = $row["cantidad"];
              $entrada += $centr;
            }
            else if($row["tipo"] == "AJUSTE")
            {
              $centr = -1;
              $csal = $row["cantidad"];
              $salida += $csal;
            }
            
            $id_presentacion = $row["id_presentacion"];
            $sql_pres = _query("SELECT unidad FROM presentacion_producto WHERE id_pp  ='$id_presentacion'");
            $dats_pres = _fetch_array($sql_pres);
            $uniades = $dats_pres["unidad"];
            $cost = $dats_pres["costo"];
            $id_compra = $row["id_compra"];
            $id_factura = $row["id_factura"];
            if($id_factura > 0)
            {
              $sql_comp = _query("SELECT tipo_documento, num_fact_impresa FROM factura WHERE id_factura='$id_factura'");
              $dats_comp = _fetch_array($sql_comp);
              $alias_tipodoc = $dats_comp["tipo_documento"];
              $numero_doc = $dats_comp["num_fact_impresa"];
            }
            if($id_compra > 0)
            {
              $sql_comp = _query("SELECT alias_tipodoc, numero_doc FROM compra WHERE id_compra='$id_compra'");
              $dats_comp = _fetch_array($sql_comp);
              $alias_tipodoc = $dats_comp["alias_tipodoc"];
              $numero_doc = $dats_comp["numero_doc"];
            }
            if($id_compra == 0 && $id_factura == 0)
            {
              $alias_tipodoc = $row['tipo'];
              $numero_doc = $row['correlativo'];
            }
            //$ultcosto = $row["costo"];//$uniades;
            $ultcosto = $row["costo"]/$uniades;
            $stock_actual = $row["stock_actual"];
            $stock_anterior = $row["stock_anterior"];
            $id_proveedor = $row["id_proveedor"];
            if($init)
      			{
              if($stock_anterior > 0)
              {
                $pdf->SetXY($set_x, $set_y);
                $pdf->Cell(162,5,"INVENTARIO INICIAL",0,1,'C',0);
                $pdf->SetXY($set_x+162, $set_y);
                $pdf->Cell(18,5,$stock_anterior,0,1,'C',0);
                $pdf->SetXY($set_x+180, $set_y);
                $pdf->Cell(18,5,number_format($ultcosto,2,".",","),0,1,'C',0);
                $pdf->SetXY($set_x+198, $set_y);
                $pdf->Cell(18,5,number_format(($stock_anterior * $ultcosto), 2),0,1,'C',0);
                $pdf->SetXY($set_x+216, $set_y);
                $pdf->Cell(56,5,"",0,1,'C',0);
                $mm+=5;
              }
              $init=0;
      			}
            $lwidth = 5;
            if($id_proveedor>0)
            {
                $sql2 = _query("SELECT p.nombre, pa.nombre as pais FROM proveedor as p LEFT JOIN paises as pa ON(p.nacionalidad=pa.id) WHERE p.id_proveedor='".$id_proveedor."'");
                $datos2 = _fetch_array($sql2);
                $nombr = utf8_decode($datos2["nombre"]);
                $nombr = $nombr." (".utf8_decode($datos2["pais"]).")";
                if(ceil(strlen($nombr))/2 > 20)
                {
                    $nom = divtextlin($nombr, 30);
                    $nn = 0;
                    foreach ($nom as $nnon)
                    {
                        $pdf->SetXY($set_x+216, $set_y+$mm+$nn);
                        $pdf->Cell(56,5,$nnon,0,0,'L',0);
                        $nn += 5;
                        $j++;
                    }
                    $lwidth = $nn;
                }
                else
                {
                    $pdf->SetXY($set_x+216, $set_y+$mm);
                    $pdf->Cell(56,$lwidth,$nombr,0,1,'C',0);
                }
            }
            else
            {
                $pdf->SetXY($set_x+216, $set_y+$mm);
                $pdf->Cell(56,$lwidth,"",0,1,'C',0);
            }

            $pdf->SetXY($set_x, $set_y+$mm);
            $pdf->Cell(18,$lwidth,$fechadoc,0,1,'C',0);
            $pdf->SetXY($set_x+18, $set_y+$mm);
            $pdf->Cell(18,$lwidth,$alias_tipodoc,0,1,'C',0);
            $pdf->SetXY($set_x+36, $set_y+$mm);
            $pdf->Cell(18,$lwidth,$numero_doc,0,1,'C',0);
            $pdf->SetXY($set_x+54, $set_y+$mm);
            if($centr >= 0)
            {
                $pdf->Cell(18,$lwidth,$centr,0,1,'C',0);
                $pdf->SetXY($set_x+72, $set_y+$mm);
                $pdf->Cell(18,$lwidth,number_format($ultcosto,2,".",","),0,1,'C',0);
                $pdf->SetXY($set_x+90, $set_y+$mm);
                $pdf->Cell(18,$lwidth,number_format(($centr * $ultcosto), 2),0,1,'C',0);
            }
            else
            {
                $pdf->Cell(18,$lwidth,"",0,1,'C',0);
                $pdf->SetXY($set_x+72, $set_y+$mm);
                $pdf->Cell(18,$lwidth,"",0,1,'C',0);
                $pdf->SetXY($set_x+90, $set_y+$mm);
                $pdf->Cell(18,$lwidth,"",0,1,'C',0);
            }
            $pdf->SetXY($set_x+108, $set_y+$mm);
            if($csal >= 0)
            {
                $pdf->Cell(18,$lwidth,$csal,0,1,'C',0);
                $pdf->SetXY($set_x+126, $set_y+$mm);
                $pdf->Cell(18,$lwidth,number_format($ultcosto,2,".",","),0,1,'C',0);
                $pdf->SetXY($set_x+144, $set_y+$mm);
                $pdf->Cell(18,$lwidth,number_format(($csal * $ultcosto), 2),0,1,'C',0);
            }
            else
            {
                $pdf->Cell(18,$lwidth,"",0,1,'C',0);
                $pdf->SetXY($set_x+126, $set_y+$mm);
                $pdf->Cell(18,$lwidth,"",0,1,'C',0);
                $pdf->SetXY($set_x+144, $set_y+$mm);
                $pdf->Cell(18,$lwidth,"",0,1,'C',0);
            }
            $pdf->SetXY($set_x+162, $set_y+$mm);
            $pdf->Cell(18,$lwidth,$stock_actual,0,1,'C',0);
            $pdf->SetXY($set_x+180, $set_y+$mm);
            $pdf->Cell(18,$lwidth,number_format($ultcosto,2,".",","),0,1,'C',0);
            $pdf->SetXY($set_x+198, $set_y+$mm);
            $pdf->Cell(18,$lwidth,number_format(($stock_actual * $ultcosto), 2),0,1,'C',0);

            $mm += $lwidth;
            $j++;
            $i++;
            if($i==1)
            {
                //Fecha de impresion y numero de pagina
                $pdf->SetXY(4, 210);
                $pdf->Cell(10, 0.4,$impress, 0, 0, 'L');
                $pdf->SetXY(258, 210);
                $pdf->Cell(20, 0.4, 'Pag. '.$pdf->PageNo().' de {nb}', 0, 0, 'R');
            }
        }
        $pdf->Line($set_x, $set_y+$mm, $set_x+272, $set_y+$mm);
        $pdf->SetXY($set_x, $set_y+$mm);
        $pdf->Cell(18,6,"",0,1,'C',0);
        $pdf->SetXY($set_x+18, $set_y+$mm);
        $pdf->Cell(36,6,"TOTAL ENTRADA",0,1,'C',0);
        $pdf->SetXY($set_x+54, $set_y+$mm);
        $pdf->Cell(18,6,$entrada,0,1,'C',0);
        $pdf->SetXY($set_x+72, $set_y+$mm);
        $pdf->Cell(36,6,"TOTAL SALIDA",0,1,'C',0);
        $pdf->SetXY($set_x+108, $set_y+$mm);
        $pdf->Cell(18,6,$salida,0,1,'C',0);
        $pdf->SetXY($set_x+126, $set_y+$mm);
        $pdf->Cell(146,6,"",0,1,'C',0);
    }
ob_clean();
$pdf->Output("kardex.pdf","I");
