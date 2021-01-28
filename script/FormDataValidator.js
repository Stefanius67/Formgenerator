function FormDataValidator(aMand, aDate, aTime, aInt, aCur)
{
    this.aMand = aMand;
    this.aDate = aDate;
    this.aTime = aTime;
    this.aInt = aInt;
    this.aCur = aCur;
    
    this.focusItem = null;
    this.focusTabIndex = null;
    this.errors = 0;
}
    
FormDataValidator.prototype.validate = function()
{
    var i;
    
    // check mandatory fields
    if (this.aMand !== null) {
        for (i = 0; i < this.aMand.length; i++) {
            this.checkMandatory(document.getElementById(this.aMand[i]));
        }
    }
    // validate date input
    if (this.aDate !== null) {
        for (i = 0; i < this.aDate.length; i++) {
            this.checkDate(document.getElementById(this.aDate[i]));
        }
    }
    // validate time input
    if (this.aTime !== null) {
        for (i = 0; i < this.aTime.length; i++) {
            this.checkTime(document.getElementById(this.aTime[i]));
        }
    }
    // validate integer input
    if (this.aInt !== null) {
        for (i = 0; i < this.aInt.length; i++) {
            this.checkInteger(document.getElementById(this.aInt[i]));
        }
    }
    // validate currency/float input
    if (this.aCur !== null) {
        for (i = 0; i < this.aCur.length; i++) {
            this.checkCurrency(document.getElementById(this.aCur[i]));
        }
    }
    
    // if errors found, dispplay message and set focus to last input
    if (this.errors > 0) {
        var strMsg;
        // TODO: language
        if (this.errors === 1) {
            strMsg = unescape("Das rot gekennzeichnete Feld wurde nicht korrekt ausgef%FCllt\n\n");
        } else {
            strMsg = unescape("Die rot gekennzeichneten Felder wurden nicht korrekt ausgef%FCllt\n\n");
        }
        strMsg += unescape("Korrigieren oder vervollst%E4ndigen Sie bitte Ihre Angaben.");
        alert( strMsg );
        if (this.focusItem !== null) {
            this.focusItem.focus();
        }
        return false;
    }
    return true;
}
    
FormDataValidator.prototype.checkMandatory = function(item)
{
    if (item !== null) {
        this.setError((item.value == '' || item.value == 'null'), item);
    }
}

FormDataValidator.prototype.checkDate = function(item)
{
    if (item !== null && item.value != '') {
        var date = this.isValidDate(item.value);
        if (date !== false) {
            item.value = date;
            this.setError(false, item);
        } else {
            this.setError(true, item);
        }
    }
}

FormDataValidator.prototype.checkTime = function(item)
{
    if (item !== null && item.value != '') {
        var time = this.isValidTime(item.value); 
        if (time !== false) {
            item.value = time;
            this.setError(false, item);
        } else {
            this.setError(true, item);
        }
    }
}

FormDataValidator.prototype.checkInteger = function(item)
{
    if (item !== null) {
        var integer = this.isValidInteger(item.value);
        if (integer !== false) {
            item.value = integer;
            this.setError(false, item);
        } else {
            this.setError(true, item);
        }
    }
}

FormDataValidator.prototype.checkCurrency = function(item)
{
    if (item !== null) {
        var currency = this.isValidCurrency(item.value);
        if (currency !== false) {
            item.value = currency;
            this.setError(false, item);
        } else {
            this.setError(true, item);
        }
    }
}

FormDataValidator.prototype.setError = function(set, item)
{
    if (set) {
        item.className = item.className.replace( /Mand/, 'MError' );
        item.className = item.className.replace( /OK/, 'Error' );
        this.setFocusItem(item);
        this.errors++;
    } else {
        item.className = item.className.replace( /MError/, 'Mand' );
        item.className = item.className.replace( /Error/, 'OK' );
    }
}

FormDataValidator.prototype.setFocusItem = function(item)
{
    if (!this.focusTabIndex || this.focusTabIndex > item.tabIndex) {
        this.focusTabIndex = item.tabIndex;
        this.focusItem = item;
    }
}

