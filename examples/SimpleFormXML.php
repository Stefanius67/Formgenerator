<?php
declare(strict_types=1);

require_once '../autoloader.php';

use SKien\Config\JSONConfig;
use SKien\Formgenerator\ArrayFormData;
use SKien\Formgenerator\XMLForm;

// $strTheme = './MSO-Theme/';
$strTheme = './';

$oConfig = new JSONConfig($strTheme . 'FormGenerator.json');

$dtTo = new DateTime();
$dtTo->setTimestamp(time() + 3600);
$aData = [
    'username' => 'Stefanius',
    'strLastname' => 'Mustermann',
    'strFirstname' => 'Max',
    'strStreet' => 'Hammerstraße',
    'strPostcode' => '12345',
    'strCity' => 'Musterstadt',
    'strGender' => 'm',
    'dateDoB' => '1974-07-23',
    'timeAvailableFrom' => time(),
    'timeAvailableTo' => $dtTo,
    'fltDue' => 1904,
    'strCatColor' => '#B0BED0',
    'fltWeight' => 71.3,
    'iPriority' => 3,
];

$aGenderSelect = ['' => '', 'männlich' => 'm', 'weiblich' => 'f', 'divers' => 'd'];
$aCitySelect = ['München', 'Berlin', 'Köln', 'Hamburg'];

$oData = new ArrayFormData($aData, ['strGender' => $aGenderSelect, 'strCity' => $aCitySelect]);

$oFG = new XMLForm($oData);
$oFG->setConfig($oConfig);
$oFG->setAction('formaction.php');
$oFG->setTarget('_blank');
// $oFG->setReadOnly(true);
// $oFG->setDebugMode(true);

if ($oFG->loadXML('xml/SimpleForm.xml') == XMLForm::E_OK) {
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
<div style="width:600px; margin: 0px auto; background-color: transparent;">
<?php echo $strFormHTML; ?>
</div>
</body>
</html>
