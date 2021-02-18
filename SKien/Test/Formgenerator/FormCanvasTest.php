<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Formgenerator\FormCanvas;
use SKien\Formgenerator\FormLine;

/**
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormCanvasTest extends TestCase
{
    use FormgeneratorHelper;
    
    public function test__construct() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oCvs = new FormCanvas('canvas', 200, 200);
        $oFL->add($oCvs);
        $strHTML = $oCvs->getHTML();
        $this->assertNotFalse(strpos($strHTML, '<canvas'));
    }
}

