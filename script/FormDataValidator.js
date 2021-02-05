/**
 * This class can validate the marked input fields of the form with given id.
 * Each field that is marked with the custom 'data-validation' attribute is 
 * validated according to the information this attribute contains.
 */
class FormDataValidator 
{
	/**
	 * pass the id of the form element to the constructor.
	 * @param string id
	 */
    constructor(config) 
	{
        this.config = config;
        this.focusItem = null;
        this.focusTabIndex = null;
        this.errors = 0;
    }

	/**
	 * Perform the validation.
	 * @returns bool true if all elements contains valid data, false on error
	 */
    validate() 
	{
        var i;
        var aValidationElements = this.getValidationElements();
        var length = aValidationElements.length;
        for (i = 0; i < length; i++) {
			// since getValidationElements() only return existing items with 'data-validation' attribute
			// there's no need to check this values against null
			let item = aValidationElements[i];
            let validate = item.getAttribute('data-validation');
            // we can't use the split function because the param may contain the ':' itself (time-separator...)
			// let [type, param] = validate.split(':');
            let pos = validate.indexOf(':');
            let type = (pos >= 0) ? validate.substring(0, pos) : validate;
            let param = (pos >= 0) ? validate.substring(pos + 1) : '';
			let valid = false;
            switch (type) {
                case 'integer':
		            valid = this.isValidInteger(item.value, param);
                    break;
                case 'float':
		            valid = this.isValidFloat(item.value, param);
                    break;
                case 'date':
		            valid = this.isValidDate(item.value, param);
                    break;
                case 'time':
		            valid = this.isValidTime(item.value, param);
                    break;
                default:
                    break;
            }
            if (valid !== false) {
                item.value = valid;
                this.setError(false, item);
            } else {
                this.setError(true, item);
            }
        }
        // if errors found, dispplay message and set focus to last input
        if (this.errors > 0) {
            var strMsg;
            if (this.config.errorMsg !== undefined) {
                strMsg = this.config.errorMsg;
            } else {
                strMsg = "The form contains invalid information that is marked in red!<br/><br/>Please correct or complete your entries. ";
            }

            var popup = document.getElementById('errorPopup');
            if (popup) {
                popup.innerHTML = strMsg;
                popup.style.display = 'block';
            }
            if (this.focusItem !== null) {
                this.focusItem.focus();
            }
            return false;
        }
        return true;
    }

	/**
	 * Get all elements inside the form with the attribute 'data-validation' set.
	 * @returns array
	 */
    getValidationElements() 
	{
        let validationElements = [];
        let form = document.getElementById(this.config.formID);
        let formElements = form.getElementsByTagName('*');
        let length = formElements.length;
        for (let i = 0; i < length; i++) {
            if (formElements[i].getAttribute('data-validation') !== null) {
                validationElements.push(formElements[i]);
            }
        }
        return validationElements;
    }

	/**
	 * Check, if input is a valid date.
	 * strParam[0]: separator
	 * strParam[1..]: YMD, DMY, MDY for the order of year, month and day 
     * @param string strDate value to checked
     * @param string
     * @returns false|string false if invalid, otherwise formated value
	 */
    isValidDate(strDate, strParam) 
	{
		let strSep = strParam.charAt(0);
		let strYMD = strParam.substring(1);
		
        // remove all whitespace
        strDate = strDate.toString().trim();
		if (strDate == '') {
			return '';
		}

		let iY = 0, iM = 0, iD = 0;
		switch (strYMD) {
			case 'YMD':
		        [iY, iM, iD] = strDate.split(strSep);
				break;
			case 'DMY':
		        [iD, iM, iY] = strDate.split(strSep);
				break;
			case 'MDY':
		        [iM, iD, iY] = strDate.split(strSep);
				break;
			default:
				// console.log('invalid format specification for date validation [' + strParam + ']!');
				return false;
		}
        if (isNaN(iY) || isNaN(iM) || isNaN(iD)) {
            return false;
        }
        // values < 33 are 21'st century and 33...99 20'st Century!
        if (iY < 100) {
            if (iY < 33) {
                iY += 2000;
            } else {
                iY += 1900;
            }
        }
        if (iY < 1900) {
            return false;
        }
        // simply initialize a new Date-Object and compare all parts... (Note: JS Date works with month 0...11 !!)
        let date = new Date(iY, --iM, iD);
        if (iD != date.getDate() || iM++ != date.getMonth() || iY != date.getFullYear()) {
            return false;
        }
		// all fine - let's format pretty...
		switch (strYMD) {
			case 'YMD':
				strDate = iY + strSep + ("00" + iM).slice(-2) + strSep + ("00" + iD).slice(-2); 
				break;
			case 'DMY':
				strDate = ("00" + iD).slice(-2) + strSep + ("00" + iM).slice(-2) + strSep + iY; 
				break;
			case 'MDY':
				strDate = ("00" + iM).slice(-2) + strSep + ("00" + iD).slice(-2) + strSep + iY; 
				break;
		}
        return strDate;
    }

