<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Formgenerator\ArrayFormData;

/**
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class ArrayFormDataTest extends TestCase
{
    public function test_setValue() : void
    {
        $oData = new ArrayFormData([]);
        $oData->setValue('test', 'value');
        $this->assertEquals('value', $oData->getValue('test'));
    }
    
    public function test_setSelectOptions() : void
    {
        $oData = new ArrayFormData([]);
        $oData->setSelectOptions('test', ['' => '', 'test1' => '1']);
        $aOptions = $oData->getSelectOptions('test');
        $this->assertIsArray($aOptions);
        $this->assertArrayHasKey('', $aOptions);
        $this->assertArrayHasKey('test1', $aOptions);
    }
}

