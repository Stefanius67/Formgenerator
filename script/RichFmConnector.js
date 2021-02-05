/**
 * Class to call the rich filemanager from a page.
 * Filemanager can be created as independent window or in 'modal' mode
 * running in a dynamicaly created iframe.
 * The path of the selected image is set as value of an edit field or
 * as src of an image element.
 * If specified, height and/or widthof the selected image file is set as
 * value to the corresponding edit field.
 */
class RichFmConnector
{
    /**
     * Constructor need the path to the rich filemanager
     */
    constructor(strRfmPath)
    {
        this.strRfmPath = strRfmPath;
        this.editID = null;
        this.imgID = null;
        this.imgWidthID = null;
        this.imgHeightID = null;
        this.oBrowserWnd = null;
        this.oBrowserDiv = null;
    }

    /**
     * Filebrowser is created inside a independent window.
     */
    browseWindow(editID, imgID, strExpand)
    {
        this.editID = editID;
        this.imgID = imgID;
        
        let strBrowser = this.strRfmPath;
        if (strExpand != '') {
            strBrowser += "?expandedFolder=" + strExpand;
        }

        /** global: jscolor */
        let iWidth = screen.width * 0.7;
        let iHeight = screen.height * 0.7;
        let iLeft = (screen.width - iWidth) / 2 ;
        let iTop = (screen.height - iHeight) / 2 ;

        let strOptions = "toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,dependent=yes";
        strOptions += ",width=" + iWidth ;
        strOptions += ",height=" + iHeight ;
        strOptions += ",left=" + iLeft ;
        strOptions += ",top=" + iTop ;
        
        this.oBrowserWnd = window.open(strBrowser, 'BrowseWindow', strOptions);
        
        window.addEventListener('message', (e) => {this.handlePostMessage(e.data);});
    }
    
    browseServerModal(strExpand)
    {
        let strBrowser = this.strRfmPath;
        if (strExpand != '') {
            strBrowser += "?expandedFolder=" + strExpand;
        }
        
        this.oBrowserDiv = document.createElement('div');
        this.oBrowserDiv.id = 'fm-container';
        let oHeader = document.createElement('h1');
        oHeader.append(document.createTextNode('Rich Filemanager'));
        oHeader.onclick = () => { this.handleOnClose(); };
        this.oBrowserDiv.append(oHeader);
        let oBrowserIFrame = document.createElement('iframe');
        oBrowserIFrame.id = 'fm-iframe';
        oBrowserIFrame.src = strBrowser;
        this.oBrowserDiv.append(oBrowserIFrame);
        document.body.append(this.oBrowserDiv);
        // $('body').css('overflow-y', 'hidden');
        
        window.onmessage = (e) => {this.handlePostMessage(e.data);};
    }

    handlePostMessage(data)
    {
        if (data.source === 'richfilemanager') {
            let oElement = this.getElement(this.editID);
            if (oElement) {
                oElement.value = data.preview_url;
            }
            oElement = this.getElement(this.imgID);
            if (oElement) {
                oElement.src = data.preview_url;
            }
            oElement = this.getElement(this.imgWidthID);
            if (oElement) {
                oElement.value = data.ressourceObject.attributes.width;
            }
            oElement = this.getElement(this.imgHeightID);
            if (oElement) {
                oElement.value = data.ressourceObject.attributes.height;
            }
            
            if (this.oBrowserWnd !== null) {
                this.oBrowserWnd.close();
                this.oBrowserWnd = null;
            } else if (this.oBrowserDiv !== null) {
                this.oBrowserDiv.remove();
                this.oBrowserDiv = null;
            }
        }
    }
    
    handleOnClose()
    {
        if (this.oBrowserDiv !== null) {
            this.oBrowserDiv.remove();
            this.oBrowserDiv = null;
        }
    }
    
    getElement(id)
    {
        var oElement = null;
        if (id !== null && id != '') {
            oElement = document.getElementById(id);
        }
        return oElement;
    }
}
