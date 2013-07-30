<?php

class Form_Person extends Zend_Form
{
	public function init() {
		
		$elementDecorators = array(
				'ViewHelper',
				array('Errors', array('class' => 'errors')),
				array('Description', array('tag' => 'small', 'class' => 'description')),
				array('HtmlTag',     array('class' => 'form-div')),
				array('Label',       array('class' => 'form-label', 'requiredSuffix' => ' *'))
				// comment out or remove the Label decorator from the element in question
				// you can do the same for any of the decorators if you don't want them rendered
		);
		
		$butonDecorators = array(
				'ViewHelper',
				array('Errors', array('class' => '')),
				array('Description', array('tag' => 'p', 'class' => 'description')),
				array('HtmlTag',     array('class' => 'form-div'))
				// comment out or remove the Label decorator from the element in question
				// you can do the same for any of the decorators if you don't want them rendered
		);
		$id = $this->addElement('hidden', 'id', array(
				'filters' => array(),
				'validators' => array(),
				'required' => true
		));
		$firstname = $this->addElement('text', 'firstname', array(
				'filters' => array(),
				'validators' => array(
						
				),
				'required' => true,
				'label' => 'Firstname',
				'decorators' => $elementDecorators
		));
		
		$lastname = $this->addElement('text', 'lastname', array(
				'filters' => array(),
				'validators' => array(
				),
				'required' => true,
				'label' => 'Lastname',
				'decorators' => $elementDecorators
		));
		$title = $this->addElement('text', 'title', array(
				'filters' => array(),
				'validators' => array(
				),
				'label' => 'Title',
				'decorators' => $elementDecorators
		));
			$companyModel = new Model_Company();
	        $multiOptions = $companyModel->fetchPairs();
		 	$company = $this->addElement('select', 'company', array(
            'required' => true,
            'label' => 'Company',
        	'multioptions' => $multiOptions,
        	'decorators' => $elementDecorators,
        	'class' => ''
        ));
				
        $phone = $this->addElement('text', 'phone1', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'validators' => array(
        	'Alnum'
            ),
            'required' => false,
            'label' => 'Phone',
        	'decorators' => $elementDecorators
         ));
         
         $email = $this->addElement('text', 'email', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'validators' => array(
        	'Email'
            ),
            'required' => false,
            'label' => 'E-mail',
        	'decorators' => $elementDecorators
         ));
         
          $website = $this->addElement('text', 'website', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'validators' => array(
            ),
            'required' => false,
            'label' => 'Website',
        	'decorators' => $elementDecorators
         ));
         
         
         $address = $this->addElement('textarea', 'address', array(
            'filters' => array( ),
            'validators' => array(),
            'required' => false,
            'label' => 'Address',
        	'decorators' => $elementDecorators
         ));
         

     /*   $password = $this->addElement('password', 'password', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                'Alnum',
                array('StringLength', false, array(6, 50)),
                
            ),
            'required' => true,
            'label' => 'Password',
        	'decorators' => $elementDecorators
        ));
        $password2 = $this->addElement('password', 'password2', array(
        		'filters' => array('StringTrim'),
        		'validators' => array(
        				'Alnum',
        				array('StringLength', false, array(6, 50)),
        				array('identical', false, array('token' => 'password'))
        		),
        		'required' => false,
        		'label' => 'Confirm password',
        		'decorators' => $elementDecorators
        ));*/
        $login = $this->addElement('submit', 'save', array(
            'required' => false,
            'ignore' => true,
            'label' => 'Save',
        	'class' => 'button button-gray',
        	'decorators' => $butonDecorators
        		
        ));
    }
}
