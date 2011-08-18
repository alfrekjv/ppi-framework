<?php
/**
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @package   Form
 * @link      www.ppiframework.com
 */
abstract class PPI_Form_Rule {

	/**
	 * @var array
	 */
	protected $_param = null;

    /**
     * Validates data by this rule
     *
     * @param mixed $data
     * @return boolean
     */
    abstract public function validate($data);

    /**
     *
     * @param mixed $param
     */
    public function __construct($param=null) {
        $this->setParam($param);
    }

    /**
     * Get the rule param
     *
     * @return string
     */
    public function getParam() {
        return $this->_param;
    }

    /**
     * Sets the rule param
     *
     * @param mixed $value
     * @return void
     */
    public function setParam($value) {
        $this->_param = $value;
    }

}
