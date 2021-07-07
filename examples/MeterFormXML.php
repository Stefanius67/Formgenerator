<?php
declare(strict_types=1);

require_once '../autoloader.php';

use SKien\Config\JSONConfig;
use SKien\Formgenerator\ArrayFormData;
use SKien\Formgenerator\XMLForm;

// $strTheme = './MSO-Theme/';
$strTheme = './';

// defining data array for test purposes
$aData = [
    'fltTempCPU' => 103.0,
    'fltTempGPU' => 96.4,
    'fltTempPOW' => 78.7,
    'iRating' => 3,
];

$oData = new ArrayFormData($aData);

$oFG = new XMLForm($oData);

// load the configuration to use
$oConfig = new JSONConfig($strTheme . 'FormGenerator.json');
$oFG->setConfig($oConfig);

if ($oFG->loadXML('xml/MeterForm.xml') == XMLForm::E_OK) {
    // generate HTML-markup and JS configuration data
    $strFormHTML = $oFG->getForm();
    $strStyleFromPHP = $oFG->getStyle();
    $strConfigFromPHP = $oFG->getScript();
} else {
    $strFormHTML = $oFG->getErrorMsg();
    $strStyleFromPHP = '';
    $strConfigFromPHP = '';
}
?>
<html>
    <head>
    	<title>Meter/StarRate Example</title>
        <link type="text/css" rel="stylesheet" href="<?= $strTheme; ?>FormGenerator.css">
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
