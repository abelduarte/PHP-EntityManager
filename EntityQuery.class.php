<?php

/**
 * Class EntityQuery
 */
class EntityQuery
{
	/**
	 * @var PDOStatement
     */
	protected $sqlStatement;
	/**
	 * @var array
     */
	protected $params;

	/**
	 * EntityQuery constructor.
	 * @param $statement
     */
	function __construct($statement)
	{
		$this->sqlStatement = $statement;
	}

	/**
	 * @param array $params
     */
	public function bindParams($params)
	{
		$this->params = $params;
	}

	/**
	 * @param $entityClass
	 * @return array
     */
	public function getResultList($entityClass)
	{
		$this->sqlStatement->execute($this->params);
		
		if($this->sqlStatement->rowCount() > 0)
		{
			$entities = array();
			
			$rows = $this->sqlStatement->fetchAll();
			foreach($rows as $row)
			{
				/** @var Entity $entity */
				$entity = new $entityClass($this);
				$entity->init($row);
				
				array_push($entities, $entity);
			}
			
			return $entities;
		}
		
		return array();
	}
}	

?>