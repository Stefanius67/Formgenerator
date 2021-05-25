<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use SKien\Config\ConfigInterface;
use SKien\Formgenerator\FormDataInterface;
use SKien\Formgenerator\XMLForm;

/**
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class XMLFormTest extends FormBaseTestCase
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
        $result = $oFG->loadXML(__DIR__ . DIRECTORY_SEPARATOR . 'testdata/AllElements.xml');
        $this->assertEquals(XMLForm::E_OK, $result);
        $this->assertValidHtmlBlock($oFG->getForm());
    }

    public function test_loadSchemaValidate() : void
    {
        $oFG = $this->createXMLFG(true);
        $oFG->setSchemaValidation(true);
        $result = $oFG->loadXML(__DIR__ . DIRECTORY_SEPARATOR . 'testdata/FormWithSchema.xml');
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

    public function test_loadErrorMissingForm() : void
    {
        $oFG = $this->createXMLFG(true);
        $result = $oFG->loadXML(__DIR__ . DIRECTORY_SEPARATOR . 'testdata/FormMissing.xml');
        $this->assertEquals(XMLForm::E_MISSING_FORM, $result);
        $this->assertNotEmpty($oFG->getErrorMsg());
    }

    public function test_loadErrorXML() : void
    {
        $oFG = $this->createXMLFG(true);
        $result = $oFG->loadXML(__DIR__ . DIRECTORY_SEPARATOR . 'testdata/FormErrorXML.xml');
        $this->assertEquals(XMLForm::E_XML_ERROR, $result);
        $this->assertValidHtmlBlock($oFG->getErrorMsg());
    }

    public function test_loadErrorXMLPlain() : void
    {
        $oFG = $this->createXMLFG(true);
        $oFG->setPlainError(true);
        $result = $oFG->loadXML(__DIR__ . DIRECTORY_SEPARATOR . 'testdata/FormErrorXML.xml');
        $this->assertEquals(XMLForm::E_XML_ERROR, $result);
        $this->assertContainsNoHtmlTag($oFG->getErrorMsg());
    }

    public function test_loadErrorUnknownElement() : void
    {
        $oFG = $this->createXMLFG(true);
        $result = $oFG->loadXML(__DIR__ . DIRECTORY_SEPARATOR . 'testdata/FormErrorUnknownElement.xml');
        $this->assertEquals(XMLForm::E_UNKNOWN_FORM_ELEMENT, $result);
        $this->assertNotEmpty($oFG->getErrorMsg());
    }

    public function test_loadErrorUnknownFlag() : void
    {
        $oFG = $this->createXMLFG(true);
        $this->expectError();
        $oFG->loadXML(__DIR__ . DIRECTORY_SEPARATOR . 'testdata/FormErrorUnknownFlag.xml');
    }

    public function test_loadErrorUnknownDivAlign() : void
    {
        $oFG = $this->createXMLFG(true);
        $this->expectError();
        $oFG->loadXML(__DIR__ . DIRECTORY_SEPARATOR . 'testdata/FormErrorUnknownDivAlign.xml');
    }

    public function test_loadErrorUnknownFieldSetType() : void
    {
        $oFG = $this->createXMLFG(true);
        $this->expectError();
        $oFG->loadXML(__DIR__ . DIRECTORY_SEPARATOR . 'testdata/FormErrorUnknownFieldSetType.xml');
    }

    public function test_loadErrorUnknownButton() : void
    {
        $oFG = $this->createXMLFG(true);
        $this->expectError();
        $oFG->loadXML(__DIR__ . DIRECTORY_SEPARATOR . 'testdata/FormErrorUnknownButton.xml');
    }

    public function test_loadErrorXSD() : void
    {
        $oFG = $this->createXMLFG(true);
        $oFG->setSchemaValidation(true);
        $result = $oFG->loadXML(__DIR__ . DIRECTORY_SEPARATOR . 'testdata/FormErrorWithSchema.xml');
        $this->assertEquals(XMLForm::E_XSD_ERROR, $result);
        $this->assertNotEmpty($oFG->getErrorMsg());
    }
}

