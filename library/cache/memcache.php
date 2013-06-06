<?php 
class Cache_Memcache extends cache{

	// Memcache has a maximum cache lifetime of 30 days
	const CACHE_CEILING = 2592000;

	protected $_memcache;

	protected $_config;
	
	protected $_flags;

	protected $_default_config = array();

	public function _sanitize_id($id)
	{
		return str_replace(array('/', '\\', ' '), '_', $id);
	}

	protected function __construct(array $config)
	{

		if ( ! extension_loaded('memcache'))
		{
			die('Memcache PHP extention not loaded');
		}
		parent::__construct($config);
		$this->_memcache = new Memcache;
		$this->_config = $config;
		$serverArr = $config;
		//$server =$this->_default_config;
		foreach ($serverArr as $server) {
			if(!$this->_memcache->addServer($server['host'], $server['port'], $server['persistent'], $server['weight'], $server['timeout'], $server['retry_interval'], $server['status'], $server['failure_callback']))
			{
				die('Memcache could not connect to host \':host\' using host:'.$server['host'].',    port:'.$server['port']);
			}
		}
		$this->_flags = FALSE;
	}


	public function get($id, $default = NULL)
	{
		// Get the value from Memcache
		$value = $this->_memcache->get($this->_sanitize_id($id));

		// If the value wasn't found, normalise it
		if ($value === FALSE)
		{
			$value = (NULL === $default) ? NULL : $default;
		}

		// Return the value
		return $value;
	}


	public function set($id, $data, $lifetime = 3600)
	{
		// If the lifetime is greater than the ceiling
		if ($lifetime > Cache_Memcache::CACHE_CEILING)
		{
			// Set the lifetime to maximum cache time
			$lifetime = Cache_Memcache::CACHE_CEILING ;
		}else{
			// Normalise the lifetime
			$lifetime = 0;
		}
		// Set the data to memcache
		return $this->_memcache->set($this->_sanitize_id($id), $data, $this->_flags, $lifetime);
	}


	public function delete($id, $timeout = 0)
	{
		// Delete the id
		return $this->_memcache->delete($this->_sanitize_id($id), $timeout);
	}


	public function delete_all()
	{
		$result = $this->_memcache->flush();
		sleep(1);
		return $result;
	}


	public function _failed_request($hostname, $port)
	{
		if ( ! $this->_config['instant_death'])
		return;

		$host = FALSE;

		if ( ! $host)
		return;
		else
		{
			return $this->_memcache->setServerParams(
			$host['host'],
			$host['port'],
			$host['timeout'],
			$host['retry_interval'],
			FALSE, // Server is offline
			array($this, '_failed_request'
			));
		}
	}


	public function increment($id, $step = 1)
	{
		return $this->_memcache->increment($id, $step);
	}


	public function decrement($id, $step = 1)
	{
		return $this->_memcache->decrement($id, $step);
	}
}