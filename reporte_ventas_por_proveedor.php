<?php
require('_core.php');
require('fpdf/fpdf.php');
$params="";

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
        // Salto de línea
        $this->Ln(5);
        $set_y=$this->GetY();
        $set_x=$this->GetX();
        $this->SetXY($set_x, $set_y);
        $this->AddFont('latin','','latin.php');
        $this->SetFont('latin', '', 9);

        $this->Cell(195, 5, utf8_decode('PRODUCTOS MAS VENDIDOS POR PROVEEDOR: '.$this->a." POR ".mb_strtoupper($this->b)), 1, 1, 'L');

        $this->Cell(10, 5, 'ID', 1, 0, 'L');
        $this->Cell(125, 5,'PRODUCTO', 1, 0, 'L');
        $this->Cell(30, 5, 'CANTIDAD', 1, 0, 'L');
        $this->Cell(30, 5, 'TOTAL', 1, 1, 'L');
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
    public function LineWriteB($array)
    {
      $ygg=0;
      $maxlines=1;
      $array_a_retornar=array();
      $array_max= array();
      foreach ($array as $key => $value) {
        // /Descripcion/
        $nombr=$value[0];
        // /fpdf width/
        $size=$value[1];
        // /fpdf alignt/
        $aling=$value[2];
        $jk=0;
        $w = $size;
        $h  = 0;
        $txt=$nombr;
        $border=0;
        if(!isset($this->CurrentFont))
          $this->Error('No font has been set');
        $cw = &$this->CurrentFont['cw'];
        if($w==0)
          $w = $this->w-$this->rMargin-$this->x;
        $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
        $s = str_replace("\r",'',$txt);
        $nb = strlen($s);
        if($nb>0 && $s[$nb-1]=="\n")
          $nb--;
        $b = 1;

        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $ns = 0;
        $nl = 1;
        while($i<$nb)
        {
          // Get next character
          $c = $s[$i];
          if($c=="\n")
          {
            $array_a_retornar[$ygg]["valor"][]=substr($s,$j,$i-$j);
            $array_a_retornar[$ygg]["size"][]=$size;
            $array_a_retornar[$ygg]["aling"][]=$aling;
            $jk++;

            $i++;
            $sep = -1;
            $j = $i;
            $l = 0;
            $ns = 0;
            $nl++;
            if($border && $nl==2)
              $b = $b2;
            continue;
          }
          if($c==' ')
          {
            $sep = $i;
            $ls = $l;
            $ns++;
          }
          $l += $cw[$c];
          if($l>$wmax)
          {
            // Automatic line break
            if($sep==-1)
            {
              if($i==$j)
                $i++;
              $array_a_retornar[$ygg]["valor"][]=substr($s,$j,$i-$j);
              $array_a_retornar[$ygg]["size"][]=$size;
              $array_a_retornar[$ygg]["aling"][]=$aling;
              $jk++;
            }
            else
            {
              $array_a_retornar[$ygg]["valor"][]=substr($s,$j,$sep-$j);
              $array_a_retornar[$ygg]["size"][]=$size;
              $array_a_retornar[$ygg]["aling"][]=$aling;
              $jk++;

              $i = $sep+1;
            }
            $sep = -1;
            $j = $i;
            $l = 0;
            $ns = 0;
            $nl++;
            if($border && $nl==2)
              $b = $b2;
          }
          else
            $i++;
        }
        // Last chunk
        if($this->ws>0)
        {
          $this->ws = 0;
        }
        if($border && strpos($border,'B')!==false)
          $b .= 'B';
        $array_a_retornar[$ygg]["valor"][]=substr($s,$j,$i-$j);
        $array_a_retornar[$ygg]["size"][]=$size;
        $array_a_retornar[$ygg]["aling"][]=$aling;
        $jk++;
        $ygg++;
        if ($jk>$maxlines) {
          // code...
          $maxlines=$jk;
        }
      }

      $ygg=0;
      foreach($array_a_retornar as $keys)
      {
        for ($i=count($keys["valor"]); $i <$maxlines ; $i++) {
          // code...
          $array_a_retornar[$ygg]["valor"][]="";
          $array_a_retornar[$ygg]["size"][]=$array_a_retornar[$ygg]["size"][0];
          $array_a_retornar[$ygg]["aling"][]=$array_a_retornar[$ygg]["aling"][0];
        }
        $ygg++;
      }



      $data=$array_a_retornar;
      $total_lineas=count($data[0]["valor"]);
      $total_columnas=count($data);

      for ($i=0; $i < $total_lineas; $i++) {
        // code...
        for ($j=0; $j < $total_columnas; $j++) {
          // code...
          $salto=0;
          $abajo="LR";
          if ($i==0) {
            // code...
            $abajo="TLR";
          }
          if ($j==$total_columnas-1) {
            // code...
            $salto=1;
          }
          if ($i==$total_lineas-1) {
            // code...
            $abajo="BLR";
          }
          if ($i==$total_lineas-1&&$i==0) {
            // code...
            $abajo="1";
          }
          // if ($j==0) {
          //   // code...
          //   $abajo="0";
          // }
          $str = $data[$j]["valor"][$i];
          if ($str=="\b")
          {
            $abajo="0";
            $str="";
          }
          $this->Cell($data[$j]["size"][$i],4,$str,$abajo,$salto,$data[$j]["aling"][$i]);
        }

      }
    }
}

