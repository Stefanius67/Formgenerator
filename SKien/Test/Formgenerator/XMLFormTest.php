<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Config\ConfigInterface;
use SKien\Formgenerator\FormDataInterface;
use SKien\Formgenerator\XMLForm;

/**
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class XMLFormgTest extends TestCase
{
    use FormgeneratorHelper;
    
    public function test__construct() : void
    {
        $oFG = new XMLForm(null);
        $this->assertIsObject($oFG);
        // check if Nulldata and NullConfig is created!
        $this->assertIsObject($oFG->getConfig());
        $this->assertTrue($oFG->getConfig() instanceof ConfigInterface);
        $this->assertIsObject($oFG->getData());
        $this->assertTrue($oFG->getData() instanceof FormDataInterface);
    }
    
    public function test_load() : void
    {
        $oFG = $this->createXMLFG(true);
        $result = $oFG->loadXML(__DIR__ . DIRECTORY_SEPARATOR . 'testdata/FormLine.xml');
        $this->assertEquals(XMLForm::E_OK, $result);
        $this->assertIsString($oFG->getForm());
    }
    
    public function test_loadErrorFileNotExist() : void
    {
        $oFG = $this->createXMLFG(true);
        $result = $oFG->loadXML('missing.xml');
        $this->assertEquals(XMLForm::E_FILE_NOT_EXIST, $result);
        $this->assertNotEmpty($oFG->getErrorMsg());
    }
    
    public function test_loadErrorMissingRoot() : void
    {
        $oFG = $this->createXMLFG(true);
        $result = $oFG->loadXML(__DIR__ . DIRECTORY_SEPARATOR . 'testdata/FormRootMissing.xml');
        $this->assertEquals(XMLForm::E_MISSING_ROOT, $result);
        $this->assertNotEmpty($oFG->getErrorMsg());
    }
    
    public function test_loadErrorXML() : void
    {
        $oFG = $this->createXMLFG(true);
        $result = $oFG->loadXML(__DIR__ . DIRECTORY_SEPARATOR . 'testdata/FormErrorXML.xml');
        $this->assertEquals(XMLForm::E_XML_ERROR, $result);
        $this->assertStringContainsString('<br/>', $oFG->getErrorMsg());
    }
    
    public function test_loadErrorXMLPlain() : void
    {
        $oFG = $this->createXMLFG(true);
        $oFG->setPlainError(true);
        $result = $oFG->loadXML(__DIR__ . DIRECTORY_SEPARATOR . 'testdata/FormErrorXML.xml');
        $this->assertEquals(XMLForm::E_XML_ERROR, $result);
        $this->assertStringNotContainsString('<br/>', $oFG->getErrorMsg());
    }
    
    public function test_loadErrorUnknownElement() : void
    {
        $oFG = $this->createXMLFG(true);
        $result = $oFG->loadXML(__DIR__ . DIRECTORY_SEPARATOR . 'testdata/FormErrorUnknownElement.xml');
        $this->assertEquals(XMLForm::E_UNKNOWN_FORM_ELEMENT, $result);
        $this->assertNotEmpty($oFG->getErrorMsg());
    }
}

