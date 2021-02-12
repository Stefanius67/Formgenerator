/** global: dtsel */

/**
 * This class dynamicaly adds a date or time - picker to all configured fields.
 */
class FormPicker
{
	/**
	 * pass the id of the form element to the constructor.
	 * @param string id
	 */
    constructor(config) 
	{
        this.config = config;
        this.aPickers = [];
    }

	/**
	 * Init all date fields.
	 */
    init() 
	{
        var i;
        var aPickerElements = this.getPickerElements();
        var length = aPickerElements.length;
        for (i = 0; i < length; i++) {
			// since getPickerElements() only return existing items with 'data-picker' attribute
			// there's no need to check this values against null
			let item = aPickerElements[i];
            let picker = item.getAttribute('data-picker');
            // we can't use the split function because the param may contain the ':' itself (time-separator...)
			// let [type, param] = validate.split(':');
            let pos = picker.indexOf(':');
            let type = (pos >= 0) ? picker.substring(0, pos) : picker;
            let param = (pos >= 0) ? picker.substring(pos + 1) : '';
            this.createPicker(item, type, param);
        }
    }

	/**
	 * Get all elements inside the form with the attribute 'data-picker' set.
	 * @returns array
	 */
    getPickerElements() 
	{
        let pickerElements = [];
        let form = document.getElementById(this.config.FormDataValidation.formID);
        let formElements = form.getElementsByTagName('*');
        let length = formElements.length;
        for (let i = 0; i < length; i++) {
            if (formElements[i].getAttribute('data-picker') !== null) {
                pickerElements.push(formElements[i]);
            }
        }
        return pickerElements;
    }

	/**
	 * Create the picker and attach to the item.
     * @param element oItem item to create the picker for
     * @param string strType 'date' or 'time'
     * @param string strParam time/date format for the picker to create
	 */
    createPicker(oItem, strType, strParam) 
	{
        let cfg = {};
        if (typeof this.config.DTSel !== 'undefined') {
            Object.assign(cfg, this.config.DTSel); 
        }
        if (strType === 'date') {
            cfg.showDate = true;
            cfg.showTime = false;
            cfg.dateFormat = strParam;
        } else if (strType === 'time'){
            cfg.showDate = false;
            cfg.showTime = true;
            cfg.timeFormat = strParam;
            cfg.showSeconds = (strParam.length > 5 );
        }
        
        this.aPickers.push(new dtsel.DTS(oItem, cfg));
    }
}
