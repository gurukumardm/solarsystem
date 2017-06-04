<?php

class Solarsystem_model extends CI_Model{
	
	protected $ss_tablename = "solarsystem";
	protected $ss_sun_table = "ss_sun";
	protected $ss_planet_table = "ss_planet";
	
	public function __construct(){
		$this->load->database();
		$this->response = array(
							"status" => "failed",
							"data" => array(),
							"msg" => "Some error occured while adding Solar System.");
	}
	public function addSolarSystem($inputparams){
		if(!empty($inputparams['ssname']) && strlen($inputparams['ssname'])<=75){
			$this->db->where('ssname', $inputparams['ssname']);
			$alreadyExits = $this->db->count_all_results($this->ss_tablename);
			if(!$alreadyExits){
				$insdata = array_merge($inputparams, array("created_at" =>time(), "updated_at" =>time()));
				$ins_status = $this->db->insert($this->ss_tablename, $insdata);
				if($ins_status){
					$this->response['status'] = "success";
					$this->response['msg'] = "Solar system has been Successfully added"; 
				}
			}else{
				$this->response['msg'] = "Provided solar system already added"; 
			}
		}
		return $this->response;
	}
	
	public function updateSolarSystem($ssid, $inputparams){
		$this->response['msg'] = "Some error occured while updating Solar System."; 
		if(!empty($ssid) && intval($ssid)> 0){
			if(isset($inputparams['ssname'])){/* Check already existance of currently provided solar system */
				$this->db->where('ssname', $inputparams['ssname']);
				$this->db->where('id!=', $ssid);
				$alreadyExits = $this->db->count_all_results($this->ss_tablename);
			}else {$alreadyExits = 0;}
			
			if(!$alreadyExits){
				$update_data = array_merge($inputparams,array("updated_at" =>time()));
				$this->db->where('id', $ssid);
				$upd_status = $this->db->update($this->ss_tablename, $update_data);
				if($upd_status){
					$this->response['status'] = "success";
					$this->response['msg'] = "Solar system has been Successfully updated"; 
				}
			}else{
				$this->response['msg'] = "Provided solar system name already used"; 
			}
		}
		return $this->response;
	}
	
	public function deleteSolarSystem($fieldname, $fieldvalue){
		$this->response['msg'] = "Some error occured while delteing Solar System.";
		/*================== Doing soft delete ====================*/
		if(!empty($fieldname) && !empty($fieldvalue) && (strcasecmp($fieldname,"id") === 0 || 0 === strcasecmp($fieldname,"ssname"))){
			$this->db->where(strtolower($fieldname), $fieldvalue);
			$this->db->where('ss_status', SS_ACTIVE_STATUS);
			$alreadyExits = $this->db->count_all_results($this->ss_tablename);
			if($alreadyExits){
				$this->db->where(strtolower($fieldname), $fieldvalue);
				$this->db->where('ss_status', SS_ACTIVE_STATUS);
				$del_status = $this->db->update($this->ss_tablename,array("ss_status" => SS_INACTIVE_STATUS));
				if($del_status){
					$this->response['status'] = "success";
					$this->response['msg'] = "Solar system(s) has been Successfully deleted";
				}
			}else{
				$this->response['msg'] = "No solar system available with active status for provided details"; 
			}
		}
		
		return $this->response;		
	}
	
	public function fetchSolarSystems($fetchtype,$ssid,$sunid){
		$this->response['msg'] = "Some error occured while fetching Solar System Information.";
		
		if(strcasecmp($fetchtype,"sun") === 0){
			$fetchresponse = $this->db->get_where($this->ss_sun_table, array('ss_id' => $ssid));
			if($fetchresponse){
				$data = $fetchresponse->result_array();
				$this->response = array("status" => "success",
								"data" => $data,
								"msg" => "Successfully fetched.");
			}
		}
		if(strcasecmp($fetchtype,"planet") === 0){
			if(intval($ssid)>0 && $sunid === ""){
				$sunidqeury = $this->db->query(" SELECT id as ss_sun_id FROM ss_sun WHERE ss_id = $ssid");
				if($sunidqeury){
					$row = $sunidqeury->row();
					$sunid = $row->ss_sun_id;
				}
			}
			
			if($sunid){
				$this->db->select('planet_name,ssp.x_coord,ssp.y_coord,ssp.z_coord');
				$this->db->from('ss_planets ssp');
				if(intval($sunid)>0){
					$this->db->where("ssp.ss_sun_id",$sunid);
				}
				$query=$this->db->get();
				$numrows = $query->num_rows();
				if($numrows>=0){
					$this->response['status'] = "success";
					$this->response['msg'] = "No records available";
					if($numrows>0){
						$this->response['msg'] = "fetched successfully";
						$this->response['data'] = $query->result_array();
					}
				}
			}
		}
		return $this->response;
		
	}
	
	function fetchSolarSystemByType($fetchtype, $inputparams){
		$this->response['msg'] = "Some error occured while fetching Solar System Information."; 
		$querystatus = false;
		if($fetchtype === "name"){
			$this->response['msg'] = "Please provide planetname / sunname / ssname to search";
			if(isset($inputparams['tablename']) && isset($inputparams['fieldname']) && isset($inputparams['fieldvalue'])){
				$querystatus = true;
				$this->db->select('*');
				$this->db->from($inputparams['tablename']);
				$this->db->like($inputparams['fieldname'],$inputparams['fieldvalue']);
			}
		}
		if($fetchtype === "size"){
			$querystatus = true;
			$this->db->select('*');
			$this->db->from($inputparams['tablename']);
			if(isset($inputparams['x_coord'])){
				$this->db->where("x_coord",floatval($inputparams['x_coord']));
			}
			if(isset($inputparams['y_coord'])){
				$this->db->where("y_coord",floatval($inputparams['y_coord']));
			}
			if(isset($inputparams['z_coord'])){
				$this->db->where("z_coord",floatval($inputparams['z_coord']));
			}
		}
		
		if($querystatus){
			//echo $this->db->get_compiled_select();exit;
			$query=$this->db->get();
			$numrows = $query->num_rows();
			if($numrows>=0){
				$this->response['status'] = "success";
				$this->response['msg'] = "No records available";
				if($numrows>0){
					$this->response['msg'] = "fetched successfully";
					$this->response['data'] = $query->result_array();
				}
			}
		}
		return $this->response;
	}
	
}