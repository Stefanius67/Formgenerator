<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Formgenerator\FormCKEdit;
use SKien\Config\JSONConfig;
use SKien\Formgenerator\FormGenerator;
use SKien\Formgenerator\ArrayFormData;
use SKien\Formgenerator\FormFlags;

/**
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormCKEditTest extends TestCase
{
    use FormgeneratorHelper;
    
    protected function getConfigForJS(FormGenerator $oFG) : array
    {
        // create reflection object to access protected property
        $reflectionObject = new \ReflectionObject($oFG);
        
        $reflectionConfig = $reflectionObject->getProperty('aConfigForJS');
        $reflectionConfig->setAccessible(true);
        return $reflectionConfig->getValue($oFG);
    }
    
    public function test__construct() : void
    {
        $oFG = $this->createFG(false);
        $oEditor = new FormCKEdit('strText', 12);
        $oEditor->setContentsCss('content.css');
        $oFG->add($oEditor);
        $strHTML = $oFG->getHTML();
        $this->assertNotFalse(strpos($strHTML, '<textarea'));
        
        $aConfigForJS = $this->getConfigForJS($oFG);
        $this->assertArrayHasKey('CKEditor', $aConfigForJS);
    }
    
    public function test_buildToolbar() : void
    {
        $oFG = $this->createFG(false);
        $oEditor = new FormCKEdit('strText', 12);
        $oEditor->setContentsCss('content.css');
        $lToolbar = FormCKEdit::TB_FULL | FormCKEdit::TB_SOURCE | FormCKEdit::TB_TEMPLATES;
        $oEditor->setToolbar($lToolbar);
        $oFG->add($oEditor);
        
        $aConfigForJS = $this->getConfigForJS($oFG);
        $this->assertArrayHasKey('editorOptions', $aConfigForJS['CKEditor']);
        $this->assertArrayHasKey('toolbar', $aConfigForJS['CKEditor']['editorOptions']);
        $this->assertEquals($lToolbar, $oEditor->getToolbar());
    }
    
    public function test_addCustomButton() : void
    {
        $oFG = $this->createFG(false);
        $oEditor = new FormCKEdit('strText', 12);
        $oEditor->setContentsCss('content.css');
        $oEditor->addCustomButton('test1', 'onTest1');
        $oEditor->addCustomButton('test2', 'onTest2', 'test2.png', FormCKEdit::BTN_TEXT_ICON);
        $oFG->add($oEditor);
        
        $aConfigForJS = $this->getConfigForJS($oFG);
        $this->assertArrayHasKey('customButtons', $aConfigForJS['CKEditor']);
        
        $strStyle = $oFG->getStyle();
        $this->assertNotFalse(strpos($strStyle, '.cke_button__ontest1_icon'));
        $this->assertNotFalse(strpos($strStyle, '.cke_button__ontest1_label'));
        $this->assertNotFalse(strpos($strStyle, '.cke_button__ontest2_icon'));
        $this->assertNotFalse(strpos($strStyle, '.cke_button__ontest2_label'));
    }
    
    public function test_setBodyID() : void
    {
        $oFG = $this->createFG(false);
        $oEditor = new FormCKEdit('strText', 12);
        $oEditor->setContentsCss('content.css');
        $oEditor->setBodyID('content_body');
        $oFG->add($oEditor);
        
        $aConfigForJS = $this->getConfigForJS($oFG);
        $this->assertArrayHasKey('bodyId', $aConfigForJS['CKEditor']['editorOptions']);
        $this->assertEquals('content_body', $aConfigForJS['CKEditor']['editorOptions']['bodyId']);
    }
    
    public function test_setAllowedContent() : void
    {
        $oFG = $this->createFG(false);
        $oEditor = new FormCKEdit('strText', 12);
        $oEditor->setContentsCss('content.css');
        $oEditor->setAllowedContent('p ul ol li');
        $oFG->add($oEditor);
        
        $aConfigForJS = $this->getConfigForJS($oFG);
        $this->assertArrayHasKey('allowedContent', $aConfigForJS['CKEditor']['editorOptions']);
        $this->assertEquals('p ul ol li', $aConfigForJS['CKEditor']['editorOptions']['allowedContent']);
    }
    
    public function test_setBrowseFolderURL() : void
    {
        $oFG = $this->createFG(false);
        $oEditor = new FormCKEdit('strText', 12);
        $oEditor->setContentsCss('content.css');
        $oEditor->setBrowseFolderLinkURL('LinkURL/');
        $oEditor->setBrowseFolderImageURL('ImageURL/');
        $oEditor->setBrowseFolderImageLinkURL('ImageLinkURL/');
        $oFG->add($oEditor);
        
        $aConfigForJS = $this->getConfigForJS($oFG);
        $this->assertArrayHasKey('expandFolder', $aConfigForJS['RichFilemanager']);
        $this->assertEquals('LinkURL/', $aConfigForJS['RichFilemanager']['expandFolder']['browseLinkURL']);
        $this->assertEquals('ImageURL/', $aConfigForJS['RichFilemanager']['expandFolder']['browseImageURL']);
        $this->assertEquals('ImageLinkURL/', $aConfigForJS['RichFilemanager']['expandFolder']['browseImageLinkURL']);
    }
    
    public function test_setJsonDataTrue()
    {
        $strContent = '<h1>This is the content</h1>';
        $oData = new ArrayFormData(['strText' => $strContent]);
        $oFG = $this->createFG(false);
        $oFG->setData($oData);
        $oEditor = new FormCKEdit('strText', 12, '100%', FormFlags::SET_JSON_DATA);
        $oEditor->setContentsCss('content.css');
        $oFG->add($oEditor);
        
        $aConfigForJS = $this->getConfigForJS($oFG);
        $this->assertArrayHasKey('editorData', $aConfigForJS['CKEditor']);
        $this->assertEquals($strContent, $aConfigForJS['CKEditor']['editorData']);
        
        $strHTML = $oEditor->getHTML();
        $this->assertFalse(strpos($strHTML, $strContent));
    }
    
    public function test_setJsonDataFalse()
    {
        $strContent = '<h1>This is the content</h1>';
        $oData = new ArrayFormData(['strText' => $strContent]);
        $oFG = $this->createFG(false);
        $oFG->setData($oData);
        $oEditor = new FormCKEdit('strText', 12, '100%');
        $oEditor->setContentsCss('content.css');
        $oFG->add($oEditor);
        
        $aConfigForJS = $this->getConfigForJS($oFG);
        $this->assertArrayNotHasKey('editorData', $aConfigForJS['CKEditor']);
        
        $strHTML = $oEditor->getHTML();
        $this->assertNotFalse(strpos($strHTML, $strContent));
    }
    
    public function test_error1() : void
    {
        $oFG = $this->createFG(false);
        $oEditor = new FormCKEdit('strText', 12);
        // no stylesheet set
        $this->expectError();
        $oFG->add($oEditor);
    }
    
    public function test_error2() : void
    {
        $oFG = $this->createFG(true);
        $oFG->setConfig(new JSONConfig(__DIR__ . DIRECTORY_SEPARATOR . 'testdata/FormGeneratorSpecial.json'));
        
        $oEditor = new FormCKEdit('strText', 12);
        $oEditor->setContentsCss('content.css');
        // can't find RFM
        $this->expectError();
        $oFG->add($oEditor);
    }
}

