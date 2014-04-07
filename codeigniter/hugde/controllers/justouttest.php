<?php

class justouttest extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->library('session');
		$this->load->library('Mobile_Detect');
	}
	
	function index(){
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$LoginFlag = $this->session->userdata('IsLoggedIn');
		if(!empty($LoginFlag)){
			$data = $this->session->all_userdata();
			//var_dump($data);
			$this->load->model('modelhome');
			$response['huggas'] = $this->modelhome->loadData('0',$data['userId'],'HIDE',4,1,'JustOut');   //(huggasPerPage,pageNo)
			$response['sidebar'] = $this->modelhome->loadSideBar();
			$response['tags'] = $this->modeltag->loadTopTags();
			$response['data']=$data;
			$response['category']='JustOut';
			//echo json_encode($response);
			
			//Detect mobile and load no-sidebar version
			$mobile = $this->mobile_detect->isMobile();
			if($mobile){
				$this->load->view('hugga_home_test',$response);
			}
			else{
				$this->load->view('hugga_home_test',$response);
			}
			
		}
		else{
			$this->load->model('modelhome');
			$response['huggas'] = $this->modelhome->loadData('0',NULL,'HIDE',4,1,'JustOut');
			$response['sidebar'] = $this->modelhome->loadSideBar();
			$response['tags'] = $this->modeltag->loadTopTags();
			$response['data']=array("userId"=>"0");
			$response['category']='JustOut';
			//echo json_encode($response);
			//var_dump($response);
			
			//Detect mobile and load no-sidebar version
			$mobile = $this->mobile_detect->isMobile();
			if($mobile){
				$this->load->view('hugga_home_test',$response);
			}
			else{
				$this->load->view('hugga_home_test',$response);
			}
			
		}
	}
}

?>