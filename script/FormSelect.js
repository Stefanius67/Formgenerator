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

function onDatePicker(id)
{
}

function onTimePicker(id)
{
}

function resetInput(id)
{
    let oEdit = document.getElementById(id);
    if (oEdit) {
        oEdit.value = '';
    }
}
