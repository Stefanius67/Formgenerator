<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Formgenerator\FormScript;

/**
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormScriptTest extends TestCase
{
    use FormgeneratorHelper;
    
    public function test__construct() : void
    {
        $oFG = $this->createFG(false);
        $oScript = new FormScript('initSomething();');
        $oFG->add($oScript);
        $strHTML = $oFG->getForm();
        $this->assertNotFalse(strpos($strHTML, '<script>initSomething();</script>'));
    }
}

