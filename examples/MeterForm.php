<?php
declare(strict_types=1);

require_once '../autoloader.php';

use SKien\Config\JSONConfig;
use SKien\Formgenerator\ArrayFormData;
use SKien\Formgenerator\FormButtonBox;
use SKien\Formgenerator\FormFlags;
use SKien\Formgenerator\FormGenerator;
use SKien\Formgenerator\FormHeader;
use SKien\Formgenerator\FormInput;
use SKien\Formgenerator\FormMeter;

// defining data array for test purposes
$aData = [
    'ID' => 24,
    'fltTempCPU' => 103.0,
    'fltTempGPU' => 96.4,
    'fltTempPOW' => 78.7,
];

$oData = new ArrayFormData($aData);

$oFG = new FormGenerator($oData);

// load the configuration to use
$oConfig = new JSONConfig('./FormGenerator.json');
$oFG->setConfig($oConfig);

$oFG->setColWidth([40, 60], '%');

$oFG->add(new FormInput('ID', 0, FormFlags::HIDDEN));

$oFG->add(new FormHeader('Meter Example', 1));

$oFS = $oFG->addFieldSet('Temperature');
$oFL = $oFS->addLine('CPU:');
$oMeter = new FormMeter('fltTempCPU', '100%', 60.0, 120.0);
$oMeter->setMessureRange( 80.0, 100.0, 60.0);
$oFL->add($oMeter);
$oFL = $oFS->addLine('Grafic chip:');
$oMeter = new FormMeter('fltTempGPU', '100%', 60.0, 120.0);
$oMeter->setMessureRange( 80.0, 100.0, 60.0);
$oFL->add($oMeter);
$oFL = $oFS->addLine('Power adapter:');
$oMeter = new FormMeter('fltTempPOW', '100%', 60.0, 120.0);
$oMeter->setMessureRange( 80.0, 100.0, 60.0);
$oFL->add($oMeter);

$oFG->add(new FormButtonBox(FormButtonBox::OK));

// generate HTML-markup and JS configuration data
$strFormHTML = $oFG->getForm();
$strStyleFromPHP = $oFG->getStyle();
$strConfigFromPHP = $oFG->getScript();
?>
<html>
    <head>
    	<title>Meter Example</title>
        <link type="text/css" rel="stylesheet" href="./FormGenerator.css">
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
