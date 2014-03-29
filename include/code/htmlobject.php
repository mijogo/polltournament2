<?php
function refresh($time)
{
	return "<meta http-equiv=\"refresh\" content=\"".$time."\">";
}

function div($content ="",$id="",$class="",$style="")
{
	$text = "<div";
	if($id!="")
		$text .= " id=\"$id\" ";
	if($class!="")
		$text .= " class=\"$class\" ";
	if($style!="")
		$text .= " style=\"$style\" ";
	$text .= ">\n";
	$text .= $content;
	$text .= "\n</div>";
	return $text;
}

function form($content="",$name="",$action="",$extra="",$method="POST")
{
	$text = "<form name=\"".$name."\" action=\"".$action."\" method=\"".$method."\" ".$extra.">
	".$content."
	</form>";
	return $text;
}

function table($datos,$width="")
{
	$text ="<table>\n";
	if($width!="")
	{
		$cuantos=explode(";",$width);
		for($i=0;$i<count($cuantos);$i++)
			$cuantos[$i]=explode("-",$cuantos[$i]);
	}
	for($i=0;$i<count($datos);$i++)
	{
		$text .="<tr>\n";
		for($j=0;$j<count($datos[$i]);$j++)
		{
			if($i==0 && $width!="")
			{
				$hay=0;
				for($k=0;$k<count($cuantos);$k++)
				{
					if($cuantos[$k][0]==$j)
					{
						$text .="<td width=\"".$cuantos[$k][1]."px\">".$datos[$i][$j]."</td>\n";
						$hay++;
					}
				}
				if($hay==0)
					$text .="<td>".$datos[$i][$j]."</td>\n";
			}
			else
				$text .="<td>".$datos[$i][$j]."</td>\n";
		}
		$text .="</tr>\n";
	}
	$text .="</table>\n";
	return $text;
}
function input($nombre,$tipo,$value="",$class="",$extra="")
{
	$text = "<input type=\"".$tipo."\" name=\"".$nombre."\"";
	if($value != "")
		$text .= " value=\"".$value."\"";
	if($class!= "")
		$text .= " class=\"".$class."\"";
	$text .=" ".$extra.">";
	return $text;
}

function selected($name ="",$values="",$extra="")
{
	$text = "";
	$text .="<SELECT NAME=\"".$name."\" ".$extra." >";
	for($i=0;$i<count($values);$i++)
	{
		$text .="<OPTION VALUE=\"".$values[$i][0]."\">".$values[$i][1];
	}
	$text .="</SELECT>";
	return $text;
}

function botonVoto($idBatalla,$idPersonaje,$idPersonajesBatalla,$content)
{
	$text = "";
	$text .="<div id=\"B".$idBatalla."-".$idPersonaje."\" class=\"botoncito\">
<button class=\"button\" id =\"R".$idPersonaje."\" onclick=\"change('B".$idBatalla."-".$idPersonaje."','".$idPersonajesBatalla."')\">
".$content."
</button>
</div>";
	return $text;
}

function botonAct($content)
{
	$text = "";
	$text .="<div class=\"botoncito\">
<button class=\"buttonAct\">
".$content."
</button>
</div>";
	return $text;
}

function botonEscoger($content,$instancia,$cantidad)
{
	$text = "";
	$text .="<div class=\"botoncito\">
<button class=\"button\" onclick=\"Instancia('".$instancia."','".$cantidad."')\">
".$content."
</button>
</div>";
	return $text;
}

function formVoto($action,$batallas,$limite,$activado=true)
{
	$text = "";
	$nameForm = "Votar";
	$text .="<form name=\"".$nameForm."\" action=\"".$action."\" method=\"post\">
<input type=\"hidden\" value=\"";
for($i=0;$i<count($batallas);$i++)
{
	$text .="B".$batallas[$i]."-L0/".$limite."-V";
	if($i+1!=count($batallas))
		$text .=";";
}
$text .="\" name=\"votacion\" />";
if($activado)
	$text .= input("Enviar","submit","Votar","subboto");
$text .="</form>
";
	return $text;
}

function Nominaciones($cant,$Admin=false)
{
	$text ="";
	$text .="
<h1>Nominaciones</h1>
<div class=\"fight\">
";
	$datos[0][0]="Nombre";
	$datos[0][1]="Serie";
for($i=0;$i<$cant;$i++)
{
	$datos[$i+1][0]=input("Nombre[]","text","","","size=\"25\"");
	$datos[$i+1][1]=input("Serie[]","text","","","size=\"35\"");
}
$datos[$cant+1][0]="";
$datos[$cant+1][1]=input("Enviar","submit","Enviar","subboto");
$text1 = table($datos,"0-200");
if($Admin)
	$text .= form($text1,"inscipcion","?id=5&action=2&trato=1");
else
	$text .= form($text1,"inscipcion","?id=5&action=2&trato=1");
$text .="</div>";
return $text;
}

function img($src,$height="",$width="",$id="",$class="")
{
	$text = "<img src=\"".$src."\" alt=\"\" ";
	if($id!="")
		$text .= " id=\"$id\" ";
	if($class!="")
		$text .= " class=\"$class\" ";
	if($height!="")
		$text .= " height=\"$height\" ";
	if($width!="")
		$text .= " width=\"$width\" ";

	$text .= "/>\n";
	return $text;
	}
?>