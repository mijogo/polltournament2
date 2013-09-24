<?php
class Schedule
{
	function Schedule(){}
	
	function run()
	{
		/*
		number ID
		inscripcion 1,sorteo 2,activar Batalla 3, conteo votos 4,cambiar estado torneo 5,crear Batallas 6
		*/
		$process = new Schedule();
		$process ->sethecho(-1);
		$process=$process->read(true,1,array("hecho"),1,array("fecha","ASC")); 
		$fechaActual = fechaHoraActual();
		$sigue=true;
		for($i=0;$i<count($process)&&$sigue;$i++)
		{
			if(FechaMayor($fechaActual,$process[$i]->getfecha())!=-1)
			{
				if($process[$i]->getaccion()=="SORTE")
				{
					$target = explode(",",$process[$i]->gettargetstring());
					$this->sorteo($target[0],$target[1],$target[2],$target[3]);
				}
				if($process[$i]->getaccion()=="ACTBA")
				{
					$this->activarBatalla($process[$i]->gettargetdate());
				}
				if($process[$i]->getaccion()=="CONVO")
				{
					$this->ConteoVotos();
				}
				if($process[$i]->getaccion()=="CHTOR")
				{
					$this->changeChampionship($process[$i]->gettargetint());
				}
				if($process[$i]->getaccion()=="CHEVE")
				{
					$this->changeEvento($process[$i]->gettargetint());
				}
				if($process[$i]->getaccion()=="CALPO")
				{
					$this->calcularPonderacion();
				}
				
				if($process[$i]->getaccion()=="INMAT")
				{
					$this->ingresarMatch();
				}			
				$process[$i]->setHecho(1);
				$process[$i]->update(1,array("hecho"),1,array("id"));
				$lognew = new log(-10,$process[$i]->getaccion(),$fechaActual,1,"system",$process[$i]->gettargetstring()." ".$process[$i]->gettargetdate()." ".$process[$i]->gettargetint());
				$lognew->save(); 
			}
			else
				$sigue=false;
		}
	}
	function sorteo($instancia="",$numeroGrupo="",$tipo=0,$seed=0)
	{
		if($tipo == "ELIMI")
		{		
			$torneoActual = new torneo();
			$torneoActual->setactivo(1);
			$torneoActual = $torneoActual->read(false,1,array("activo"));
			
			$newConf = new configuracion();
			$newConf->setnombre($instancia);
			$newConf->setidtorneo($torneoActual->getid());
			$newConf = $newConf->read(false,2,array("nombre","idtorneo"));
			
			$personajeTotal = new personajepar();
			$personajeTotal->setronda($instancia);
			$personajeTotal = $personajeTotal->read(true,1,array("ronda"));
			$numeroTotal = count($personajeTotal);
			$cantidadGrupo = floor($numeroTotal/$newConf->getnumerogrupos());
			if(($personajeTotal-($cantidadGrupo*$newConf->getnumerogrupos()))>=$numeroGrupo)
				$cantidadGrupo++;
				
			$perListos = new personajepar()
			$perListos->setronda($instancia);
			$perListos->setgrupo($numeroGrupo);
			$perListos->setidtorneo($torneoActual->getid());
			$perListos = $perListos->read(true,3,array("ronda","AND","grupo","AND","idtorneo"));
			$cantidadListos = count($perListos);
			
			$personajesSortear = new personajepar();
			$personajesSortear->setronda($instancia);
			$personajesSortear->setgrupo("N");
			$personajesSortear->setidtorneo($torneoActual->getid());
			$personajesSortear = $personajesSortear->read(true,3,array("ronda","AND","grupo","AND","idtorneo"));
			
			for($i=0;$i<count($personajesSortear);$i++)
			{
				$perSort[$i]["prox"]=($i+1)%count($personajesSortear);
				$perSort[$i]["ant"]=($i-1)%count($personajesSortear);
				$perSort[$i]["act"]=0;
			}
			if($seed==1)
			{
				$totalPonderacion = 0;
				for($i=0;$i<$cantidadListos;$i++)
					$totalPonderacion += $perListos[$i]->getponderacion();
				
				for($i=$cantidadListos;$i<$cantidadGrupo;$i++)
				{
					$escoger=rand(0,count($personajesSortear));
					$sigue=true;
					while($sigue)
					{
						if($perSort[$escoger]["act"]==1)
							$escoger=$perSort[$escoger]["prox"];
						else
						{
							$porcentaje = ($totalPonderacion+$personajesSortear[$escoger]->getponderacion())/($cantidadListos+$i+1);
							$porcentaje = abs(($porcentaje-$torneoActual->getponderacionprom())/$torneoActual->getponderacionprom())
							$porcentaje = 1000 - $porcentaje*1000;
							if($porcentaje<0)
								$escoger=$perSort[$escoger]["prox"];
							else
							{
								$res = rand(0,1000);
								if($res<$porcentaje)
								{
									$personajesSortear[$escoger]->setgrupo($numeroGrupo);
									$personajesSortear[$escoger]->update(1,array("grupo"),1,array("id"))
									$perSort[$escoger]["act"]=1;
									$temSort = $perSort[$escoger];
									
									$tempProx=$perSort[$escoger]["prox"];
									while($perSort[$tempProx]["act"]==1&&$perSort[$tempMov]["prox"]!=$escoger)
										$tempProx=$perSort[$tempMov]["prox"];
									$tempAnt=$perSort[$escoger]["ant"];
									while($perSort[$tempAnt]["act"]==1&&$perSort[$tempMov]["ant"]!=$escoger)
										$tempAnt=$perSort[$tempAnt]["ant"];
										
									$perSort[$escoger]["prox"]=$tempProx;
									$perSort[$escoger]["ant"]=$tempAnt;
									$perSort[$tempAnt]["prox"]=$tempProx;
									$perSort[$tempProx]["ant"]=$tempAnt;
									$sigue=false;
									
									$totalPonderacion += $personajesSortear[$escoger]->getponderacion();							
								}
								else
									$escoger=$perSort[$escoger]["prox"];
							}
						}
					}
				}
			}
			else
			{
				for($i=$cantidadListos;$i<$cantidadGrupo;$i++)
				{
					$escoger=rand(0,count($personajesSortear));
					$sigue=true;
					while($sigue)
					{
						if($perSort[$escoger]["act"]==1)
							$escoger=$perSort[$escoger]["prox"];
						else
						{
							$personajesSortear[$escoger]->setgrupo($numeroGrupo);
							$personajesSortear[$escoger]->update(1,array("grupo"),1,array("id"))
							$perSort[$escoger]["act"]=1;
							$temSort = $perSort[$escoger];
									
							$tempProx=$perSort[$escoger]["prox"];
							while($perSort[$tempProx]["act"]==1)
								$tempProx=$perSort[$tempMov]["prox"]
							$tempAnt=$perSort[$escoger]["ant"];
							while($perSort[$tempAnt]["act"]==1)
								$tempAnt=$perSort[$tempAnt]["ant"]
										
							$perSort[$escoger]["prox"]=$tempProx;
							$perSort[$escoger]["ant"]=$tempAnt;
							$perSort[$tempAnt]["prox"]=$tempProx;
							$perSort[$tempProx]["ant"]=$tempAnt;
							$sigue=false;
						}
					}
				}
			}
		}
		elseif($tipo == "ELGRU")
		{
			$torneoActual = new torneo();
			$torneoActual->setactivo(1);
			$torneoActual = $torneoActual->read(false,1,array("activo"));
			
			$newConf = new configuracion();
			$newConf->setnombre($instancia);
			$newConf->setidtorneo($torneoActual->getid());
			$newConf = $newConf->read(false,2,array("nombre","idtorneo"));
			
			$personajeTotal = new personajepar();
			$personajeTotal->setronda($instancia);
			$personajeTotal = $personajeTotal->read(true,1,array("ronda"));
			$numeroTotal = count($personajeTotal);
			
			$totalDelGrupo = 0;
			$cantidadListos = 0;
			$totalPonderacion= 0;
			for($i=0;$i<$newConf->getnumerobatallas();$i++)
			{
				$datosGrupo[$i]["cantidad"] = floor($numeroTotal/($newConf->getnumerogrupos()*$newConf->getnumerobatallas()));
				$datosGrupo[$i]["nombre"] = $numeroGrupo."-".($i+1);
				if(($personajeTotal-($cantidadGrupo*$newConf->getnumerogrupos()))>=convertNumber($numeroGrupo)+$i*$newConf->getnumerogrupos())
					$datosGrupo[$i]["cantidad"]++;
				$totalDelGrupo += $datosGrupo[$i]["cantidad"];
				
				$perListos = new personajepar()
				$perListos->setronda($instancia);
				$perListos->setgrupo($datosGrupo[$i]["nombre"]);
				$perListos->setidtorneo($torneoActual->getid());
				$perListos = $perListos->read(true,3,array("ronda","AND","grupo","AND","idtorneo"));
				$cantidadListos += count($perListos);
				if(count($perListos)>0&&$seed==1)
					for($j=0;$j<count($perListos);$j++)
						$totalPonderacion+=$perListos[$j]->getponderacion();
			}
			$personajesSortear = new personajepar();
			$personajesSortear->setronda($instancia);
			$personajesSortear->setgrupo("N");
			$personajesSortear->setidtorneo($torneoActual->getid());
			$personajesSortear = $personajesSortear->read(true,3,array("ronda","AND","grupo","AND","idtorneo"));
			for($i=0;$i<count($personajesSortear);$i++)
			{
				$perSort[$i]["prox"]=($i+1)%count($personajesSortear);
				$perSort[$i]["ant"]=($i-1)%count($personajesSortear);
				$perSort[$i]["act"]=0;
			}
			if($seed==1)
			{
				for($i=$cantidadListos;$i<$totalDelGrupo;$i++)
				{
							
					$escoger=rand(0,count($personajesSortear));
					$sigue=true;
					while($sigue)
					{
						if($perSort[$escoger]["act"]==1)
							$escoger=$perSort[$escoger]["prox"];
						else
						{
							$porcentaje = ($totalPonderacion+$personajesSortear[$escoger]->getponderacion())/($cantidadListos+$i+1);
							$porcentaje = abs(($porcentaje-$torneoActual->getponderacionprom())/$torneoActual->getponderacionprom())
							$porcentaje = 1000 - $porcentaje*1000;
							if($porcentaje<15)
								$porcentaje=15;
							$res = rand(0,1000);
							if($res<$porcentaje)
							{
								$sorGrupo[$i-$cantidadListos]["prox"]=($i-$cantidadListos+1)%(count($totalDelGrupo)-$cantidadListos);
								$sorGrupo[$i-$cantidadListos]["ant"]=($i-$cantidadListos-1)%(count($totalDelGrupo)-$cantidadListos);
								$sorGrupo[$i-$cantidadListos]["act"]=0;
								$sorGrupo[$i-$cantidadListos]["id"]=$escoger;

								$perSort[$escoger]["act"]=1;
								$temSort = $perSort[$escoger];
											
								$tempProx=$perSort[$escoger]["prox"];
								while($perSort[$tempProx]["act"]==1&&$perSort[$tempMov]["prox"]!=$escoger)
									$tempProx=$perSort[$tempMov]["prox"];
								$tempAnt=$perSort[$escoger]["ant"];
								while($perSort[$tempAnt]["act"]==1&&$perSort[$tempMov]["ant"]!=$escoger)
									$tempAnt=$perSort[$tempAnt]["ant"];
												
								$perSort[$escoger]["prox"]=$tempProx;
								$perSort[$escoger]["ant"]=$tempAnt;
								$perSort[$tempAnt]["prox"]=$tempProx;
								$perSort[$tempProx]["ant"]=$tempAnt;
								$sigue=false;
											
								$totalPonderacion += $personajesSortear[$escoger]->getponderacion();							
							}
							else
								$escoger=$perSort[$escoger]["prox"];
						}
					}
				}
				$datosGrupo[$i]["cantidad"]
				for($i=0;$i<count($datosGrupo);$i++)
				{
					
				}
			}
		}
	}
	function activarBatalla($fecha="")
	{}
	function ConteoVotos()
	{}
	function changeChampionship($nuevoEstado="")
	{}
	function changeEvento($nuevoEstado="")
	{}
	function calcularPonderacion()
	{}

}
?>