<?php

class tags extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->library('session');
		$this->load->library('Mobile_Detect');
	}
	
	function index(){
		
	}
	
	function inputtag($tagName){
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$LoginFlag = $this->session->userdata('IsLoggedIn');
		if(!empty($LoginFlag)){
			$data = $this->session->all_userdata();
			//var_dump($data);
			$this->load->model('modelhome');
			$this->load->model('modeltag');
			$response['huggas'] = $this->modeltag->loadData($data['userId'],4,1,$tagName);   //(userid,huggasPerPage,pageNo)
			$response['sidebar'] = $this->modelhome->loadSideBar();
			$response['tagsbar'] = $this->modeltag->loadTopTags();
			$response['data']=$data;
			$response['category']=$tagName;
			//echo json_encode($response);
			
			//Detect mobile and load no-sidebar version
			$mobile = $this->mobile_detect->isMobile();
			if(!empty($response['huggas'])){
				if($mobile){
				 $this->load->view('hugga_home_mobile',$response);
				}
				else{
					$this->load->view('hugga_home',$response);
				}
			}else{
				$this->load->view('error',$response);
			}		
		}
		else{
			$this->load->model('modelhome');
			$this->load->model('modeltag');
			$response['huggas'] = $this->modeltag->loadData(NULL,4,1,$tagName);
			$response['sidebar'] = $this->modelhome->loadSideBar();
			$response['tagsbar'] = $this->modeltag->loadTopTags();
			$response['data']=array("userId"=>"0");
			$response['category']=$tagName;
			//echo json_encode($response);
			//var_dump($response);
			
			//Detect mobile and load no-sidebar version
			$mobile = $this->mobile_detect->isMobile();
			if(!empty($response['huggas'])){
				if($mobile){
					$this->load->view('hugga_home_mobile',$response);
				}
				else{
					$this->load->view('hugga_home',$response);
				}
			}else{
				$this->load->view('error',$response);
			}
			
		}	
	}

	function autoload($tagName){
		$huggasPerPage = $this->input->post('HPP');
		$pageNo = $this->input->post('PN');
		$LoginFlag = $this->session->userdata('IsLoggedIn');
		if(!empty($LoginFlag)){
			$data = $this->session->all_userdata();
			//var_dump($data);
			$this->load->model('modelhome');
			$this->load->model('modeltag');
			$response['huggas'] = $this->modeltag->loadData($data['userId'],$huggasPerPage,$pageNo,$tagName);   //(huggasPerPage,pageNo)
			$response['data']=$data;
			//var_dump($response['data']);
			//return HTML code
			//echo json_encode($response);
			if(!empty($response['huggas'])){
				echo $this->jsonToHtml($response);
			}
			
			
		}
		else{
			$this->load->model('modelhome');
			$this->load->model('modeltag');
			$response['data']=array("userId"=>"0");
			$response['huggas'] = $this->modeltag->loadData(NULL,$huggasPerPage,$pageNo,$tagName);
			
			//return HTML code
			if(!empty($response['huggas'])){
				echo $this->jsonToHtml($response);
			}
			
		}
	}

	function addTag($tagName,$userId,$huggaId){
		$this->load->model('modeltag');
		//clean tag
		$tagName = $this->modeltag->toLower($tagName);
		//$tagName = $this->modeltag->cleanTag($tagName);
		
		if($this->modeltag->tagExist($tagName)==1){
			$tagId = $this->modeltag->getTagId($tagName);
			//check if tag Mapping also exists or not
			if($this->modeltag->tagMappingExist($tagId,$huggaId)==1){
				//Do nothing
				echo json_encode(array("response"=>"0"));
			}else{
				$this->modeltag->insertTagMap($tagId,$huggaId);
			}
		}else{
			//$tagId = $this->modeltag->getTagId($tagName);
			//add tag first
			$tagId=$this->modeltag->addTag($tagName,$userId);
			//add tag Mapping
			$this->modeltag->insertTagMap($tagId,$huggaId);
		}
	}

	function deleteTag($huggaId,$tagName,$userId){
		$this->load->model('modeltag');
		//if tag exists with this particular hugga only then delete tag from mapping as well as tag table
		// otherwise just delete the tag mapping for this huggaid
		$LoginFlag = $this->session->userdata('IsLoggedIn');
		if(!empty($LoginFlag)){
			//clean tag
			$tagName = $this->modeltag->toLower($tagName);
			//$tagName = $this->modeltag->cleanTag($tagName);
			$tagId = $this->modeltag->getTagId($tagName);
			if($this->modeltag->huggaPerTag($tagName)>1){
				//delete only tag mapping
				$this->modeltag->deleteTagMap($tagId,$huggaId);
			}else{
				//deleta tag
				$this->modeltag->deleteTag($tagName,$userId);
				//delete mapping
				$this->modeltag->deleteTagMap($tagId,$huggaId);
			}
		}
	}
	
	function getTags($huggaId){
		$this->load->model('modeltag');
		return $this->modeltag->getTags($huggaId);
	}
	
	function totalHuggas(){
		$this->load->model('modelhome');
		$result = $this->modelhome->totalHuggas();
		echo json_encode($result);
	}
	
	function jsonToHtml($data){
		$x='';
		foreach($data['huggas'] as $item){
			if($item['lick']['licked']==1)
			{$y='green';}
			else{$y='default';}
			
			if($item['flush']['flushed']==1)
			{$z='red';}
			else{$z='default';}
			
			if($item['flush']['flushed']==1) {$a='style="opacity: 0.3"';}else{$a='';}
			if($item['lick']['licked']==1) {$b='style="opacity: 0.3"';}else{$b='';}
			$tags = explode(",", $item['tags']['tagvalues']);	
		$x.='	
		<div class="row">
                  		<div class="col-md-12">
                  			<div class="clearfix">                 				
                  			<!------------In case already licked by the user then the id - licked else unlicked. Similar for flush --------
                  			lick(userId,HuggaId)
                  			-->	
                  			<a href="javascript:void(0);" onclick="lick('.$data['data']['userId'].','.$item["huggaId"].',this);" '.$a.'  class="btn '.$y.' lick" id="licked">Lick <i class="icon-chevron-up"></i> <span class="badge badge-danger">'.$item['licks'].'</span></a>                			
                  			<a href="javascript:void(0);" onclick="flush('.$data['data']['userId'].','.$item['huggaId'].',this);" '.$b.' class="btn '.$z.' flush" id="flushed">Flush <i class="icon-chevron-down"></i> <span class="badge badge-success">'.$item['flushes'].'</span></a>                           
                  			</div>		
                  		</div>	
                  	</div>
                  	 <a href="'.base_url().'hugga/'.$item['huggaId'].'" target="_blank"><h3 style="font-weight: 600 !important">'.$item['title'].'</h3></a>
                     
                     <style>
                     	ul.blog-tags a{
                     		background: #eee;
							padding: 1px 4px;
							margin: 0 4px 4px 0;
							display: inline-block;
							text-decoration:none;
                     	}
                     	ul.blog-tage a:hover{
                     		background: #eff;
                     	}
                     </style>
                     
                     <div class="row">
                     	<div class="col-md-12">
                           <ul class="list-inline blog-tags">
                              <li>
                              	 <?php
                                 if(!empty($data[\'IsAdmin\'])){
                                 	echo\'<i class="addTags icon-edit"></i>\';
                                 } 
                                 ?> 
                              	 <input class="huggaId" type="hidden" value="<?php echo $item[\'huggaId\']; ?>" />
                                 <i class="icon-tags"></i>
                                 <?php
                                 foreach($tags as $itemtag){
                                 ?>              
                                 <a href="http://hugde.com/tags/inputtag/<?php echo $itemtag; ?>"><?php echo $itemtag; ?></a>
                                 <?php
                                 }  
                                 ?> 
                              </li>
                           </ul>
                     	</div>
                     </div>
           			 <p class="tagFailed" style="display:none">Tagging Failed. Please retry</p>
           			 <p class="tagMappingError" style="display:none">Tag already exists. Please retry</p>
                     
                     <div class="row <?php echo $item[\'huggaId\']; ?>" style="display: none">
                     	<div class="form-group">
                           <div class="col-md-12">
                              <input class="tags_1" type="text" class="form-control tags medium" value="<?php echo $item[\'tags\'][\'tagvalues\']; ?>" />
                           	  <input class="huggaId" type="hidden" value="<?php echo $item[\'huggaId\']; ?>" />		
                           </div>
                        </div>
                     </div>
                     
                     <div class="blog-tag-data">
                     	<a href="'.base_url().'hugga/'.$item['huggaId'].'" target="_blank"><img src="http://d2nds2wyuzde9r.cloudfront.net/hugde_assets/img/longLoader.gif" onload="this.src=\''. $item['images'][0]['originalImageUrl'].'\'" class="img-responsive" alt="" style="width:100%"></a>
                        <div class="row">
                           <!--<div class="col-md-2">
                           	
                           		<a href="https://twitter.com/share?count=horizontal" class="twitter-share-button" data-lang="en">Tweet</a>
                           		<div class="pull-right" style="margin-top:-25px"><i class="icon-calendar"></i> <a href="#">April 16, 2013</a></div>
                           
                           	</div>
                           	<div class="col-md-4">
                           	  	<div class="fb-like" data-href="'.base_url().'hugga/'.$item['huggaId'].'" data-layout="button_count" data-action="like" data-show-faces="true" data-share="true" style="width: 150px !important"></div>
                           		
                          	</div>-->
                          	<div class="col-md-12">
                           	
							
                           		<!--<a href="https://twitter.com/share?count=horizontal" class="twitter-share-button" data-lang="en">Tweet</a>
                           		<!--<div class="pull-right" style="margin-top:-25px"><i class="icon-calendar"></i> <a href="#">April 16, 2013</a></div>-->
                           	<a href="https://twitter.com/share?count=horizontal" class="btn btn-info"><i class="icon-twitter"></i> Twitter</a>
                           	<a href="#" onclick="window.open(\'https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Fhugde.com%2Fhugga%2F'.$item['huggaId'].'\',\'facebook-share-dialog\',\'width=626,height=436\');return false;" class="btn btn-primary"><i class="icon-facebook"></i> Facebook</a>
                           	<a href="https://plus.google.com/share?url=http%3A%2F%2Fhugde.com%2Fhugga%2F'.$item['huggaId'].'" onclick="javascript:window.open(this.href,\'\',\'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;" class="btn btn-danger"><i class="icon-google-plus"></i> Google Plus</a>
                           	
                           	</div>
                           	<script type="text/javascript">
								  (function() {
								    var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;
								    po.src = \'https://apis.google.com/js/plusone.js\';
								    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
								  })();
								</script>
                          	<!--<div class="col-md-3">
                          		Posted by: '. $item['postedBy'].'
                          	</div>
                          	<div class="col-md-3" style="font-size: 18px">
                          		Views '.$item['views'].'
                          	</div>-->
                          	
                        </div>
                        <div class="row" style="margin-top: 5px">
                        	<div class="col-md-12">
                        		<div class="fb-like" data-href="'.base_url().'hugga/'.$item['huggaId'].'" data-layout="standard" data-action="like" data-show-faces="false" data-share="false"></div>
                        	</div>
                        </div>
                     </div>
                     <!--end news-tag-data-->
                    
                    <hr>
';
}
	return $x;
	}
}

?>