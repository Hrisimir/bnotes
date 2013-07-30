<?php
class S_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch (Zend_Controller_Request_Abstract $request)
    {
    	$mname = $request->getModuleName();
    	$cname = $request->getControllerName();
    	$aname = $request->getActionName();
    	
    	if ( $mname == 'default' and $cname == 'auth' and $aname == 'process' ){
    		
    	} else {
    		//redirect kym logina
    		if($_COOKIE['SOfficeOAuth'] != ''){
    			
    			$user = unserialize(base64_decode($_COOKIE['SOfficeOAuth']));
    			
    			$db 	= Zend_Registry::get('db');
    			$table 	= new Zend_Db_Table('session');
    			$select = $table->select()->where('hash = '.$db->quote($user['u'], 'string'));
    			$row = $db->fetchRow($select);
    			if($row == NULL ){
    				echo 'tuk be';
    				setcookie("SOfficeOAuth", "", time()-3600);
    				header("Location: http://auth.soffice.net");die;
    			}
    			if($row != null && $row->id_user != null){
    				$table->delete('id_user = '. $db->quote($row->id_user));
    				$hash = sha1(time(). md5(mt_rand(0, 999999999)));
    				if( $table->insert(array('hash'=>$hash,'id_user'=>$row->id_user ))){
    					$user['u'] = $hash;
    					setcookie('SOfficeOAuth',base64_encode(serialize($user)),time()+1800,'/','.soffice.net');
    				}
    			} 
    		} else {
    			header("Location: http://auth.soffice.net");
    		}
    	}
    	
        
    }
}