<?php

/**
 * Class Entity
 */
class Entity
{
	/**
	 * @var EntityManager
     */
	protected $entityManager;
	/**
	 * @var string
     */
	protected $tableName;
	/**
	 * @var string
     */
	protected $primaryKey;
	/**
	 * @var string
     */
	protected $primaryKeyName = "id";
	/**
	 * @var stdClass
     */
	public $object;

	/**
	 * Entity constructor.
	 * @param $entityManager
	 * @param $tableName
     */
	function __construct($entityManager, $tableName)
	{
		$this->entityManager = $entityManager;
		$this->tableName = $tableName;
		$this->object = new stdClass();
	}

	/**
	 * @param $property
	 * @return mixed
     */
	public function __get($property)
   {
        if (!property_exists($this, $property)) 
        {
            if(property_exists($this->object, $property))
            {
	            return $this->object->$property;
            }
        }

	   return null;
    }

	/**
	 * @param $property
	 * @param $value
     */
	public function __set($property, $value)
    {
        if (!property_exists($this, $property)) 
        {
            $this->object->$property = $value;
        }
    }

	/**
	 * @param EntityManager $entityManager
     */
	public function setEntityManager($entityManager)
	{
		$this->entityManager = $entityManager;
	}


	/**
	 * @param string $primaryKey
     */
	public function setPrimaryKey($primaryKey)
	{
		$keyName = $this->primaryKeyName;
		$this->primaryKey = $primaryKey;
		$this->object->$keyName = $primaryKey;
	}

	/**
	 * @return string
     */
	public function getPrimaryKey()
	{
		return $this->primaryKey;
	}

	/**
	 * @param string $primaryKeyName
     */
	public function setPrimaryKeyName($primaryKeyName)
	{
		$this->primaryKeyName = $primaryKeyName;
	}

	/**
	 * @return string
     */
	public function getPrimaryKeyName()
	{
		return $this->primaryKeyName;
	}

	/**
	 * @param string $tableName
     */
	public function setTableName($tableName)
	{
		$this->tableName = $tableName;
	}

	/**
	 * @return string
     */
	public function getTableName()
	{
		return $this->tableName;
	}

	/**
	 * @param $object
     */
	public function init($object)
	{
		$this->object = (object)$object;
	}

	/**
	 * @param $property
	 * @param $value
     */
	public function setProperty($property, $value)
	{
		$this->object->$property = $value;
	}

	/**
	 * @param $property
	 * @return mixed
     */
	public function getProperty($property)
	{
		return $this->object->$property;
	}

	/**
	 *
     */
	public function load()
	{
		$this->entityManager->load($this, $this->tableName);	
	}

	/**
	 * @return mixed
     */
	public function create()
	{
		$pk = $this->entityManager->create($this->tableName, $this->object);
		$this->setPrimaryKey($pk);
		
		return $pk;
	}

	/**
	 * @param null $criteria
	 * @return mixed
     */
	public function lock($criteria = null)
	{
		if(!isset($criteria))
			$criteria = array("id" => $this->getPrimaryKey());
			
		return $this->entityManager->lock($this->tableName, $criteria);
	}

	/**
	 * @param null $criteria
	 * @return mixed
     */
	public function update($criteria = null)
	{
		if(!isset($criteria))
			$criteria = array("id" => $this->getPrimaryKey());
			
		return $this->entityManager->update($this->tableName, $this->object, $criteria);
	}

	/**
	 * @param null $criteria
	 * @return mixed
     */
	public function remove($criteria = null)
	{
		if(!isset($criteria))
			$criteria = array("id" => $this->getPrimaryKey());
			
		return $this->entityManager->remove($this->tableName, $criteria);
	}
}
	
?>