<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Formgenerator\FormLine;
use SKien\Formgenerator\FormHeader;

/**
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormHeaderTest extends TestCase
{
    use FormgeneratorHelper;
    
    public function test__construct() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oH1 = new FormHeader('just a test header', 1);
        $oFL->add($oH1);
        $strHTML = $oH1->getHTML();
        $this->assertNotFalse(strpos($strHTML, '<h1>just a test header</h1>'));
    }
}

