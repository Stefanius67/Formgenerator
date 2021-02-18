<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Formgenerator\FormLine;
use SKien\Formgenerator\FormColor;

/**
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormColorTest extends TestCase
{
    use FormgeneratorHelper;
    
    public function test__construct() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oColor = new FormColor('strColor1');
        $oFL->add($oColor);
        $strHTML = $oColor->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'data-jscolor="'));
        $strJS = $oFG->getScript();
        $this->assertNotFalse(strpos($strJS, '"Color":'));
    }
}

