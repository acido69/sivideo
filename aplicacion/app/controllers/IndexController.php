<?php
require_once 'Zend/Gdata.php';
require_once 'Zend/Gdata/Query.php';
require_once 'Zend/Gdata/YouTube.php';
class IndexController extends Zend_Controller_Action
{
	private $config;
	public function preDispatch()
	{
        $this->config		= new Zend_Config_Xml(dirname(__FILE__) . '/../etc/DataUser.xml', 'youtube');
        $yt 				= new Zend_Gdata_YouTube();	
	    $this->view->video	= $yt->getUserFavorites($this->config->user);
	    $this->view->title	= 'La Calle 13';

	    $this->_getVideos();
    }
	public function indexAction()
    {
    	$_i =0;
    	foreach ($this->view->video as $_e)
    	{
    		$_i++;
    	}
    	$showvid = rand(0, $_i-1);
    	if($showvid<0)
    		$showvid=0;
    	$this->view->showvid = $showvid;
    }
    public function openAction()
    {
    	$_id				= $this->_request->getParam('id', 0);
    	$this->view->title	.=' :: video :: '.$this->_request->getParam('title', 0); 
    	foreach ($this->view->video as $video)
    	{
    		$_idvideo	= explode('?v=', $video->mediaGroup->player[0]->url);
    		if($_id == $_idvideo[1])
    		{
    			$this->view->video_principal = $video->mediaGroup->content[0]->url;
    			break;
    		}    		
    	}

    }
    
    private function _getVideos()
    {
    	$yt 				= new Zend_Gdata_YouTube();	
	    $favorites			= $yt->getUserFavorites($this->config->user);
	    $videos				= array();
	    foreach ($favorites as $video){
	    	$player		= $video->mediaGroup->player[0]->url;
	    	$content	= $video->mediaGroup->content[0]->url;
	    	$thumbnail	= $video->mediaGroup->thumbnail[0]->url;
	    	$title		= $video->mediaGroup->title->text;
	    	$id			= $this->_getIdPlayer($player);
	    	$videos[$id] = array('player'	=>$player, 
	    						'content'	=>$content, 
	    						'thumbnail'	=>$thumbnail,
	    						'title'		=>$title,
	    						'id'		=>$id);
	    }
	    return $videos;
	    
    }
    
    private function _getIdPlayer($urlPlayer){
	    $id = explode('?v=', $urlPlayer);
	    return $id[1];
    }
}