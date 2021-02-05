<?php
declare(strict_types=1);

require_once '../autoloader.php';

use SKien\Formgenerator\FormGenerator;
use SKien\Formgenerator\FormFlags;
use SKien\Config\JSONConfig;
use SKien\Formgenerator\FormButtonBox;
use SKien\Formgenerator\FormCKEdit;

$oConfig = new JSONConfig('FormGenerator.json');

$oFG = new FormGenerator(null);
$oFG->setDebugMode(true);
$oFG->setConfig($oConfig);

$oFG->setColWidth([100], '%');
$oEditor = new FormCKEdit('editor', 20, '100%');
$oEditor->setContentsCss('../style/FormGenerator.css');
$oEditor->setToolbar(FormCKEdit::TB_FULL | FormCKEdit::TB_SOURCE);
$oEditor->setBrowseFolderImageURL('images/news/2020-21/');
$oEditor->addCustomButton('H1-Header', 'InsertH1');
$oFG->add($oEditor);

$oBtnBox = new FormButtonBox(FormButtonBox::SAVE_CANCEL, FormFlags::ALIGN_RIGHT);
$oFG->add($oBtnBox);
$oBtnBox->addButton('Vorschau', 'btnPreview', FormButtonBox::FIRST);

?>
<html>
<head>
<link type="text/css" rel="stylesheet" href="../style/FormGenerator.css">
<script type="text/javascript" src="../script/FormGenerator.js"></script>
<script type="text/javascript" src="../script/FormCKEdit.js"></script>
<script type="text/javascript" src="../script/FormDataValidator.js"></script>
<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
<script>
<?php echo $oFG->getScript(); ?>

function InsertH1(editor)
{
	editor.insertHtml( '<h1>Inserted H1-Header</h1>' );
}
</script>
<style>
<?php echo $oFG->getStyle(); ?>
</style>
</head>
<body>
<h1>Form with WYSIWYG - Htmleditor</h1>
<div style="width:700px;">
<?php echo $oFG->getForm(); ?>
</div>
</body>
</html>
