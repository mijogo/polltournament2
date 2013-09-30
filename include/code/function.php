<?php
function setCookies()
{
	$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
	$cad = "";
	for($i=0;$i<20;$i++) 
		$cad .= substr($str,rand(0,62),1);
	setcookie("CodePassVote",$cad,time()+(14*60*60*24));
	return $cad;
}

function fechaHoraActual($format="Y-m-d H:i:s")
{
 	date_default_timezone_set('UTC');
	return date($format);
}
function FechaMayor($fecha1,$fecha2)
{
	$corte1 = explode(" ",$fecha1);
	$Hora1 = explode(":",$corte1[1]);
	$Dia1 = explode("-",$corte1[0]);
	$corte2 = explode(" ",$fecha2);
	$Hora2 = explode(":",$corte2[1]);
	$Dia2 = explode("-",$corte2[0]);
	if($Hora1[0]==$Hora2[0]&&$Hora1[1]==$Hora2[1]&&$Hora1[2]==$Hora2[2]&&$Dia1[0]==$Dia2[0]&&$Dia1[1]==$Dia2[1]&&$Dia1[2]==$Dia2[2])
		return 0;
	
	if($Dia1[0]>$Dia2[0])
		return 1;
	else if($Dia1[0]<$Dia2[0])
		return -1;
	else if($Dia1[1]>$Dia2[1])
		return 1;
	else if($Dia1[1]<$Dia2[1])
		return -1;
	else if($Dia1[2]>$Dia2[2])
		return 1;
	else if($Dia1[2]<$Dia2[2])
		return -1;	
	else if($Hora1[0]>$Hora2[0])
		return 1;
	else if($Hora1[0]<$Hora2[0])
		return -1;
	else if($Hora1[1]>$Hora2[1])
		return 1;
	else if($Hora1[1]<$Hora2[1])
		return -1;
	else if($Hora1[2]>$Hora2[2])
		return 1;
	else if($Hora1[2]<$Hora2[2])
		return -1;	
}

function grafico($titulo="",$nombre="",$titulos="",$datos="")
{
	$grafic = "";
	$grafic .= "    <script type=\"text/javascript\" src=\"https://www.google.com/jsapi\"></script>
    <script type=\"text/javascript\">
      google.load(\"visualization\", \"1\", {packages:[\"corechart\"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([";
        for($i=0;$i<count($titulos);$i++)
        {
        	if($i==0)
        		$grafic .= "[";
        	$grafic .= "'".$titulos[$i]."'";
        	if($i==count($titulos)-1)
        		$grafic.="]";
        	$grafic.=",";
        }
        $grafic.="\n";
        for($j=0;$j<count($datos);$j++)
        {
        	for($i=0;$i<count($titulos);$i++)
       	 	{
        		if($i==0)
        			$grafic .= "[";
        		if($i==0)
        			$grafic .= "'".$datos[$j][$i]."'";
        		else
        			$grafic .= $datos[$j][$i];   			
        		if($i==count($titulos)-1)
        			$grafic.="]";
        		if(!($i==count($titulos)-1&&$j==count($datos)-1))
        			$grafic.=",";
        	}
        	$grafic .= "\n";
        }
        $grafic .= "]);

        var options = {
          title: '".$titulo."'
        };

        var chart = new google.visualization.LineChart(document.getElementById('".$nombre."'));
        chart.draw(data, options);
      }
    </script>
";
	return $grafic;
}

function GenerarSiguiente($actual,$Ronda)
{
	$topo = explode("-",$Ronda);
	if($topo[0] == "Ronda")
	{
		$actual = explode("-",$actual);
		$cantidad = configuracion($Ronda,"NBatalla")/configuracion(configuracion($Ronda,"nextRonda1"),"NBatalla");
		$actual[1]=$actual[1]/$cantidad;
		$actual[1]=$actual[1];
		$actual[1]=ceil($actual[1]);
		if($actual[1]<10)
			$actual[1] = "0".$actual[1];
		return $actual[0]."-".$actual[1];
	}
	else
	{
		$cantidad = configuracion($Ronda,"NGrupos")/configuracion(configuracion($Ronda,"nextRonda1"),"NGrupos");
		$actual=$actual/$cantidad;
		$actual=$actual;
		$actual=ceil($actual);
		if($actual<10)
			$actual = "0".$actual;
		return $actual;

	}
}

function Redireccionar($url="")
{
	echo "<head>
<script languaje=\"JavaScript\">
location.href='".$url."';
</script>
</head>";
}

