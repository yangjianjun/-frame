<?php 

class Cache_File extends cache{


	protected static function filename($string)
	{
		return sha1($string).'.cache';
	}


	protected $_cache_dir;


	public function __construct(array $config)
	{
		// Setup parent
		//parent::__construct($config);
		
		try
		{
			//$directory = Arr::get($this->_config, 'cache_dir', Kohana::$cache_dir);
			$directory = $config["cache_dir"];
			$this->_cache_dir = new SplFileInfo($directory);
		}
		// PHP < 5.3 exception handle
		catch (ErrorException $e)
		{
			$this->_cache_dir = $this->_make_directory($directory, 0777, TRUE);
		}
		// PHP >= 5.3 exception handle
		catch (UnexpectedValueException $e)
		{
			$this->_cache_dir = $this->_make_directory($directory, 0777, TRUE);
		}

		// If the defined directory is a file, get outta here
		if ($this->_cache_dir->isFile())
		{
			throw new Cache_Exception('Unable to create cache directory as a file already exists : :resource', array(':resource' => $this->_cache_dir->getRealPath()));
		}

		// Check the read status of the directory
		if ( ! $this->_cache_dir->isReadable())
		{
			throw new Cache_Exception('Unable to read from the cache directory :resource', array(':resource' => $this->_cache_dir->getRealPath()));
		}

		// Check the write status of the directory
		if ( ! $this->_cache_dir->isWritable())
		{
			throw new Cache_Exception('Unable to write to the cache directory :resource', array(':resource' => $this->_cache_dir->getRealPath()));
		}
	}


	public function get($id, $default = NULL)
	{
		$filename = Cache_File::filename($this->_sanitize_id($id));
		$directory = $this->_resolve_directory($filename);

		// Wrap operations in try/catch to handle notices
		try
		{
			// Open file
			$file = new SplFileInfo($directory.$filename);

			// If file does not exist
			if ( ! $file->isFile())
			{
				// Return default value
				return $default;
			}
			else
			{
				// Open the file and parse data
				$created  = $file->getMTime();
				$data     = $file->openFile();
				$lifetime = $data->fgets();

				// If we're at the EOF at this point, corrupted!
				if ($data->eof())
				{
					throw new Cache_Exception(__METHOD__.' corrupted cache file!');
				}

				$cache = '';

				while ($data->eof() === FALSE)
				{
					$cache .= $data->fgets();
				}

				// Test the expiry
				if (($created + (int) $lifetime) < time())
				{
					// Delete the file
					$this->_delete_file($file, NULL, TRUE);
					return $default;
				}
				else
				{
					return unserialize($cache);
				}
			}
			
		}
		catch (ErrorException $e)
		{
			// Handle ErrorException caused by failed unserialization
			if ($e->getCode() === E_NOTICE)
			{
				throw new Cache_Exception(__METHOD__.' failed to unserialize cached object with message : '.$e->getMessage());
			}

			// Otherwise throw the exception
			throw $e;
		}
	}

	public function _sanitize_id($id)
	{
		return str_replace(array('/', '\\', ' '), '_', $id);
	}

	public function set($id, $data, $lifetime = NULL)
	{
		$filename = Cache_File::filename($this->_sanitize_id($id));
		$directory = $this->_resolve_directory($filename);
		// If lifetime is NULL
		if ($lifetime === NULL)
		{
			// Set to the default expiry
			$lifetime = Cache::DEFAULT_EXPIRE;
		}

		// Open directory
		$dir = new SplFileInfo($directory);

		// If the directory path is not a directory
		if ( ! $dir->isDir())
		{
			// Create the directory 
			if ( ! mkdir($directory, 0777, TRUE))
			{
				throw new Cache_Exception(__METHOD__.' unable to create directory : :directory', array(':directory' => $directory));
			}

			// chmod to solve potential umask issues
			chmod($directory, 0777);
		}

		// Open file to inspect
		$resouce = new SplFileInfo($directory.$filename);
		$file = $resouce->openFile('w');

		try
		{
			$data = $lifetime."\n".serialize($data);
			$file->fwrite($data, strlen($data));
			return (bool) $file->fflush();
		}
		catch (ErrorException $e)
		{
			// If serialize through an error exception
			if ($e->getCode() === E_NOTICE)
			{
				// Throw a caching error
				throw new Cache_Exception(__METHOD__.' failed to serialize data for caching with message : '.$e->getMessage());
			}

			// Else rethrow the error exception
			throw $e;
		}
	}


