<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Formgenerator\FormLine;
use SKien\Formgenerator\FormFlags;
use SKien\Formgenerator\FormInt;

/**
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormIntTest extends TestCase
{
    use FormgeneratorHelper;

    public function test_ValueSet1() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = $oFL->add(new FormInt('iCount', 10));
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'value="567"'));
        $this->assertNotFalse(strpos($strHTML, 'data-validation="int'));
    }
    
    public function test_ValueNotSet() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = $oFL->add(new FormInt('iNotSet', 10));
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'value="0"'));
        $this->assertNotFalse(strpos($strHTML, 'data-validation="int'));
    }
    
    public function test_ValueNoZero() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = $oFL->add(new FormInt('iNotSet', 10, FormFlags::NO_ZERO));
        $strHTML = $oEdit->getHTML();
        $this->assertFalse(strpos($strHTML, 'value=""'));
        $this->assertNotFalse(strpos($strHTML, 'data-validation="int'));
    }
    
    public function test_ValueSuffix() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = new FormInt('iCount', 10);
        $oEdit->setSuffix('kg');
        $oFL->add($oEdit);
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, '&nbsp;kg'));
    }
    
    public function test_MinMax() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = new FormInt('iCount', 10);
        $oFL->add($oEdit);
        $oEdit->setMinMax(10, 50);
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'min="10"'));
        $this->assertNotFalse(strpos($strHTML, 'max="50"'));
    }
}
