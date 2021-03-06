<?php
ini_set('display_errors',1);
error_reporting(-1);
class memes extends CI_Controller{
	
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
			$this->load->model('modelmeme');
			//loaddata(HPP,PageNo)
			$response['memes'] = $this->modelmeme->loadData($data['userId'],100,1);   //(huggasPerPage,pageNo)
			//$response['sidebar'] = $this->modelhome->loadSideBar();
			$response['data']=$data;
			$response['category']='memes';
			$this->load->view('memegrid',$response);
		}
		else{
			redirect('http://hugde.com');
			/*
			$this->load->model('modelhome');
			$response['huggas'] = $this->modelhome->loadData('0',NULL,'HIDE',4,1,'ALL');
			$response['sidebar'] = $this->modelhome->loadSideBar();
			$response['data']=array("userId"=>"0");
			$response['category']='ALL';
			//echo json_encode($response['sidebar']);
			//var_dump($response);
			
			//Detect mobile and load no-sidebar version
			$mobile = $this->mobile_detect->isMobile();
			if($mobile){
				$this->load->view('hugga_home_mobile',$response);
			}
			else{
				$this->load->view('hugga_home',$response);
			}
			 * 
			 */		
		}
	}

	function autoload(){
		$huggasPerPage = $this->input->post('HPP');
		$pageNo = $this->input->post('PN');
		$LoginFlag = $this->session->userdata('IsLoggedIn');
		if(!empty($LoginFlag)){
			$data = $this->session->all_userdata();
			//var_dump($data);
			$this->load->model('modelmeme');
			$response['memes'] = $this->modelmeme->loadData($data['userId'],$huggasPerPage,$pageNo);   //(huggasPerPage,pageNo)
			$response['data']=$data;
			//var_dump($response['data']);
			//return HTML code
			//echo json_encode($response);
			if(!empty($response['memes'])){
				echo $this->jsonToHtml($response);
			}
			
			
		}
		else{
			/*
			$this->load->model('modelhome');
			$response['data']=array("userId"=>"0");
			$response['huggas'] = $this->modelhome->loadData('0',NULL,'HIDE',$huggasPerPage,$pageNo,$category);
			
			//return HTML code
			if(!empty($response['huggas'])){
				echo $this->jsonToHtml($response);
			}
			*/
		}
	}

	function delete($huggaId){
		$LoginFlag = $this->session->userdata('IsLoggedIn');
		if(!empty($LoginFlag)){
			$data = $this->session->all_userdata();
			//var_dump($data);
			$this->load->model('modelhome');
			$this->modelhome->deleteHugga($huggaId);   //(huggasPerPage,pageNo)
			$url = 'http://hugde.com/home/myhugge';
			redirect ($url);
		}
	}
			
	function jsonToHtml($data){
		$x='';
		foreach($data['memes'] as $item){
			if($item['memeTitle']!=''){$title=$item['memeTitle'];}else{$title='No Title Set';}
			if($item['userowned']['owned']==1) {$category="myself";}else{$category= $item['category'];}
			$x.='<div class="col-md-3 col-sm-4 mix '.$category.' mix_all" style="display:block; opacity:1">
                                 <div class="mix-inner">
                                    <img class="img-responsive" src="'.$item['images'][0]['originalImageUrl'].'" alt="" style="width: 300px;height: 287px">
                                    <div class="mix-details">
                                       <h4>'.$title.'</h4>
                                       <a href="'.base_url().'memes/createhugga?t='.$item['images'][0]['originalImageUrl'].'&memeid='.$item['memeId'].'" >Create Meme</a>
                                       <a class="mix-preview fancybox-button" href="'.$item['images'][0]['originalImageUrl'].'" title="'.$title.'" data-rel="fancybox-button"><i class="icon-search"></i></a>
                    
                                    </div>
                                 </div>
                              </div>';
		}
	return $x;
	}
	
	function totalmemes(){
		$this->load->model('modelmeme');
		$result = $this->modelmeme->totalMemes();
		echo json_encode($result);
	}
		
	function createhugga(){
		$imageUrl= $this->input->get('t');
		$memeid=$this->input->get('memeid');
		$LoginFlag = $this->session->userdata('IsLoggedIn');
		if(!empty($LoginFlag)){
			$userId = $this->session->userdata('userId');
			$data = $this->session->all_userdata();
			$this->load->model('modelhome');
			//loadData($huggaId=NULL,$userId=NULL,$myhugga=NULL,$huggasPerPage=NULL,$pageNo=NULL)
			$response['huggas'] = $this->modelhome->loadData('0',$data['userId'],'SHOW',100,1,'ALL');
			$response['sidebar'] = $this->modelhome->loadSideBar();
			$response['data']=$data;
			$response['imageUrl']=$imageUrl;
			$response['memeid']=$memeid;
			//Detect mobile and load no-sidebar version
			$mobile = $this->mobile_detect->isMobile();
			$this->load->view('createHugga',$response);
		}
		else{
			redirect('http://hugde.com');
		}
	}
	
	function createhuggaview(){
		$imgData = $this->input->post('imgdata');
		$memeid = $this->input->post('memeid');
		$filteredData=substr($_POST['imgdata'], strpos($_POST['imgdata'], ",")+1);

		// Need to decode before saving since the data we received is already base64 encoded
		$decodedData=base64_decode($filteredData);
		$fn = substr(md5(time()), 0, 5);
		$fp = fopen( "/var/www/html/hugde_assets/upload/$fn.png", 'wb' );
		fwrite( $fp, $decodedData);
		fclose( $fp );
		
		//$fp2= fopen( "/var/www/html/hugde_assets/upload/$fn.png", 'r+' );
		$fp2="/var/www/html/hugde_assets/upload/".$fn.'.png';
		//echo filesize($fp2);
		$this->load->model('photo');
		$this->photo->uploadimage($fp2,$fn,$memeid);
	}
	
	function next($huggaId,$userId){
		
		// seed with microseconds
		$this->load->model('modelhome');
		
		$result = $this->modelhome->getNextHugga($huggaId,$userId);
		echo json_encode($result);
	}
	
	function flag($huggaId){
		$userId = $this->session->userdata('userId');
		$response = $this->input->post('temp');
		parse_str($response,$output);
		$feedback=$output['feedback'];
		$this->load->model('modellickflush');
		$this->modellickflush->updateFlagCount($huggaId);
		$this->modellickflush->insertFlag($huggaId,$userId,$feedback);	
	}
}

?>