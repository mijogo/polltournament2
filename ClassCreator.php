<?php
$NombreTabla="batalla";
$ElementosTabla=array();
$ElementosTabla[]="id";
$ElementosTabla[]="fecha";
$ElementosTabla[]="ronda";
$ElementosTabla[]="grupo";
$ElementosTabla[]="idtorneo";
$ElementosTabla[]="estado";
$ElementosTabla[]="numerovotos";
$ElementosTabla[]="ganador";
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

echo $text;

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

	function read(\$multi=true , \$cantConsulta = 0 , \$Consulta = \"\" , \$cantOrden = 0 , \$Orden = \"\" , , \$consultaextra=\"\")
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
echo $text;
$fp = fopen("include/class/".$NombreTabla."BD.php", 'w');
fwrite($fp, $text);
fclose($fp);

?>