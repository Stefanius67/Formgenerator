<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Formgenerator\FormInput;
use SKien\Formgenerator\FormDiv;

/**
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormDivTest extends TestCase
{
    use FormgeneratorHelper;
    
    public function test__construct() : void
    {
        $oFG = $this->createFG(true);
        $oDiv1 = $oFG->addDiv(50, FormDiv::LEFT, 'div1');
        $oDiv2 = $oFG->addDiv(50, FormDiv::RIGHT, 'div2');
        $oDivClear = $oFG->addDiv(100, FormDiv::CLEAR);
        $this->assertIsObject($oDiv1);
        $this->assertIsObject($oDiv2);
        $this->assertIsObject($oDivClear);
        $this->assertTrue($oDiv1 instanceof FormDiv);
        $this->assertTrue($oDiv2 instanceof FormDiv);
        $this->assertTrue($oDivClear instanceof FormDiv);
    }
    
    public function test_getHTML() : void
    {
        $oFG = $this->createFG(true);
        $oFG->addDiv(50, FormDiv::LEFT, 'div1');
        $oFG->addDiv(50, FormDiv::RIGHT, 'div2');
        $oFG->addDiv(100, FormDiv::CLEAR);
        $oDiv = $oFG->addDiv(100, FormDiv::NONE);
        $oDiv->add(new FormInput('strInput1', 10));
        $strHTML = $oFG->getForm();
        $this->assertNotFalse(strpos($strHTML, 'float: left;'));
        $this->assertNotFalse(strpos($strHTML, 'float: right;'));
        $this->assertNotFalse(strpos($strHTML, 'clear: both;'));
        $this->assertGreaterThan(strpos($strHTML, 'float: left;'), strpos($strHTML, 'float: right;'));
        $this->assertGreaterThan(strpos($strHTML, 'float: right;'), strpos($strHTML, 'clear: both;'));
    }
}

