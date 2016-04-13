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
	 * @var string
     */
	protected $engine;
	/**
	 * @var string
     */
	protected $host;
	/**
	 * @var string
     */
	protected $charset;
	/**
	 * @var string
     */
	protected $database;
	/**
	 * @var string
     */
	protected $user;
	/**
	 * @var string
     */
	protected $pass;

	/**
	 * @var bool
     */
	protected $connected;

	/**
	 * PersistenceContext constructor.
	 * @param string $host
	 * @param string $database
	 * @param string $username
	 * @param string $password
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
		$this->connected = false;

		$dns = $this->engine.':dbname='.$this->database.";host=".$this->host.";charset=".$this->charset;

		try
		{
			$this->pdo = new PDO($dns, $this->user, $this->pass);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->entityManager = new EntityManager($this->pdo);
			$this->connected = true;
		}
		catch(Exception $exception)
		{
			$this->entityManager = null;
			$this->connected = false;
		}
	}

	/**
	 * @return EntityManager
     */
	public function entityManager()
	{
		return $this->entityManager;
	}

	/**
	 * @return bool
     */
	public function isConnected()
	{
		return $this->connected;
	}
}

?>