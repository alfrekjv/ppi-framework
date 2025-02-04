<?php
/**
 * This is the PPI Appliations Configuration class which is used in the Bootstrap
 *
 * @package   Core
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @link      http://www.ppiframework.com
 */
class PPI_App {

	/**
	 * The Environment Options for the PPI Application
	 *
	 * @var array
	 */
	protected $_envOptions = array(
		'siteMode'         => 'development', // This determines how PPI handles things like exceptions
		'configBlock'      => 'development', // The block in the config file to get the config data from
		'configFile'       => 'general.ini', // The default filename for the config file
		'configCachePath'  => '', // The path to the config cache
		'cacheConfig'      => false, // Config object caching
		'errorLevel'       => E_ALL, // The error level to throw via error_reporting()
		'showErrors'       => 'On', // Whether to display errors or not. This gets fired into ini_set('display_errors')
		'router'           => null,
		'session'          => null,
		'config'           => null,
		'dispatcher'       => null,
		'request'          => null
	);


	/**
	 * @param array $p_aParams
	 */

	function __construct($p_aParams = array()) {
		if(!empty($p_aParams)) {
			foreach ($p_aParams as $key => $value) {
				if (method_exists($this, ($sMethod = 'set' . ucfirst($key)))) {
					$this->$sMethod($value);
				}
			}
		}
	}

	/**
	 * Setter for the environment, passing in options determining how the app will behave
	 *
	 * @param array $p_aOptions The options
	 * @return void
	 */
	function setEnv(array $p_aOptions) {

		// If we pass in a bad sitemode, lets just default to 'development' gracefully.
		if(isset($p_aOptions['siteMode'])) {
			if(!in_array($p_aOptions['siteMode'], array('development', 'production'))) {
				unset($p_aOptions['siteMode']);
			}
		}

		// Any further options passed, eg: it maps; 'errorLevel' to $this->_errorLevel
		foreach($p_aOptions as $optionName => $option) {
			$this->_envOptions[$optionName] = $option;
		}
	}

	/**
	 * Magic setter function, this is an alias of setEnv()
	 *
	 * @param string $option The Option
	 * @param string $value The Value
	 * @return void
	 */
	function __set($option, $value) {
		$this->setEnv(array($option => $value));
	}

	/**
	 * Obtain the value of an environment option
	 *
	 * @param string $key The Environment Option
	 * @param mixed $default The default value to return if the key is not found
	 * @return mixed If your key is not found, then NULL is returned
	 */
	function getEnv($key, $default = null) {
		return isset($this->_envOptions[$key]) ? $this->_envOptions[$key] : $default;
	}

	/**
	 * Magic getter function, this is an alias of getEnv()
	 *
	 * @param string $option The Option
	 * @return mixed
	 */
	function __get($option) {
		return $this->getEnv($option);
	}

	/**
	 * Set the router object for the app bootup
	 *
	 * @param PPI_Router_Interface $p_oRouter The router object
	 * @return void
	 */
	function setRouter(PPI_Router_Interface $p_oRouter) {
		$this->_envOptions['router'] = $p_oRouter;
	}

	/**
	 * Set the dispatch object for the app bootup
	 *
	 * @param PPI_Dispatch_Interface $p_oDispatch The dispatch object
	 * @return void
	 */
	function setDispatcher(PPI_Dispatch_Interface $p_oDispatch) {
		$this->_envOptions['dispatcher'] = $p_oDispatch;
	}

		/**
	 * Set the request object for the app bootup
	 *
	 * @param object $p_oRequest
	 * @return void
	 */
	function setRequest($p_oRequest) {
		$this->_envOptions['request'] = $p_oRequest;
	}

	/**
	 * Set the session object for the app bootup
	 *
	 * @param PPI_Session_Interface $p_oSession The session object
	 * @return void
	 */
	function setSession(PPI_Session_Interface $p_oSession) {
		$this->_envOptions['session'] = $p_oSession;
	}

