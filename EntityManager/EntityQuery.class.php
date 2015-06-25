<?php 

class EntityQuery
{	
	protected $sqlStatement;
	protected $params;

	function __construct($statement)
	{
		$this->sqlStatement = $statement;
	}

	public function bindParams($params)
	{
		$this->params = $params;
	}

	public function getResultList($entityClass)
	{
		$this->sqlStatement->execute($this->params);
		
		if($this->sqlStatement->rowCount() > 0)
		{
			$entities = array();
			
			$rows = $this->sqlStatement->fetchAll();
			foreach($rows as $row)
			{
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