	public function delete($id)
	{
		$filename = Cache_File::filename($this->_sanitize_id($id));
		$directory = $this->_resolve_directory($filename);

		return $this->_delete_file(new SplFileInfo($directory.$filename), NULL, TRUE);
	}


	public function delete_all()
	{
		return $this->_delete_file($this->_cache_dir, TRUE);
	}


	public function garbage_collect()
	{
		$this->_delete_file($this->_cache_dir, TRUE, FALSE, TRUE);
		return;
	}


	protected function _delete_file(SplFileInfo $file, $retain_parent_directory = FALSE, $ignore_errors = FALSE, $only_expired = FALSE)
	{
		// Allow graceful error handling
		try
		{
			// If is file
			if ($file->isFile())
			{
				try
				{
					// Handle ignore files
					if (in_array($file->getFilename(), $this->config('ignore_on_delete')))
					{
						$delete = FALSE;
					}
					// If only expired is not set
					elseif ($only_expired === FALSE)
					{
						// We want to delete the file
						$delete = TRUE;
					}
					// Otherwise...
					else
					{
						// Assess the file expiry to flag it for deletion
						$json = $file->openFile('r')->current();
						$data = json_decode($json);
						$delete = $data->expiry < time();
					}

					// If the delete flag is set delete file
					if ($delete === TRUE)
						return unlink($file->getRealPath());
					else
						return FALSE;
				}
				catch (ErrorException $e)
				{
					// Catch any delete file warnings
					if ($e->getCode() === E_WARNING)
					{
						throw new Cache_Exception(__METHOD__.' failed to delete file : :file', array(':file' => $file->getRealPath()));
					}
				}
			}
			// Else, is directory
			elseif ($file->isDir())
			{
				// Create new DirectoryIterator
				$files = new DirectoryIterator($file->getPathname());

				// Iterate over each entry
				while ($files->valid())
				{
					// Extract the entry name
					$name = $files->getFilename();

					// If the name is not a dot
					if ($name != '.' AND $name != '..')
					{
						// Create new file resource
						$fp = new SplFileInfo($files->getRealPath());
						// Delete the file
						$this->_delete_file($fp);
					}

					// Move the file pointer on
					$files->next();
				}

				// If set to retain parent directory, return now
				if ($retain_parent_directory)
				{
					return TRUE;
				}

				try
				{
					// Remove the files iterator
					// (fixes Windows PHP which has permission issues with open iterators)
					unset($files);

					// Try to remove the parent directory
					return rmdir($file->getRealPath());
				}
				catch (ErrorException $e)
				{
					// Catch any delete directory warnings
					if ($e->getCode() === E_WARNING)
					{
						throw new Cache_Exception(__METHOD__.' failed to delete directory : :directory', array(':directory' => $file->getRealPath()));
					}
					throw $e;
				}
			}
			else
			{
				// We get here if a file has already been deleted
				return FALSE;
			}
		}
		// Catch all exceptions
		catch (Exception $e)
		{
			// If ignore_errors is on
			if ($ignore_errors === TRUE)
			{
				// Return
				return FALSE;
			}
			// Throw exception
			throw $e;
		}
	}


	protected function _resolve_directory($filename)
	{
		return $this->_cache_dir->getRealPath().DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR;
	}


	protected function _make_directory($directory, $mode = 0777, $recursive = FALSE, $context = NULL)
	{
		if ( ! mkdir($directory, $mode, $recursive, $context))
		{
			throw new Cache_Exception('Failed to create the defined cache directory : :directory', array(':directory' => $directory));
		}
		chmod($directory, $mode);

		return new SplFileInfo($directory);;
	}
}
