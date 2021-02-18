<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Formgenerator\FormLine;
use SKien\Formgenerator\FormButton;

/**
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormButtonTest extends TestCase
{
    use FormgeneratorHelper;
    
    public function test_Button() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oBtn = new FormButton('btnSelect', 'Select', '');
        $oFL->add($oBtn);
        $strHTML = $oBtn->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'type="button"'));
        $this->assertNotFalse(strpos($strHTML, 'value="Select"'));
    }
    
    public function test_Title() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oBtn = new FormButton('btnSelect', 'Select', '');
        $oFL->add($oBtn);
        $oBtn->setTitle('the title');
        $strHTML = $oBtn->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'title="the title"'));
    }
    
    public function test_Style() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oBtn = new FormButton('btnSelect', 'Select', '', 0, 'padding: 0');
        $oFL->add($oBtn);
        $strHTML = $oBtn->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'padding: 0'));
    }
    
    public function test_OnClick() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oBtn = new FormButton('btnSelect', 'Select', 'testfunc()');
        $oFL->add($oBtn);
        $strHTML = $oBtn->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'onclick="testfunc();"'));
    }
    
    /**
     * @dataProvider provideAlignFlag
     */
    public function test_Flags($strStyle, $wFlags) : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oBtn = new FormButton('btnSelect', 'Select', '', $wFlags);
        $oFL->add($oBtn);
        $strHTML = $oBtn->getHTML();
        $this->assertNotFalse(strpos($strHTML, $strStyle));
    }
}
