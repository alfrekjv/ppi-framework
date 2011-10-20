<?php
/**
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @package   DataSource
 * @link      www.ppiframework.com
 */
class PPI_DataSource {

	/**
	 * List of configuration sets
	 * 
	 * @var array
	 */
	protected $_config = array();
	
	/**
	 * List of connections to return via singleton-like
	 * 
	 * @var array
	 */
	protected $_handles = array();

	/**
	 * The constructor, taking in options which are currently
	 * 
	 * @param array $options
	 */
	function __construct(array $options = array()) {
		$this->_config = $options;
	}
	
	/**
	 * Create a new instance of ourself.
	 * 
	 * @static
	 * @param array $options
	 * @return PPI_DataSource
	 */
	static function create(array $options = array()) {
		return new self($options);
	}

	/**
	 * The DataSource Factory - this is where we manufacture our drivers
	 * 
	 * @throws PPI_Exception
	 * @param string $key
	 * @return object
	 */
	function factory(array $options) {

		// Apply our default prefix
		if(!isset($options['prefix'])) {
			$options['prefix'] = 'PPI_DataSource_';
		}

		// Lets get our suffix, to load up the right adapter, (PPI_DataSource_[PDO|Mongo])
		if($options['type'] === 'mongo') {
			$suffix = 'Mongo';
		} elseif(substr($options['type'], 0, 4) === 'pdo_') {
			$suffix = 'PDO';
		} else {
			$suffix = $options['type'];
		}

		// Lets instantiate up and get our  driver
		$adapterName = $options['prefix'] . $suffix;
		$adapter     = new $adapterName();
		$driver      = $adapter->getDriver($options);

		return $driver;
	}
	
	/**
	 * Return the connection from the factory
	 * 
	 * @throws PPI_Exception
	 * @param string $key
	 * @return object
	 */
	function getConnection($key) {
		
		// Connection Caching
		if(isset($this->_handles[$key])) {
			return $this->_handles[$key];
		}
		
		// Check that we asked for a valid key
		if(!isset($this->_config[$key])) {
			throw new PPI_Exception('Invalid DataSource Key: ' . $key);
		}
		
		$conn = $this->factory($this->_config[$key]);
		
		// Connection Caching
		$this->_handles[$key] = $conn;

		return $conn;
	}

}