$pdf = new PDF('P', 'mm', 'letter');
$pdf->SetMargins(10, 10);
$pdf->SetLeftMargin(10);
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);
$pdf->AliasNbPages();
$year=$_REQUEST['year'];
$por=$_REQUEST['por'];
$limit=$_REQUEST['limit'];

$meses = array(
  '01',
  '02',
  '03',
  '04',
  '05',
  '06',
  '07',
  '08',
  '09',
  '10',
  '11',
  '12'
);
$sql_proveedor = _query("SELECT id_proveedor,nombre FROM proveedor order BY proveedor.nombre ASC");

while ($rowp = _fetch_array($sql_proveedor)) {

  $id_proveedor = $rowp['id_proveedor'];
  $nombre = $rowp['nombre'];
  $ip=0;
  $ja=0;
  foreach ($meses as $key => $value) {
    $inicio = date("$year-$value-01");
    $fin = date("$year-$value-t");
    $ja=0;
    if($por=='Cantidad')
    {
      $qpro = _query("SELECT producto.descripcion,producto.id_producto, SUM(factura_detalle.subtotal) as tot, SUM(factura_detalle.cantidad) as totc  from factura_detalle join producto on producto.id_producto= factura_detalle.id_prod_serv JOIN factura ON factura.id_factura=factura_detalle.id_factura where  factura.anulada=0 and factura.finalizada=1 AND tipo_documento!='DEV' AND tipo_documento!='NC' AND  producto.id_proveedor=$id_proveedor AND factura_detalle.fecha between '$inicio' AND '$fin' GROUP BY factura_detalle.id_prod_serv ORDER BY SUM(factura_detalle.cantidad) DESC limit $limit");
    }
    else {
      $qpro = _query("SELECT producto.descripcion,producto.id_producto, SUM(factura_detalle.subtotal) as tot, SUM(factura_detalle.cantidad) as totc from factura_detalle join producto on producto.id_producto= factura_detalle.id_prod_serv JOIN factura ON factura.id_factura=factura_detalle.id_factura where  factura.anulada=0 and factura.finalizada=1 AND tipo_documento!='DEV' AND tipo_documento!='NC' AND producto.id_proveedor=$id_proveedor AND factura_detalle.fecha between '$inicio' AND '$fin' GROUP BY factura_detalle.id_prod_serv ORDER BY SUM(factura_detalle.subtotal) DESC limit $limit");
    }
    while ($rowpro = _fetch_array($qpro)) {
      if ($ip==0) {
        $pdf->setear($nombre,$por,"","","","","","");
        $pdf->AddPage();
        $ip++;
      }
      if ($ja==0) {
        $pdf->Cell(195, 5, "$year-$value", 1, 1, 'L');
      }

      $array_data = array(
            array($rowpro['id_producto'],10,"C"),
            array(utf8_decode($rowpro['descripcion']),125,"L"),
            array(utf8_decode(number_format($rowpro['totc'],2)),30,"R"),
            array(utf8_decode(number_format($rowpro['tot'],2)),30,"R"),
        );
        $pdf->LineWriteB($array_data);
      $ja++;
    }
  }
}






/*  $array_data = array(
      array($row['id_producto'],10,"C"),
      array(utf8_decode($row['descripcion']),85,"L"),
      array(utf8_decode($row['nombre']),40,"L"),
      array(utf8_decode($row['unidad']),30,"R"),
      array(utf8_decode(($row['cantidad']/$row['unidad'])),30,"R"),
  );
  $pdf->LineWriteB($array_data);
*/
$pdf->Output("traslado_enviado.pdf", "I");
