<?php
declare(strict_types=1);

require_once '../autoloader.php';

use SKien\Formgenerator\FormGenerator;
use SKien\Formgenerator\FormFlags;
use SKien\Config\JSONConfig;
use SKien\Formgenerator\FormButtonBox;
use SKien\Formgenerator\FormHeader;
use SKien\Formgenerator\FormTextArea;
use SKien\Formgenerator\FormSelect;
use SKien\Formgenerator\ArrayFormData;

$aData = [];
$aPlaceholderSelect = ['Firstname' => '{{FN}}', 'Lastname' => '{{LN}}', 'Street' => '{{STR}}', 'Postcode' => '{{PC}}', 'City' => '{{CITY}}'];

$oData = new ArrayFormData($aData, ['strPlaceholder' => $aPlaceholderSelect]);

$oConfig = new JSONConfig('FormGenerator.json');

$oFG = new FormGenerator($oData);
$oFG->setConfig($oConfig);
$oFG->setColWidth([100], '%');
$oFG->add(new FormHeader('Textarea and a Selectbutton', 1));

$oFS = $oFG->addFieldSet('');
$oFL = $oFS->addLine('You can insert Placeholders for the Contact:');
$oFL->setColWidth([80, 20]);
$oSelBtn = new FormSelect('strPlaceholder', 1, FormFlags::SELECT_BTN);
$oSelBtn->setSelectBtnText('insert Placeholder');
$oSelBtn->addAttribute('onchange', 'insertPlaceholde(this);');
$oFL->add($oSelBtn);
$oFS->add(new FormTextArea('strText', 80, 20, '100%'));

$oBtnBox = new FormButtonBox(FormButtonBox::SAVE_CANCEL, FormFlags::ALIGN_RIGHT);
$oFG->add($oBtnBox);
$oBtnBox->addButton('Vorschau', 'btnPreview', FormButtonBox::FIRST);

// generate HTML-markup and JS configuration data
$strFormHTML = $oFG->getForm();
$strStyleFromPHP = $oFG->getStyle();
$strConfigFromPHP = $oFG->getScript();
?>
<html>
<head>
<link type="text/css" rel="stylesheet" href="../style/FormGenerator.css">
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

function insertPlaceholde(list)
{
	var oTextarea = document.getElementById( 'strText' );
	var strPlaceholder = list.value;
	if (oTextarea != null) {
		// first set Focus to textarea
		oTextarea.focus();
		
		// different behavior from IE, Firefox, ...
		if (typeof document.selection != 'undefined') {
			// ... IE
			var range = document.selection.createRange();
			range.text = strPlaceholder;
		}
		else if (typeof oTextarea.selectionStart != 'undefined') {
			// Gecko...
			var start = oTextarea.selectionStart;
			var end = oTextarea.selectionEnd;
			oTextarea.value = oTextarea.value.substr(0, start) + strPlaceholder + oTextarea.value.substr(end);
			
			// SetCursor behind Marker
			oTextarea.selectionStart = start + strPlaceholder.length;
			oTextarea.selectionEnd = oTextarea.selectionStart;
		}
	}
	list.selectedIndex = -1;
}

</script>
</head>
<body>
<div style="width:700px; margin: 0px auto; background-color: transparent;">
<?php echo $strFormHTML; ?>
</div>
</body>
</html>
