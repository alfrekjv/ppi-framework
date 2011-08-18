<?php
/**
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @package   Form
 * @link      www.ppiframework.com
 */
abstract class PPI_Form_Rule {

	/**
	 * The abritary param
	 *
	 * @var array
	 */
	protected $_ruleData = null;

    /**
     * Validates data by this rule
     *
     * @param mixed $data
     * @return boolean
     */
    abstract public function validate($data);

    /**
     * The Constructor
     *
     * @param mixed $param
     */
    public function __construct($ruleData = null) {

		if($ruleData !== null) {
	        $this->setParam($ruleData);
		}
    }

    /**
     * Get the rule param
     *
     * @return string
     */
    public function getRuleData() {
        return $this->_ruleData;
    }

    /**
     * Sets the rules value
     *
     * @param mixed $value
     * @return void
     */
    public function setRuleData($value) {
        $this->_ruleData = $value;
    }

}
