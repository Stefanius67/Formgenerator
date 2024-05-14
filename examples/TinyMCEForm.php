<?php

declare(strict_types=1);

require_once '../autoloader.php';

use SKien\Config\JSONConfig;
use SKien\Formgenerator\ArrayFormData;
use SKien\Formgenerator\FormButtonBox;
use SKien\Formgenerator\FormFlags;
use SKien\Formgenerator\FormGenerator;
use SKien\Formgenerator\FormHeader;
use SKien\Formgenerator\FormTinyMCE;

$strTheme = './MSO-Theme/';
// $strTheme = './';

$oConfig = new JSONConfig($strTheme . 'FormGenerator.json');
$oData = new ArrayFormData([
        'strText' =>
            '<h1>This is the content</h1>' . PHP_EOL .
            '<p>with a  little text...!</p>'
    ]);

$oFG = new FormGenerator($oData);
$oFG->setAction('formaction.php');
$oFG->setTarget('_blank');
$oFG->setConfig($oConfig);

$oFG->add(new FormHeader('TinyMCE connected with elFinder', 1));
$oFG->setColWidth([100], '%');
$oEditor = new FormTinyMCE('strText', 20, '100%');
$oFG->add($oEditor);

$oBtnBox = new FormButtonBox(FormButtonBox::SAVE_CANCEL, FormFlags::ALIGN_RIGHT);
$oFG->add($oBtnBox);
$oBtnBox->addButton('Insert Image', 'btnInsert', FormButtonBox::FIRST);

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
<script src="/packages/Formgenerator/tinyMCE/tinymce.min.js"></script>
<script>
<?php echo $strConfigFromPHP; ?>

// "https://cdn.tiny.cloud/1/owfuaf3m7j6nn00y62svxt1b4qpnm4p9y39u21vhcpsbgu78/tinymce/6/tinymce.min.js"

var aStyleFormats = [
    {
    	title: 'Überschriften', items: [
			{ title: 'Überschrift 1', block: 'h1' },
			{ title: 'Überschrift 2', block: 'h2' },
			{ title: 'Überschrift 3', block: 'h3' },
			{ title: 'Überschrift 4', block: 'h4' },
		]
	},
    {
    	title: 'Blöcke', items: [
			{ title: 'Absatz', block: 'p' },
			{ title: 'Abschnitt', block: 'div' },
			{ title: 'Vorformatiert', block: 'pre' },
			{ title: 'Code', block: 'code' },
    	]
    },
    {
    	title: 'Stilarten', items: [
			{ title: 'Telefon-Nr', inline: 'span', classes: ['phone'] },
			{ title: 'Handy-Nr', inline: 'span', classes: ['mobile'] },
			{ title: 'Roter Text', inline: 'span', styles: { color: '#ff0000' } },
			{ title: 'Tastatur', inline: 'kbd' },
    	]
    },
    {
    	title: 'Links', items: [
			{ title: 'Homepage', inline: 'a', classes: ['homepage'] },
			{ title: 'e-Mail', inline: 'a', classes: ['mailto'] },
			{ title: 'YouTube', inline: 'a', classes: ['youtube'] },
			{ title: 'Karte', inline: 'a', classes: ['map'] },
    	]
    },
    {
    	title: 'Definition List', items: [
			{ title: 'dl', block: 'dl' },
			{ title: 'dt', format: 'dt' },
			{ title: 'dd', format: 'dd' },
    	]
    },
];

var aToolbar = [
    { name: 'removeformat', items: ['removeformat'] },
    { name: 'basicstyles', items: ['bold', 'italic', 'underline', 'superscript', 'subscript'] },
    { name: 'block', items: ['align', 'numlist', 'bullist', 'blockquote'] },
    { name: 'link', items: ['link', 'unlink'] },
    { name: 'image', items: ['image'] },
    { name: 'color', items: ['forecolor', 'backcolor'] },
    { name: 'style', items: ['styles'] },
    { name: 'placeholder', items: ['placeholder'] },
    { name: 'source', items: ['code'] },
];

tinymce.init({
	selector: '#strText',
	language: 'de',
    skin: 'tinymce-5',
	plugins: 'lists advlist image link code placeholder',
	menubar: false,
    statusbar: false,
	toolbar: aToolbar,
    // 'removeformat | bold italic underline superscript subscript | align numlist bullist blockquote | link unlink | image | forecolor backcolor | styles | code',
	toolbar_mode: 'sliding',
	// content_css: '/myLayout.css',
	style_formats: aStyleFormats,
	style_formats_autohide: true,
	file_picker_callback: elFinderTinyMCE,
    placeholders: '/packages/Formgenerator/examples/placeholders.php',
    placeholdersS: ["placeholder1","placeholder2","placeholder3"],
    placeholdersL: [
        {
            "text": "Facebook-Video",
            "value": "fb_video:URL"
        },
        {
            "text": "Altersgruppe",
            "value": "age-group:XX"
        },
        {
            "text": "Saison",
            "value": "season"
        }
    ],    
});

var g_callback = null;
var g_iTimerID = 0;

function elFinderTinyMCE(callback, value, meta)
{
	g_callback = callback;
	// callback ('/packages/Formgenerator/doc/QuickStartMSO.png', { alt: 'Hallo'});
	g_iTimerID = setInterval(btnInsertClicked, 1000);
}

function btnInsertClicked()
{
	if (g_callback !== null) {
    	g_callback('/packages/Formgenerator/doc/QuickStartMSO.png', { alt: 'Hallo'});
    	g_callback = null;
    	clearInterval(g_iTimerID);
	}
}

</script>
</head>
<body>
<div style="width:700px; margin: 0px auto; background-color: transparent;">
<?php echo $strFormHTML; ?>
</div>
<pre>
</pre>
</body>
</html>