	/**
	 * Check, if input is a valid time
	 * strParam[0]: separator
	 * strParam[1]: 1 if with seconds, 0 without 
	 * strParam[2]: m if allowed to input minutes only, not allowed all other value
     * @param string strTime value to checked
     * @param string
     * @returns false|string false if invalid, otherwise formated value
	 */
    isValidTime(strTime, strParam)
	{
		if (strParam.length != 3) {
			// console.log('invalid format specification for time validation [' + strParam + ']!');
			return false;
		}
		let strSep = strParam.charAt(0);
		let strSec = strParam.charAt(1);
		let strMO = strParam.charAt(2);
        
        // remove all whitespace
        strTime = strTime.toString().trim();
		if (strTime == '') {
			return '';
		}

		let [iH, iM, iS] = strTime.split(strSep);
        if (iM === undefined) {
            if (strMO != 'm') {
                return false;
            }
            // if only a number is typed in, we interpret it as minutes...
            iM = iH;
            iH = 0;
        }
        iS = (iS === undefined ? 0 : iS);
        if (isNaN(iM) || isNaN(iH) || isNaN(iS)) {
            return false;
        }
        
        if (iM > 59) {
            iH += (iM - (iM % 60)) / 60;
            iM = iM % 60;
        }
        // ... 23:59 is the end
        if (iH > 23) {
            return false;
        }

        // simply initialize a new Date-Object and compare all parts...
        let date = new Date(0, 0, 0, iH, iM, iS);
        if (iH != date.getHours() || iM != date.getMinutes() || iS != date.getSeconds()) {
            return false;
        }
		// all fine - let's format pretty...
        strTime = ("00" + iH).slice(-2) + strSep + ("00" + iM).slice(-2);
        if (strSec != '0') {
            strTime += strSep + ("00" + iS).slice(-2); 
        }
        return strTime;
    }

	/**
     * Check for valid integer.
	 * strParam[0]: 'e' if empty value allowed, all other empty value is set to '0'
     * @param string strInt value to checked
     * @param string
     * @returns false|string false if invalid, otherwise formated value
	 */
    isValidInteger(strInt, strParam) 
	{
        // remove all whitespace
        strInt = strInt.toString().trim();
        if (isNaN(strInt) || strInt.indexOf('.') !== -1) {
            return false;
        }
        if (strInt == '' && strParam != 'e') {
            strInt = '0';
        }
        return strInt;
    }

	/**
	 * Check, if input is a valid float.
	 * strParam[1]: 'e' if empty value allowed, all other empty value is set to '0'
	 * strParam[2]: decimal digits 
	 * strParam[3]: decimal point
	 * strParam[4]: thousands separator
     * @param string strCur value to checked
     * @param string
     * @returns false|string false if invalid, otherwise formated value
	 */
    isValidFloat(strCur, strParam) 
	{
        let iLength = strParam.length
		if (iLength != 3 && iLength != 4) {
			// console.log('invalid format specification for float validation [' + strParam + ']!');
			return false;
		}
		let strEmpty = strParam.charAt(0);
		let strDD = strParam.charAt(1);
		let strDP = strParam.charAt(2);
		let strTS = (iLength == 3) ? '' : strParam.charAt(3);
		
        // remove all whitespace
        strCur = strCur.toString().trim();

        if (strCur == '') {
            // empty vlues are allowed
            if (strEmpty == 'e') {
                return '';
            }
            strCur = '0';
        } else {
	        // remove thousands separator and replace decimal point with '.'
	        strCur = strCur.replace(strTS, "");
	        strCur = strCur.replace(strDP, ".");
		}
        if (isNaN(strCur)) {
            return false;
        }
        strCur = Number.parseFloat(strCur).toFixed(strDD);
        strCur = strCur.replace(".", strDP);
        strCur = strCur.replace(/\B(?=(\d{3})+(?!\d))/g, strTS);
        return strCur;
    }

	/**
     * Mark/unmark element as error.
     * @param bool set true marks error
     * @param element item to mark
	 */
    setError(set, item) 
	{
        if (set) {
            item.className = item.className.replace(/Mand/, 'MError');
            item.className = item.className.replace(/OK/, 'Error');
            this.setFocusItem(item);
            this.errors++;
        } else {
            item.className = item.className.replace(/MError/, 'Mand');
            item.className = item.className.replace(/Error/, 'OK');
        }
    }

    /**
     * Save invalid item with lowest tabindex to set focus after error message.
     */
    setFocusItem(item) {
        if (!this.focusTabIndex || this.focusTabIndex > item.tabIndex) {
            this.focusTabIndex = item.tabIndex;
            this.focusItem = item;
        }
    }
}
