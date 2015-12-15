<?php

class Entity
{
	protected $entityManager;
	protected $tableName;
	protected $primaryKey;
	protected $primaryKeyName = "id";
	public $object;
	
	function __construct($entityManager, $tableName)
	{
		$this->entityManager = $entityManager;
		$this->tableName = $tableName;
		$this->object = new stdClass();
	}
	
	public function __get($property) 
   {
        if (!property_exists($this, $property)) 
        {
            if(property_exists($this->object, $property))
            {
	            return $this->object->$property;
            }
        }
    }

    public function __set($property, $value) 
    {
        if (!property_exists($this, $property)) 
        {
            $this->object->$property = $value;
        }
    }
	
	public function setEntityManager($entityManager)
	{
		$this->entityManager = $entityManager;
	}
	
	public function setPrimaryKey($primaryKey)
	{
		$keyName = $this->primaryKeyName;
		$this->primaryKey = $primaryKey;
		$this->object->$keyName = $primaryKey;
	}
	
	public function getPrimaryKey()
	{
		return $this->primaryKey;
	}
	
	public function setPrimaryKeyName($primaryKeyName)
	{
		$this->primaryKeyName = $primaryKeyName;
	}
	
	public function getPrimaryKeyName()
	{
		return $this->primaryKeyName;
	}
	
	public function setTableName($tableName)
	{
		$this->tableName = $tableName;
	}
	
	public function getTableName()
	{
		return $this->tableName;
	}
	
	public function init($object)
	{
		$this->object = (object)$object;
	}
	
	public function setProperty($property, $value)
	{
		$this->object->$property = $value;
	}
	
	public function getProperty($property)
	{
		return $this->object->$property;
	}
	
	public function load()
	{
		$this->entityManager->load($this, $this->tableName);	
	}
	
	public function create()
	{
		$pk = $this->entityManager->create($this->tableName, $this->object);
		$this->setPrimaryKey($pk);
		
		return $pk;
	}
	
	public function lock($criteria = null)
	{
		if(!isset($criteria))
			$criteria = array("id" => $this->getPrimaryKey());
			
		return $this->entityManager->lock($this->tableName, $this->object, $criteria);
	}
	
	public function update($criteria = null)
	{
		if(!isset($criteria))
			$criteria = array("id" => $this->getPrimaryKey());
			
		return $this->entityManager->update($this->tableName, $this->object, $criteria);
	}
	
	public function remove($criteria = null)
	{
		if(!isset($criteria))
			$criteria = array("id" => $this->getPrimaryKey());
			
		return $this->entityManager->remove($this->tableName, $criteria);
	}
}
	
?>