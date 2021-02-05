/** global: g_oConfigFromPHP */
/** global: FormDataValidator */
/** global: jscolor */
/** global: CKEDITOR */
/** global: FormCKEditor */

var g_oCKEdit = null;
document.addEventListener('DOMContentLoaded', initFormGenerator);

function initFormGenerator()
{
    if (FormDataValidator === undefined) {
        displayJSError('You must include [FormDataValidator.js] for Form Validation!', 'Warning');
    }
    
    if (g_oConfigFromPHP.Color !== undefined) {
        if (typeof jscolor === undefined) {
            displayJSError('You must include [jscolor.js / jscolor.min.js] to use the FormColor input element!', 'Warning');
        }
        jscolor.presets.default = g_oConfigFromPHP.Color;
    }
    
    if (g_oConfigFromPHP.CKEditor !== undefined) {
        if (typeof CKEDITOR === undefined) {
            displayJSError('You must include [ckeditor.js] to use the FormCKEdit input element!', 'Warning');
        }
        if (typeof FormCKEditor === undefined) {
            displayJSError('You must include [FormCKEdit.js] to use the FormCKEdit input element!', 'Warning');
        }
        g_oCKEdit = new FormCKEditor(g_oConfigFromPHP);
        g_oCKEdit.load();
    }
}

function validateForm()
{
    var FDV = new FormDataValidator(g_oConfigFromPHP.FormDataValidation);
    return FDV.validate();
}


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

/** global: RichFmConnector */
function browseServer(editID, imgID, strExpand)
{
    let FmConnector = new RichFmConnector(g_oConfigFromPHP.RichFilemanager.Path);
    FmConnector.editID = editID;
    FmConnector.imgID = imgID;
    FmConnector.browseServerModal(strExpand);
}
        

