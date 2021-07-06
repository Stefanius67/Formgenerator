<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use SKien\Formgenerator\FormFlags;
use SKien\Formgenerator\FormLine;
use SKien\Formgenerator\FormRadioGroup;
use SKien\Test\HtmlTestCase;

/**
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormRadioGroupTest extends HtmlTestCase
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

    public function test_selectOptions() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oRadio = new FormRadioGroup('strTest', FormFlags::HORZ_ARRANGE);
        $oRadio->setSelectOptions(['zero' => 0, 'one' => 1]);
        $oFL->add($oRadio);
        $this->assertValidHtmlBlock($oRadio->getHTML());
    }

    public function test_empty() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oRadio = new FormRadioGroup('strEmpty', FormFlags::HORZ_ARRANGE);
        $oFL->add($oRadio);
        // radio group without options
        $this->expectError();
        $oRadio->getHTML();
    }
}

