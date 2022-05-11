<?php
error_reporting(E_ERROR | E_PARSE);
require("_core.php");
require("num2letras.php");
require('fpdf/fpdf.php');


$pdf=new fPDF('P','mm', 'Letter');
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
$nombre_a = utf8_decode(Mayu(utf8_decode(trim($row_emp["descripcion"]))));
//$direccion = Mayu(utf8_decode($row_emp["direccion_empresa"]));
$direccion = utf8_decode(Mayu(utf8_decode(trim($row_emp["direccion"]))));
$nrc = $row_emp['nrc'];
$nit = $row_emp['nit'];
$whatsapp=$row_emp["whatsapp"];
$email=$row_emp["email"];
$depa = $row_emp["id_departamento"];
$muni = $row_emp["id_municipio"];
$telefono1 = $row_emp["telefono1"];
$telefono2 = $row_emp["telefono2"];

$sql2 = _query("SELECT dep.* FROM departamento as dep WHERE dep.id_departamento='$depa'");
$row2 = _fetch_array($sql2);
$departamento = $row2["nombre_departamento"];

$sql3 = _query("SELECT mun.* FROM municipio as mun WHERE dep.id_municipio='$muni'");
$row3 = _fetch_array($sql3);
$municipio = $row3["nombre_municipio"];

$iftike = $_REQUEST["tiket"];
if($iftike == 1)
{
  $extra = "";
}
else
{
    $extra = " AND tipo_documento != 'TIK'";
}
$min = $_REQUEST["l"];
$fini = ($_REQUEST["fini"]);
$fin = ($_REQUEST["ffin"]);
$logo = "img/logo_sys.png";

$title = $nombre_a;
$titulo = "REPORTE DE INGRESOS Y EGRESOS";
if($fini!="")
{
    list($a,$m,$d) = explode("-", $fini);

    $fech="AL $d DE ".meses($m)." DE $a";

}
$impress = "REPORTE DE COSTO UTILIDAD ".$fech;


$existenas = "";
if($min>0)
{
    $existenas = "CANTIDAD: $min";
}


class PDF extends FPDF
{
    var $a;
    var $b;
    var $c;
    var $d;
    var $e;
    var $f;
    // Cabecera de página\
    public function Header()
    {
      $this->SetFont('Latin','',12);

      $this->Cell(100,6,utf8_decode($this->b),0,0,'L');
      $this->MultiCell(100,6,$this->a,0,'R',0);
      $this->SetFont('Latin','',12);
      $this->Cell(100,6,$this->c,0,0,'L');
      $this->SetFont('Latin','',10);
      $this->Cell(100,6,$this->d,0,1,'R');
      $this->SetFont('latin','',8);
      $this->Cell(20,5,"",0,1,'C',0);
    }

    public function Footer()
    {
      // Posición: a 1,5 cm del final
      $this->SetY(-15);
      // Arial italic 8
      $this->SetFont('Arial', 'I', 8);
      // Número de página requiere $pdf->AliasNbPages();
      //utf8_decode() de php que convierte nuestros caracteres a ISO-8859-1
      $this-> Cell(40, 10, utf8_decode('Fecha de impresión: '.date('Y-m-d')), 0, 0, 'L');
      $this->Cell(160, 10, utf8_decode('Página ').$this->PageNo().'/{nb}', 0, 0, 'R');
    }
    public function setear($a,$b,$c,$d,$e,$f,$g)
    {
      # code...
      $this->a=$a;
      $this->b=$b;
      $this->c=$c;
      $this->d=$d;
      $this->e=$e;
      $this->f=$f;
      $this->g=$g;
    }
}

$pdf=new PDF('P','mm', 'Letter');
$pdf->setear($title,$empresa,$titulo,$fech,$n_sucursal,$id_traslado,$destino);
$pdf->SetMargins(10,5);
$pdf->SetTopMargin(8);
$pdf->SetLeftMargin(8);
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true,20);
$pdf->AddFont("latin","","latin.php");
$pdf->AddPage();

