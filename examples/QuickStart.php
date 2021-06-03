<?php
declare(strict_types=1);

require_once '../autoloader.php';

use SKien\Config\JSONConfig;
use SKien\Formgenerator\ArrayFormData;
use SKien\Formgenerator\FormButtonBox;
use SKien\Formgenerator\FormDate;
use SKien\Formgenerator\FormFlags;
use SKien\Formgenerator\FormFloat;
use SKien\Formgenerator\FormGenerator;
use SKien\Formgenerator\FormHeader;
use SKien\Formgenerator\FormInput;
use SKien\Formgenerator\FormInt;
use SKien\Formgenerator\FormSelect;
use SKien\Formgenerator\FormStatic;

// defining data array for test purposes
$aData = [
    'ID' => 24,
    'strLastname' => 'Johnson',
    'strFirstname' => 'Dwayne ',
    'strNickname' => 'the Rock',
    'strGender' => 'm',
    'dateDoB' => '1972-05-02',
    'iHeight' => 196,
    'fltWeight' => 116.0,
];

$oData = new ArrayFormData($aData);

$oFG = new FormGenerator($oData);

// load the configuration to use
$oConfig = new JSONConfig('./MSO-Theme/FormGenerator.json');
$oFG->setConfig($oConfig);

// just a script that displays all posted data
$oFG->setAction('formaction.php');
$oFG->setTarget('_blank');

//$oFG->setColWidth([20, 80], '%');
$oFG->setColWidth([100, 100], '%');

$oFG->add(new FormInput('ID', 0, FormFlags::HIDDEN));

$oFG->add(new FormHeader('Quick Start Example', 1));

$oFS = $oFG->addFieldSet('Name');
$oFL = $oFS->addLine('Lastname:');
$oFL->add(new FormInput('strLastname', '100%', FormFlags::MANDATORY, 50));
$oFL = $oFS->addLine('Firstname:');
$oFL->add(new FormInput('strFirstname', '100%', 0, 50));
$oFL = $oFS->addLine('Nickname:');
$oFL->add(new FormInput('strNickname', '100%', 0, 50));

$oFS = $oFG->addFieldSet('Personal Data');
//$oFS->setColWidth([20, 25, 20, 35], '%');
$oFS->setColWidth([35, 65, 35, 65], '%');
$oFL = $oFS->addLine('Gender:');
$oCtrl = new FormSelect('strGender', 1, FormFlags::MANDATORY);
$oCtrl->setSelectOptions(['' => '', 'male' => 'm', 'female' => 'f', 'diverse' => 'd']);
$oFL->add($oCtrl);
$oFL->add(new FormStatic('Birthday:'));
$oFL->add(new FormDate('dateDoB', FormFlags::NO_ZERO | FormFlags::ADD_DATE_PICKER));

$oFL = $oFS->addLine('Height:');
$oCtrl = new FormInt('iHeight', 4);
$oCtrl->setSuffix('cm');
$oFL->add($oCtrl);
$oFL->add(new FormStatic('Weight:'));
$oCtrl = new FormFloat('fltWeight', 5, 1);
$oCtrl->setSuffix('kg');
$oFL->add($oCtrl);

$oFG->add(new FormButtonBox(FormButtonBox::SAVE | FormButtonBox::DISCARD, FormFlags::ALIGN_RIGHT));

// generate HTML-markup and JS configuration data
$strFormHTML = $oFG->getForm();
$strStyleFromPHP = $oFG->getStyle();
$strConfigFromPHP = $oFG->getScript();
?>
<html>
    <head>
    	<title>Quick Start Example</title>
        <link type="text/css" rel="stylesheet" href="./MSO-Theme/FormGenerator.css">
        <style>
            <?php echo $strStyleFromPHP; ?>
        </style>

        <script type="text/javascript" src="../script/FormGenerator.js"></script>
        <script>
    	    <?php echo $strConfigFromPHP; ?>
        </script>
    </head>
    <body style="background-color: #777; width: 100%; padding-top: 20px;">
        <div style="width:320px; margin: 0px auto; background-color: transparent;">
        	<?php echo $strFormHTML; ?>
        </div>
    </body>
</html>
