<?php

class Solarsystemsun_model extends CI_Model{
	
	protected $ss_tablename = "solarsystem";
	protected $ss_sun_table = "ss_sun";
	protected $ss_planet_table = "ss_planet";
	
	public function __construct(){
		$this->load->database();
		$this->response = array(
							"status" => "failed",
							"data" => array(),
							"msg" => "Some error occured while adding Solar System Sun.");
	}
	
	public function addSolarSystemSun($inputparams){
		if(!empty($inputparams['sun_name']) && strlen($inputparams['sun_name'])<=75){
			$alreadyActiveExits = 0; $fkExits = 1; $alreadyExits = 0;
			
			/* Check whether solar system is available or not*/
			$this->db->where('id', $inputparams['ss_id']);
			$this->db->where('ss_status', SS_ACTIVE_STATUS);
			$fkExits = $this->db->count_all_results($this->ss_tablename);
			if($fkExits){
				/*Check Solar system already has active sun -- if exists dont allow another to get added*/
				
				$this->db->where('ss_id', $inputparams['ss_id']);
				$this->db->where('sun_status', SS_ACTIVE_STATUS);
				$alreadyActiveExits = $this->db->count_all_results($this->ss_sun_table);
				
				if(!$alreadyActiveExits){
					/*Check whether solar system sun name already used*/
					$this->db->where('sun_name', $inputparams['sun_name']);
					$alreadyExits = $this->db->count_all_results($this->ss_sun_table);
				}
			}
				
			
			if(!$alreadyExits && $fkExits && !($alreadyActiveExits)){
				$insdata = array_merge($inputparams, array("created_at" =>time(), "updated_at" =>time()));
				$ins_status = $this->db->insert($this->ss_sun_table, $insdata);
				if($ins_status){
					$this->response['status'] = "success";
					$this->response['msg'] = "Solar system Sun has been Successfully added"; 
				}
			}else{
				if($alreadyExits){
					$this->response['msg'] = "Provided solar system Sun already added"; 
				}
				if(!$fkExits){
					$this->response['msg'] = "Invalid mapping of solar system and sun -- might be solar system will be inactive"; 
				}
				if($alreadyActiveExits){
					$this->response['msg'] = "Sun already available under this Solar system"; 
				}
			}
		}
		return $this->response;
	}
	
	public function updateSolarSystemSun($sunid, $inputparams){
		$this->response['msg'] = "Some error occured while updating Solar System sun."; 
		if(!empty($sunid) && intval($sunid)> 0){
			if(isset($inputparams['sun_name'])){/* Check already existance of currently provided solar system sun */
				$this->db->where('sun_name', $inputparams['sun_name']);
				$this->db->where('id!=', $sunid);
				$alreadyExits = $this->db->count_all_results($this->ss_sun_table);
			}else {$alreadyExits = 0;}
			
			if(!$alreadyExits){
				$update_data = array_merge($inputparams,array("updated_at" =>time()));
				$this->db->where('id', $sunid);
				$upd_status = $this->db->update($this->ss_sun_table, $update_data);
				if($upd_status){
					$this->response['status'] = "success";
					$this->response['msg'] = "Solar system sun has been Successfully updated"; 
				}
			}else{
				$this->response['msg'] = "Provided solar system name already used"; 
			}
		}
		return $this->response;
	}
	
}