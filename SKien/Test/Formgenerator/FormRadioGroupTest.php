<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Formgenerator\FormLine;
use SKien\Formgenerator\FormFlags;
use SKien\Formgenerator\FormRadioGroup;

/**
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormRadioGroupTest extends TestCase
{
    use FormgeneratorHelper;
    
    public function test__construct() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oRadio = new FormRadioGroup('strGender');
        $oFL->add($oRadio);
        $strHTML = $oRadio->getHTML();
        $this->assertNotFalse(strpos($strHTML, '<input type="radio"'));
    }
    
    public function test_horz() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oRadio = new FormRadioGroup('strGender', FormFlags::HORZ_ARRANGE);
        $oFL->add($oRadio);
        $strHTML = $oRadio->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'style="float: left'));
    }
    
    public function test_readonly() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oRadio = new FormRadioGroup('strGender', FormFlags::HORZ_ARRANGE | FormFlags::READ_ONLY);
        $oFL->add($oRadio);
        $strHTML = $oRadio->getHTML();
        $this->assertNotFalse(strpos($strHTML, ' disabled '));
    }
}

