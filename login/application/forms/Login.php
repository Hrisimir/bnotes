<?php

class Application_Form_Login extends Zend_Form
{

	public function init() {
		
		$elementDecorators = array(
				'ViewHelper',
				array('Errors', array('class' => 'message error')),
				array('Description', array('tag' => 'p', 'class' => 'description')),
				array('HtmlTag',     array('class' => 'form-div')),
				array('Label',       array('class' => 'form-label', 'requiredSuffix' => ' *'))
				// comment out or remove the Label decorator from the element in question
				// you can do the same for any of the decorators if you don't want them rendered
		);
		
        $username = $this->addElement('text', 'username', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'EmailAddress'
            ),
            'required' => true,
            'label' => 'E-mail address',
        	'decorators' =>$elementDecorators
        ));

        $password = $this->addElement('password', 'password', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                'Alnum',
                array('StringLength', false, array(6, 20)),
                
            ),
            'required' => true,
            'label' => 'password',
        	'decorators' =>$elementDecorators
         ));

        $login = $this->addElement('submit', 'login', array(
            'required' => false,
            'ignore' => true,
            'label' => 'enter',
        	'class' => 'button button-gray',
                ))->removeDecorator('label');
    }
}
