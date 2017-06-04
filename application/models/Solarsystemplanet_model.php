<?php

class Solarsystemplanet_model extends CI_Model{
	
	protected $ss_tablename = "solarsystem";
	protected $ss_sun_table = "ss_sun";
	protected $ss_planet_table = "ss_planets";
	
	public function __construct(){
		$this->load->database();
		$this->response = array(
							"status" => "failed",
							"data" => array(),
							"msg" => "Some error occured while adding Solar System Planet.");
	}
	
	public function addSolarSystemPlanet($inputparams){
		if(!empty($inputparams['planet_name']) && strlen($inputparams['planet_name'])<=75){
			/* Check whether solar system sun is available or not*/
			$this->db->where('id', $inputparams['ss_sun_id']);
			$this->db->where('sun_status', SS_ACTIVE_STATUS);
			$sunExits = $this->db->count_all_results($this->ss_sun_table);
			
			
			/*Check whether solar system planet name already used*/
			$this->db->where('planet_name', $inputparams['planet_name']);
			$alreadyExits = $this->db->count_all_results($this->ss_planet_table);
			
			if(!$alreadyExits && $sunExits){
				$insdata = array_merge($inputparams, array("created_at" =>time(), "updated_at" =>time()));
				$ins_status = $this->db->insert($this->ss_planet_table, $insdata);
				if($ins_status){
					$this->response['status'] = "success";
					$this->response['msg'] = "Solar system planet has been Successfully added"; 
				}
			}else{
				if($alreadyExits){
					$this->response['msg'] = "Provided solar system planet already added"; 
				}
				
			}
		}
		return $this->response;
	}
	
	public function updateSolarSystemPlanet($planetid, $inputparams){
		$this->response['msg'] = "Some error occured while updating Solar System planet."; 
		if(!empty($planetid) && intval($planetid)> 0){
			if(isset($inputparams['planet_name'])){/* Check already existance of currently provided solar system planet */
				$this->db->where('planet_name', $inputparams['planet_name']);
				$this->db->where('id!=', $planetid);
				$alreadyExits = $this->db->count_all_results($this->ss_planet_table);
			}else {$alreadyExits = 0;}
			
			if(!$alreadyExits){
				$update_data = array_merge($inputparams,array("updated_at" =>time()));
				$this->db->where('id', $planetid);
				$upd_status = $this->db->update($this->ss_planet_table, $update_data);
				if($upd_status){
					$this->response['status'] = "success";
					$this->response['msg'] = "Solar system planet has been Successfully updated"; 
				}
			}else{
				$this->response['msg'] = "Provided solar system planet has already used"; 
			}
		}
		return $this->response;
	}
}