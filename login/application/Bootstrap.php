<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initDoctype()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
    }
    
    protected function _initConfig ()
    {
    	$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/database.ini');
    	$config2 = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
    	Zend_Registry::set('config2', $config2);
    	Zend_Registry::set('config', $config);
    	return $config;
    }
    protected function _initDatabase ()
    {
    	$db = null;
    	$config = $this->bootstrap('config')->getResource('config');
    	if (! empty($config)) {
    		$db = Zend_Db::factory($config->database);
    		$db->setFetchMode(Zend_Db::FETCH_ASSOC);
    		$db->query(new Zend_Db_Expr('SET NAMES utf8'));
    		Zend_Registry::set('db', $db);
    		Zend_Db_Table_Abstract::setDefaultAdapter($db);
    	}
    	
    	return $db;
    }
    
    
	protected function _initTranslator(){
		$domain = Zend_Registry::get('config2')->production->domain;
        if (!isset($_COOKIE['lang'])) {
        	setcookie('lang', 'bg', time()+30672000, '/',$domain );
        	$lang = 'bg';
        } else {
        	$lang = $_COOKIE['lang'];
        }
        try
        {	
        	$path = APPLICATION_PATH . DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'languages'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.$lang.'.ini';
	        $contentObj = new Zend_Config_Ini($path);
	        $translate = new Zend_Translate(
	        		array(
	        				'adapter' => 'array',
	        				'content' => $contentObj->toArray(),
	        				'locale'  => $lang
	        		)
	        );
	        Zend_Validate_Abstract::setDefaultTranslator($translate);
	        Zend_Form::setDefaultTranslator($translate);
	        Zend_Registry::set('Zend_Translate', $translate);
        	$this->bootstrap('layout');
	        $layout = $this->getResource('layout');
			$view = $layout->getView();
			
			$view->domain = $domain;
	        return $translate;
        }    
	    catch(Exception $e)
        {	
            $translate = new Zend_Translate(
            		array(
            				'adapter' => 'array',
            		)
            );
           
        	return $translate;
        }
       
    }

}

