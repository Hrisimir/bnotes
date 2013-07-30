<?php
class Model_Import extends Model_Abstract
{
	 
	//protected $_dbTableClass = 'Model_DbTable_Person';
	protected $id;
	/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param field_type $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

  
    
}