<?php

/**
 * Class PersistenceContext
 */
class PersistenceContext
{
	/**
	 * @var PDO
     */
	public $pdo;
	/**
	 * @var EntityManager
     */
	public $entityManager;

	/**
	 * PersistenceContext constructor.
	 * @param $host
	 * @param $database
	 * @param $username
	 * @param $password
	 * @param string $engine
	 * @param string $charset
     */
	public function __construct($host, $database, $username, $password, $engine = 'mysql', $charset = 'utf8')
	{
		$this->engine = $engine;
		$this->host = $host;
		$this->charset = $charset;
		$this->database = $database;
		$this->user = $username;
		$this->pass = $password;

		$dns = $this->engine.':dbname='.$this->database.";host=".$this->host.";charset=".$this->charset;

		$this->pdo = new PDO($dns, $this->user, $this->pass);
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$this->entityManager = new EntityManager($this->pdo);
	}

	/**
	 * @return EntityManager
     */
	public function entityManager()
	{
		return $this->entityManager;
	}
}

?>