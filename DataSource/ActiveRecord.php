<?php
/**
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @package   DataSource
 * @link      www.ppiframework.com
 */
class PPI_DataSource_ActiveRecord {
	
	/**
	 * The table name
	 * 
	 * @var null
	 */
	protected $_table = null;
	
	/**
	 * The primary key
	 * 
	 * @var null
	 */
	protected $_primary = null;
	
	/**
	 * The datasource connection
	 * 
	 * @var null
	 */
	protected $_conn = null;
	
	/**
	 * Find a single record by value
	 * 
	 * @param integer $val
	 * @return void
	 */
	function find($val) {
		// SELECT * from [table] WHERE [primary] = $val
	}
	
	/**
	 * Fetch rows from the current table based on some conditions
	 * 
	 * @param $identifier
	 * @return void
	 */
	function fetchAll($identifier) {
		// SELECT * from [table]
	}

}