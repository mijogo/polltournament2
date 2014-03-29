<?php
class logicv
{
	function logicv()
	{
	}
	
	function logicaView($id_pagina,$tipo_pagina)
	{
		$paginas[0]=1;
		if($id_pagina == $paginas[0])
		{
			$texto[0]="";
			$texto[1]="";
			return $texto;
		}
	}
}