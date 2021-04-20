<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Formgenerator\FormLine;
use SKien\Formgenerator\FormFlags;
use SKien\Formgenerator\FormRange;

/**
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormRangeTest extends TestCase
{
    use FormgeneratorHelper;

    public function test_ValueSet1() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = $oFL->add(new FormRange('iCount', '80%', 100, 600));
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'value="567"'));
        $this->assertNotFalse(strpos($strHTML, 'min="100"'));
        $this->assertNotFalse(strpos($strHTML, 'max="600"'));
        $this->assertNotFalse(strpos($strHTML, 'step="1"'));
    }
    
    public function test_ValueNotSet() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = $oFL->add(new FormRange('iNotSet', '80%'));
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'value="0"'));
        $this->assertNotFalse(strpos($strHTML, 'min="0"'));
        $this->assertNotFalse(strpos($strHTML, 'max="100"'));
    }
    
    public function test_MinMax() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = new FormRange('iCount', '80%');
        $oFL->add($oEdit);
        $oEdit->setMinMax(10, 50);
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'min="10"'));
        $this->assertNotFalse(strpos($strHTML, 'max="50"'));
    }
    
    public function test_Step() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = new FormRange('iCount', '80%');
        $oFL->add($oEdit);
        $oEdit->setStep(2);
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'step="2"'));
    }
    
    public function test_ShowValueLeft() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = new FormRange('iCount', '80%', 0, 100, FormFlags::SHOW_VALUE);
        $oFL->add($oEdit);
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'label class="slider_label"'));
    }
    
    public function test_ShowValueRight() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = new FormRange('iCount', '80%', 0, 100, FormFlags::SHOW_VALUE | FormFlags::ALIGN_RIGHT);
        $oFL->add($oEdit);
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'label class="slider_label"'));
    }
}
