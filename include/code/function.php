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
?>