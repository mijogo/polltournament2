<?php
//estadistica
//$NombreTablaL[0]="estadistica";
//$ElementosTablaL[0]=array("idpersonaje","idbatalla","fecha","votos");
//ip
//$NombreTablaL[0]="ip";
//$ElementosTablaL[0]=array("fecha","ip","codepass","forumcode","user","idevento","tiempo","usada","uniquecode","mastercode","masterip","info");
//log
//$NombreTablaL[0]="log";
//$ElementosTablaL[0]=array("iduser","accion","fecha","estado","ip","accioncompleta");
//menu
//$NombreTablaL[0]="menu";
//$ElementosTablaL[0]=array("id","dependencia","titulo","tituloingles","url","descripcion");
//participacionBD
//$NombreTablaL[0]="participacion";
//$ElementosTablaL[0]=array("idpersonaje","idbatalla");
//pelea
//$NombreTablaL[0]="pelea";
//$ElementosTablaL[0]=array("idpersonaje","idbatalla","votos");
//personaje
//$NombreTablaL[0]="personaje";
//$ElementosTablaL[0]=array("id","nombre","serie","imagen","idserie","nparticipaciones","mejorpos");
//personajepar
//$NombreTablaL[0]="personajepar";
//$ElementosTablaL[0]=array("id","nombre","serie","idpersonaje","idserie","imagenpeq","imagen","idtorneo","estado","grupo","ronda","seiyuu","ponderacion");
//torneo
//$NombreTablaL[0]="torneo";
//$ElementosTablaL[0]=array("id","ano","version","nombre","activo","estado","duracionbatalla","extraconteo","nominaciones","intervalo","horainicio","duracionlive","maxmiembrosgraf","opcionpartida","limitadoractivo","duracionlimitador","porcentajelimite");
//usuario
$NombreTablaL[0]="usuario";
$ElementosTablaL[0]=array("idusuario","poder","facecode","facecodeex","twittercode","twittercodeex","extracode","extracodeex");
for($mn =0; $mn < count($NombreTablaL);$mn++)
{
$NombreTabla = $NombreTablaL[$mn];
$ElementosTabla = $ElementosTablaL[$mn];
$text = "";
$text = "<?php
require_once \"".$NombreTabla."BD.php\";
class ".$NombreTabla." extends ".$NombreTabla."BD
{
	function ".$NombreTabla."(";
	for($i=0;$i<count($ElementosTabla);$i++)
	{
		$text .= "$".$ElementosTabla[$i]."=\"\"";
		if($i!=count($ElementosTabla)-1)
			$text.=",";
	}
	$text .=")\n	{\n";
	
	for($i=0;$i<count($ElementosTabla);$i++)
	{
		$text .= "		\$this->".$ElementosTabla[$i]." = $".$ElementosTabla[$i].";\n";
	}


$text.="	}\n";

	for($i=0;$i<count($ElementosTabla);$i++)
	{
		$text .= "	function set".$ElementosTabla[$i]."($".$ElementosTabla[$i].")
	{
		\$this->".$ElementosTabla[$i]."=$".$ElementosTabla[$i].";
	}
";
		$text .= "	function get".$ElementosTabla[$i]."()
	{
		return \$this->".$ElementosTabla[$i].";
	}
";
}
$text.="}?>\n\n";

$fp = fopen("include/class/".$NombreTabla.".php", 'w');
fwrite($fp, $text);
fclose($fp);

$text ="<?php\n";
$text .="require_once \"DataBase.php\";
class ".$NombreTabla."BD extends DataBase
{
	function ".$NombreTabla."BD(){}
	
	function save()
	{";
$text.="		\$sql = \"INSERT INTO ".$NombreTabla." (";
for($i=0;$i<count($ElementosTabla);$i++)
{
	$text.=$ElementosTabla[$i];
	if($i!=count($ElementosTabla)-1)
		$text .= ",";
}
$text.=") VALUES 
		(\n";
for($i=0;$i<count($ElementosTabla);$i++)
{
	$text.="		'\".\$this->".$ElementosTabla[$i].".\"'";
	if($i!=count($ElementosTabla)-1)
		$text .= ",\n";
}
$text.=")\";
		return \$this->insert(\$sql);
	}

	function read(\$multi=true , \$cantConsulta = 0 , \$Consulta = \"\" , \$cantOrden = 0 , \$Orden = \"\" , \$consultaextra=\"\")
	{
		\$sql=\"SELECT * FROM ".$NombreTabla." \";
		if(\$consultaextra==\"\")
			if(\$cantConsulta != 0)
			{
				\$sql .= \"WHERE \";
				for(\$i=0;\$i<\$cantConsulta;\$i++)
				{
					\$sql .= \$Consulta[\$i*2].\" = '\".\$this->\$Consulta[\$i*2].\"' \";
					if(\$i != \$cantConsulta-1)
						\$sql .= \$Consulta[\$i*2+1].\" \";
				}
			}
		else
			\$sql=\"WHERE \".\$consultaextra;
		
		if(\$cantOrden != 0)
		{
			\$sql .= \"ORDER BY \";
			for(\$i=0;\$i<\$cantOrden;\$i++)
			{
				\$sql .= \$Orden [\$i*2].\" \".\$Orden [\$i*2+1].\" \";
				if(\$i != \$cantOrden-1)
					\$sql .= \",\";
			}
		}
		if(\$multi)
		{
			\$result = \$this->select(\$sql);
			\$".$NombreTabla."s = array();
			while(\$row = \$this->fetch(\$result))
			{
				\$i=0;
				\$".$NombreTabla."s[]=new ".$NombreTabla."(";
			for($i=0;$i<count($ElementosTabla);$i++)
			{
				$text.= "\$row[\$i++]";
				if($i!=count($ElementosTabla)-1)
					$text.= ",";
			}
			$text.=");
			}
			return \$".$NombreTabla."s;
		}
		else
		{
			\$result = \$this->select(\$sql);
			\$row = \$this->fetch(\$result);
			\$i=0;
			\$".$NombreTabla."s= new ".$NombreTabla."(";
			for($i=0;$i<count($ElementosTabla);$i++)
			{
				$text.= "\$row[\$i++]";
				if($i!=count($ElementosTabla)-1)
					$text.= ",";
			}
			$text.=");
			return $".$NombreTabla."s;
		}
	}
	
	function update(\$cantSet = 0 , \$Set = \"\" , \$cantConsulta = 0 , \$Consulta= \"\")
	{
		\$sql=\"UPDATE ".$NombreTabla." \";
		if(\$cantSet != 0)
		{
			\$sql .= \"SET \";
			for(\$i=0;\$i<\$cantSet;\$i++)
			{
				\$sql .= \$Set[\$i].\" = '\".\$this->\$Set[\$i].\"' \";
				if(\$i != \$cantSet-1)
					\$sql .= \",\";
			}
		}
		
		if(\$cantConsulta != 0)
		{
			\$sql .= \"WHERE \";
			for(\$i=0;\$i<\$cantConsulta;\$i++)
			{
				\$sql .= \$Consulta[\$i*2].\" = '\".\$this->\$Consulta[\$i*2].\"' \";
				if(\$i != \$cantConsulta-1)
					\$sql .= \$Consulta[\$i*2+1].\" \";
			}
		}
		return \$this->insert(\$sql);
	}
	
	function delete(\$cantConsulta = 0 , \$Consulta = \"\")
	{
		\$sql = \"DELETE FROM ".$NombreTabla." \";
		if(\$cantConsulta != 0)
		{
			\$sql .= \"WHERE \";
			for(\$i=0;\$i<\$cantConsulta;\$i++)
			{
				\$sql .= \$Consulta[\$i*2].\" = '\".\$this->\$Consulta[\$i*2].\"' \";
				if(\$i != \$cantConsulta-1)
					\$sql .= \$Consulta[\$i*2+1].\" \";
			}
		}
		return \$this->insert(\$sql);
	}
}
?>";
echo $NombreTabla." done <br>";
$fp = fopen("include/class/".$NombreTabla."BD.php", 'w');
fwrite($fp, $text);
fclose($fp);
}
?>