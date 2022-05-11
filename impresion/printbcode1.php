<?php
header("Access-Control-Allow-Origin: *");
//windows
//$tmpdir = sys_get_temp_dir();   # directorio temporal
$array = $_REQUEST['datosproductos'];

$puerto=system('ls /dev/usb/lp*');
if ($puerto=='/dev/usb/lp0')
	$printer="/dev/usb/lp0";
else
	$printer="/dev/usb/lp1";

//$printer="archivo.txt";




$keyval="";
$n=1;
foreach ($array as $fila){
	foreach ($fila as $key => $val){

	if($key!='fin'){
		$keyval.=$val."|";

	}
	if($key=='fin'){
  	$keyval.=";";
		$n+=1;
	}

}

}
$listadatos=explode(';',$keyval);
$string="";
for ($i=0;$i<$n ;$i++){
		 list($barcode,$descrip,$precio,$desc_pre,$despre,$qty)=explode('|',$listadatos[$i]);
		 for ($j=0;$j<$qty;$j++){

		// $string.=$barcode."-".$descrip."-".$precio."-".$talla."-".$estilo."-".$color."-".$rango."-".$id_proveedor."-".$qty."\n";

			 	$posx=245; //x,y posicion
				$string.="^XA";
 				$posy=16;

		 		$string.="^CF0,30";
				$string.="^FO".$posx.",".$posy."^FD"."DISTRIBUCIONES GLOBALES"."^FS";
				$posx=250;
				$posy+=32;
				$string.="^CF0,20";
				$string.="^FO".$posx.",".$posy."^FD".utf8_decode($descrip)."^FS";
		 		$posx+=100;
		 		$string.="^CF0,25";
		 		$string.="^FO".$posx.",".$posy."^FD".""."^FS";
		 		$posx+=215;
				$string.="^CF0,35";
				$string.="^FO".$posx.",".$posy."^FD".""."^FS";
				$string.="^CF0,30";
				$string.="^BY3,1";
				$posx=265; $posy=70;
				$string.="^FO".$posx.",".$posy;
				$string.="^BCN,70,N";
				$string.="^FD".$barcode."^FS";
				$string.="^CF0,25";
				$posx=552; $posy=80;
				$string.="^FO".$posx.",".$posy."^FD".""."^FS";
				$posx=250; $posy=150;
				$string.="^CF0,20";
				$string.="^FO".$posx.",".$posy."^FD".$despre." (".$desc_pre.")^FS";
				$string.="^CF0,25";
				$posx=250; $posy=180;
				$string.="^FO".$posx.",".$posy."^FD".""."^FS";
				$posx=410; $posy=180;
			  $string.="^FO".$posx.",".$posy."^FD".""."^FS";
				$posx=480; $posy=170;
				$string.="^CF0,35";
				$string.="^FO".$posx.",".$posy."^FD".$barcode."^FS";
				$string.="^XZ";

	}

}

$fp=fopen($printer, 'wb');
fwrite($fp, $string);
fclose($fp);

?>
