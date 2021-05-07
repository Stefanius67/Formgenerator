<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Formgenerator\FormLine;
use SKien\Formgenerator\FormImage;

/**
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormImageTest extends TestCase
{
    use FormgeneratorHelper;

    /**
     * @dataProvider provideStdImage
     */
    public function test_StdImage($strImage, $iStdImage) : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oImage = $oFL->add(new FormImage('strImage', $iStdImage, ''));
        $strHTML = $oImage->getHTML();
        $this->assertNotFalse(strpos($strHTML, $strImage));
    }
    
    public function test_DefaultImage() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oImage = new FormImage('strImage', '', '');
        $oFL->add($oImage);
        $oImage->setDefault('default.png');
        $strHTML = $oImage->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'data-default="default.png"'));
        $this->assertNotFalse(strpos($strHTML, 'src="default.png"'));
    }
    
    public function test_Title() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oImage = new FormImage('strImage', 'image.png', '');
        $oFL->add($oImage);
        $oImage->setTitle('the title');
        $strHTML = $oImage->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'title="the title"'));
    }
    
    public function test_BindTo() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oImage = new FormImage('imgImage', 'image.png', '');
        $oFL->add($oImage);
        $oImage->bindTo('strImage');
        $strHTML = $oImage->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'data-bound-to="strImage"'));
    }
    
    public function test_Style() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oImage = new FormImage('imgImage', 'image.png', '', 0, 'padding: 0');
        $oFL->add($oImage);
        $strHTML = $oImage->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'padding: 0'));
    }
    
    public function test_OnClick() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oImage = new FormImage('imgImage', 'image.png', 'testfunc()');
        $oFL->add($oImage);
        $strHTML = $oImage->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'onclick="testfunc();"'));
    }
    
    /**
     * @dataProvider provideAlignFlag
     */
    public function test_Flags($strStyle, $wFlags) : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oImage = new FormImage('imgImage', 'image.png', '', $wFlags);
        $oFL->add($oImage);
        $strHTML = $oImage->getHTML();
        $this->assertNotFalse(strpos($strHTML, $strStyle));
    }
    
    public function provideStdImage()
    {
        return [
            "Delete" => [
                "delete.png",
                FormImage::IMG_DELETE
            ],
            "Search" => [
                "search.png",
                FormImage::IMG_SEARCH
            ],
            "Browse" => [
                "browse.png",
                FormImage::IMG_BROWSE
            ],
            "DatePicker" => [
                "datepicker.png",
                FormImage::IMG_DATE_PICKER
            ],
            "TimePicker" => [
                "timepicker.png",
                FormImage::IMG_TIME_PICKER
            ],
            "InsertDTU" => [
                "insert_dtu.png",
                FormImage::IMG_DTU
            ]
        ];
    }
}
