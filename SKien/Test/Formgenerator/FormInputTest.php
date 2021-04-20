<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Formgenerator\FormLine;
use SKien\Formgenerator\FormInput;
use SKien\Formgenerator\FormFlags;

/**
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormInputTest extends TestCase
{
    use FormgeneratorHelper;

    public function test_setPlaceholder() : void
    {
        $oFG = $this->createFG(true);
        $oFL = $oFG->add(new FormLine('testline'));
        $oInput = new FormInput('strInput1', 10);
        $oFL->add($oInput);
        $oInput->setPlaceholder('YYYY-MM-DD');
        $strHTML = $oInput->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'placeholder="YYYY-MM-DD"'));
    }
    
    public function test_setSelectImg() : void
    {
        $oFG = $this->createFG(true);
        $oFL = $oFG->add(new FormLine('testline'));
        $oInput = new FormInput('strInput1', 10, FormFlags::ADD_SELBTN);
        $oFL->add($oInput);
        $oInput->setSelectImg('test.png', 'testtext');
        $strHTML = $oInput->getHTML();
        $this->assertNotFalse(strpos($strHTML, '<img class="picker"'));
        $this->assertNotFalse(strpos($strHTML, 'src="test.png"'));
        $this->assertNotFalse(strpos($strHTML, 'alt="[?]"'));
        $this->assertNotFalse(strpos($strHTML, 'title="testtext"'));
        $this->assertNotFalse(strpos($strHTML, 'onclick="onSelect(\'strInput1\')'));
    }
    
    public function test_setSuffix() : void
    {
        $oFG = $this->createFG(true);
        $oFL = $oFG->add(new FormLine('testline'));
        $oInput = new FormInput('strInput1', 10);
        $oFL->add($oInput);
        $oInput->setSuffix('Suffix');
        $strHTML = $oInput->getHTML();
        $this->assertNotFalse(strpos($strHTML, '&nbsp;Suffix</div>'));

        $oInput = new FormInput('strInput2', 10, FormFlags::READ_ONLY);
        $oFL->add($oInput);
        $oInput->setSuffix('Suffix');
        $strHTML = $oInput->getHTML();
        $this->assertNotFalse(strpos($strHTML, '<span class="readonly">Suffix</span>'));
    }
    
    public function test_setTabIndex() : void
    {
        $oFG = $this->createFG(true);
        $oFL = $oFG->add(new FormLine('testline'));
        // hidden input must not have tabindex
        $oInput = $oFL->add(new FormInput('strInput1', 10, FormFlags::HIDDEN));
        $strHTML = $oInput->getHTML();
        $this->assertFalse(strpos($strHTML, 'tabindex'));
    }
    
    public function test_processFlags() : void
    {
        $oFG = $this->createFG(true);
        $oFL = $oFG->add(new FormLine('testline'));
        // hidden input must not have tabindex
        $oInput = $oFL->add(new FormInput('strInput1', 10, FormFlags::MANDATORY));
        $strHTML = $oInput->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'required'));
        
        $oInput = $oFL->add(new FormInput('strInput2', 10, FormFlags::READ_ONLY));
        $strHTML = $oInput->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'readonly'));
        
        $oInput = $oFL->add(new FormInput('strInput3', 10, FormFlags::DISABLED));
        $strHTML = $oInput->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'disabled'));
    }
    
    public function test_typeFromFlags() : void
    {
        $oFG = $this->createFG(true);
        $oFL = $oFG->add(new FormLine('testline'));
        // hidden input must not have tabindex
        $oInput = $oFL->add(new FormInput('strInput1', 10, FormFlags::PASSWORD));
        $strHTML = $oInput->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'type="password"'));
        
        $oInput = $oFL->add(new FormInput('strInput2', 10, FormFlags::FILE));
        $strHTML = $oInput->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'type="file"'));
        
        $oInput = $oFL->add(new FormInput('strInput3', 10, FormFlags::FILE | FormFlags::HIDDEN));
        $strHTML = $oInput->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'visibility: hidden'));
    }
    
    public function test_setSize() : void
    {
        $oFG = $this->createFG(true);
        $oFL = $oFG->add(new FormLine('testline'));
        // hidden input must not have tabindex
        $oInput = $oFL->add(new FormInput('strInput1', '20px'));
        $strHTML = $oInput->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'width: 20px;'));
    }
    
    public function test_trim() : void
    {
        $oFG = $this->createFG(true);
        $oFL = $oFG->add(new FormLine('testline'));
        $oInput = $oFL->add(new FormInput('strFirstname', 30, FormFlags::TRIM));
        $strHTML = $oInput->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'value="Max"'));
    }
    
    public function test_addDTU() : void
    {
        $oFG = $this->createFG(true);
        $oFL = $oFG->add(new FormLine('testline'));
        // hidden input must not have tabindex
        $oInput = $oFL->add(new FormInput('strInput1', 30, FormFlags::ADD_DTU));
        $strHTML = $oInput->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'onInsertDateTimeUser'));
    }
    
    public function test_addReadonlyDTU() : void
    {
        $oFG = $this->createFG(true);
        $oFL = $oFG->add(new FormLine('testline'));
        // hidden input must not have tabindex
        $oInput = $oFL->add(new FormInput('strInput1', 30, FormFlags::ADD_DTU | FormFlags::READ_ONLY));
        $strHTML = $oInput->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'resetElement'));
    }
    
    public function test_error1() : void
    {
        $oFG = $this->createFG(true);
        $oFL = $oFG->add(new FormLine('testline'));
        // hidden input must not have tabindex
        $oInput = $oFL->add(new FormInput('strInput1', 30, FormFlags::ADD_DTU | FormFlags::ADD_DATE_PICKER));
        $this->expectError();
        $oInput->getHTML();
    }
    
    public function test_error2() : void
    {
        $oFG = $this->createFG(true);
        $oFL = $oFG->add(new FormLine('testline'));
        $oInput = new FormInput('strInput1', 30, FormFlags::ADD_DTU | FormFlags::ADD_DATE_PICKER);
        $oFL->add($oInput);
        $this->expectError();
        $oInput->addAttribute('style', 'margin: 0');
    }
    
    public function test_buildClass() : void
    {
        $oFG = $this->createFG(true);
        $oFL = $oFG->add(new FormLine('testline'));
        // hidden input must not have tabindex
        $oInput = $oFL->add(new FormInput('strInput1', 10));
        $oInput->setClass('blinking');
        $strHTML = $oInput->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'class="blinking '));
        
        $oInput = $oFL->add(new FormInput('strInput2', 10));
        $oInput->setClass('blinking');
        $oInput->addClass('red');
        $strHTML = $oInput->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'class="blinking red'));
        
        $oInput = $oFL->add(new FormInput('strInput2', 10));
        $this->expectError();
        $oInput->addClass('red');
    }
    
    public function test_configForJS() : void
    {
        $oFG = $this->createFG(true);
        $oFL = $oFG->add(new FormLine('testline'));
        $oInput = new FormInput('strInput', 10, FormFlags::BROWSE_SERVER);
        $oInput->setExpandFolder('expandfolder');
        $oFL->add($oInput);
        $strHTML = $oInput->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'browseServer'));
        $this->assertNotFalse(strpos($strHTML, "'expandfolder'"));
        
        // create reflection object to access protected property
        $reflectionObject = new \ReflectionObject($oInput->getFG());
        
        $reflectionConfig = $reflectionObject->getProperty('aConfigForJS');
        $reflectionConfig->setAccessible(true);
        $this->assertArrayHasKey('RichFilemanager', $reflectionConfig->getValue($oFG));
    }
    
    public function test_linkList() : void
    {
        $oFG = $this->createFG(true);
        $oFL = $oFG->add(new FormLine('testline'));
        $oInput = new FormInput('strLinklist', 10, FormFlags::BROWSE_SERVER);
        $oFL->add($oInput);
        $strHTML = $oInput->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'list="liststrLinklist"'));
        $this->assertNotFalse(strpos($strHTML, '<datalist id="liststrLinklist"'));
        $this->assertNotFalse(strpos($strHTML, '<option value="Freiburg">'));
        $this->assertNotFalse(strpos($strHTML, '<option value="Karlsruhe">'));
        $this->assertNotFalse(strpos($strHTML, '<option value="Stuttgart">'));
    }
}
