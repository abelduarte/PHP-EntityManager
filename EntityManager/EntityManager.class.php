<?php 

class EntityManager
{	
	protected $database;
	
	function __construct($database)
	{
		$this->database = $database;
		$this->sqlStatement = null;
	}
	
	function errorCode(){
		return $this->database->errorCode;
	}
	
	function errorInfo(){
		return $this->database->errorInfo;
	}
	
	function lastInsertId(){
		return $this->database->lastInsertId;
	}
	
	function beginTransaction(){
		$this->database->beginTransaction();
	}
	
	function inTransaction(){
		return $this->database->inTransaction();
	}
	
	function commit(){
		$this->database->commit();
	}
	
	function rollback(){
		$this->database->rollback();
	}
	
	function prepare($sqlQuery){
		return $this->database->prepare($sqlQuery);
	}
	
	public function load($entity, $tableName)
	{
		$primaryKey = $entity->getPrimaryKey();
		$primaryKeyName = $entity->getPrimaryKeyName();
		$criteria = array($primaryKeyName => $primaryKey);
		
		$where = $this->buildWhereClause($criteria);
		
		$sqlQuery = "SELECT * FROM ".$tableName." WHERE ".$where;
		$stm = $this->database->prepare($sqlQuery);
		
		// Bind the where values
		$parameterIndex = 1;
		foreach($criteria as $fieldName => &$value)
		{
			$stm->bindParam($parameterIndex, $value);
			$parameterIndex++;
		}
		
		$stm->execute();
		
		if($stm->rowCount() > 0)
		{	
			$row = $stm->fetch(PDO::FETCH_OBJ);
			$entity->init($row);
			
			$primaryKeyName = $entity->getPrimaryKeyName();
			$entity->setPrimaryKey($entity->object->$primaryKeyName);
		}
	}
	
	public function createManagedEntity($tableName = null, $entityClass = "Entity")
	{
		$entity = new $entityClass($this, $tableName);
		return $entity;
	}
	
	public function getReference($pk, $tableName = null, $entityClass = "Entity")
	{	
		$entity = new $entityClass($this, $tableName);
		$entity->setEntityManager($this);
		$entity->setPrimaryKey($pk);
		return $entity;
	}
	
	public function findOne($tableName, $criteria, $entityClass = "Entity")
	{	
		$where = $this->buildWhereClause($criteria);
		
		$sqlQuery = "SELECT * FROM ".$tableName." WHERE ".$where." LIMIT 0, 1";
		$stm = $this->database->prepare($sqlQuery);
		
		// Bind where parameters
		$parameterIndex = 1;
		foreach($criteria as $columnName => &$value){
			$stm->bindParam($parameterIndex, $value);
			$parameterIndex++;
		}
		
		$stm->execute();
		
		if($stm->rowCount() > 0)
		{
			$entities = array();
			
			$rows = $stm->fetchAll(PDO::FETCH_OBJ);
			foreach($rows as $row)
			{
				$entity = new $entityClass($this, $tableName);
				$entity->init($row);
				
				$primaryKeyName = $entity->getPrimaryKeyName();
				$entity->setPrimaryKey($entity->object->$primaryKeyName);
				
				array_push($entities, $entity);
			}
			
			return $entities[0];
		}
		
		return null;
	}
	
	public function findBy($tableName, $criteria, $offset, $limit, $entityClass = "Entity")
	{	
		$where = $this->buildWhereClause($criteria);
		
		$sqlQuery = "SELECT * FROM ".$tableName." WHERE ".$where." LIMIT ".$offset.", ".$limit;
		$stm = $this->database->prepare($sqlQuery);
		
		// Bind where parameters
		$parameterIndex = 1;
		foreach($criteria as $columnName => &$value){
			$stm->bindParam($parameterIndex, $value);
			$parameterIndex++;
		}
		
		$stm->execute();
		
		if($stm->rowCount() > 0)
		{
			$entities = array();
			
			$rows = $stm->fetchAll(PDO::FETCH_OBJ);
			foreach($rows as $row)
			{
				$entity = new $entityClass($this, $tableName);
				$entity->init($row);
				
				$primaryKeyName = $entity->getPrimaryKeyName();
				$entity->setPrimaryKey($entity->object->$primaryKeyName);
				
				array_push($entity);
			}
			
			return $entities;
		}
		
		return array();
	}
	
	public function findAll($tableName = null, $criteria = null, $offset = 0, $count = 100, $entityClass = "Entity")
	{	
		$sqlQuery = "SELECT * FROM ".$tableName;
		
		if(isset($criteria))
		{
			$where = $this->buildWhereClause($criteria);
			$sqlQuery .= " WHERE ".$where;	
		}
		
		$sqlQuery .= " LIMIT $offset, $count";
		
		$stm = $this->database->prepare($sqlQuery);
		
		if(isset($criteria))
		{
			// Bind where parameters
			$parameterIndex = 1;
			foreach($criteria as $columnName => &$value){
				$stm->bindParam($parameterIndex, $value);
				$parameterIndex++;
			}	
		}
		
		$stm->execute();
		
		if($stm->rowCount() > 0)
		{
			$entities = array();
			
			$rows = $stm->fetchAll(PDO::FETCH_OBJ);
			foreach($rows as $row)
			{
				$entity = new $entityClass($this, $tableName);
				$entity->init($row);
				
				$primaryKeyName = $entity->getPrimaryKeyName();
				$entity->setPrimaryKey($entity->object->$primaryKeyName);
				
				array_push($entities, $entity);
			}
			
			return $entities;
		}
		
		return array();
	}
	
