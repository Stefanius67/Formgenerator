<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use SKien\Formgenerator\FormGenerator;
use SKien\Formgenerator\ArrayFormData;
use SKien\Formgenerator\FormDataInterface;
use SKien\Config\ConfigInterface;
use SKien\Config\JSONConfig;
use SKien\Formgenerator\FormFlags;

/**
 * Base testcase for all Formgenerator testcases.
 * 
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
trait FormgeneratorHelper
{
    /**
     */
    protected function createFG(bool $bDefault) : FormGenerator
    {
        $_SERVER['DOCUMENT_ROOT'] = "/var/www/html";
        $oFG = new FormGenerator($this->createData());
        if (!$bDefault) {
            $oFG->setConfig($this->createConfig());
        }
        
        return $oFG;
    }
    
    protected function createConfig() : ConfigInterface
    {
        return new JSONConfig(__DIR__ . DIRECTORY_SEPARATOR . 'testdata/FormGeneratorDE.json');
    }
    
    protected function createData() : FormDataInterface
    {
        $dtTo = new \DateTime();
        $dtTo->setTimestamp(time() + 3600);
        $aData = [
            'strLastname' => 'Mustermann',
            'strFirstname' => 'Max ',
            'strStreet' => 'Hammerstraße',
            'strPostcode' => '12345',
            'strCity' => 'Musterstadt',
            'strGender' => 'm',
            'dateDoB1' => '1974-07-23',
            'dateDoB2' => mktime(0, 0, 0, 7, 23, 1974),
            'dateDoB3' => new \DateTime('1974-07-23'),
            'timeAvailableFrom1' => mktime(20, 23, 34, 0, 0, 0),
            'timeAvailableFrom2' => new \DateTime('20:23:34'),
            'timeAvailableFrom3' => '20:23:34',
            'fltDue' => 1904,
            'strCatColor' => '#B0BED0',
            'fltWeight' => 71.3,
            'iCount' => 567,
            'strTextBR' => 'Text containing <br>, <br/> and <br />',
            'strImage' => '/packages/Formgenerator/public/images/sample1.jpg',
            'btnCheck1' => true,
            'btnCheck2' => false,
            'btnCheck3' => 'Yes',
            'btnCheck4' => 1,
            'btnCheck5' => 'false',
            'btnCheck6' => 0,
        ];
        $aGenderSelect = ['' => '', 'männlich' => 'm', 'weiblich' => 'f', 'divers' => 'd'];
        $aLinkList = ['Freiburg', 'Karlsruhe', 'Stuttgart'];
        $aCheckBtnValue = ['btnCheck1' => 'board'];
        $oData = new ArrayFormData($aData, ['strGender' => $aGenderSelect, 'strLinklist' => $aLinkList], $aCheckBtnValue);
        
        return $oData;
    }
    
    public function provideAlignFlag()
    {
        return [
            "center" => [
                'text-align: center;',
                FormFlags::ALIGN_CENTER
            ],
            "right" => [
                'text-align: right;',
                FormFlags::ALIGN_RIGHT
            ]
        ];
    }
}