function fechaGenerador($nombreFecha="")
{
	for($i=2013;$i<2030;$i++)
	{
		
		$anio[$i-2013][0]=$i;
		$anio[$i-2013][1]=$i;
	}
	for($i=1;$i<13;$i++)
	{
		if($i<10)
		{
			$mes[$i-1][0]="0".$i;
			$mes[$i-1][1]="0".$i;
		}
		else
		{
			$mes[$i-1][0]=$i;
			$mes[$i-1][1]=$i;
		}
	}
	for($i=1;$i<32;$i++)
	{
		if($i<10)
		{
			$dis[$i-1][0]="0".$i;
			$dis[$i-1][1]="0".$i;
		}
		else
		{
			$dis[$i-1][0]=$i;
			$dis[$i-1][1]=$i;
		}
	}
	for($i=0;$i<24;$i++)
	{
		if($i<10)
		{
			$hora[$i][0]="0".$i;
			$hora[$i][1]="0".$i;
		}
		else
		{
			$hora[$i][0]=$i;
			$hora[$i][1]=$i;
		}
	}
	for($i=0;$i<60;$i++)
	{
		if($i<10)
		{
			$min[$i][0]="0".$i;
			$min[$i][1]="0".$i;
		}
		else
		{
			$min[$i][0]=$i;
			$min[$i][1]=$i;
		}
	}
	for($i=0;$i<60;$i++)
	{
		if($i<10)
		{
			$seg[$i][0]="0".$i;
			$seg[$i][1]="0".$i;
		}
		else
		{
			$seg[$i][0]=$i;
			$seg[$i][1]=$i;
		}
	}
	return selected($nombreFecha."Anio",$anio)."-".selected($nombreFecha."Mes",$mes)."-".selected($nombreFecha."Dia",$dis)." ".selected($nombreFecha."Hora",$hora).":".selected($nombreFecha."Min",$min)." ".selected($nombreFecha."Seg",$seg);
}

function fechaGeneradorwoHora($nombreFecha="")
{
	for($i=2010;$i<2030;$i++)
	{
		
		$anio[$i-2010][0]=$i;
		$anio[$i-2010][1]=$i;
	}
	for($i=1;$i<13;$i++)
	{
		if($i<10)
		{
			$mes[$i-1][0]="0".$i;
			$mes[$i-1][1]="0".$i;
		}
		else
		{
			$mes[$i-1][0]=$i;
			$mes[$i-1][1]=$i;
		}
	}
	for($i=1;$i<32;$i++)
	{
		if($i<10)
		{
			$dis[$i-1][0]="0".$i;
			$dis[$i-1][1]="0".$i;
		}
		else
		{
			$dis[$i-1][0]=$i;
			$dis[$i-1][1]=$i;
		}
	}
	return selected($nombreFecha."Anio",$anio)."-".selected($nombreFecha."Mes",$mes)."-".selected($nombreFecha."Dia",$dis);
}


function cambioFecha($actual,$min)
{
	$actual = explode(" ",$actual);
	$fecha1 = explode("-",$actual[0]);
	$fecha2 = explode(":",$actual[1]);
	$timestamp = mktime($fecha2[0], $fecha2[1]+$min,$fecha2[2], $fecha1[1],$fecha1[2], $fecha1[0]);
    return date('Y-m-d H:i:s', $timestamp);
}

function getRealIP() 
{
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];
           
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
       
        return $_SERVER['REMOTE_ADDR'];
 }
 
function TransformDato($stringEntrada)
{
	$batallasB=explode(";",$stringEntrada);
	$valido=true;
	
	for($i=0;$i<count($batallasB)&&$valido;$i++)
	{
		$datosNa=explode("-",$batallasB[$i]);
		$nBatalla=substr($datosNa[0],1);
		$valoCom = explode("/",substr($datosNa[1],1));
		if($valoCom[0]>$valoCom[1])
			$valido=false;
		$contar[$nBatalla]["nVotos"]=$valoCom[0];
		if(($contar[$nBatalla]["nVotos"]!=0&&count($datosNa)!=$contar[$nBatalla]["nVotos"]+2)||($contar[$nBatalla]["nVotos"]==0&&count($datosNa)!=3))
			$valido=false;
		for($j=2;$j<count($datosNa)&&$valido;$j++)
		{
			if($j==2)
				$contar[$nBatalla][$j-2]=substr($datosNa[$j],1);
			else
				$contar[$nBatalla][$j-2]=$datosNa[$j];
		}
	}
	$regresar=array();
	$regresar[]=$valido;
	$regresar[]=$contar;
	return $regresar;
}

function fechaCorta($fecha)
{
	$fecha = explode(" ",$fecha);
	$fecha2=explode("-",$fecha[0]);
	return $fecha2[2]."/".$fecha2[1];
}

function cambioGrupo($grupo,$nActual,$nSiguiente,$tipo)
{
	if($tipo=="ELIMI")
	{
		$cantidad = $nActual/$nSiguiente;
		$actual=$actual/$cantidad;
		$actual=ceil($actual);
		return $actual;
	}
	elseif($tipo=="ELGRU")
	{
		$actual = explode("-",$actual);
		$cantidad = $nActual/$nSiguiente;
		$actual[1]=$actual[1]/$cantidad;
		$actual[1]=ceil($actual[1]);
		return $actual[0]."-".$actual[1];
	}
}
?>