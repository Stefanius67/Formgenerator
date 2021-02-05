/** global: displayJSError */
/** global: CKEDITOR */

/**
 * Class to load, configure the CKEditor and connect to Rich Filemanager
 * if available.
 */
class FormCKEditor
{
    /**
     * Constructor get the configuration passed from PHP.
     */
    constructor(config)
    {
        this.config = config;
        this.oEditor = null;
    }
    
    /**
     * Load the CKEditor:
     * - replace the textarea and pass option from PHP configuration
     * - create custom buttons if specified
     * - set if data (content) if comes through the PHP options
     * - connect to Rich Filemanager if configured
     */
    load()
    {
        this.replaceTextArea();
        this.createCustomButtons();
        if (this.config.CKEditor.editorData !== undefined) {
            this.oEditor.setData(this.config.CKEditor.editorData);
        }
        if (this.config.RichFilemanager !== undefined) {
            CKEDITOR.on('dialogDefinition', (event) => {this.connectRichFilemanager(event);});
        }
    }
    
    /**
     * Replace the textarea and pass option from PHP configuration.
     * Dimensions of the textarea are saved and set in the 'instanceReady' event to the created editor
     */
    replaceTextArea()
    {
        var oTA = document.getElementById(this.config.CKEditor.editorID);
        if (!oTA) {
            if (typeof displayJSError === "function") {
                displayJSError('Element [' + this.config.CKEditor.editorID + 'to be replaced by CKEditor not exists!', 'error');
            }
            return;
        }
        // get initial size of textarea to replace
        var iHeight = oTA.offsetHeight;
        var iWidth = oTA.offsetWidth;
        this.oEditor = CKEDITOR.replace(this.config.CKEditor.editorID, this.config.CKEditor.editorOptions);
        // resize to desired size
        this.oEditor.on('instanceReady', function(event) {event.editor.resize(iWidth, iHeight);});
    }

    /**
     * create custom buttons.
     */
    createCustomButtons()
    {
        if (Array.isArray(this.config.CKEditor.customButtons)) {
            let length = this.config.CKEditor.customButtons.length;
            for (let i = 0; i < length; i++) {
                this.addCustomButton(this.config.CKEditor.customButtons[i]);
            }
        }
    }
    
    /**
     * add the custom button.
     * Button and command are only added, if the specified handler is defined.
     */
    addCustomButton(btn)
    {
        var exec = window[btn.func];
        if (typeof exec === "function") {
            this.oEditor.addCommand('cmd_' + btn.func, {exec: function(oEditor) {window[btn.func](oEditor);}});
            this.oEditor.ui.addButton(btn.func, {label: btn.name, command: 'cmd_' + btn.func, icon: btn.icon});
        } else if (typeof displayJSError === "function") {
            displayJSError('Handler for Custom Button [' + btn.func + '] is not defined!', 'error');
        }
    }

    /**
     * Connect to Rich Filemanager.
     * The code from the Rich Filemanager example is modified
     * - jQuery code is replaced by plain javascript
     * - dependen on the dialog/page (link, image, image-link) different folder to
     *   expand are set.
     */
    connectRichFilemanager(event)
    {
        var editor = event.editor;
        var dialogDefinition = event.data.definition;
        var dialogName = event.data.name;
        var cleanUpFuncRef = CKEDITOR.tools.addFunction(function () {
            let oIFrame = document.getElementById('fm-iframeCKE');
            if (oIFrame) {
                oIFrame.remove();
            }
            document.body.style.overflowY = 'scroll';
        });
            
        var tabCount = dialogDefinition.contents.length;
        for (var i = 0; i < tabCount; i++) {
            var dialogTab = dialogDefinition.contents[i];
            if (!(dialogTab && typeof dialogTab.get === 'function')) {
                continue;
            }
                
            var browseButton = dialogTab.get('browse');
            if (browseButton !== null) {
                browseButton.hidden = false;
                var params = 
                    '?CKEditorFuncNum=' + CKEDITOR.instances[event.editor.name]._.filebrowserFn +
                    '&CKEditorCleanUpFuncNum=' + cleanUpFuncRef +
                    '&langCode=' + this.config.RichFilemanager.language +
                    '&CKEditor=' + event.editor.name;
                    
                if (dialogName == 'link') {
                    params += '&expandedFolder=' + this.config.RichFilemanager.expandFolder.browseLinkURL;
                } else if (dialogTab.id == 'info') {
                    params += '&expandedFolder=' + this.config.RichFilemanager.expandFolder.browseImageURL;
                } else {
                    params += '&expandedFolder=' + this.config.RichFilemanager.expandFolder.browseImageLinkURL;
                }
                browseButton.filebrowser.rfm_path = this.config.RichFilemanager.Path + params;
                browseButton.onClick = function (dialog) {
                    editor._.filebrowserSe = this;
                    
                    let oIFrame = document.createElement('iframe');
                    oIFrame.id = 'fm-iframeCKE';
                    oIFrame.className = 'fm-modal';
                    oIFrame.src = dialog.sender.filebrowser.rfm_path;
                    document.body.append(oIFrame);
                    document.body.style.overflowY = 'hidden';
                }
            }
        }
    }
}
