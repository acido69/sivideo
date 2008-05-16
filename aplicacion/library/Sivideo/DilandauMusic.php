<?php
class DilandauMusic{
	
	/*
	 * @
	 */
	private $_expFile 		= '(<h2\s+class="title"\s+title="(.*)">.+\n.+\n.+\n.+\n.+/download/(.+)" target)';
	private $_expPagination	= '(href="/descargar_musica/@@string@@\-(\d+)\.html)';
	private $_url			= 'http://www.dilandau.com/descargar_musica/@@string@@-@@pagination@@.html';
	private $_prefixDownload= 'http://www.dilandau.com/download/';
	private	$_totalPages	= null;
	private	$_options		= array('page'=>1);
	private	$_search		= null;
	private $_htmlPage		= null;
	
	public function __construct($search, $options=null){
		$this->_search = $search;
		if(is_array($options)){
			$this->_options	= $options;
		}
		$this->_htmlPage	= $this->_getHtmlPage();
	}
	private function _prepareUrl(){
		$_url = str_replace('@@string@@', urlencode(strtolower((string) $this->_search)), $this->_url);
		$_url = str_replace('@@pagination@@', (string) $this->_options['page'], $_url);
		return $_url;
	}
	private function _prepareExpPagination(){
		$this->_expPagination = str_replace('@@string@@', $this->_search, $this->_expPagination);
	}
	
	private function _getHtmlPage(){
		return @file_get_contents($this->_prepareUrl());
	}
	public function setOptionPage($num){
		$this->_options['page'] = $num;
	}
	public function getFiles($numPage=null){
		if(is_int($numPage)){
			$this->setOptionPage($numPage);
			$this->_htmlPage = $this->_getHtmlPage();
		}
		$_files			= array();
		if(preg_match_all($this->_expFile, $this->_htmlPage, $info, PREG_SET_ORDER)) {
	        foreach($info as $file) {
	            $_files[] = array(
	            'title' => $file[1],
	            'url' => $this->_prefixDownload.$file[2]
	            );
	        } 
	    }
		return $_files;
	}
	private function _setTotalPages(){
		
		$this->_prepareExpPagination();
		$_totalPages		= 0;
		if(preg_match_all($this->_expPagination, $this->_htmlPage, $info, PREG_SET_ORDER)) {
	        foreach($info as $paginas) {
	            if($_totalPages < (int)$paginas[1]){
	            	$_totalPages = (int) $paginas[1];
	            }
	        } 
	    }
	    $this->_totalPages = $_totalPages;
	}
	public function getTotalPages(){
		if(!isset($this->_totalPages)){
			$this->_setTotalPages();
		}
	    return $this->_totalPages;
	}
	public function getAllFiles(){
		$_files=array();
		for($_i=2;$_i<=$this->getFiles();$_i++){
			$_files=array_merge($_files, $this->getFiles($_i));
		}
		return $_files;
	}
	
}
?>