	/**
	 * Run the boot process, boot up our app. Call the relevant classes such as:
	 * config, registry, session, dispatch, router.
	 *
	 * @return $this Fluent interface
	 */
	function boot() {

		error_reporting($this->_envOptions['errorLevel']);
		ini_set('display_errors', $this->getEnv('showErrors', 'On'));

		// Fire up the default config handler
		if($this->_envOptions['config'] === null) {

			$this->_config = new PPI_Config(array(
				'configBlock'     => $this->_envOptions['configBlock'],
				'configFile'      => $this->_envOptions['configFile'],
				'cacheConfig'     => $this->_envOptions['cacheConfig'],
				'configCachePath' => $this->_envOptions['configCachePath']
			));
		}

		// So are we auto-loading datasource
		if(isset($this->_envOptions['ds']) && $this->_envOptions['ds'] && file_exists(CONFIGPATH . 'connections.php')) {
			$ds = PPI_DataSource::create($this->loadDSConnections());
			PPI_Registry::set('DataSource', $ds);
		}

		$this->_config = $this->_config->getConfig();

		// -- Set the config into the registry for quick read/write --
		PPI_Registry::set('PPI_Config', $this->_config);

		// ------------- Initialise the session -----------------
		if(!headers_sent()) {
			// Fire up the default session handler
			if($this->_envOptions['session'] === null) {
				$this->_envOptions['session'] = new PPI_Session();
			}
			PPI_Registry::set('PPI_Session', $this->_envOptions['session']);
		}

		// By default we always load up PPI_Router as it contains default routes now such as __404__
		if($this->_envOptions['router'] === null) {
			$this->_envOptions['router'] = new PPI_Router();
		}

		// -- Fire up the default dispatcher --
		if($this->_envOptions['dispatcher'] === null) {
			$this->_envOptions['dispatcher'] = new PPI_Dispatch();
		}

		// -- Set the PPI_Request object --
		if($this->_envOptions['request'] === null) {
			$this->_envOptions['request'] = new PPI_Request();
		}

		// -------------- Library Autoloading Process --------------
		if(!empty($this->_config->system->autoloadLibs)) {
			foreach(explode(',', $this->_config->system->autoloadLibs) as $sLib) {
				switch(strtolower(trim($sLib))) {
					case 'zf':
						PPI_Autoload::add('Zend', array(
							'path'   => SYSTEMPATH . 'Vendor/Zend/',
							'prefix' => 'Zend_'
						));
						break;

					case 'github':
						$githubAutoloader = SYSTEMPATH . 'Vendor/Github/Autoloader.php';
						if(!file_exists($githubAutoloader)) {
							throw new PPI_Exception('Unable to autoload github, the github autoloader was no found.');
						}
						include_once(SYSTEMPATH . 'Vendor/Github/Autoloader.php');
						Github_Autoloader::register();
						break;

					// @todo - test this.
					case 'swift':
						include_once(SYSTEMPATH . 'Vendor/Swift/swift_required.php');
						break;

					case 'solar':
						include_once(SYSTEMPATH . 'Vendor/Solar.php');
						PPI_Autoload::add('Solar', array(
							'path'   => SYSTEMPATH . 'Vendor/Solar/',
							'prefix' => 'Solar_'
						));
						break;

				}

			}
		}

		PPI_Registry::set('PPI_App', $this);

		return $this; // Fluent Interface
	}

	/**
	 * Call the dispatch process. Running the dispatcher and dispatching
	 *
	 * @return $this Fluent interface
	 */
	function dispatch() {

		// Urls and Uris
		$baseUrl = $this->getConfig()->system->base_url;
		$url = $this->_envOptions['request']->getUrl();
		$uri = strpos($url, $baseUrl) !== false ? str_replace($baseUrl, '', $url) : $url;

		// Router
		$router = $this->_envOptions['router'];
		$router->setUri($uri);
		$router->init();

		$uri = $router->getMatchedRoute();

		// Dispatcher
		$this->_envOptions['dispatcher']->setUri($uri);

		if(!$this->_envOptions['dispatcher']->check()) {
			$this->_envOptions['dispatcher']->setUri($router->get404Route());
			$this->_envOptions['dispatcher']->check();
		}

		// Get the instantiated controller class
		$controller = $this->_envOptions['dispatcher']->getController();

		// Update the URI for the request object so things like $this->get() work
		$this->_envOptions['request'] = new PPI_Request(array(
			'uri' => $uri
		));

		PPI_Registry::set('PPI_Request', $this->_envOptions['request']);
		PPI_Registry::set('PPI_Dispatch', $this->_envOptions['dispatcher']);

		$this->_envOptions['view'] = new PPI_View();
		$this->_envOptions['response'] = new PPI_Response();

		if($controller !== null) {
			$controller->systemInit($this);
			$this->_envOptions['dispatcher']->setController($controller);
		}
		$this->_envOptions['dispatcher']->dispatch();
	}
	
	/**
	 * Load the connections from $path
	 * 
	 * @return array
	 */
	function loadDSConnections() {

		$path = $this->getEnv('dsConnectionsPath', CONFIGPATH . 'connections.php');
		include_once($path);
		return isset($connections) ? $connections : array();
	}

	/**
	 * Get the current site mode set, such as 'development' or 'production'
	 *
	 * @return string
	 */
	function getSiteMode() {
		return $this->_envOptions['siteMode'];
	}

	/**
	 * Get the config
	 *
	 * @return void
	 */
	function getConfig() {
		return $this->_config;
	}

}