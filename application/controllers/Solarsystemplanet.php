<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Solarsystemplanet extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->helper("solarsystem");
		$this->load->model('solarsystemplanet_model','ss_planet_model');
	}
	
	
	/**
	*Function add/insert solar system Planet
	*@param ss_id int reference of solar system id
	*@param planet_name string
	*@param x_coordinate float
	*@param y_coordinate float
	*@param z_coordinate float
	*@response mixed  JSON
	*/
	public function addSolarSystemPlanet(){
		$response = array(
			"status" => "failed", "data" => array(), 
			"msg" => "Some error occured while adding Solar System Planet .");

		$sunid = isset($_POST['sunid'])?$_POST['sunid']: "";
		$planet_name = isset($_POST['planet_name'])?$_POST['planet_name']: "";
		$x_coord = isset($_POST['x_coordinate'])?$_POST['x_coordinate']:"";
		$y_coord = isset($_POST['y_coordinate'])?$_POST['y_coordinate']:"";
		$z_coord = isset($_POST['z_coordinate'])?$_POST['z_coordinate']:"";
		$coordResponse = validateCoordinates(array($x_coord, $y_coord, $z_coord));
		$validationResponse = validateSSName($planet_name, "Planet");
		$ssidValidation = validateSSId($sunid);
		if(!empty($ssidValidation)){
			$response['msg'] = "Solar system Sun reference is mandatory to add Planet";
			echo json_encode($response);
			exit;
		}

		if(!empty($planet_name) && strlen($planet_name)<=75 && $validationResponse === "" && $coordResponse === ""){
			$inputparams = array('ss_sun_id' => $sunid,'planet_name' => $planet_name, 'x_coord' => $x_coord, 'y_coord' => $y_coord, 'z_coord' => $z_coord);
			$response = $this->ss_planet_model->addSolarSystemPlanet($inputparams);
		}else{
			if(!empty($coordResponse)){$response['msg'] = $coordResponse;}
			if(!empty($validationResponse)){$response['msg'] = $validationResponse;}
		}
		echo json_encode($response);
	}
	
	
	/**
	*Function to update/ soft delete solar system planet-- should be given atleast one parameter along with ssid
	* Here i am combining both update and delete
	*@param planetid int
	*@param planet_name string - optional
	*@param x_coordinate float - optional
	*@param y_coordinate float - optional
	*@param z_coordinate float - optional
	*@param p_status enum '1' or '0'
	*@response mixed  JSON
	*/
	public function updateSolarSystemPlanet(){
		$response = array(
			"status" => "failed", "data" => array(), 
			"msg" => "Some error occured while updating Solar System Planet.");
			
		$xyz_coords = $inputparams = array();
		
		$planetid = isset($_POST['planetid'])?$_POST['planetid']:"";
		$ssidValidation = validateSSId($planetid);
		if(!empty($ssidValidation)){
			$response['msg'] = $ssidValidation;
			echo json_encode($response);
			exit;
		}

		if(isset($_POST['planet_name'])){
			$planet_name = $_POST['planet_name'];
			$ssNameValidation = validateSSName($planet_name);
			if(!empty($ssNameValidation)){
				$response['msg'] = $ssNameValidation;
				echo json_encode($response);
				exit;
			}
			else{$inputparams['planet_name'] = $planet_name;}
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
				$inputparams['p_status'] = (string)$_POST['status'];
			}else{
				$response['msg'] = "Invalid status provided";
				echo json_encode($response);
				exit;

			}
		}
		/* $inputparams contains fields for which data provided to update except planetid*/
		if(!empty($planetid) && intval($planetid)> 0){
			$response = $this->ss_planet_model->updateSolarSystemPlanet($planetid, $inputparams);
		}
		echo json_encode($response);
	}
}