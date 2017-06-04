<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Solarsystem extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->helper("solarsystem");
		$this->load->model('solarsystem_model','ss_model');
	}
	
	/**
	*Function add/insert solar system
	*@param ssname string
	*@param x_coordinate float
	*@param y_coordinate float
	*@param z_coordinate float
	*@response mixed  JSON
	*/
	public function addSolarSystem(){
		$response = array(
			"status" => "failed", "data" => array(), 
			"msg" => "Some error occured while adding Solar System.");

		$ssname = isset($_POST['ssname'])?$_POST['ssname']: "";
		$x_coord = isset($_POST['x_coordinate'])?$_POST['x_coordinate']:"";
		$y_coord = isset($_POST['y_coordinate'])?$_POST['y_coordinate']:"";
		$z_coord = isset($_POST['z_coordinate'])?$_POST['z_coordinate']:"";
		$coordResponse = ValidateCoordinates(array($x_coord, $y_coord, $z_coord));
		$validationResponse = validateSSName($ssname);
		if(!empty($ssname) && strlen($ssname)<=75 && $validationResponse === "" && $coordResponse === ""){
			$inputparams = array('ssname' => $ssname, 'x_coord' => $x_coord, 'y_coord' => $y_coord, 'z_coord' => $z_coord);
			$response = $this->ss_model->addSolarSystem($inputparams);
		}else{
			if(!empty($coordResponse)){$response['msg'] = $coordResponse;}
			if(!empty($validationResponse)){$response['msg'] = $validationResponse;}
		}
		echo json_encode($response);
	}
	
	
	/**
	*Function to update solar system -- should be given atleast one parameter along with ssid
	*@param ssid int
	*@param ssname string - optional
	*@param x_coordinate float - optional
	*@param y_coordinate float - optional
	*@param z_coordinate float - optional
	*@response mixed  JSON
	*/
	public function updateSolarSystem(){
		$response = array(
			"status" => "failed", "data" => array(), 
			"msg" => "Some error occured while updating Solar System.");
			
		$xyz_coords = $inputparams = array();
		
		$ssid = isset($_POST['ssid'])?$_POST['ssid']:"";
		$ssidValidation = validateSSId($ssid);
		if(!empty($ssidValidation)){
			$response['msg'] = $ssidValidation;
			echo json_encode($response);
			exit;
		}

		if(isset($_POST['ssname'])){
			$ssname = $_POST['ssname'];
			$ssNameValidation = validateSSName($ssname);
			if(!empty($ssNameValidation)){
				$response['msg'] = $ssNameValidation;
				echo json_encode($response);
				exit;
			}
			else{$inputparams['ssname'] = $ssname;}
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
		/* $inputparams contains fields for which data provided to update except ssid*/
		if(!empty($ssid) && intval($ssid)> 0){
			$response = $this->ss_model->updateSolarSystem($ssid, $inputparams);
		}
		echo json_encode($response);
	}
	
	/*Function to deletion(soft) can be done by using either solar system name or id
	*@param fieldname -- value can be either id (or) ssname
	*@param fieldvalue -- depend on selection of fieldtype
	*@response mixed JSON
	*
	*/
	function deleteSolarSystem(){
		
		$response = array(
			"status" => "failed", "data" => array(), 
			"msg" => "Some error occured while deleting Solar System.");
		$fieldname = isset($_POST['fieldname'])?$_POST['fieldname']:"";
		$fieldvalue = isset($_POST['fieldvalue'])?$_POST['fieldvalue']:"";
		if(empty($fieldname) || empty($fieldvalue) || !(strcasecmp($fieldname,"id") === 0 || 0 === strcasecmp($fieldname,"ssname"))){
			$response['msg'] = "Invalid inputs provided";
			echo json_encode($response);
			exit;
		}
		$response = $this->ss_model->deleteSolarSystem($fieldname, $fieldvalue);
		echo json_encode($response);
	}
	 
	/* Function to find 
			i. solar system sun  
			ii. all planets that orbit a sun 
			iii. all planets in solar system
			
	*/
	public function fetchSolarSystems(){
		$response = array(
			"status" => "failed", "data" => array(), 
			"msg" => "Some error occured while fetching Solar System Information.");
		$fetchtype = isset($_POST['fetchtype'])?$_POST['fetchtype']:"";
		$ssid = isset($_POST['ssid'])?$_POST['ssid']:"";
		$sunid = isset($_POST['sunid'])?$_POST['sunid']:"";
		
		if(strcasecmp($fetchtype,"sun") !== 0 && strcasecmp($fetchtype,"sun") !== 0){
			$response['msg'] = " Fetch type should be either sun or planet";
		}
		if(strcasecmp($fetchtype,"sun") === 0){
			/*To find sun of solar system then only solar system id is enough*/
			$ssidValidation = validateSSId($ssid);
			if(!empty($ssidValidation)){
				$response['msg'] = $ssidValidation;
				echo json_encode($response);
				exit;
			}
		}
		if(strcasecmp($fetchtype,"planet") === 0){
			/* To find planets that orbit a sun or belongs to solar system then either solsr system id (or) sun id (or) both can be provided*/
			if(empty($ssid) && empty($sunid)){
				$response['msg'] = "Please provide either solsr system id (or) sun id (or) both can be provided";
				echo json_encode($response);
				exit;
			}
			
			if(isset($_POST['ssid'])){
				$ssidValidation = validateSSId($ssid);
				if(!empty($ssidValidation)){
					$response['msg'] = $ssidValidation;
					echo json_encode($response);
					exit;
				}
			}
			if(isset($_POST['sunid'])){
				$sunidValidation = validateSSId($sunid);
				if(!empty($sunidValidation)){
					$response['msg'] = $sunidValidation;
					echo json_encode($response);
					exit;
				}
			}
		}	
		
		if(strcasecmp($fetchtype,"sun") === 0 || strcasecmp($fetchtype,"planet") === 0){
			$response = $this->ss_model->fetchSolarSystems($fetchtype,$ssid,$sunid);
		}
		echo json_encode($response);
	}
	
	function fetchSolarSystemByType(){
		$fetchtype = isset($_POST['fetchtype'])?$_POST['fetchtype']:"";
		$inputparams = array();
		
		if(empty($fetchtype) || (strcasecmp($fetchtype,"name") !== 0 && strcasecmp($fetchtype,"size") !== 0)){
			$response['msg'] = "Please provide fetchtype as either size or name";
			echo json_encode($response);
			exit;
		}
		
		if(isset($_POST['ssname'])){
			$sunidValidation = validateSSName($_POST['ssname'], "");
			if(!empty($sunidValidation)){
				$response['msg'] = $sunidValidation;
				echo json_encode($response);
				exit;
			}
			$inputparams['fieldname'] = "ssname";
			$inputparams['tablename'] = "solarsystem";
			$inputparams['fieldvalue'] = $_POST['ssname'];
		}
		
		if(isset($_POST['sunname'])){
			$sunidValidation = validateSSName($_POST['sunname'], "sun name");
			if(!empty($sunidValidation)){
				$response['msg'] = $sunidValidation;
				echo json_encode($response);
				exit;
			}
			$inputparams['fieldname'] = "sun_name";
			$inputparams['tablename'] = "ss_sun";
			$inputparams['fieldvalue'] = $_POST['sunname'];
		}
		
		if(isset($_POST['planetname'])){
			$sunidValidation = validateSSName($_POST['planetname'], "planet name");
			if(!empty($sunidValidation)){
				$response['msg'] = $sunidValidation;
				echo json_encode($response);
				exit;
			}
			$inputparams['fieldname'] = "planet_name";
			$inputparams['tablename'] = "ss_planets";
			$inputparams['fieldvalue'] = $_POST['planetname'];
		}
		
		if(isset($_POST['searchfor'])){
			$searchfor = $_POST['searchfor'];
			if($searchfor === "planet"){$inputparams['tablename'] = "ss_planets";}
			if($searchfor === "sun"){$inputparams['tablename'] = "ss_sun";}
			if($searchfor === "ss"){$inputparams['tablename'] = "solarsystem";}
		
		
			if(isset($_POST['x_coordinate'])){
				$inputparams['x_coord'] = $_POST['x_coordinate'];
			}
			if(isset($_POST['y_coordinate'])){
				$inputparams['y_coord'] = $_POST['y_coordinate'];
			}
			if(isset($_POST['z_coordinate'])){
				$inputparams['z_coord'] = $_POST['z_coordinate'];
			}
		}
		if(empty($inputparams) || !(sizeof($inputparams) > 0)){
			$response['msg'] = "Please provide searchfor key value pair (planet/sun/ss name)";
			echo json_encode($response);
			exit;
		}
		$response = $this->ss_model->fetchSolarSystemByType($fetchtype, $inputparams);
		echo json_encode($response);
	}
	
}

