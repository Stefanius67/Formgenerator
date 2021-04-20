/** global: g_oConfigFromPHP */
/** global: FormDataValidator */
/** global: jscolor */
/** global: CKEDITOR */
/** global: FormCKEditor */
/** global: RichFmConnector */
/** global: FormPicker */

var g_oCKEdit = null;
var g_FDP = null;

/**
 * post load some additional JS scripts after DOM is loaded...
 */
document.addEventListener('DOMContentLoaded', loadScriptfiles);

function loadScriptfiles()
{
    loadScript(g_oConfigFromPHP.JavaScript.Path + 'FormDataValidator.js');
    if (g_oConfigFromPHP.Color !== undefined) {
        loadScript(g_oConfigFromPHP.JavaScript.Path + 'jscolor.js');
    }
    
    if (g_oConfigFromPHP.DTSel !== undefined) {
        loadScript(g_oConfigFromPHP.JavaScript.Path + 'dtsel.js');
        loadScript(g_oConfigFromPHP.JavaScript.Path + 'FormPicker.js');
    }

    if (g_oConfigFromPHP.RichFilemanager !== undefined) {
        loadScript(g_oConfigFromPHP.JavaScript.Path + 'RichFmConnector.js');
    }
    
    if (g_oConfigFromPHP.CKEditor !== undefined) {
        loadScript(g_oConfigFromPHP.CKEditor.Path + 'ckeditor.js');
        loadScript(g_oConfigFromPHP.JavaScript.Path + 'FormCKEdit.js');
    }
}

/**
 * Initialization.
 * For initialization we lisen to the window onload-event. At this point the 
 * additionally loaded script files from the DOMContentLoaded-event should also be 
 * available. 
 * Dependent on the config from PHP we have to initialize some modules:
 * - FormDataValidation needs no initialization
 * - if form contains colorpicker(s) we have to initialize them
 * - contained date or time pickers also needs initialization
 * - embedded CKEditor have to be loaded and configured
 * - add Eventlistener to display value of range elements in assigned value label
 */
window.addEventListener('load', initFormGenerator);

function initFormGenerator()
{
    if (g_oConfigFromPHP.Color !== undefined) {
        jscolor.presets.default = g_oConfigFromPHP.Color;
        jscolor.init();
    }
    
    if (g_oConfigFromPHP.DTSel !== undefined) {
    	g_FDP = new FormPicker(g_oConfigFromPHP);
        g_FDP.init();
    }
    
    if (g_oConfigFromPHP.CKEditor !== undefined) {
        g_oCKEdit = new FormCKEditor(g_oConfigFromPHP, CKEDITOR);
        g_oCKEdit.load();
    }
    
	var oRanges = document.querySelector("input[type=range]");
	
	oRanges.addEventListener('change', rangeChanged);
	oRanges.addEventListener('input', rangeChanged);
}

/**
 * Event - handler for the 'change' and 'input' event trigerred by each range element in the form.
 * If there exist a label element that is assigned to the triggering range through the 'for' attrib,
 * the value of the range element is set as content of the label.
 */
function rangeChanged()
{
	var oRange = this;
	var oLabels = document.getElementsByTagName('label');
	for (var i = 0; i < oLabels.length; i++) {
	    if (oLabels[i].htmlFor == oRange.id) {
	    	oLabels[i].innerHTML = this.value;
	    }
	}	
}

/**
 * Helper function to create Error/Warning-Message in the document.
 * The Messeage is only created, if the debug mode is enabled.
 * @param string msg text to display
 * @param string level level for the message
 */
function displayJSError(msg, level)
{
    if (g_oConfigFromPHP.DebugMode) {
        let div = document.createElement('div');
        div.id = 'JSError';
        let header = document.createElement('h1');
        div.appendChild(header);
        let body = document.createElement('p');
        div.appendChild(body);
        header.innerHTML = 'Javascript ' + level;
        body.innerHTML = msg;
        document.body.insertBefore(div, document.body.firstChild);
    }
}

/**
 * Validate the form.
 */
function validateForm()
{
    var FDV = new FormDataValidator(g_oConfigFromPHP.FormDataValidation);
    return FDV.validate();
}

/**
 * Call the filemanager to browse for a file on the server.
 */
function browseServer(editID, imgID, strExpand)
{
    let FmConnector = new RichFmConnector(g_oConfigFromPHP.RichFilemanager.Path);
    FmConnector.editID = editID;
    FmConnector.imgID = imgID;
    FmConnector.browseServerModal(strExpand);
}

/**
 * Handler for the DTU button (Date,Time User)
 */
function onInsertDateTimeUser(id, strUsername)
{
    let oEdit = document.getElementById(id);
    if (oEdit) {
        let date = new Date();
        let strValue = oEdit.innerHTML = date.toLocaleString();
        if (strUsername != '') {
            strValue += ' / ' + strUsername;
        }
        oEdit.value = strValue;
    }
}

/**
 * Handler for the reset - Button.
 * Used to to reset the content of readonly-inputs or images that get the value
 * from filebrowser or another picker etc.
 */
function resetElement(id)
{
    let oElement = document.getElementById(id);
    if (oElement) {
        if (oElement.tagName.toLowerCase() === 'img') {
            oElement.src = oElement.getAttribute('data-default');
            resetElement(oElement.getAttribute('data-bound-to'));
        } else {
            oElement.value = '';
        }
    }
}

/**
 * Adjust the height of two columns.
 */
function adjustColumnHeight(col1, col2)
{
	var oCol1 = document.getElementById(col1);
	var oCol2 = document.getElementById(col2);
	if (oCol1 && oCol2) {
		if (oCol1.offsetHeight > oCol2.offsetHeight) {
			oCol2.style.height = oCol1.offsetHeight + 'px'; 
		} else {
			oCol1.style.height = oCol2.offsetHeight + 'px'; 
		}
	}
}

/**
 * Dynamic loading of additional scripts.
 */
function loadScript(strScriptfile)
{
    var oScript = document.createElement('script');
    document.head.appendChild(oScript);
    // oScript.onload = function() {console.log('script ' + strScriptfile + ' loaded!');};
    oScript.type = 'text/javascript';
    oScript.src = strScriptfile;
}

