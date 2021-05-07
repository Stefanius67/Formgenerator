<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Formgenerator\FormLine;
use SKien\Formgenerator\FormInput;

/**
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormLineTest extends TestCase
{
    use FormgeneratorHelper;
    
    public function test__construct() : void
    {
        $oFG = $this->createFG(true);
        $oFL = $oFG->add(new FormLine('testline'));
        $this->assertIsObject($oFL);
        $this->assertTrue($oFL instanceof FormLine);
    }
    
    public function test_getHTML() : void
    {
        $oFG = $this->createFG(true);
        $oFG->setColWidth([20, 23, 25, 32], '%');
        $oFL = $oFG->add(new FormLine('testline'));
        $oFL->add(new FormInput('strInput1', 10));
        $oFL->add(new FormInput('strInput2', 10));
        $oFL->add(new FormInput('strInput3', 10));
        $strHTML = $oFG->getHTML();
        $this->assertNotFalse(strpos($strHTML, '>testline</label>'));
        $this->assertNotFalse(strpos($strHTML, 'tabindex="1"'));
        $this->assertNotFalse(strpos($strHTML, 'tabindex="2"'));
        $this->assertNotFalse(strpos($strHTML, 'tabindex="3"'));
        $this->assertNotFalse(strpos($strHTML, 'width: 20%'));
        $this->assertNotFalse(strpos($strHTML, 'width: 23%'));
        $this->assertNotFalse(strpos($strHTML, 'width: 25%'));
        $this->assertNotFalse(strpos($strHTML, 'width: 32%'));
    }
    
    public function test_HR() : void
    {
        $oFG = $this->createFG(true);
        $oFG->add(new FormLine(FormLine::HR));
        $strHTML = $oFG->getHTML();
        $this->assertNotFalse(strpos($strHTML, '<hr>'));
    }
}

