<?php

class PPI_Request_Get extends PPI_Request_Abstract {

	/**
	 * Constructor
	 *
	 * If data is supplied then that data is used, otherwise we automatically
	 *
	 * @param array $data Supplied Data
	 */
	public function __construct($data = null) {
		if (is_string($data)) {
			parse_str($data, $this->_array);
			$this->_isCollected = false;

		} elseif (is_array($data)) {
			if (is_array($data)) {
				$this->_array = $data;
			}

			$this->_isCollected = false;
		} else {
			$this->_array = $_GET;
		}
	}
}