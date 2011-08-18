<?php
class PPI_Test_RequiredRuleTest extends PHPUnit_Framework_TestCase {

	function setUp() {
		$this->_rule = new PPI_Form_Rule_Required();
	}

	function tearDown() {
		unset($this->_rule);
	}

    /**
     * @dataProvider providerForValidationTrue
     */
	function testValidatesTrue($data) {
        $this->assertTrue($this->_rule->validate($data));
    }
    
    /**
     * @dataProvider providerForValidationFalse
     */
	function testValidatesFalse($data) {
        $this->assertFalse($this->_rule->validate($data));
    }
    
    function providerForValidationTrue() {
        return array(
            array('foo'),
            array(1),
            array(0), //zero could be a valid form value
        );
    }
    
    function providerForValidationFalse() {
        return array(
            array(''),
            array(' '), //whitespace is not valid
        );
    }
}