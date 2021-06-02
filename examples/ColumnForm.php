<?php
declare(strict_types=1);

require_once '../autoloader.php';

use SKien\Config\JSONConfig;
use SKien\Formgenerator\ArrayFormData;
use SKien\Formgenerator\FormButton;
use SKien\Formgenerator\FormButtonBox;
use SKien\Formgenerator\FormCheck;
use SKien\Formgenerator\FormDate;
use SKien\Formgenerator\FormDiv;
use SKien\Formgenerator\FormFlags;
use SKien\Formgenerator\FormGenerator;
use SKien\Formgenerator\FormHeader;
use SKien\Formgenerator\FormImage;
use SKien\Formgenerator\FormInput;
use SKien\Formgenerator\FormLine;
use SKien\Formgenerator\FormRadioGroup;

$strTheme = './MSO-Theme/';

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
    'strImage' => '/packages/Formgenerator/examples/public/images/sample1.jpg',
    'bBoardMember' => true,
];

$oData = new ArrayFormData($aData);

$oFG = new FormGenerator($oData);
$oFG->setConfig($oConfig);
$oFG->setAction('formaction.php');
$oFG->setTarget('_blank');

$oFG->setColWidth([25, 75], '%');

$oFG->add(new FormHeader('Personnel data sheet', 1));

$oCol1 = $oFG->addDiv(70, FormDiv::LEFT);
$oFS = $oCol1->addFieldSet('Name and Adress', 'col1');
$oFL = $oFS->addLine('Lastname:');
$oFL->add(new FormInput('strLastname', '100%', FormFlags::MANDATORY));
$oFL = $oFS->addLine('Firstname:');
$oFL->add(new FormInput('strFirstname', '100%'));
$oFL = $oFS->addLine('Street:');
$oFL->add(new FormInput('strStreet', '100%'));
$oFL = $oFS->addLine('Postcode, City:');
$oFL->setColWidth([25, 25, 50], '%');
$oFL->add(new FormInput('strPostcode', '90%'));
$oFL->add(new FormInput('strCity', '100%'));
$oFL = $oFS->addLine(FormLine::HR);
$oFL = $oFS->addLine('Gender:');
$oCtrl = new FormRadioGroup('strGender', FormFlags::HORZ_ARRANGE);
$oCtrl->setSelectOptions(['' => '', 'male' => 'm', 'female' => 'f', 'diverse' => 'd']);
$oFL->add($oCtrl);
$oFL = $oFS->addLine('Birthday:');
$oFL->add(new FormDate('dateDoB', FormFlags::ADD_DATE_PICKER));
$oFL = $oFS->addLine();
$oFL->add(new FormCheck('bBoardMember', 0, 'member of the board'));

$oCol2 = $oFG->addDiv(30, FormDiv::LEFT);
$oFS = $oCol2->addFieldSet('Portrait', 'col2');
$oFS->setColWidth([100]);

// Right col containing an image to display portrait.
// This consists of:
// - the image itself, that
//     * is bound to a hidden input field that contains the imagepath as value
//     * has a onclick handler to call the filebrowser on the server
//     * holds a default image to display, if no image is set
// - a additional button [Select] to call the filebrowser
// - a additional button [Reset] to reset the image to default and clear the associated input field

$strBrowseServer = "browseServer('strImage', 'imgImage', '/images')";
$oFL->add(new FormInput('strImage', 0, FormFlags::HIDDEN | FormFlags::BROWSE_SERVER));
$oImg = new FormImage('imgImage', '', $strBrowseServer, FormFlags::ALIGN_CENTER);
$oImg->setDefault('.\public\images\contact_empty.png');
$oImg->bindTo('strImage');
$oImg->setTitle('click to select an image');
$oFS->add($oImg);

$oFL = $oFS->addLine();
$oFL->setColWidth([0, 50, 50]);
$oFL->add(new FormButton('btnImageSelect', 'Select', $strBrowseServer, FormFlags::ALIGN_CENTER));
$oFL->add(new FormButton('btnImageReset', 'Reset', "resetElement('imgImage')", FormFlags::ALIGN_CENTER));

$oFG->add(new FormButtonBox(FormButtonBox::SAVE | FormButtonBox::DISCARD, FormFlags::ALIGN_RIGHT));

$oFG->adjustColHeight('col1', 'col2');

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
