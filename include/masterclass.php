<?php
require_once "include.php";
class MasterClass
{
	function MasterClass()
	{
		if(!isset($_GET['id']))
			$this->id_pagina =1;
		else
			$this->id_pagina = $_GET['id'];
		
		if(!isset($_GET['accion']))
			$this->accion=1;
		else
			$this->accion = $_GET['accion'];
		
		if(!isset($_GET['tipo']))
			$this->tipo=1;
		else
			$this->tipo = $_GET['tipo'];
			
		if(!isset($_GET['nivel']))
		{
			$BG = new DataBase();
			$BG->connect();
			$a_menu = new menu();
			$a_menu->setid($this->id_pagina);
			$a_menu = $a_menu->read(true,1,array("id"));
			if(count($a_menu) == 0)
				$this->nivel=-1;
			else
			{	
				
				$b_menu = new menu();
				$b_menu->setid($a_menu[0]->getdependencia());
				$b_menu = $b_menu->read(true,1,array("id"));
				if(count($b_menu) == 0)
					$this->nivel=$a_menu[0]->getdependencia();
				else
					$this->nivel=$b_menu[0]->getdependencia();
			}
			$BG->close();
		}
		else
			$this->nivel = $_GET['nivel'];
	}
	// id numero pagina, tipo a que pagina se refiere, action, tipo de accion
	function Trabajar()
	{
		$BG = new DataBase();
		$BG->connect();
		//$this->torneoActual();
		//$this->usuarioACC();
		if($this->accion == 1)
		{
			$file = fopen("include/estructura.html", "r") or exit("Unable to open file!");
			//Output a line of the file until the end is reached
			$pagina="";
			while(!feof($file))
			{
				$pagina .= fgets($file);
			}
			$logicaU = new logicv();
			$datos = $logicaU->logicaView($this->id_pagina,$this->tipo);
			echo ingPagina($pagina,$this->menu_u(),$datos[0],$datos[1],widget());
		}
		if($this->accion == 2)
		{
			$logicaU = new logicc();
			$datos = $logicaU->trabaja($this->id_pagina,$this->tipo);
			Redireccionar($datos);	
		}
		$BG->close();
	}
	
	function menu_u()
	{
		$b_menu = new menu();
		$b_menu->setdependencia($this->nivel);
		$b_menu = $b_menu->read(true,1,array("dependencia"));
		$k=0;
		$datos = array("");
		for($i=0;$i<count($b_menu);$i++)
		{
			$datos[$k][0] = $b_menu[$i]->getdependencia();
			$datos[$k][1] = $b_menu[$i]->getid();
			$datos[$k][2] = $b_menu[$i]->gettitulo();
			if($this->id_pagina == $b_menu[$i]->getid())
				$datos[$k][3] = 1;
			else
				$datos[$k][3] = 0;
			$datos[$k][5] = $b_menu[$i]->geturl();
			$c_menu = new menu();
			$c_menu->setdependencia($b_menu[$i]->getid());
			$c_menu = $c_menu->read(true,1,array("dependencia"));
			if(count($c_menu)==0)
			{
				$datos[$k][4] = 0;
				$k++;
			}
			else
			{
				$datos[$k][4] = 1;
				$k++;
				for($j=0;$j<count($c_menu);$j++)
				{
					$datos[$k][0] = $c_menu[$j]->getdependencia();
					$datos[$k][1] = $c_menu[$j]->getid();
					$datos[$k][2] = $c_menu[$j]->gettitulo();
					if($this->id_pagina == $c_menu[$j]->getid())
						$datos[$k][3] = 1;
					else
						$datos[$k][3] = 0;
					$datos[$k][5] = $c_menu[$j]->geturl();
					$datos[$k][4] = 0;
					$k++;
				}
			}
		}
		return menu_html($datos,$this->nivel);
	}
	
	function torneoActual()
	{
		$torneoActual = new torneo();
		$torneoActual->setactivo(1);
		$torneoActual = $torneoActual->read(true,1,array("activo"));
		if(count($torneoActual)>0)
		{
			$this->torneoActivo=true;
			$this->torneoActual = $torneoActual[0];
		}
		else
			$this->torneoActivo=false;
	}
	