FormDataValidator.prototype.isValidDate = function(strDate)
{
    // remove all whitespace
    strDate = strDate.toString().trim();

    // ',' is replaced with '.' (fast input through num-pad...)
    strDate = strDate.replace(/,/g, ".");
    // we only accept sequence D.M.Y
    var aSplit = strDate.split(".");

    // First there must be three parts
    if (aSplit.length != 3) {
        return false;
    }
    
    // Caution: remove leading '0' otherwise parseInt treats values as octal and return '0' in case of '08' and '09' !!!!
    var iD = parseInt(aSplit[0].replace(/^0+/,""));
    var iM = parseInt(aSplit[1].replace(/^0+/,""));
    var iY = parseInt(aSplit[2].replace(/^0+/,""));
    if (isNaN(iD) || isNaN(iM) || isNaN(iY)) {
        return false;
    }
    // values < 25 are 21'st century and 25...99 20'st Century!
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
    iM--;
    // initializte Date-Object
    var date = new Date(iY, iM, iD);
    
    // and compare all parts
    var iD2 = date.getDate();
    var iM2 = date.getMonth();
    var iY2 = date.getFullYear();
    if (iD != iD2 || iM != iM2 || iY != iY2) {
        // alert( 'In: ' + iD + '.' + iM + '.' + iY + '\nOut: ' + iD2 + '.' + iM2 + '.' + iY2 );
        return false;
    }
    var strDate = '';
    if (iD < 10) {
        strDate = '0';
    }
    strDate += iD + '.';
    if (iM < 10) {
        strDate += '0';
    }
    strDate += iM + '.' + iY;
    return strDate;
}

FormDataValidator.prototype.isValidTime = function(strTime)
{
    // remove all whitespace
    strTime = strTime.toString().trim();

    // we only accept ':' as Delimiter and expect sequence H:i[:s]
    var aSplit = strTime.split(":");

    var iH = 0;
    var iM = 0;
    switch (aSplit.length) {
        case 1: // no separator - interpret as minutes
            if (aSplit[0] != '0' && aSplit[0] != '00') {
                iM = parseInt(aSplit[0].replace(/^0+/,""));
            }
            break;
        case 2: // hour:minutes specified
        case 3: // hour:minutes:seconds
            // only handle h:m
            if (aSplit[0] != '0' && aSplit[0] != '00') {
                iH = parseInt(aSplit[0].replace(/^0+/,""));
            }
            if (aSplit[1] != '0' && aSplit[1] != '00') {
                iM = parseInt(aSplit[1].replace(/^0+/,""));
            }
            break;
        default:
            // all other combinations are invalid
            return false;
            break;
    }
    
    if ( isNaN(iM) || isNaN(iH)) {
        return false;
    }

    if (iM > 59) {
        // javascript down know an operator like DIV (integer division)
        iH += (iM - (iM % 60)) / 60;
        iM = iM % 60;
    }
    // ... 23:59 is the end
    if (iH > 23) {
        return false;
    }

    // initializte Date-Object
    var date = new Date(0, 0, 0, iH, iM, iS);
    
    // and compare relevant parts
    var iH2 = date.getHours();
    var iM2 = date.getMinutes();
    if (iH != iH2 || iM != iM2) {
        return false;
    }
    strTime = '';
    if (iH < 10) {
        strTime = '0';
    }
    strTime += iH + ':';
    if (iM < 10) {
        strTime += '0';
    }
    strTime += iM; // + ':00';
    return strTime;
}

FormDataValidator.prototype.isValidInteger = function(strInt)
{
    // remove all whitespace
    strInt = strInt.toString().trim();
    if (isNaN(strInt) || strInt.indexOf('.') !== -1) {
        return false;
    }
    if (strInt == '') {
        strInt = '0';
    }
    return strInt;
}

FormDataValidator.prototype.isValidCurrency = function(strCur)
{
    // remove all whitespace
    strCur = strCur.toString().trim();
    
    // remove thousands separator and replace comma to point
    strCur = strCur.replace( ".", "" );
    strCur = strCur.replace( ",", "." );
    if (isNaN(strCur)) {
        return false;
    }
    if (strCur == '') {
        strCur = '0';
    }
    strCur = Number.parseFloat(strCur).toFixed(2);
    strCur = strCur.replace( ".", "," );
    strCur = strCur.replace(/\B(?=(\d{3})+(?!\d))/g, ".");        
    return strCur;
}
