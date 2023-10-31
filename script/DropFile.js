

class DropFile
{
    /**
     * Constructor.
     */
    constructor() 
    {
        this.oDropFile = null;
        this.oFileInput = null;
        this.oFileSelect = null;
        this.oSelectedFiles = null;
        
        this.dataTransfer = new DataTransfer();
    }
    
    init(strName)
    {
        this.oDropFile = document.querySelector('.dropfile');
        this.oFileInput = document.querySelector('[name="files[]"');
        this.oFileSelect = document.querySelector('.fileselect');
        this.oSelectedFiles = document.querySelector('.selectedfiles');

        ['drag', 'dragstart', 'dragend', 'dragover', 'dragenter', 'dragleave', 'drop'].forEach( event => this.oDropFile.addEventListener(event, function(e) {
            e.preventDefault();
            e.stopPropagation();
        }), false );
        
        ['dragover', 'dragenter'].forEach( event => this.oDropFile.addEventListener(event, function(e) {
            g_oDropFile.oDropFile.classList.add('dragover');
        }), false );
        
        ['dragleave', 'dragend', 'drop'].forEach( event => this.oDropFile.addEventListener(event, function(e) {
            g_oDropFile.oDropFile.classList.remove('dragover');
        }), false );
        
        this.oDropFile.addEventListener('drop', function(e) {
            for (var i = 0; i < e.dataTransfer.files.length; i++) {
                // TODO: check, if allowed
                g_oDropFile.dataTransfer.items.add(e.dataTransfer.files[i]);
            }
            updateFileList();
        }, false );
        
        this.oFileInput.addEventListener('change', function(e) {
            for (var i = 0; i < g_oDropFile.oFileInput.files.length; i++) {
                // TODO: check, if allowed
                g_oDropFile.dataTransfer.items.add(g_oDropFile.oFileInput.files[i]);
            }
            updateFileList();
        }, false );
    }
    
    setSelectedFiles()
    {
        this.oFileInput.files = g_oDropFile.dataTransfer.files;
        
        return true;
    }
}

function updateFileList() 
{
    /*
    const filesArray = Array.from(g_oDropFile.dataTransfer.files);
    if (filesArray.length > 1) {
        g_oDropFile.oSelectedFiles.innerHTML = '<p>Selected files:</p><ul><li>' + filesArray.map(f => f.name).join('</li><li>') + '</li></ul>';
    } else if (filesArray.length == 1) {
        g_oDropFile.oSelectedFiles.innerHTML = `<p>Selected file: ${filesArray[0].name}</p>`;
    } else {
        g_oDropFile.oSelectedFiles.innerHTML = '';
    }
    */
    if (g_oDropFile.dataTransfer.files.length === 0) {
        g_oDropFile.oSelectedFiles.innerHTML = '';
        return;
    }
    const aCSSClasses = {
        'image/jpg'                                                                 : 'imgfile',
        'image/jpeg'                                                                : 'imgfile',
        'image/png'                                                                 : 'imgfile',
        'image/gif'                                                                 : 'imgfile',
        'image/bmp'                                                                 : 'imgfile',
        'image/svg+xml'                                                             : 'imgfile',
        'application/pdf'                                                           : 'pdffile',
        'application/vnd.ms-excel'                                                  : 'xlsfile',
        'application/msexcel'                                                       : 'xlsfile',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         : 'xlsfile',
        'text/csv'                                                                  : 'xlsfile',
        'application/vnd.ms-word'                                                   : 'docfile',
        'application/msword'                                                        : 'docfile',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   : 'docfile',
        'text/x-vcard'                                                              : 'vcard',
        'text/calendar'                                                             : 'ical',
        'application/zip'                                                           : 'archive',
        'application/x-zip-compressed'                                              : 'archive',
        'application/vnd.rar'                                                       : 'archive',
        'application/x-rar-compressed'                                              : 'archive',
        'application/x-tar'                                                         : 'archive',
        'application/x-tar-compressed'                                              : 'archive',
        'application/x-7z-compressed'                                               : 'archive',
        'audio/mpeg'                                                                : 'audiofile', 
        'audio/mp3'                                                                 : 'audiofile', 
        'video/mpeg'                                                                : 'videofile', 
        'video/mp4'                                                                 : 'videofile',
        'video/x-msvideo'                                                           : 'videofile', 
    };
    var strSelectedFiles = '';
    for (var i = 0; i < g_oDropFile.dataTransfer.files.length; i++) {
        var strCSSClass = 'file';
        if (aCSSClasses[g_oDropFile.dataTransfer.files[i].type]) {
            strCSSClass += ' ' + aCSSClasses[g_oDropFile.dataTransfer.files[i].type];
        }
        const EOL = "\n";
        const strName = g_oDropFile.dataTransfer.files[i].name;
        const strOnClick = "deleteSelectedFile(" + i + ", '" + strName + "');";
        const strDeleteFromList = 'Eintrag aus der Liste entfernen';
        
        strSelectedFiles += '<div class="dropfileitem">' + EOL;
        strSelectedFiles += '    <div class="dropedfile">' + EOL;
        strSelectedFiles += '        <div class="dropedname">' + EOL;
        strSelectedFiles += '            <span class="' + strCSSClass + '" title="' + strName + '">' + strName + '</span>' + EOL;
        strSelectedFiles += '        </div>' + EOL;
        strSelectedFiles += '        <div class="dropeddelete" onclick="' + strOnClick + '" title="' + strDeleteFromList + '"></div>' + EOL;
        strSelectedFiles += '    </div>' + EOL;
        strSelectedFiles += '</div>' + EOL;
    }
    g_oDropFile.oSelectedFiles.innerHTML = strSelectedFiles;
}

function deleteSelectedFile(iIndex, strFilename)
{
    if (confirm('Soll die Datei [' + strFilename + '] aus der Liste entfernt werden?') == true) {
        g_oDropFile.dataTransfer.items.remove(iIndex);
        updateFileList();
    }
}

const g_oDropFile = new DropFile();

