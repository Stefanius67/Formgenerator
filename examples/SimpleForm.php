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
use SKien\Formgenerator\FormInt;
use SKien\Formgenerator\FormColor;
use SKien\Formgenerator\FormHeader;

$oConfig = new JSONConfig('FormGenerator.json');

$dtTo = new DateTime();
$dtTo->setTimestamp(time() + 3600);
$aData = [
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
];

$aGenderSelect = ['' => '', 'männlich' => 'm', 'weiblich' => 'f', 'divers' => 'd'];

$oData = new ArrayFormData($aData, ['strGender' => $aGenderSelect]);

$oFG = new FormGenerator($oData);
$oFG->setConfig($oConfig);
// $oFG->setReadOnly(true);
// $oFG->setDebugMode(true);
$oFG->setColWidth([20, 80], '%');

$oFG->add(new FormHeader('With exposed Header...'));

$oFS = $oFG->addFieldSet('Name');
$oFL = $oFS->addLine('Lastname:');
$oFL->add(new FormInput('strLastname', '100%', FormFlags::MANDATORY));
$oFL = $oFS->addLine('Firstname:');
$oFL->add(new FormInput('strFirstname', '100%'));
$oFS = $oFG->addFieldSet('Address');
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
$oFL->add(new FormStatic('And here are some internal informations...', FormFlags::INFO | FormFlags::BOLD));
$oFL = $oFS->addLine('Gender:');
$oFL->add(new FormSelect('strGender', 1, FormFlags::MANDATORY));
$oFL->add(new FormStatic('Date of Birth:'));
$oFL->add(new FormDate('dateDoB', FormFlags::NO_ZERO));
$oFL = $oFS->addLine('Available from:');
$oFL->add(new FormTime('timeAvailableFrom'));
$oFL->add(new FormStatic('to:'));
$oFL->add(new FormTime('timeAvailableTo'));
$oFL = $oFS->addLine('Member due:');
$oFL->add(new FormCur('fltDue', 10, FormFlags::ADD_CUR));
$oFL->add(new FormStatic('extra fee:'));
$oFL->add(new FormCur('fltExtra', 10, FormFlags::ADD_CUR | FormFlags::NO_ZERO));
$oFL = $oFS->addLine('Priority:');
$oFL->add(new FormInt('iPriority', 8));
$oFL->add(new FormStatic('category:'));
$oFL->add(new FormColor('strCatColor'));

$oFG->add(new FormButtonBox(FormButtonBox::SAVE_CANCEL, FormFlags::ALIGN_RIGHT));

?>
<html>
<head>
<link type="text/css" rel="stylesheet" href="../style/FormGenerator.css">
<script type="text/javascript" src="../script/FormDataValidator.js"></script>
<script type="text/javascript" src="../script/jscolor.min.js"></script>
<script>
<?php echo $oFG->getScript(); ?>
</script>
</head>
<body>
<h1>A Simple Form</h1>
<div style="width:500px;">
<?php echo $oFG->getForm(); ?>
</div>
</body>
</html>
