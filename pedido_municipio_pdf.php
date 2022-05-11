<?php
require('_core.php');
require('fpdf/fpdf.php');
$params="";

if (isset($_REQUEST['ini'])) {
  # code...

  $ini = $_REQUEST['ini'];
  $fin = $_REQUEST['fin'];

  $array_datas = array(
    'ini' => ED($ini),
    'fin' => ED($fin),
  );
class PDF extends FPDF
{
    var $a = array();
    // Cabecera de página\
    public function Header()
    {

        // Logo
        $this->AddFont('latin','','latin.php');
        $this->SetFont('latin', '', 10);
        /*$array_data = array(
            array("PEDIDOS POR MUNICIPIO DESDE ".$this->a['ini']." HASTA ".$this->a['fin'],195,"C"),
          );
        $this->LineWrite($array_data);
        $this->Ln(5);*/

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
    public function setear($a)
    {
      # code...
      $this->a=$a;
    }

    public function LineWrite($array)
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

          $abajo="0";

          $str = $data[$j]["valor"][$i];
          $this->Cell($data[$j]["size"][$i],4,$str,$abajo,$salto,$data[$j]["aling"][$i]);
        }

      }
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

          $str = $data[$j]["valor"][$i];
          $this->Cell($data[$j]["size"][$i],4,$str,$abajo,$salto,$data[$j]["aling"][$i]);
        }

      }
    }
}

$pdf = new PDF('P', 'mm', 'letter');

$pdf->setear($array_datas);
$pdf->SetMargins(10, 10);
$pdf->SetLeftMargin(10);
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);
$pdf->AliasNbPages();
$pdf->AddPage();

$sql = _query("SELECT COUNT(*) as c,pedido.finalizada,pedido.anulada FROM pedido WHERE pedido.fecha BETWEEN '$ini' AND '$fin' GROUP BY pedido.finalizada ");

$array_data = array(
    array("PEDIDOS GENERADOS Y ESTADO DESDE ".ED($ini)." HASTA ".ED($fin),195,"C"),
  );
$pdf->LineWrite($array_data);

while($row = _fetch_array($sql))
{
  $estado = "";
  switch ($row['finalizada']) {
    case '0':
      // code...
      $estado = "PENDIENTE:";
      break;
    case '1':
      // code...
      $estado = "FINALIZADO:";
      break;
    case '2':
      // code...
      $estado = "ANULADO:";
      break;

    default:
      // code...
      break;
  }

  $array_data = array(
			array($estado,30,"L"),
      array($row['c'],25,"R"),
    );
  $pdf->LineWrite($array_data);
}


$pdf->Ln(5);
$array_data = array(
    array("PEDIDOS FINALIZADOS TOTALES POR FECHA DESDE ".ED($ini)." HASTA ".ED($fin),195,"C"),
  );
$pdf->LineWrite($array_data);

$sql = _query("SELECT SUM(pedido.total) as facturado,pedido.fecha_factura FROM pedido WHERE pedido.fecha_factura BETWEEN '$ini' AND  '$fin' AND finalizada=1 GROUP BY pedido.fecha_factura ORDER BY fecha_factura ASC");
$total=0;
while($row = _fetch_array($sql))
{
  $total = $total + round($row['facturado'],2);
  $array_data = array(
      array(ED($row['fecha_factura']),30,"L"),
      array("$ ".number_format($row['facturado'],2),25,"R"),
    );
  $pdf->LineWrite($array_data);
}

$pdf-> Cell(30, 5,"TOTAL", "T", 0, 'L');
$pdf-> Cell(25, 5,"$ ".number_format($total,2), "T", 1, 'R');

$pdf->Ln(5);
$array_data = array(
    array("PEDIDOS FINALIZADOS POR MUNICIPIO DESDE ".ED($ini)." HASTA ".ED($fin),195,"C"),
  );
$pdf->LineWrite($array_data);
$sql = _query("SELECT SUM(pedido.total) as facturado,pedido.id_departamento,pedido.id_municipio,departamento.nombre_departamento,municipio.nombre_municipio FROM pedido LEFT JOIN departamento ON departamento.id_departamento=pedido.id_departamento LEFT JOIN municipio ON municipio.id_municipio=pedido.id_municipio WHERE pedido.fecha_factura BETWEEN '$ini' AND  '$fin' AND finalizada=1 GROUP BY pedido.id_departamento,municipio.id_municipio ");
$total=0;
while($row = _fetch_array($sql))
{
  $departamento = $row['nombre_departamento'];
  $municipio = $row['nombre_municipio'];
  if($departamento=="")
  {
    $departamento='NO ASIGNADO';
  }
  if($municipio=="")
  {
    $municipio='NO ASIGNADO';
  }
  $total = $total + round($row['facturado'],2);
  $array_data = array(
      array(utf8_decode($departamento),30,"L"),
      array(utf8_decode($municipio),60,"L"),
      array("$ ".number_format($row['facturado'],2),25,"R"),
    );
  $pdf->LineWrite($array_data);
}
$pdf-> Cell(60, 5,"TOTAL", "T", 0, 'L');
$pdf-> Cell(55, 5,"$ ".number_format($total,2), "T", 1, 'R');

$pdf->Ln(5);
$array_data = array(
    array("PEDIDOS FINALIZADOS POR EMPLEADO DESDE ".ED($ini)." HASTA ".ED($fin),195,"C"),
  );
$pdf->LineWrite($array_data);
$sql = _query("SELECT SUM(pedido.total) as facturado,pedido.id_empleado, usuario.nombre,usuario.usuario FROM pedido
LEFT JOIN usuario ON usuario.id_usuario=pedido.id_empleado
WHERE
pedido.fecha_factura
BETWEEN '$ini' AND  '$fin' AND finalizada=1 GROUP BY pedido.id_empleado");
$total=0;
while($row = _fetch_array($sql))
{
  $nombre = $row['nombre'];
  if($nombre=="")
  {
    $nombre='NO ASIGNADO';
  }
  $total = $total + round($row['facturado'],2);
  $array_data = array(
      array($nombre,90,"L"),
      array("$ ".number_format($row['facturado'],2),25,"R"),
    );
  $pdf->LineWrite($array_data);
}
$pdf-> Cell(90, 5,"TOTAL", "T", 0, 'L');
$pdf-> Cell(25, 5,"$ ".number_format($total,2), "T", 1, 'R');



  /*$array_data = array(
			array($n,10,"L"),
			array($row['producto'],97,"L"),
			array(round($row['cantidad']/$row['unidad'],0),30,"C"),
			array(number_format($row['precio_venta'],2),30,"R"),
			array(number_format($sub,2),30,"R"),
    );
  $pdf->LineWriteB($array_data);
  */
$pdf->Output("pedido_pdf.pdf", "I");

}
else {
  # code...
  echo "<script languaje='javascript' type='text/javascript'>window.close();</script>";
}
