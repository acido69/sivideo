<?php

/**
 * sonidoController
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';
require_once 'Sivideo/DilandauMusic.php';

class sonidoController extends Zend_Controller_Action {
	public function preDispatch(){
		$this->view->title = 'Sonido :: Mp3 para descargar y escuchar';
	}
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		// TODO Auto-generated sonidoController::indexAction() default action
		$_mp3s					= new DilandauMusic('La calle 13');
		$this->view->numPaginas	= $_mp3s->getTotalPages();
	    $this->view->sonido		= array_merge($_mp3s->getFiles(), $_mp3s->getFiles(2));
	    //$this->view->sonido2		= $_mp3s->getAllFiles(2);
	    
	}
}
