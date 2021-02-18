<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Formgenerator\FormTextArea;
use SKien\Formgenerator\FormFlags;

/**
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormTextAreaTest extends TestCase
{
    use FormgeneratorHelper;
    
    public function test__construct() : void
    {
        $oFG = $this->createFG(false);
        $oTA = new FormTextArea('strText', 60, 12);
        $oFG->add($oTA);
        $strHTML = $oTA->getHTML();
        $this->assertNotFalse(strpos($strHTML, '<textarea'));
        $this->assertNotFalse(strpos($strHTML, 'cols="60"'));
        $this->assertNotFalse(strpos($strHTML, 'rows="12"'));
    }
    
    public function test_Flags() : void
    {
        $oFG = $this->createFG(false);
        $oTA = new FormTextArea('strTextBR', 60, 12, '100%', FormFlags::REPLACE_BR_CR);
        $oFG->add($oTA);
        $strHTML = $oTA->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'width: 100%'));
        $this->assertFalse(strpos($strHTML, '<br>'));
        $this->assertFalse(strpos($strHTML, '<br/>'));
        $this->assertFalse(strpos($strHTML, '<br />'));
    }
    
    public function test_width() : void
    {
        $oFG = $this->createFG(false);
        $oTA = new FormTextArea('strText', 60, 12);
        $oFG->add($oTA);
        $strHTML = $oTA->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'width: 95%'));
    }
}

