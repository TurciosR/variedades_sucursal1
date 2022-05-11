<?php
require('_core.php');
require('fpdf/fpdf.php');
$params="";

if (isset($_REQUEST['id_pedido'])) {
  # code...
$id_pedido=$_REQUEST['id_pedido'];


$sql_suc=_query("SELECT pedido.*,cliente.nombre,cliente.nit,cliente.nrc,cliente.giro,cliente.telefono1,cliente.giro, municipio.nombre_municipio,departamento.nombre_departamento,usuario.nombre as user,cliente.direccion as dircli FROM pedido left join cliente on cliente.id_cliente=pedido.id_cliente left JOIN departamento ON departamento.id_departamento=pedido.id_departamento LEFT JOIN municipio ON municipio.id_municipio=pedido.id_municipio
LEFT JOIN usuario ON pedido.id_empleado=usuario.id_usuario
WHERE pedido.id_pedido='$id_pedido'");

$row_tras=_fetch_array($sql_suc);
$destino=$row_tras['lugar_entrega'];
$ho=$row_tras['hora_pedido'];

$sql_empresa = "SELECT * FROM sucursal WHERE id_sucursal=1";

$resultado_emp=_query($sql_empresa);
$row_emp=_fetch_array($resultado_emp);
$nombre_a = utf8_decode(Mayu(utf8_decode(trim($row_emp["descripcion"]))));
$tel1 = $row_emp['telefono1'];
$n_sucursal = 1;
// $n_sucursal = $row_emp['n_sucursal'];
// $tel2 = $row_emp['telefono2'];
$direccion = $row_emp['direccion'];
$telefonos="TEL. ".$tel1;
// $telefonos="TEL. ".$tel1." y ".$tel2;

list($y,$m,$d)=explode('-',$row_tras['fecha']);
$fech =$d." DE ".utf8_decode(Mayu(utf8_decode(meses($m))))." DEL ".$y."";

$cabebera=utf8_decode("HOJA DE CONTEO");

class PDF extends FPDF
{
    var $a = array();
    // Cabecera de página\
    public function Header()
    {
        if($this->a['id_pedido']>1600)
        {
          if($this->a['id_pedido'] % 1600 == 0)
          {
            $this->a['id_pedido'] = 1600;
          }
          else {
            $this->a['id_pedido'] =  $this->a['id_pedido'] % 1600;
          }

        }

        // Logo
        $this->AddFont('latin','','latin.php');
        $this->SetFont('latin', '', 10);
        $array_data = array(
            array("REVISE SU MERCADERIA","B",195,"L"),
          );
        $this->LineWriteB($array_data);
        $array_data = array(
            array("ORDEN DE PEDIDO: ".utf8_decode(str_pad($this->a['id_pedido'],8,0,STR_PAD_LEFT)),"B",195,"L"),
          );
        $this->LineWriteB($array_data);
        $array_data = array(
            array("CLIENTE: ".utf8_decode($this->a['nombre']),"B",195,"L"),
          );
        $this->LineWriteB($array_data);

        $dir = $this->a['lugar_entrega'];
        if ($dir=="")
        {
          $dir= $this->a['dircli'];
        }
        $array_data = array(
            array("DIRECCION: ".utf8_decode($dir),"B",195,"L"),
          );
        $this->LineWriteB($array_data);

        // Salto de línea
        $this->Ln(5);
        $set_y=$this->GetY();
        $set_x=$this->GetX();
        $this->SetXY($set_x, $set_y);
        $this->AddFont('latin','','latin.php');
        $this->SetFont('latin', '', 9);
        $this->Cell(10, 5, 'ID', 1, 0, 'L');
        $this->Cell(97, 5, 'PRODUCTO', 1, 0, 'L');
        $this->Cell(30, 5, 'CANTIDAD', 1, 0, 'L');
        $this->Cell(30, 5, "P.U", 1, 0, 'L');
        $this->Cell(30, 5, 'SUBTOTAL', 1, 1, 'L');
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
          $this->Cell($data[$j]["size"][$i],4,$str,$abajo,$salto,$data[$j]["aling"][$i]);
        }

      }
    }
}

$pdf = new PDF('P', 'mm', 'letter');

$pdf->setear($row_tras);
$pdf->SetMargins(10, 10);
$pdf->SetLeftMargin(10);
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);
$pdf->AliasNbPages();
$pdf->AddPage();

$sql="SELECT producto.id_producto, pedido_detalle.unidad, producto.descripcion AS producto, presentacion.nombre,presentacion_producto.id_pp as id_presentacion ,presentacion_producto.descripcion, presentacion_producto.unidad ,pedido_detalle.id_pedido_detalle,pedido_detalle.precio_venta, pedido_detalle.cantidad,pedido_detalle.cantidad as cantidad_enviar, pedido_detalle.subtotal
  FROM pedido_detalle
  JOIN producto ON (pedido_detalle.id_prod_serv=producto.id_producto)
  JOIN presentacion_producto ON (pedido_detalle.id_presentacion=presentacion_producto.id_pp)
  JOIN presentacion ON (presentacion_producto.id_presentacion=presentacion.id_presentacion)
  WHERE pedido_detalle.id_pedido='$id_pedido'";
$n=1;
$sql_detalle = _query($sql);
while ($row=_fetch_array($sql_detalle)) {
  $sub = round($row['cantidad']/$row['unidad'] * $row["precio_venta"],2);
  $array_data = array(
			array($n,10,"L"),
			array($row['producto'],97,"L"),
			array(round($row['cantidad']/$row['unidad'],0),30,"C"),
			array(number_format($row['precio_venta'],2),30,"R"),
			array(number_format($sub,2),30,"R"),
    );
    $pdf->LineWriteB($array_data);
  $n++;
}

$pdf -> Cell(167, 5,"TOTAL", 1, 0, 'C');
$pdf -> Cell(30, 5,number_format($row_tras["total"],2), 1, 0, 'R');


$pdf->Output("pedido_pdf.pdf", "I");

}
else {
  # code...
  echo "<script languaje='javascript' type='text/javascript'>window.close();</script>";
}
