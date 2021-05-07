<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Formgenerator\FormLine;
use SKien\Formgenerator\FormCheck;
use SKien\Formgenerator\FormFlags;

/**
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormCheckTest extends TestCase
{
    use FormgeneratorHelper;
    
    /**
     * @dataProvider provideValues
     */
    public function test_Button(string $strName, bool $bChecked) : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oBtn = new FormCheck($strName, 0, 'this is the description');
        $oFL->add($oBtn);
        $strHTML = $oBtn->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'type="checkbox"'));
        $this->assertNotFalse(strpos($strHTML, '&nbsp;this is the description'));
        $this->assertEquals($bChecked, strpos($strHTML, ' checked '));
    }
    
    public function test_BtnValue() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oBtn = new FormCheck('btnCheck1', 0, 'this is the description');
        $oBtn->setBtnValue('test');
        $oFL->add($oBtn);
        $strHTML = $oBtn->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'value="test"'));
    }
    
    
    /**
     * @dataProvider provideValues
     */
    public function test_ReadOnly(string $strName, bool $bChecked) : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oBtn = new FormCheck($strName, FormFlags::READ_ONLY, 'this is the description');
        $oFL->add($oBtn);
        $strHTML = $oBtn->getHTML();
        $this->assertNotFalse(strpos($strHTML, '<input type="hidden" name="' . $strName . '"'));
    }
    
    public function provideValues()
    {
        return [
            ['btnCheck1', true],
            ['btnCheck2', false],
            ['btnCheck3', true],
            ['btnCheck4', true],
            ['btnCheck5', false],
            ['btnCheck6', false],
        ];
    }
    
    
}