	function usuarioACC()
	{
		if(isset($_COOKIE['phpbb3_tdqhk_sid']))
		{
			$sesionForo = new phpbb_sessions();
			$sesionForo->setsession_id($_COOKIE['phpbb3_tdqhk_sid']);
			$sesionForo = $sesionForo->read(false,1,array("session_id"));
			
			$userForo = new phpbb_users();
			$userForo->setuser_id($sesionForo->getsession_user_id());
			$userForo = $userForo->read(false,1,array("user_id"));
			
			if($userForo->getusername()!="Anonymous")
			{
				$this->activoForo=true;
				$this->userForo = $userForo;
			}
			else
				$this->activoForo=false;
		}
		else
			$this->activoForo=false;
		
		if($this->activoForo)
		{
			$userpage = new usuario();
			$userpage->setidusuario($this->userForo->getuser_id());
			$userpage = $userpage->read(true,1,array("idusuario"));
			if(count($userpage)==0)
			{
				$userpage = new usuario();
				$userpage->setidusuario($this->userForo->getuser_id());
				$userpage->setpoder(0);
				$userpage->save();
			}
		}
		
		if(isset($_COOKIE['CodePassVote']))
		{
			$this->userAnterior=true;
			$this->cookies=$_COOKIE['CodePassVote'];
			setcookie("CodePassVote",$this->cookies,time()+(14*60*60*24));
		}
		else
		{
			$this->userAnterior=false;
			$this->cookies=setCookies();
		}
		
		$evetoActual = new evento();
		$evetoActual->setestado(1);
		$evetoActual = $evetoActual->read(true,1,array("estado"));
		
		if(count($evetoActual)>0)
		{
			$ipcreada = false;
			$evetoActual = $evetoActual[0];
			if(isset($_COOKIE['uniqueCode']))
			{
				$estaIp = new ip();
				$estaIp->setuniquecode($_COOKIE['uniqueCode']);
				$estaIp = $estaIp->read(false,1,array("uniquecode"));
				if($estaIp->getidevento()==$evetoActual->getid())
				{
					$ipcreada=true;
					if($this->activoForo && $estaIp->gettiempo()>0)
					{
						$estaIp->settiempo(0);
						$estaIp->setuser($this->userForo->getuser_id());
						$estaIp->update(1,array("tiempo","user"),1,array("uniquecode"));
					}
				}
			}
			if(!$ipcreada)
			{
				$this->newUniqueCode = $this->cookies."-".$evetoActual->getid();
				$this->ip = getRealIP();
				$this->crearIp();
			}
			//analizar datos anteriores
			$ipUsadas=new ip();
			$ipUsadas->setcodepass($this->cookies);
			$ipUsadas->setip($this->ip);
			$ipUsadas->setidevento($evetoActual->getid());
			if($this->activoForo)
			{
				$ipUsadas->setuser($this->userForo->getuser_id());
				$ipUsadas = $ipUsadas->read(true,0,"",1,array("fecha","ASC")," idevento = ".$ipUsadas->getidevento()." AND (codepass = '".$ipUsadas->getcodepass()."' OR ip = '".$ipUsadas->getip()."' OR user = ".$ipUsadas->getuser().") ");
			}
			else
				$ipUsadas = $ipUsadas->read(true,0,"",1,array("fecha","ASC")," idevento = ".$ipUsadas->getidevento()." AND (codepass = '".$ipUsadas->getcodepass()."' OR ip = '".$ipUsadas->getip()."') ");
			$mastercode="";
			$masterip="";
			$usado=0;
			for($i=0;$i<count($ipUsadas);$i++)
			{
				if($i==0)
				{
					$mastercode = $ipUsadas[$i]->getmastercode();
					$masterip = $ipUsadas[$i]->getmasterip();			
				}
				if($ipUsadas[$i]->getusada()>0)
					 $usado=1;
			}
			for($i=0;$i<count($ipUsadas);$i++)
			{
				if($ipUsadas[$i]->getusada()==0 && ($ipUsadas[$i]->getmastercode() !=  $mastercode||$ipUsadas[$i]->getmasterip() !=  $masterip))
				{
					$ipUsadas[$i]->setmastercode($mastercode);
					$ipUsadas[$i]->setmasterip($masterip);
					if($usado==0)
						$ipUsadas[$i]->update(2,array("mastercode","masterip"),1,array("uniquecode"));
					else
					{
						$ipUsadas[$i]->setusada(3);
						$ipUsadas[$i]->update(3,array("mastercode","masterip","usada"),1,array("uniquecode"));
					}
				}
			}
		}
	}
	
	function crearIp($MasterCode="",$MasterIp="",$tipoUsada="")
	{
		$creaIp = new ip();
		$creaIp->setfecha(fechaHoraActual());
		$creaIp->setip($this->ip);
		if($this->userAnterior)
			$creaIp->setTiempo(30);
		else
		{
			$buscIP = new ip();
			$buscIP->setip($this->ip);
			$buscIP->setcodepass($this->cookies);
			$buscIP->setusada(1);
			$buscIP = $buscip->read(true,3,array("ip","AND","codepass","AND","usada"));
			if(count($buscIP)>0)
				$creaIp->setTiempo(0);
			else
			{
				$buscIP = new ip();
				$buscIP->setcodepass($this->cookies);
				$buscIP->setusada(1);
				$buscIP = $buscIP->read(true,2,array("codepass","AND","usada"));
				$valor = count($buscIP);
				if($valor == 0)
					$creaIp->settiempo(25);
				else if($valor>15)
					$creaIp->settiempo(rand(5,20));
				else
					$creaIp->settiempo(rand(20-$valor,20));
			}
		}
		if($tipoUsada=="")
			$creaIp->setUsada(0);
		else
			$creaIp->setUsada($tipoUsada);

		$creaIp->setCodePass($this->cookies);	
			
		if($MasterCode=="")
			$creaIp->setMasterCode($this->cookies);
		else
			$creaIp->setMasterCode($MasterCode);
						
		if($MasterIp=="")
			$creaIp->setMasterIP($this->ip);
		else
			$creaIp->setMasterIP($MasterIp);
		
		/*$ipBan = new ip();
		$ipBan->setUsada(8);
		$ipBan = $ipBan->read(true,1,array("Usada"));
		for($i=0;$i<count($ipBan);$i++)
		{
			if($ipBan[$i]->getCodePass()==$creaIp->getCodePass()||$ipBan[$i]->getIp()==$creaIp->getIp())
			{
				$creaIp->setTiempo(720);
			}
		}*/
		$extraInfo = $_SERVER['HTTP_USER_AGENT'];
		$creaIp->setinfo($extraInfo);
		if($this->activoForo)
			$creaIp->setuser($this->userForo->getuser_id());
		else
			$creaIp->setuser(-1);
		$creaIp->setuniquecode($this->newUniqueCode);
		$creaIp->save();
		setcookie("uniqueCode",$this->newUniqueCode,time()+(2*60*60*24));
		return $creaIp;
	}
}
?>