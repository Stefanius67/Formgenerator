<?php
declare(strict_types=1);

require_once '../autoloader.php';

use SKien\Config\JSONConfig;
use SKien\Formgenerator\ArrayFormData;
use SKien\Formgenerator\FormButtonBox;
use SKien\Formgenerator\FormCKEdit;
use SKien\Formgenerator\FormFlags;
use SKien\Formgenerator\FormGenerator;
use SKien\Formgenerator\FormHeader;

$strTheme = './MSO-Theme/';
// $strTheme = './';

$oConfig = new JSONConfig($strTheme . 'FormGenerator.json');
$oData = new ArrayFormData([
        'strText' =>
            '<h1>This is the content</h1>' . PHP_EOL .
            '<p>with a  little text...</p>'
    ]);

$oFG = new FormGenerator($oData);
$oFG->setAction('formaction.php');
$oFG->setTarget('_blank');
$oFG->setConfig($oConfig);

$oFG->add(new FormHeader('CFKEditor connected with Rich-Filemanager', 1));
$oFG->setColWidth([100], '%');
$oEditor = new FormCKEdit('strText', 20, '100%', FormFlags::SET_JSON_DATA);
$oEditor->setContentsCss('../style/FormGenerator.css');
$oEditor->setToolbar(FormCKEdit::TB_FULL | FormCKEdit::TB_SOURCE);
$oEditor->setBrowseFolderImageURL('images/news/2020-21/');
$oEditor->addCustomButton('H1-Header', 'InsertH1');
$oFG->add($oEditor);

$oBtnBox = new FormButtonBox(FormButtonBox::SAVE_CANCEL, FormFlags::ALIGN_RIGHT);
$oFG->add($oBtnBox);
$oBtnBox->addButton('Vorschau', 'btnPreview', FormButtonBox::FIRST);

// generate HTML-markup and JS configuration data
$strFormHTML = $oFG->getForm();
$strStyleFromPHP = $oFG->getStyle();
$strConfigFromPHP = $oFG->getScript();
?>
<!DOCTYPE html>
<html>
<head>
<title>Test</title>
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

/**
 * sample handler for the custom button
 */
function InsertH1(editor)
{
	editor.insertHtml( '<h1>Inserted H1-Header</h1>' );
}

/**
 * .. and a 'poor' preview ;-)
 */
function btnPreviewClicked()
{
    let iWidth = screen.width * 0.6;
    let iHeight = screen.height * 0.6;
    let iLeft = (screen.width - iWidth) / 2 ;
    let iTop = (screen.height - iHeight) / 2 ;

    let strOptions = "toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,dependent=no";
    strOptions += ",width=" + parseInt(iWidth);
    strOptions += ",height=" + parseInt(iHeight);
    strOptions += ",left=" + parseInt(iLeft);
    strOptions += ",top=" + parseInt(iTop);

	var win = window.open("", "Preview", strOptions);
	win.document.body.innerHTML = g_oCKEdit.oEditor.getData();
}
</script>
</head>
<body>
<div style="width:700px; margin: 0px auto; background-color: transparent;">
<?php echo $strFormHTML; ?>
</div>
</body>
</html>
