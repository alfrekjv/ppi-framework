<?php
/**
 *
 * @author    Alexandre Gaigalas <alexandre@gaigalas.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @package   Form
 * @link      www.ppiframework.com
 */
class PPI_Form_Rule_Maxlength  extends PPI_Form_Rule {

    public function validate($data) {
        $data = trim($data);
        return strlen($data) <= $this->getParam();
    }

}
