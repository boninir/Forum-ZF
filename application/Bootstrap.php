<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	public function run()
	{
		parent::run();
	}

	protected function _initConfig()
	{		
		Zend_Registry::set('config', new Zend_Config($this->getOptions()));
	}

	protected function _initLoaderForum()
	{
		$autoloader = Zend_Loader_Autoloader::getInstance();
		// $autoloader->registerNamespace('Forum_');
		$autoloader->setFallbackAutoloader(true);
	}

	protected function _initSession()
	{
		$session = new Zend_Session_Namespace('forum', true);
		return $session;
	}

	protected function _initJquery()
	{
		$view = new Zend_View();
		$view->AddHelperPath('ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');

		$view->JQuery()
			 ->enable()
			 ->addStylesheet('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/ui-lightness/jquery-ui.css')//add the css
			 ->uiEnable();//enable ui

		//on ajoute une viewRenderer
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$viewRenderer->setView($view);

		return $view;
	}

	protected function _initDb()
	{
		$db = Zend_Db::factory(Zend_Registry::get('config')->database); // on initialise la base de données
		Zend_Db_Table_Abstract::setDefaultAdapter($db); // on initialise l'adapter de Zend_Db_Table
		Zend_Registry::set('db', $db);
	}

	// protected function _initMails()
	// {
	// 	$config = new Zend_Config_Ini(APPLICATION_PATH . '/config/application.ini', 'mail');
	// 	$mailConfig = $config->toArray();

	// 	Zend_Registry::set('Mail_Config', $mailConfig['mail']['config']);
	// }

}

