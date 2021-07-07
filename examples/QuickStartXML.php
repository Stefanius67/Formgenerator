<?php
declare(strict_types=1);

require_once '../autoloader.php';

use SKien\Config\JSONConfig;
use SKien\Formgenerator\ArrayFormData;
use SKien\Formgenerator\XMLForm;

// $strTheme = './MSO-Theme/';
$strTheme = './';

$oConfig = new JSONConfig($strTheme . 'FormGenerator.json');

$aData = [
    'strLastname' => 'Johnson',
    'strFirstname' => 'Dwayne ',
    'strNickname' => 'the Rock',
    'strGender' => 'm',
    'dateDoB' => '1972-05-02',
    'iHeight' => 196,
    'fltWeight' => 116.0,
];

$oData = new ArrayFormData($aData);

$oFG = new XMLForm($oData);
$oFG->setConfig($oConfig);
$oFG->setAction('formaction.php');
$oFG->setTarget('_blank');

if ($oFG->loadXML('xml/QuickStart.xml') == XMLForm::E_OK) {
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
<title>Quick Start Example</title>
<link type="text/css" rel="stylesheet" href="<?= $strTheme; ?>FormGenerator.css">
<style>
body
{
    background-color: #777;
    width: 100%;
    padding-top: 20px;
}

<?php echo $strStyleFromPHP; ?>
</style>
<script type="text/javascript" src="../script/FormGenerator.js"></script>
<script>
<?php echo $strConfigFromPHP; ?>
</script>
</head>
<body>
<div style="width:400px; margin: 0px auto; background-color: transparent;">
<?php echo $strFormHTML; ?>
</div>
</body>
</html>
