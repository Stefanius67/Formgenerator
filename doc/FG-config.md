# Configuration of the Formgenerator package

Several functions of the package can be configured with an external config file. 
Following features can be configured:

- [The location of the needed Javascript files.](#location-of-the-needed-javascript-files)
- [All text parts that are used, can be translated to a chosen language:](#translation-of-the-internal-used-text-parts)
  - Form messages.
  - Text of all Default Buttons (Save, Cancel, ...).
  - Weekday- and Monthnames for the date picker.
  - Tooltips of the internal used images.
- [File and/or path of images needed for some standard purposes.](#images-needed-for-some-standard-purposes)
- [The format (order, separator, placeholder) can be adapted to the prefered locale:](#localization-of-the-date--time--and-numeric-input-fields)
  - Date.
  - Time.
  - Integer, Floating-point and Currency.
- [The color picker:](#settings-for-the-color-picker)
  - appearance.
  - Colors that can be selected from a palette.
- [Some settings for an embedded CKEditor.](#settings-for-an-embedded-ckeditor)
- [Some settings for a connected RichFilemanager.](#settings-for-a-connected-richfilemanager)

**See the example config files** ***FormGenerator.json*** **in the examples directory**
**and in the examples/MSO-Theme directory.**

> Best practice will be to use one of this example files and adapt it to your own
requirements.

**all values are case sensitive.**

## Location of the needed Javascript files
Some Javascript files are needed from the Formgenerator to work properly.
You can find all needed Files in the directory ***script***.

In the PHP script for your form you only have to include the *FormGenerator.js* file
in the header section:
```html
<head>
    ...
    <script type="text/javascript" src="YOUR PATH TO THE SCRIPT FILES/FormGenerator.js"></script>
</head>
```
All other JS files MUST be located in the same directory and will be **autoloaded** 
depending if they are needed.

This path must also be specified in the configuration so that the additionally required 
scripts can be loaded.
```php
    JavaScript
        Path : path (YOUR PATH TO THE SCRIPT FILES), NO default value
```
The path must either be specified
- relative to the location of the PHP file containing thze form definition
- absolute from the server document root (**recommended**)
- as complete URL

<table>
    <tbody>
        <tr><td>dtsel.js</td><td>Date- and time picker.</td></tr>
        <tr><td>dtsel-LICENSE</td><td>license fiel for the date- and time picker.</td></tr>
        <tr><td>FormCKEdit.js</td><td>script to crreate an embedded WYSIWIG editor control.</td></tr>
        <tr><td>FormDataValidator.js</td><td>Form validation (date, time, float, integer).</td></tr>
        <tr><td>FormGenerator.js</td><td>Some general Form Functionality and loading of all other JS-Files.</td></tr>
        <tr><td>FormPicker.js</td><td>Script to autocreate the pickers.</td></tr>
        <tr><td>jscolor.js</td><td>the color picker.</td></tr>
        <tr><td>jscolor.min.js</td><td>minimized version of the color picker.</td></tr>
        <tr><td>RichFmConnector.js</td><td>Connector to use the RichFilemanger.</td></tr>
    </tbody>
</table>

## Translation of the internal used text parts

**The default language is english.**

### Message that is displayed to the user, if any validation of the form failed:
```php
    FormDataValidation:
        errorMsg     :  string, NO default value
```
There is no default text for this value!

### Default Buttons
The Buttontext for all standard buttons inside a ButtonBox
can be changed in this section.
```php
	ButtonBox:
		ButtonText:
			OK       :  string, default "OK"
			OPEN     :  string, default "Open"
			SAVE     :  string, default "Save"
			YES      :  string, default "Yes"
			NO       :  string, default "No"
			CANCEL   :  string, default "Cancel"
			CLOSE    :  string, default "Close"
			DISCARD  :  string, default "Discard"
			APPLY    :  string, default "Apply"
			RESET    :  string, default "Reset"
			RETRY    :  string, default "Retry"
			IGNORE   :  string, default "Ignore"
			BACK     :  string, default "Back"
```

### Text for the date- and time picker
To give the users the posibility to select date and/or time values in a picker, the package has
a modified version of the datetime selector from https://github.com/crossxcell99/dtsel included.
Modifications from the origin source are only to make the picker localizable.

The names can be localized in the ```DTSel``` section:
```php
    DTSel:
        direction       : "TOP" or "BOTTOM", default value "TOP"
        months          : array of 12 strings, 
                          default ["January", "February", "March", "April", "May", "June", 
                                   "July", "August", "September", "October", "November", "December"],
        monthsShort     : array of 12 strings, 
                          default ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                                   "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        weekdays        : array of 7 strings (week starts with Sunday!), 
                          default ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
        weekdaysShort   : array of 7 strings (week starts with Sunday!), 
                          default ["So", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
        timeDescr       : array of 3 strings (short for 'Hour:', 'Minute', 'Second'),
                          default ["HH:", "MM:", "SS:"]
```

The appearance of the pickers can be changed in the ***FormGenerator.css*** stylesheet. (TODO: link)

### Images needed for some standard purposes
Some graphics are defined for various common standard purposes, which can be changed in 
this section and labeled in a language-specific manner.

Defined standard images:
<table>
    <tbody>
        <tr><td>Delete</td><td>Image that can be used for deletion of the content of a read only input field or to reset any other content or setting.</td></tr>
        <tr><td>Search</td><td>Default image used for input field with select button specified.</td></tr>
        <tr><td>Browse</td><td>Default image used for input field connected to file browser.</td></tr>
        <tr><td>DatePicker</td><td>Image showing that the field has a date picker </td></tr>
        <tr><td>TimePicker</td><td>Image showing that the field has a time picker.</td></tr>
        <tr><td>InsertDTU</td><td>Image showing the 'insert Date, Time and User' - functionality.</td></tr>
    </tbody>
</table>

```php
    Images:
        Path           : path (YOUR PATH TO THE IMAGE FILES), NO default value
        StdImages:
            Delete:
                Image  : filename, default "delete.png"
                Text   : string, default "Delete content"
            Search:
                Image  : filename, default "search.png",
                Text   : string, default "Select"
            Browse:
                Image  : filename, default "browse.png",
                Text   : string, default "Browse on server"
            DatePicker:
                Image  : filename, default "datepicker.png",
                Text   : string, default "Select date"
            TimePicker:
                Image  : filename, default "timepicker.png",
                Text   : string, default "Select time"
            InsertDTU:
                Image  : filename, default "insert_dtu.png",
                Text   : string, default "insert current date, time and user"
```
If no ```path``` is specified, the image are loaded from the directory **StdImages** in the source 
folder of the package (**.../SKien/Formgenerator**). If a path is specified, it must be relative 
to the script that contains the form definition or absolut from the document root.

The text is used as label for the images.

### Localization of the date-, time- and numeric input fields
As long, as the ```onsubmit``` attribute of the form - element is not changed to any other 
JS function, the package performs an automatic JS - validation for some field types.
The ```placeholder``` value is used to set the HTML5 attribute of the respective input type. 
This value can be overwritten/reset separately for each element.

#### Date input

```php
	Date:
		Format           : string "YMD", "DMY" or "MDY", default "YMD"
        Separator        : string (1 char), default "-",
        Placeholder      : string, default empty
        UseHTML5Type     : boolean, default false
```
> If the value ```UseHTML5Type``` is set to *true*, the HTML5 input type 'date' is used 
> instead of the internal validation mechanism. This option is in a **'beta'** state
> (->nearly untested) since the date input currently is supported only by a few browsers 
> and always works internal with the english date format...

#### Time input

```php
	Time:
        Separator        : string (1 char), default ":",
        Seconds          : boolean, default false,
        AllowMinutesOnly : boolean, default true,
        Placeholder      : string, default empty
```
- The ```Seconds``` option determines whether the time is specified with (*true*) 
  or without (*false*) seconds.
- The ```MinutesOnly``` option determines whether it is posible to enter minutes 
  without hours (an entry of e.g. 100 is recognized as correct input when the form 
  is sent and will be converted to the timevalue '1:40').

#### Integer input

```php
	Integer:
		RightAlign       : boolean, default true
        Placeholder      : string, default empty
```

#### Floating point input

```php
	Float:
		DecimalPoint     : string (1 char), default see annotation below
		ThousandsSep     : string (1 char), default see annotation below
		RightAlign       : boolean, default true
        Placeholder      : string, default empty
```
The default value for the decimal point and the thousands separator depends on the 
data based upon the current locale as set by ```setlocale()```. If no locale settings 
available, defaults are "." for ```DecimalPoint``` and "," for ```ThousandsSep```.

#### Currency input

```php
	Float:
        Symbol           : string, default see annotation below 
		DecimalPoint     : string (1 char), default see annotation below
		ThousandsSep     : string (1 char), default see annotation below
		RightAlign       : boolean, default see annotation below
        Placeholder      : string, default empty
```
The default values for the decimal point and the thousands separator depends on the
settings for the floating point input but can be modified separate for currency inputs.

The default value for the currrency symbol depends upon the current locale and is set 
to "USD", if no locale is available.

### Settings for the color picker
To make it easier for the user to enter color values, the package has a color picker 
for visual selection. JSColor (see https://jscolor.com) was integrated for this purpose. 

**!!! For commercial use take care of the JSColor licence conditions !!!**

Since the appearance of JSColor is not based on a stylesheet, some settings can be 
made via the configuration. 

In addition, an individual palette can be specified containing any number of color 
values that are displayed in the picker as directly selectable colors.

(see example config files for a sample palette definition)

```php
	Color:
		borderColor      : color string, default "#000000"
        borderRadius     : integer, default 0
        padding          : integer, default 12
		backgroundColor  : color string, default "#FFFFFF"
		position         : string "bottom", "left", "right" or "top", default "bottom"
		palette          : array of color strings , default empty
```

### Settings for an embedded CKEditor

Since the integration of the CKEditor is a complex matter, it is dealt with in a separate 
description in which the setting options via the configuration are also explained in detail.

### Settings for a connected RichFilemanager

The connection with the RichFilemanager also requires a more detailed description, 
which also goes into its configuration.
