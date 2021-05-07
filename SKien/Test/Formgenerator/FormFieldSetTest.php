<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Formgenerator\FormFieldSet;
use SKien\Formgenerator\FormLine;

/**
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormFieldSetTest extends TestCase
{
    use FormgeneratorHelper;
    
    public function test__construct() : void
    {
        $oFG = $this->createFG(true);
        $oFS = $oFG->addFieldSet('test');
        $this->assertIsObject($oFS);
        $this->assertTrue($oFS instanceof FormFieldSet);
    }
    
    public function test_setImageHeight() : void
    {
        $oFG = $this->createFG(true);
        $oFS = $oFG->addFieldSet('testfieldset', 'fs1', FormFieldSet::IMAGE);
        $oFS->setImageHeight(20);
        $strHTML = $oFS->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'style="height: 20px;"'));
    }
    
    public function test_addLine() : void
    {
        $oFG = $this->createFG(true);
        $oFS = $oFG->addFieldSet('testfieldset');
        $oFL = $oFS->addLine('testline');
        $this->assertIsObject($oFL);
        $this->assertTrue($oFL instanceof FormLine);
    }
    
    public function test_getHTML() : void
    {
        $oFG = $this->createFG(true);
        $oFS = $oFG->addFieldSet('testfieldset');
        $oFS->addLine('testline');
        $strHTML = $oFS->getHTML();
        $this->assertNotFalse(strpos($strHTML, '<legend>testfieldset</legend>'));
        $this->assertNotFalse(strpos($strHTML, '>testline</label>'));
    }
}

