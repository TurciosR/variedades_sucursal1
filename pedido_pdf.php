<?php
require('_core.php');
require('num2letras.php');
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
        $this->SetFont('Arial', '', 7);

        /*$this->Cell(130,4,"",1,0,"L");
        $this->Cell(10,4,"",1,0,"L");
        $this->Cell(130,4,"",1,1,"L");*/

        $set_y=$this->GetY();
        $set_x=$this->GetX();

        $this->SetFont('Arial', 'B', 8);

        $this->SetXY(245, 10);
        $this->Cell(30, 6, 'ORDEN DE PEDIDO', "TLR", 1, 'C');
        $this->SetX(245);
        $this->Cell(30, 6, utf8_decode('N° ').str_pad($this->a['id_pedido'],8,0,STR_PAD_LEFT), "BLR", 1, 'C');

        $this->SetXY(105, 10);
        $this->Cell(30, 6, 'ORDEN DE PEDIDO', "TLR", 1, 'C');
        $this->SetX(105);
        $this->Cell(30, 6, utf8_decode('N° ').str_pad($this->a['id_pedido'],8,0,STR_PAD_LEFT), "BLR", 1, 'C');


        $this->SetFont('Arial', '', 7);
        $this->SetXY($set_x, $set_y);
        $array_data = array(
            array("",30,"C"),
            array("SUPER  ACCESS",60,"C"),
            array("",80,"C"),
            array("SUPER  ACCESS",60,"C"),
        );
        $this->LineWrite($array_data);
        $array_data = array(
            array("",30,"C"),
            array(utf8_decode("Avenida Gerardo Barrios 6a Calle Oriente N° 102 Frente a Portón de Centro de Telas, San Miguel, El Salvador."),60,"C"),
            array("",80,"C"),
            array(utf8_decode("Avenida Gerardo Barrios 6a Calle Oriente N° 102 Frente a Portón de Centro de Telas, San Miguel, El Salvador."),60,"C"),

        );
        $this->LineWriteS($array_data);
        $array_data = array(
            array("",30,"C"),
            array(utf8_decode("Giro: Venta al por Mayor y Menor  de Otros Productos N. C. P."),60,"C"),
            array("",80,"C"),
            array(utf8_decode("Giro: Venta al por Mayor y Menor  de Otros Productos N. C. P."),60,"C"),

        );
        $this->LineWriteS($array_data);

        $ancho=26;
        $alto=22;
        $logo='img/logo_pedido/logo.jpg';
        $this->Image($logo,5,5,$ancho,$alto);
        $this->Image($logo,145,5,$ancho,$alto);

        $logo2='img/logo_pedido/marca.jpg';
        $this->Image($logo2,20,75,100,90);
        $this->Image($logo2,160,75,100,90);

        $this->Line(140,5,140,210);

        $this->Ln(4);


        $array_data = array(
            array("FECHA: ".ED($this->a['fecha']),30,"L"),
            array(utf8_decode("NRC: ".$this->a['nrc']),50,"L"),
            array(utf8_decode("NIT: ".$this->a['nit']),50,"L"),
            array("\b",10,"L"),
            array("FECHA: ".ED($this->a['fecha']),30,"L"),
            array(utf8_decode("NRC: ".$this->a['nrc']),50,"L"),
            array(utf8_decode("NIT: ".$this->a['nit']),50,"L"),
        );
        $this->LineWriteB($array_data);
        $array_data = array(
            array("TEL: ".$this->a['telefono1'],30,"L"),
            array(utf8_decode("GIRO: ").$this->a['giro'],100,"L"),
            array("\b",10,"L"),
            array("TEL: ".$this->a['telefono1'],30,"L"),
            array(utf8_decode("GIRO: ").$this->a['giro'],100,"L"),
        );
        $this->LineWriteB($array_data);
        $array_data = array(
            array(utf8_decode("CLIENTE: ".$this->a['nombre']),130,"L"),
            array("\b",10,"L"),
            array(utf8_decode("CLIENTE: ".$this->a['nombre']),130,"L"),
        );
        $this->LineWriteB($array_data);

        $dir = $this->a['lugar_entrega'];
        if ($dir=="")
        {
          $dir= $this->a['dircli'];
        }
        $array_data = array(
            array(utf8_decode("DIRECCION: ".$dir),130,"L"),
            array("\b",10,"L"),
            array(utf8_decode("DIRECCION: ".$dir),130,"L"),
        );
        $this->LineWriteB($array_data);
        $array_data = array(
            array(utf8_decode("MUNICIPIO: ".$this->a['nombre_municipio']),70,"L"),
            array(utf8_decode("DEPARTAMENTO: ".$this->a['nombre_departamento']),60,"L"),
            array("\b",10,"L"),
            array(utf8_decode("MUNICIPIO: ".$this->a['nombre_municipio']),70,"L"),
            array(utf8_decode("DEPARTAMENTO: ".$this->a['nombre_departamento']),60,"L"),
        );
        $this->LineWriteB($array_data);
        $array_data = array(
            array(utf8_decode("FORMA DE PAGO: "),70,"L"),
            array(utf8_decode("TRANSPORTE: "),60,"L"),
            array("\b",10,"L"),
            array(utf8_decode("FORMA DE PAGO: "),70,"L"),
            array(utf8_decode("TRANSPORTE: "),60,"L"),
        );
        $this->LineWriteB($array_data);

        $this->Ln(4);

        $set_y=$this->GetY();
        $set_x=$this->GetX();
        $this->SetXY($set_x, $set_y);
        $this->Cell(10, 5, 'CANT', 1, 0, 'L');
        $this->Cell(10, 5, 'COD', 1, 0, 'L');
        $this->Cell(80, 5, 'DESCRIPCION', 1, 0, 'L');
        $this->Cell(15, 5, "PRECIO U.", 1, 0, 'L');
        $this->Cell(15, 5, 'SUBTOTAL', 1, 0, 'L');

        $this->Cell(10, 5, '', "LR", 0, 'L');

        $this->Cell(10, 5, 'CANT', 1, 0, 'L');
        $this->Cell(10, 5, 'COD', 1, 0, 'L');
        $this->Cell(80, 5, 'DESCRIPCION', 1, 0, 'L');
        $this->Cell(15, 5, "PRECIO U.", 1, 0, 'L');
        $this->Cell(15, 5, 'SUBTOTAL', 1, 1, 'L');

        $set_y=$this->GetY();
        $this->Line(5,$set_y,5,190);
        $this->Line(15,$set_y,15,160);
        $this->Line(25,$set_y,25,160);
        $this->Line(105,$set_y,105,190);
        $this->Line(120,$set_y,120,190);
        $this->Line(135,$set_y,135,190);

        $this->Line(5+140,$set_y,5+140,190);
        $this->Line(15+140,$set_y,15+140,160);
        $this->Line(25+140,$set_y,25+140,160);
        $this->Line(105+140,$set_y,105+140,190);
        $this->Line(120+140,$set_y,120+140,190);
        $this->Line(135+140,$set_y,135+140,190);

        //lineas observacion
        $this->Line(5,160,105,160);
        $this->Line(145,160,245,160);

        //lineas fondo
        $this->Line(5,190,135,190);
        $this->Line(145,190,275,190);
        //lineas earriba
        $this->Line(5,185,135,185);
        $this->Line(145,185,275,185);
    }

    public function Footer()
    {
        $this->SetY(161);

        $array_data = array(
            array(utf8_decode("OBSERVACIONES: ".$this->a['transporte']),50,"L"),
            array("",90,"C"),
            array(utf8_decode("OBSERVACIONES: ".$this->a['transporte']),50,"L"),
        );
        $this->LineWriteS($array_data);

        $this->SetY(186);

        $total_final=sprintf("%.2f",$this->a['total']);
        list($entero,$decimal)=explode('.',$total_final);
        $enteros_txt=num2letras($entero);
        $decimales_txt=num2letras($decimal);

        if($entero>1)
        	$dolar=" dolares";
        else
        	$dolar=" dolar";
        $txtletra= "Son: ".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.";

        $array_data = array(
            array("".$txtletra,100,"L"),
            array(utf8_decode("TOTAL"),15,"C"),
            array(utf8_decode(number_format($this->a['total'],2)),15,"R"),
            array(" ",10,"L"),
            array("".$txtletra,100,"L"),
            array(utf8_decode("TOTAL"),15,"C"),
            array(utf8_decode(number_format($this->a['total'],2)),15,"R"),
        );
        $this->LineWriteS($array_data);

        // Posición: a 2 cm del final
        $this->SetY(-20);

        $this->Cell(40, 5,utf8_decode($this->a['user']), "B", 0, 'L');
        $this->Cell(3, 5,"", "", 0, 'L');
        $this->Cell(25, 5,"", "B", 0, 'L');
        $this->Cell(3, 5,"", "", 0, 'L');
        $this->Cell(25, 5,"", "B", 0, 'L');
        $this->Cell(3, 5,"", "", 0, 'L');
        $this->Cell(30, 5,"", "B", 0, 'L');

        $this->Cell(10, 5, "", 0, 0, 'L');

        $this->Cell(40, 5,utf8_decode($this->a['user']), "B", 0, 'L');
        $this->Cell(3, 5,"", "", 0, 'L');
        $this->Cell(25, 5,"", "B", 0, 'L');
        $this->Cell(3, 5,"", "", 0, 'L');
        $this->Cell(25, 5,"", "B", 0, 'L');
        $this->Cell(3, 5,"", "", 0, 'L');
        $this->Cell(30, 5,"", "B", 0, 'L');
        $this->Cell(10, 5, "", 0, 1, 'L');

        $this->Cell(40, 5,"NOMBRE Y FIRMA VENDEDOR", "", 0, 'L');
        $this->Cell(3, 5,"", "", 0, 'L');
        $this->Cell(25, 5,"AUTORIZADO", "", 0, 'C');
        $this->Cell(3, 5,"", "", 0, 'L');
        $this->Cell(25, 5,"AUTORIZADO", "", 0, 'C');
        $this->Cell(3, 5,"", "", 0, 'L');
        $this->Cell(30, 5,"FIRMA Y SELLO CLIENTE", "", 0, 'C');

        $this->Cell(10, 5, "", 0, 0, 'L');

        $this->Cell(40, 5,"NOMBRE Y FIRMA VENDEDOR", "", 0, 'L');
        $this->Cell(3, 5,"", "", 0, 'L');
        $this->Cell(25, 5,"AUTORIZADO", "", 0, 'C');
        $this->Cell(3, 5,"", "", 0, 'L');
        $this->Cell(25, 5,"AUTORIZADO", "", 0, 'C');
        $this->Cell(3, 5,"", "", 0, 'L');
        $this->Cell(30, 5,"FIRMA Y SELLO CLIENTE", "", 1, 'C');

        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);

        $this->Cell(130, 5, utf8_decode('Página ').$this->PageNo().'/{nb}', 0, 0, 'L');
        $this->Cell(10, 5, "", 0, 0, 'L');
        $this->Cell(130, 5, utf8_decode('Página ').$this->PageNo().'/{nb}', 0, 0, 'L');
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

    public function LineWriteS($array)
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
          $this->Cell($data[$j]["size"][$i],3,$str,$abajo,$salto,$data[$j]["aling"][$i]);
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

$pdf = new PDF('L', 'mm', 'letter');
$pdf->setear($row_tras);
$pdf->SetMargins(5, 5);
$pdf->SetLeftMargin(5);
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 55);
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
      array(round($row['cantidad']/$row['unidad'],0),10,"C"),
			array($row['id_producto'],10,"R"),
			array(utf8_decode($row['producto']),80,"L"),
			array(number_format($row['precio_venta'],2),15,"R"),
			array(number_format($sub,2),15,"R"),
      array("",10,"R"),
      array(round($row['cantidad']/$row['unidad'],0),10,"C"),
			array($row['id_producto'],10,"R"),
			array(utf8_decode($row['producto']),80,"L"),
			array(number_format($row['precio_venta'],2),15,"R"),
			array(number_format($sub,2),15,"R"),
    );
    $pdf->LineWriteS($array_data);
  $n++;
}

$pdf->Output("pedido_pdf.pdf", "I");

}
else {
  # code...
  echo "<script languaje='javascript' type='text/javascript'>window.close();</script>";
}
