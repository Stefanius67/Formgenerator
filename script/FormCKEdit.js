function loadEditor()
{
    var oEditor = null;
    
    var oTA = document.getElementById(g_oConfigFromPHP.CKEditor.editorID);
    if (!oTA) {
        displayJSError('Element [' + g_oConfigFromPHP.CKEditor.editorID + 'to be replaced by CKEditor not exists!', 'error');
        return;
    }
    // get initial size of textarea to replace
    var iHeight = oTA.offsetHeight;
    var iWidth = oTA.offsetWidth;
    oEditor = CKEDITOR.replace(g_oConfigFromPHP.CKEditor.editorID, g_oConfigFromPHP.CKEditor.editorOptions);

    // custom buttons
    if (Array.isArray(g_oConfigFromPHP.CKEditor.customButtons)) {
        let length = g_oConfigFromPHP.CKEditor.customButtons.length;
        for (let i = 0; i < length; i++) {
            let btn = g_oConfigFromPHP.CKEditor.customButtons[i];
            
            var exec = window[btn.func];
            if (typeof exec === "function") {
                oEditor.addCommand('cmd_' + btn.func, {exec: function(oEditor) {window[btn.func](oEditor);}});
                oEditor.ui.addButton(btn.func, {label: btn.name, command: 'cmd_' + btn.func, icon: btn.icon});
            } else {
                displayJSError('Handler for Custom Button [' + btn.func + '] is not defined!', 'error');
            }
        }
    }

    // resize to desired size
    CKEDITOR.on('instanceReady', function(event) {event.editor.resize(iWidth, iHeight);});

    if (g_oConfigFromPHP.CKEditor.editorData !== undefined) {
        oEditor.setData(g_oConfigFromPHP.CKEditor.editorData);
    }

    if (g_oConfigFromPHP.RichFilemanager === undefined) {
        return;
    }
    // check filemanager...
    CKEDITOR.on('dialogDefinition', function (event) {
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
                    '&langCode=' + g_oConfigFromPHP.RichFilemanager.language +
                    '&CKEditor=' + event.editor.name;
                    
                if (dialogName == 'link') {
                    params += '&expandedFolder=' + g_oConfigFromPHP.RichFilemanager.expandFolder.browseLinkURL;
                } else if (dialogTab.id == 'info') {
                    params += '&expandedFolder=' + g_oConfigFromPHP.RichFilemanager.expandFolder.browseImageURL;
                } else {
                    params += '&expandedFolder=' + g_oConfigFromPHP.RichFilemanager.expandFolder.browseImageLinkURL;
                }
                browseButton.filebrowser.params = params;
                browseButton.onClick = function (dialog, i) {
                    editor._.filebrowserSe = this;
                    
                    let oIFrame = document.createElement('iframe');
                    oIFrame.id = 'fm-iframeCKE';
                    oIFrame.className = 'fm-modal';
                    oIFrame.src = g_oConfigFromPHP.RichFilemanager.Path + dialog.sender.filebrowser.params;
                    document.body.append(oIFrame);
                    document.body.style.overflowY = 'hidden';
                }
            }
        }
    });
}
