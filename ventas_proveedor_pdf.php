<?php
error_reporting(E_ERROR | E_PARSE);
require("_core.php");
require("num2letras.php");
require('fpdf/fpdf.php');

$fini = $_REQUEST["fini"];
$fin = $_REQUEST["ffin"];
//INSERT INTO `modulo` (`id_server`, `unique_id`, `id_modulo`, `id_menu`, `nombre`, `descripcion`, `filename`, `mostrarmenu`) VALUES ('145', '', NULL, '17', 'Ventas por Proveedor', 'Ventas por Proveedor', 'ventas_proveedor.php', '1');
$sql_empresa = "SELECT * FROM sucursal WHERE id_sucursal=$_SESSION[id_sucursal]";

$resultado_emp=_query($sql_empresa);
$row_emp=_fetch_array($resultado_emp);
$nombre_a = utf8_decode(Mayu(utf8_decode(trim($row_emp["descripcion"]))));
$tel1 = $row_emp['telefono1'];
$n_sucursal = $row_emp['n_sucursal'];
$tel2 = $row_emp['telefono2'];
$direccion = $row_emp['direccion'];
$telefonos="TEL. ".$tel1." y ".$tel2;



class PDF extends FPDF
{
    var $a;
    var $b;
    var $c;
    var $d;
    var $e;
    var $f;
    var $w;
    // Cabecera de página\
    public function Header()
    {

        // Logo
        $this->AddFont('latin','','latin.php');
        $this->SetFont('latin', '', 10);
        // Movernos a la derecha
        // Título
        //$this->SetX(43);
        //$this->Cell(130, 4, 'HOJA DE CONTEO ', 0, 1, 'C');

        $this->Cell(195,6,$this->a,0,1,'C');
        $this->SetFont('Latin','',10);
        $this->SetX(40);
        $this->MultiCell(140,5,utf8_decode((Mayu(utf8_decode("Sucursal ".": ".$this->c)))),0,'C',0);
        $this->Cell(195,6,$this->b,0,1,'C');
        $this->Cell(195,6,utf8_decode("REPORTE DE VENTAS POR PROVEEDOR"),0,1,'C');
        $this->Cell(195,6,$this->d,0,1,'C');
        // Salto de línea
        $this->Ln(5);
        $set_y=$this->GetY();
        $set_x=$this->GetX();
        $this->SetXY($set_x, $set_y);
        $this->AddFont('latin','','latin.php');
        $this->SetFont('latin', '', 9);
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
        $this->Cell(156, 10, utf8_decode('Página ').$this->PageNo().'/{nb}', 0, 0, 'R');
    }
    public function setear($a,$b,$c,$d,$e,$f,$g,$w)
    {
      # code...
      $this->a=$a;
      $this->b=$b;
      $this->c=$c;
      $this->d=$d;
      $this->e=$e;
      $this->f=$f;
      $this->g=$g;
      $this->w=$w;
    }
}

$pdf = new PDF('P', 'mm', 'letter');
$fech =  "DEL ".ED($fini)." AL ".ED($fin);
$pdf->setear($nombre_a,$telefonos,$direccion,$fech,$n_sucursal,"","","");
$pdf->SetMargins(10, 10);
$pdf->SetLeftMargin(10);
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);
$pdf->AliasNbPages();
$pdf->AddPage();
$fk=$fini;


$fecha_actual = $fk;

$sql_totales = _query("SELECT proveedor.nombre,proveedor.id_proveedor, SUM(factura_detalle.cantidad/presentacion_producto.unidad*factura_detalle.precio_venta) as total FROM factura JOIN factura_detalle ON factura_detalle.id_factura=factura.id_factura
JOIN presentacion_producto ON presentacion_producto.id_pp=factura_detalle.id_presentacion
JOIN producto ON producto.id_producto=factura_detalle.id_prod_serv
JOIN proveedor ON proveedor.id_proveedor = producto.id_proveedor
WHERE factura.finalizada=1 AND factura.anulada=0 AND factura.tipo_documento!='DEV' AND factura.tipo_documento!='NC' AND
factura.fecha BETWEEN '$fini' AND '$fin' GROUP BY proveedor.id_proveedor");


$pdf->Cell(10,5,utf8_decode("ID"),"B",0,'L',0);
$pdf->Cell(140,5,"Nombre","B",0,'L',0);
$pdf->Cell(45,5,"Total vendido","B",1,'L',0);

$total=0;
while($row = _fetch_array($sql_totales))
{
  $pdf->Cell(10,5,$row['id_proveedor'],0,0,'L',0);
  $pdf->Cell(140,5,utf8_decode($row['nombre']),0,0,'L',0);
  $pdf->Cell(45,5,"$ ".number_format(($row['total']),2,".",""),0,1,'R',0);

  $total = $total + $row['total'];
}

$pdf->Cell(10,5,$row['id_proveedor'],0,0,'L',0);
$pdf->Cell(140,5,"TOTAL",0,0,'L',0);
$pdf->Cell(45,5,"$ ".number_format(($total),2,".",""),0,1,'R',0);

ob_clean();
$pdf->Output("reporte_ventas_proveedor.pdf","I");
