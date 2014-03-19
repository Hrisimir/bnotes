<?php
/**
 * 	Translation plugin
 * 	@author Hrisimir Cholakov <hr.cholakov@gmail.com>
 *  
 */
class S_Controller_Plugin_Language extends Zend_Controller_Plugin_Abstract
{	
	/**
	 * 	If a cookie with a language code is set reads translations and stores them in the registry. 
	 * 	English is the default language.
	 *  @param Zend_Controller_Request_Abstract $request
	 *   
	 */
	
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $ini = '';
		if (!isset($_COOKIE['lang'])) {
            setcookie('lang', 'en', time()+30672000, '/', Zend_Registry::get('config2')->production->domain);
            $lang = 'en';
        } else {
            $lang = $_COOKIE['lang'];
        }
       	
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
    }

}