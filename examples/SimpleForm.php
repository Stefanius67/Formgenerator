<?php
declare(strict_types=1);

require_once '../autoloader.php';

use SKien\Formgenerator\FormGenerator;
use SKien\Formgenerator\FormInput;
use SKien\Formgenerator\FormFlags;
use SKien\Formgenerator\ArrayFormData;
use SKien\Formgenerator\FormSelect;
use SKien\Formgenerator\FormStatic;
use SKien\Formgenerator\FormDate;
use SKien\Formgenerator\FormTime;
use SKien\Config\JSONConfig;
use SKien\Formgenerator\FormButtonBox;
use SKien\Formgenerator\FormCur;
use SKien\Formgenerator\FormColor;
use SKien\Formgenerator\FormHeader;
use SKien\Formgenerator\FormFloat;
use SKien\Formgenerator\FormRange;

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

$oFG = new FormGenerator($oData);
$oFG->setConfig($oConfig);
$oFG->setAction('formaction.php');
$oFG->setTarget('_blank');
// $oFG->setReadOnly(true);
// $oFG->setDebugMode(true);
$oFG->setColWidth([20, 80], '%');

$oFG->add(new FormHeader('Personnel data sheet', 1));

$oFS = $oFG->addFieldSet('Name');
$oFL = $oFS->addLine('Lastname:');
$oFL->add(new FormInput('strLastname', '100%', FormFlags::MANDATORY));
$oFL = $oFS->addLine('Firstname:');
$oFL->add(new FormInput('strFirstname', '100%'));
$oFS = $oFG->addFieldSet('Address');
$oFL = $oFS->addLine('Region:');
$oEdit = new FormInput('strRegion', '100%');
$oFL->add($oEdit);
$oEdit->setPlaceholder('enter Region');
$oFL = $oFS->addLine('Street:');
$oFL->add(new FormInput('strStreet', '100%'));
$oFL = $oFS->addLine('Postcode, City:');
$oFL->setColWidth([20, 20, 60], '%');
$oFL->add(new FormInput('strPostcode', '90%'));
$oFL->add(new FormInput('strCity', '100%'));
$oFS = $oFG->addFieldSet('some personal Data');
$oFS->setColWidth([20, 30, 20, 30], '%');
$oFL = $oFS->addLine();
$oFL->setColWidth([0, 100], '%');
$oFL->add(new FormStatic('And here are some informations...', FormFlags::INFO | FormFlags::BOLD));
$oFL = $oFS->addLine('Gender:');
$oFL->add(new FormSelect('strGender', 1, FormFlags::MANDATORY));
$oFL->add(new FormStatic('Date of Birth:'));
$oFL->add(new FormDate('dateDoB', FormFlags::NO_ZERO | FormFlags::ADD_DATE_PICKER));
$oFL = $oFS->addLine('Available from:');
$oFL->add(new FormTime('timeAvailableFrom', FormFlags::ADD_TIME_PICKER));
$oFL->add(new FormStatic('to:'));
$oFL->add(new FormTime('timeAvailableTo', FormFlags::ADD_TIME_PICKER));
$oFL = $oFS->addLine('Member due:');
$oFL->add(new FormCur('fltDue', 10, FormFlags::ADD_CUR));
$oFL->add(new FormStatic('extra fee:'));
$oFL->add(new FormCur('fltExtra', 10, FormFlags::ADD_CUR | FormFlags::NO_ZERO));
$oFL = $oFS->addLine('Weight:');
$oFloat = new FormFloat('fltWeight', 10, 1);
$oFL->add($oFloat);
$oFloat->setSuffix('kg');
$oFL = $oFS->addLine('Priority:');
// $oFL->add(new FormInt('iPriority', 8));
$oFL->add(new FormRange('iPriority', '70%', 1, 10, FormFlags::SHOW_VALUE));
$oFL->add(new FormStatic('category:'));
$oFL->add(new FormColor('strCatColor'));
$oFL = $oFS->addLine('Resumé:');
$oFL->setColWidth([20, 80], '%');
$oEdit = new FormInput('strResumeFile', 'calc(100% - 45px)', FormFlags::BROWSE_SERVER | FormFlags::READ_ONLY);
$oEdit->setExpandFolder('files/');
$oFL->add($oEdit);
$oFS = $oFG->addFieldSet('internal processing');
$oFL = $oFS->addLine('Validated:');
$oFL->add(new FormInput('strValidated', 30, FormFlags::ADD_DTU | FormFlags::READ_ONLY));

$oFG->add(new FormButtonBox(FormButtonBox::SAVE | FormButtonBox::DISCARD, FormFlags::ALIGN_RIGHT));

// generate HTML-markup and JS configuration data
$strFormHTML = $oFG->getForm();
$strStyleFromPHP = $oFG->getStyle();
$strConfigFromPHP = $oFG->getScript();
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
