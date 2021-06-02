<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use SKien\Formgenerator\FormFlags;
use SKien\Formgenerator\FormLine;
use SKien\Formgenerator\FormSelect;

/**
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormSelectTest extends FormBaseTestCase
{
    use FormgeneratorHelper;

    public function test__construct() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oSelect = new FormSelect('strGender');
        $oFL->add($oSelect);
        $strHTML = $oSelect->getHTML();
        $this->assertNotFalse(strpos($strHTML, '<select'));
    }

    public function test_SelectBtn() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oSelect = new FormSelect('strGender', 1, FormFlags::SELECT_BTN);
        $oSelect->setSelectBtnText('Gender');
        $oFL->add($oSelect);
        $strHTML = $oSelect->getHTML();
        $this->assertNotFalse(strpos($strHTML, '<button class="sbBtn">'));
    }

    public function test_width() : void
    {
        $oFG = $this->createFG(false);
        $oFG->setColWidth([30, 70]);
        $oFL = $oFG->add(new FormLine('testline'));
        $oSelect = new FormSelect('strGender');
        $oFL->add($oSelect);
        $strHTML = $oSelect->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'width: 70%'));
    }

    public function test_selectOptions() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oSelect = new FormSelect('strTest', 1);
        $oSelect->setSelectOptions(['zero' => 0, 'one' => 1]);
        $oFL->add($oSelect);
        $this->assertValidHtmlBlock($oSelect->getHTML());
    }

    public function test_error1() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        // SELECT_BTN MUST be of size 1
        $this->expectError();
        $oSelect = new FormSelect('strGender', 2, FormFlags::SELECT_BTN);
        $oFL->add($oSelect);
    }

    public function test_error2() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oSelect = new FormSelect('strEmpty', 1);
        $oFL->add($oSelect);
        // selectlist of size 1 without options
        $this->expectError();
        $oSelect->getHTML();
    }

    public function test_error3() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oSelect = new FormSelect('strGender');
        $oFL->add($oSelect);
        // setSelectBtnText for non SELECT_BTN
        $this->expectError();
        $oSelect->setSelectBtnText('Test');
    }
}

