<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use SKien\Formgenerator\FormLine;
use SKien\Formgenerator\FormMeter;
use SKien\Test\HtmlTestCase;

/**
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormMeterTest extends HtmlTestCase
{
    use FormgeneratorHelper;

    public function test_ValueSet1() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oMeter = $oFL->add(new FormMeter('fltMeter', '80%', 10.0, 50.0));
        $strHTML = $oMeter->getHTML();
        $this->assertHtmlElementAttribEquals($strHTML, 'fltMeter', 'value', '34.5');
        $this->assertHtmlElementAttribEquals($strHTML, 'fltMeter', 'min', '10');
        $this->assertHtmlElementAttribEquals($strHTML, 'fltMeter', 'max', '50');
    }

    public function test_ValueNotSet() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oMeter = $oFL->add(new FormMeter('iNotSet', '80%'));
        $strHTML = $oMeter->getHTML();
        $this->assertHtmlElementAttribEquals($strHTML, 'iNotSet', 'value', '0');
        $this->assertHtmlElementAttribEquals($strHTML, 'iNotSet', 'min', '0');
        $this->assertHtmlElementAttribEquals($strHTML, 'iNotSet', 'max', '1');
    }

    public function test_MinMax() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oMeter = new FormMeter('fltMeter', '80%');
        $oMeter->setMinMax(10, 50);
        $oFL->add($oMeter);
        $strHTML = $oMeter->getHTML();
        $this->assertHtmlElementAttribEquals($strHTML, 'fltMeter', 'value', '34.5');
        $this->assertHtmlElementAttribEquals($strHTML, 'fltMeter', 'min', '10');
        $this->assertHtmlElementAttribEquals($strHTML, 'fltMeter', 'max', '50');
    }

    public function test_MessureRange() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oMeter = new FormMeter('fltMeter', '80%');
        $oMeter->setMessureRange(30, 70, 15);
        $oFL->add($oMeter);
        $strHTML = $oMeter->getHTML();
        $this->assertHtmlElementAttribEquals($strHTML, 'fltMeter', 'low', '30');
        $this->assertHtmlElementAttribEquals($strHTML, 'fltMeter', 'high', '70');
        $this->assertHtmlElementAttribEquals($strHTML, 'fltMeter', 'optimum', '15');
    }
}
