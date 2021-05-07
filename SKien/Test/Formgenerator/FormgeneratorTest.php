<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Formgenerator\FormGenerator;
use SKien\Config\ConfigInterface;
use SKien\Formgenerator\FormDataInterface;
use SKien\Formgenerator\FormFlags;
use SKien\Formgenerator\FormInput;
use SKien\Formgenerator\FormDiv;

/**
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormgeneratorTest extends TestCase
{
    use FormgeneratorHelper;
    
    public function test__construct() : void
    {
        $oFG = new FormGenerator(null);
        $this->assertIsObject($oFG);
        // check if Nulldata and NullConfig is created!
        $this->assertIsObject($oFG->getConfig());
        $this->assertTrue($oFG->getConfig() instanceof ConfigInterface);
        $this->assertIsObject($oFG->getData());
        $this->assertTrue($oFG->getData() instanceof FormDataInterface);
    }
    
    public function test_setData() : void
    {
        $oFG = new FormGenerator(null);
        $oFG->setData($this->createData());
        $this->assertIsObject($oFG->getData());
        $this->assertTrue($oFG->getData() instanceof FormDataInterface);
    }
    
    public function test_setConfig() : void
    {
        $oFG = new FormGenerator(null);
        $oFG->setConfig($this->createConfig());
        $this->assertIsObject($oFG->getConfig());
        $this->assertTrue($oFG->getConfig() instanceof ConfigInterface);
    }
    
    public function test_setAction() : void
    {
        $oFG = new FormGenerator(null);
        $oFG->setAction('?action=save');
        $strHTML = $oFG->getForm();
        $this->assertNotFalse(strpos($strHTML, 'action="?action=save"'));
    }
    
    public function test_setImagePath() : void
    {
        $oFG = new FormGenerator(null);
        $oFG->setImagePath('testpath');
        $this->assertEquals('testpath', $oFG->getImagePath());
    }
    
    public function test_setTarget() : void
    {
        $oFG = new FormGenerator(null);
        $oFG->setTarget('parent');
        $oFG->addAttribute('autocomplete', 'off');
        $strHTML = $oFG->getForm();
        $this->assertNotFalse(strpos($strHTML, 'target="parent"'));
        $this->assertNotFalse(strpos($strHTML, 'autocomplete="off"'));
    }
    
    public function test_setFormWidth() : void
    {
        $oFG = new FormGenerator(null);
        $oFG->setFormWidth(300);
        $strStyle = $oFG->getStyle();
        $this->assertNotFalse(strpos($strStyle, 'body { width: 300px'));
    }
    
    public function test_setReadOnly() : void
    {
        $oFG = $this->createFG(true);
        $oFG->setReadOnly(true);
        $this->assertEquals(FormFlags::READ_ONLY, $oFG->getGlobalFlags());
        $oFG->setReadOnly(false);
        $this->assertEquals(0, $oFG->getGlobalFlags());
    }
    
    public function test_setDebugMode() : void
    {
        $oFG = $this->createFG(true);
        $oFG->setDebugMode(true);
        $this->assertTrue($oFG->getDebugMode());
    }

    public function test_setDialog() : void
    {
        $oFG = $this->createFG(true);
        $oFG->setDialog(true);
        $this->assertTrue($oFG->isDialog());
    }
    
    public function test_getStyle() : void
    {
        $oFG = $this->createFG(true);
        $oFS = $oFG->addFieldSet('fieldset');
        $oFL = $oFS->addLine('line');
        $oFL->add(new FormInput('strTest', 10));
        $this->assertEmpty($oFG->getStyle());
    }
    
    public function test_adjustColHeight() : void
    {
        $oFG = $this->createFG(true);
        $oFG->addDiv(50, FormDiv::LEFT, 'div1');
        $oFG->addDiv(50, FormDiv::RIGHT, 'div2');
        $oFG->adjustColHeight('div1', 'div2');
        $strHTML = $oFG->getForm();
        $i1 = strpos($strHTML, '<script>');
        $i2 = strpos($strHTML, 'window.addEventListener');
        $i3 = strpos($strHTML, 'adjustColumnHeight');
        $i4 = strpos($strHTML, '</script>');
        $this->assertNotFalse($i1);
        $this->assertNotFalse($i2);
        $this->assertNotFalse($i3);
        $this->assertNotFalse($i4);
        $this->assertGreaterThan($i1, $i2);
        $this->assertGreaterThan($i2, $i3);
        $this->assertGreaterThan($i3, $i4);
    }
}