	public function createQuery($sqlQuery)
	{
		$sqlStatement = $this->database->prepare($sqlQuery);
		return new EntityQuery($sqlStatement);
	}
	
	public function lock($tableName, $values, $criteria)
	{
		$set = $this->buildSetClause($values);
		$where = $this->buildWhereClause($criteria);
		
		// Lock the row
		$sqlQuery = "SELECT * FROM ".$tableName." WHERE ".$where." FOR UPDATE";
		
		$stm = $this->database->prepare($sqlQuery);
		
		$parameterIndex = 1;
		// Bind where values
		foreach($criteria as $fieldName => &$value){
			$stm->bindParam($parameterIndex, $value);
			$parameterIndex++;
		}
		
		return $stm->execute();
	}
	
	public function update($tableName, $values, $criteria)
	{
		
		$set = $this->buildSetClause($values);
		$where = $this->buildWhereClause($criteria);
		
		// Lock the row
		$sqlQuery = "SELECT * FROM ".$tableName." WHERE ".$where." FOR UPDATE";
		
		$stm = $this->database->prepare($sqlQuery);
		
		$parameterIndex = 1;
		// Bind where values
		foreach($criteria as $fieldName => &$value){
			$stm->bindParam($parameterIndex, $value);
			$parameterIndex++;
		}
		
		$stm->execute();
		
		// Update
		$sqlQuery = "UPDATE ".$tableName." SET ".$set." WHERE ".$where;
		$stm = $this->database->prepare($sqlQuery);
		
		// Bind set values
		$parameterIndex = 1;
		foreach($values as $fieldName => &$value){
			$stm->bindParam($parameterIndex, $value);
			$parameterIndex++;
		}
		
		// Bind where values
		foreach($criteria as $fieldName => &$value){
			$stm->bindParam($parameterIndex, $value);
			$parameterIndex++;
		}
			
		return $stm->execute();
	}
	
	public function remove($tableName, $criteria)
	{
		$where = $this->buildWhereClause($criteria);
		
		$sqlQuery = "DELETE FROM ".$tableName." WHERE ".$where;

		$stm = $this->database->prepare($sqlQuery);

		// Bind where values
		$parameterIndex = 1;
		foreach($criteria as $fieldName => &$value)
		{
			$stm->bindParam($parameterIndex, $value);
			$parameterIndex++;
		}
		
		return $stm->execute();
	}
	
	public function create($tableName, $values)
	{
		$insert = $this->buildInsertClause($values);
		
		$sqlQuery = "INSERT INTO ".$tableName."  ".$insert;
		
		$stm = $this->database->prepare($sqlQuery);

		// Bind where values
		$parameterIndex = 1;
		foreach($values as $fieldName => &$value)
		{
			$stm->bindParam($parameterIndex, $value);
			$parameterIndex++;
		}
		
		$stm->execute();
		
		if($stm->rowCount() > 0)
		{
			return $this->database->lastInsertId();	
		}
		else
		{
			return -1;
		}
	}
	
	public function createMany($tableName, $keys, $values)
	{	
		$fieldNames = implode(", ", $keys);
		
		$sqlQuery = "INSERT INTO ".$tableName." ";
		$sqlQuery .= "(".$fieldNames.") VALUES ";
		
		$valuesString = "";
		$valueIndex = 1;
		$valueCount = count($values);
		
		foreach($values as $value)
		{
			$valuesString .= "(";
			
			$keyIndex = 1;
			$keyCount = count($keys);
			
			foreach($keys as $key)
			{
				//$valuesString .= $value[$key];
				$valuesString .= "?";
				
				if($keyIndex < $keyCount)
					$valuesString .= ", ";
				
				$keyIndex++;
			}
			
			if($valueIndex < $valueCount)
				$valuesString .= "), ";
			else if($valueIndex == $valueCount)
				$valuesString .= ");";
				
			$valueIndex++;
		}
		
		$sqlQuery .= $valuesString;
		
		$stm = $this->database->prepare($sqlQuery);

		// Bind values
		$parameterIndex = 1;
		foreach($values as $value)
		{			
			foreach($keys as $key)
			{
				$stm->bindParam($parameterIndex, $value[$key]);
				$parameterIndex++;
			}
		}
		
		$stm->execute();
		
		if($stm->rowCount() > 0)
		{
			return true;	
		}
		
		return false;
	}
	
	// Query building
	
	public function buildWhereClause($criteria)
	{
		$where = "";
		$isFirst = true;
		
		foreach($criteria as $columnName => $value)
		{
			if($isFirst)
			{
				$where .= $columnName." = ?";
				$isFirst = false;
			}
			else 
			{
				$where .= " AND ".$columnName." = ?";
			}
		}
		
		return $where;
	}
	
	public function buildSetClause($values){
		
		$set = "";
		$isFirst = true;
		
		foreach($values as $columnName => $value)
		{
			if($isFirst)
			{
				$set .= $columnName." = ?";
				$isFirst = false;
			}
			else 
			{
				$set .= ", ".$columnName." = ?";
			}
		}
		
		return $set;	
	}
	
	public function buildInsertClause($values){
		$insert = "";
		$isFirst = true;
		
		$tablefields = "";
		$tablevalues = "";
		
		foreach($values as $field => $value){
			if($isFirst)
			{
				$tablefields .= $field;
				$tablevalues .= "?";
				$isFirst = false;
			}
			else
			{
				$tablefields .= ", ".$field;
				$tablevalues .= ", ?";
			}
		}
		
		$insert = "(".$tablefields.") VALUES (".$tablevalues.")";
		
		return $insert;	
	}

}	

?>