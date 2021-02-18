<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Formgenerator\FormButtonBox;

/**
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormButtonBoxTest extends TestCase
{
    use FormgeneratorHelper;
    
    /**
     * @dataProvider provideBtn
     */
    public function test_ButtonBoxSingle(int $iBtn, string $strText, bool $bSubmit) : void
    {
        $oFG = $this->createFG(true);
        $oBtnBox = new FormButtonBox($iBtn);
        $oFG->add($oBtnBox);
        $strHTML = $oBtnBox->getHTML();
        if ($bSubmit) {
            $this->assertNotFalse(strpos($strHTML, 'type="submit"'));
        } else {
            $this->assertNotFalse(strpos($strHTML, 'type="button"'));
        }
        $strValue = 'value="' . $strText . '"';
        $this->assertNotFalse(strpos($strHTML, $strValue));
    }
    
    public function provideBtn()
    {
        return [
            [FormButtonBox::OK, "OK", true],
            [FormButtonBox::OPEN, "Open", false],
            [FormButtonBox::SAVE, "Save", true],
            [FormButtonBox::YES, "Yes", true],
            [FormButtonBox::NO, "No", false],
            [FormButtonBox::CANCEL, "Cancel", false],
            [FormButtonBox::CLOSE, "Close", false],
            [FormButtonBox::DISCARD, "Discard", false],
            [FormButtonBox::APPLY, "Apply", true],
            [FormButtonBox::RESET, "Reset", false],
            [FormButtonBox::RETRY, "Retry", true],
            [FormButtonBox::IGNORE, "Ignore", false],
            [FormButtonBox::BACK, "Back", false],
        ];
    }
    
    public function test_addButton() : void
    {
        $oFG = $this->createFG(true);
        $oBtnBox = new FormButtonBox(FormButtonBox::OK);
        $oBtnBox->addButton('Preview', 'btnPreview', FormButtonBox::FIRST);
        $oFG->add($oBtnBox);
        $strHTML = $oBtnBox->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'type="button"'));
        $this->assertNotFalse(strpos($strHTML, 'value="Preview"'));
    }
    
    public function test_empty() : void
    {
        $oFG = $this->createFG(true);
        $oBtnBox = new FormButtonBox(0);
        $oFG->add($oBtnBox);
        $strHTML = $oBtnBox->getHTML();
        $this->assertEmpty($strHTML);
    }
    
    /**
     * @dataProvider provideAlignFlag
     */
    public function test_Flags($strStyle, $wFlags) : void
    {
        $oFG = $this->createFG(false);
        $oFG->setReadOnly(true);
        $oBtnBox = new FormButtonBox(FormButtonBox::OK, $wFlags);
        $oFG->add($oBtnBox);
        $strHTML = $oBtnBox->getHTML();
        $this->assertNotFalse(strpos($strHTML, $strStyle));
    }
}
