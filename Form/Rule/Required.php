<?php
/**
 *
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @package   Form
 * @link      www.ppiframework.com
 */
class PPI_Form_Rule_Required extends PPI_Form_Rule {

	/**
	 * Validate against the Required rule
	 *
	 * @param string $data
	 * @return bool
	 */
    public function validate($data) {
        return trim($data) !== '';
    }

}
