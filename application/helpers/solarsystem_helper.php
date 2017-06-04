<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	if ( ! function_exists('validateSSName'))
	{
		function validateSSName($ssname, $appenstr=""){
			$errormsg = "";
			if(empty($ssname)){
				$errormsg = "Invalid Solar System ".$appenstr." name provided";
			}
			if(strlen($ssname)>75){
				$errormsg = "Solar System name ".$appenstr." length should be less than 75 characters";
			}
			return $errormsg;
		}
	}
	
	if ( ! function_exists('validateSSId'))
	{
		function validateSSId($ssid){
			$errormsg = "";
			if($ssid <= 0){
				$errormsg = "Invalid input provided";
			}
			return $errormsg;
		}
	}
	
	if ( ! function_exists('validateCoordinates'))
	{
		function validateCoordinates($xyz_coords){
			$errormsg = "";
			foreach($xyz_coords as $xyz_coord){
				if(empty($xyz_coord) || !is_numeric($xyz_coord)){
					$errormsg = "Invalid coordinates provided";
					break;
				}
			}
			return $errormsg;
		}
	}