<?php

	include("config.php");
	$coder = new alphanumaricAsciiConverter;

	$char0 = $_GET["char0"];
	$char1 = $_GET["char1"];
	$char2 = $_GET["char2"];
	$char3 = $_GET["char3"];
	$digit0= $_GET["digit0"];
	$digit1= $_GET["digit1"];
	$digit2= $_GET["digit2"];
	$digit3= $_GET["digit3"];
	
	$array0 = array($char0,$char1,$char2,$char3);
	$array1 = array($digit0,$digit1,$digit2,$digit3);
	$coder->encodeAlphanumaricToAscii($array1);
	$coder->decodeAsciiToAlphanumaric($array0);
	echo 'test';
?>