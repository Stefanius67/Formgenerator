<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use SKien\Formgenerator\FormLine;
use SKien\Formgenerator\FormStarRate;
use SKien\Test\HtmlTestCase;

/**
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormStarRateTest extends HtmlTestCase
{
    use FormgeneratorHelper;

    public function test_Construct() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oStar = $oFL->add(new FormStarRate('iRating'));
        $strHTML = $oStar->getHTML();
        $this->assertValidHtmlBlock($strHTML);
        $this->assertHtmlHasElement($strHTML, 'iRatingStar1');
        $this->assertHtmlHasElement($strHTML, 'iRatingStar5');
    }

    public function test_ValueSet() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oStar = $oFL->add(new FormStarRate('iRating'));
        $strHTML = $oStar->getHTML();
        $this->assertHtmlElementHasAttrib($strHTML, 'iRatingStar4', 'checked');
    }

    public function test_SetTitles() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oStar = new FormStarRate('iRating');
        $oStar->setTitles(['ungenügend', 'mangelhaft', 'ausreichend', 'befriedigend', 'gut', 'sehr gut']);
        $oFL->add($oStar);
        $strHTML = $oStar->getHTML();
        $this->assertHtmlHasElement($strHTML, 'iRatingStar6');
        $this->assertHtmlElementAttribEquals($strHTML, 'iRatingStar4Label', 'title', 'befriedigend');
    }

    public function test_SetSubmitTitles() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oStar = new FormStarRate('iRating');
        $oStar->setSubmitTitle(true);
        $oFL->add($oStar);
        $strHTML = $oStar->getHTML();
        $this->assertHtmlElementAttribEquals($strHTML, 'iRatingStar1', 'value', 'terrible');
    }

    /**
     * @dataProvider provideAlignFlag
     */
    public function test_Align($strStyle, $wFlags) : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oStar = $oFL->add(new FormStarRate('iRating', $wFlags));
        $strHTML = $oStar->getHTML();
        $this->assertNotFalse(strpos($strHTML, $strStyle));
    }
}
