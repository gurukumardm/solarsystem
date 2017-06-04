<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Solarsystemsun extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->helper("solarsystem");
		$this->load->model('solarsystemsun_model','ss_sun_model');
	}
	
	
	/**
	*Function add/insert solar system Sun
	*@param ssid int reference of solar system id
	*@param sun_name string
	*@param x_coordinate float
	*@param y_coordinate float
	*@param z_coordinate float
	*@response mixed  JSON
	*/
	public function addSolarSystemSun(){
		$response = array(
			"status" => "failed", "data" => array(), 
			"msg" => "Some error occured while adding Solar System Sun.");

		$ssid = isset($_POST['ssid'])?$_POST['ssid']: "";
		$sun_name = isset($_POST['sun_name'])?$_POST['sun_name']: "";
		$x_coord = isset($_POST['x_coordinate'])?$_POST['x_coordinate']:"";
		$y_coord = isset($_POST['y_coordinate'])?$_POST['y_coordinate']:"";
		$z_coord = isset($_POST['z_coordinate'])?$_POST['z_coordinate']:"";
		$coordResponse = validateCoordinates(array($x_coord, $y_coord, $z_coord));
		$validationResponse = validateSSName($sun_name, "Sun");
		$ssidValidation = validateSSId($ssid);
		if(!empty($ssidValidation)){
			$response['msg'] = "Solar system reference is mandatory to add sun";
			echo json_encode($response);
			exit;
		}

		if(!empty($sun_name) && strlen($sun_name)<=75 && $validationResponse === "" && $coordResponse === ""){
			$inputparams = array('ss_id' => $ssid,'sun_name' => $sun_name, 'x_coord' => $x_coord, 'y_coord' => $y_coord, 'z_coord' => $z_coord);
			$response = $this->ss_sun_model->addSolarSystemSun($inputparams);
		}else{
			if(!empty($coordResponse)){$response['msg'] = $coordResponse;}
			if(!empty($validationResponse)){$response['msg'] = $validationResponse;}
		}
		echo json_encode($response);
	}
	
	
	/**
	*Function to update/ soft delete solar system -- should be given atleast one parameter along with ssid
	* Here i am combining both update and delete
	*@param sunid int
	*@param sun_name string - optional
	*@param x_coordinate float - optional
	*@param y_coordinate float - optional
	*@param z_coordinate float - optional
	*@param status enum '1' or '0'
	*@response mixed  JSON
	*/
	public function updateSolarSystemSun(){
		$response = array(
			"status" => "failed", "data" => array(), 
			"msg" => "Some error occured while updating Solar System Sun.");
			
		$xyz_coords = $inputparams = array();
		
		$sunid = isset($_POST['sunid'])?$_POST['sunid']:"";
		$ssidValidation = validateSSId($sunid);
		if(!empty($ssidValidation)){
			$response['msg'] = $ssidValidation;
			echo json_encode($response);
			exit;
		}

		if(isset($_POST['sun_name'])){
			$sun_name = $_POST['sun_name'];
			$ssNameValidation = validateSSName($sun_name);
			if(!empty($ssNameValidation)){
				$response['msg'] = $ssNameValidation;
				echo json_encode($response);
				exit;
			}
			else{$inputparams['sun_name'] = $sun_name;}
		}
		
		if(isset($_POST['x_coordinate'])){
			array_push($xyz_coords,$_POST['x_coordinate']);
			$inputparams['x_coord'] = $_POST['x_coordinate'];
		}
		if(isset($_POST['y_coordinate'])){
			array_push($xyz_coords,$_POST['y_coordinate']);
			$inputparams['y_coord'] = $_POST['y_coordinate'];
		}
		if(isset($_POST['z_coordinate'])){
			array_push($xyz_coords,$_POST['z_coordinate']);
			$inputparams['z_coord'] = $_POST['z_coordinate'];
		}
		
		if(sizeof($xyz_coords)){
			$coordResponse = ValidateCoordinates($xyz_coords);
			if(!empty($coordResponse)){
				$response['msg'] = $coordResponse;
				echo json_encode($response);
				exit;
			}
		}
		
		if(isset($_POST['status'])){
			if($_POST['status'] == 1 || $_POST['status'] == 0){
				$inputparams['sun_status'] = (string)$_POST['status'];
			}else{
				$response['msg'] = "Invalid status provided";
				echo json_encode($response);
				exit;

			}
		}
		/* $inputparams contains fields for which data provided to update except sunid*/
		if(!empty($sunid) && intval($sunid)> 0){
			$response = $this->ss_sun_model->updateSolarSystemSun($sunid, $inputparams);
		}
		echo json_encode($response);
	}
}