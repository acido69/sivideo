<?php
require_once 'Zend/Service/Flickr.php';
require_once 'Sivideo/Extensionflickr.php';
class FotosController extends Zend_Controller_Action
{
    private $keyFlickr;
    private $userFlickr;
    private $perPage;
	private $flickr;
    private $results;
	public function preDispatch()
    {
    	$config						= new Zend_Config_Xml(dirname(__FILE__) . '/../etc/DataUser.xml', 'flickr');
    	$this->keyFlickr			= $config->key;
    	$this->userFlickr			= $config->user;
    	$this->perPage				= $config->perPage;
    	
    	$this->flickr				= new Sivideo_Extencionflickr($this->keyFlickr);
		$options['per_page']		= (string)$this->perPage;
		$options['page']			= $this->_request->getParam('page', 1);
		$this->results				= $this->flickr->superUserSearch('flickr.favorites.getPublicList', $this->userFlickr, $options);

		$images						= array();
		foreach($this->results as $result)
		{
			$images[]				= array('detail'=>$this->flickr->getImageDetails($result->id), 'general'=>$result);
		}
		$this->view->images 		= $images;
		$this->view->page			= $options['page'];
		$this->view->existNext		= $this->existNext();
		$this->view->existBack		= $this->existBack();
    }
	public function indexAction()
    {
	    $this->view->image			= $this->view->images[array_rand($this->view->images)];
    }
    public function openAction()
    {
    	$_id						= $this->_request->getParam('id', 0);
    	$this->view->image			= array('general'	=>$this->flickr->getImageDetails($_id), 
    										'detail'	=>array('title'	=>$this->_request->getParam('title', ''),
    															'id'	=>$_id));
    }
    private function existNext()
    {
    	$_total		= $this->results->totalResultsAvailable;
    	$_actual	= $this->results->firstResultPosition;
    	$_next		= $_actual+$this->perPage;
    	if($_next>$_total)
    	{
    		return false;
    	}else{
    		return true;
    	}
    }
    private function existBack()
    {
    	if($this->results->firstResultPosition == 1){
    		return false;
    	}else{
    		return true;
    	}
    }
    
}