//$pdf->AddPage();
$pdf->SetFont('latin','',9);
$pdf->Cell(20,5,utf8_decode("INGRESOS POR VENTA AL CONTADO"),0,1,'L',0);
$set_x = $pdf->GetX();
$set_y = $pdf->GetY();
$pdf->Line($set_x,$set_y,$set_x+205,$set_y);
$pdf->SetFont('latin','',8);
$pdf->Cell(20,5,utf8_decode("CODIGO"),0,0,'L',0);
$pdf->Cell(147,5,utf8_decode("PRODUCTO"),0,0,'C',0);
$pdf->Cell(18,5,utf8_decode("CANTIDAD"),0,0,'C',0);
$pdf->Cell(18,5,utf8_decode("VENTA"),0,1,'C',0);

$set_x = $pdf->GetX();
$set_y = $pdf->GetY();
$pdf->Line($set_x,$set_y,$set_x+205,$set_y);




if($cuenta_egreso > 0)
{
  $row_eg = _fetch_array($sql_egreso);
  $egreso = $row_eg['egreso'];
}
else
{
  $ingreso = 0;
}
    //$pdf->SetTextColor(0,0,0);
$set_y = 50;
$linea = 0;
$j = 0;
$sum_cantidad = 0;
$sum_costo = 0;
$sum_venta = 0;
$sum_utilidad = 0;
$sum_porcentaje = 0;
$sum_margen = 0;
$sql_producto = _query("SELECT SUM(fd.cantidad) as cant, SUM(fd.precio_venta*fd.cantidad/pre.unidad) as precio, SUM(pre.costo*fd.cantidad/pre.unidad) as cost, p.descripcion, p.barcode, fd.id_prod_serv as idfd, p.id_producto as idp
FROM factura_detalle as fd, presentacion_producto as pre, producto as p, factura as f
WHERE p.id_producto = fd.id_prod_serv AND pre.id_pp = fd.id_presentacion AND f.id_factura=fd.id_factura AND f.tipo_documento!='DEV' AND f.tipo_documento!='NC' AND f.credito=0 AND f.anulada=0 AND f.finalizada=1 AND fd.fecha BETWEEN '$fini' AND '$fin' GROUP BY fd.id_prod_serv");

$cuenta = _num_rows($sql_producto);
if($cuenta > 0)
{

  while ($row = _fetch_array($sql_producto))
  {
    $barcode = $row["barcode"];
    $descripcion = $row["descripcion"];
    $costo = round($row["cost"], 4);
    $precio = round($row["precio"], 4);
    $cantidad = $row["cant"];

    $costo_final = $costo * $cantidad;
    $precio_final = $precio * $cantidad;
    $utilidad = round(($precio - $costo), 4);
    $por_utilidad = round(($utilidad/$costo),4)*100;
    $margen = round($utilidad/($costo / 1.13), 4)*100;
    $pdf->Cell(20,5,utf8_decode($barcode),0,0,'L',0);
    $pdf->Cell(147,5,utf8_decode($descripcion),0,0,'L',0);
    $pdf->Cell(18,5,utf8_decode(number_format($cantidad,0)),0,0,'C',0);
    $pdf->Cell(18,5,utf8_decode(number_format($precio, 2)),0,1,'C',0);
    $linea += 5;

    $sum_cantidad += $cantidad;
    $sum_costo += $costo;
    $sum_venta += $precio;
    $sum_utilidad += $utilidad;
    $sum_porcentaje += $por_utilidad;
    $sum_margen += $margen;
  }
}
$set_x = $pdf->GetX();
$set_y = $pdf->GetY();
$pdf->Line($set_x,$set_y,$set_x+205,$set_y);
$pdf->Cell(167,5,"TOTAL INGRESOS POR VENTA AL CONTADO",0,0,'L',0);
$pdf->Cell(18,5,utf8_decode($sum_cantidad),0,0,'C',0);
$pdf->Cell(18,5,utf8_decode(number_format($sum_venta, 2)),0,1,'C',0);


$set_x = $pdf->GetX();
$set_y = $pdf->GetY();
$pdf->SetXY($set_x,$set_y);
$pdf->Cell(185,5,"",0,1,'R',0);
$pdf->SetFont('latin','',10);
//$pdf->SetXY($set_x+169,$set_y+$linea);
$pdf->Cell(185,5,utf8_decode("Ingresos por venta contado"),0,0,'R',0);
$pdf->Cell(18,5,"$".utf8_decode(number_format($sum_venta, 2)),0,1,'R',0);

ob_clean();
$pdf->Output("ventas_rangos.pdf","I");
