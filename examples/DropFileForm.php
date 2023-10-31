<?php
declare(strict_types=1);

require_once '../autoloader.php';

use SKien\Config\JSONConfig;
use SKien\Formgenerator\ArrayFormData;
use SKien\Formgenerator\FormButtonBox;
use SKien\Formgenerator\FormDropFile;
use SKien\Formgenerator\FormFlags;
use SKien\Formgenerator\FormGenerator;
use SKien\Formgenerator\FormHeader;

$aData = [];
$aPlaceholderSelect = ['Firstname' => '{{FN}}', 'Lastname' => '{{LN}}', 'Street' => '{{STR}}', 'Postcode' => '{{PC}}', 'City' => '{{CITY}}'];

$oData = new ArrayFormData($aData, ['strPlaceholder' => $aPlaceholderSelect]);

$strTheme = './MSO-Theme/';
//$strTheme = './';

$oConfig = new JSONConfig($strTheme . 'FormGenerator.json');

$oFG = new FormGenerator($oData);
$oFG->setConfig($oConfig);
$oFG->addAttribute('enctype', 'multipart/form-data');
$oFG->setColWidth([100], '%');
$oFG->add(new FormHeader('File Upload supporting Drag´n Drop', 1));
$oFG->setOnSubmit('onSubmitForm()');

$oFS = $oFG->addFieldSet('Upload file');
$oFS->add(new FormDropFile('fileUpload', 2, 1));

$oBtnBox = new FormButtonBox(FormButtonBox::CANCEL, FormFlags::ALIGN_RIGHT);
$oFG->add($oBtnBox);
$oBtnBox->addButton('Upload File', 'btnUpload', FormButtonBox::CANCEL, true);

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
<script type="text/javascript" src="../script/DropFile.js"></script>
<script>
<?php echo $strConfigFromPHP; ?>

function onSubmitForm()
{
	return g_oDropFile.setSelectedFiles();
}

function onLoad()
{
	g_oDropFile.init('');
}

</script>
</head>
<body onload="onLoad()">
<div style="width:700px; margin: 0px auto; background-color: transparent;">
<?php echo $strFormHTML; ?>
</div>
</body>
</html>
