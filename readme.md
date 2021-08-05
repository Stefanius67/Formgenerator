# Create complex HTML forms

 ![Latest Stable Version](https://img.shields.io/badge/release-v1.0.0-brightgreen.svg)
 ![License](https://img.shields.io/packagist/l/gomoob/php-pushwoosh.svg) 
 [![Donate](https://img.shields.io/static/v1?label=donate&message=PayPal&color=orange)](https://www.paypal.me/SKientzler/5.00EUR)
 ![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg)
 [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Stefanius67/Formgenerator/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/Stefanius67/Formgenerator/?branch=main)
 [![codecov](https://codecov.io/gh/Stefanius67/Formgenerator/branch/main/graph/badge.svg?token=FPC3ND3MF5)](https://codecov.io/gh/Stefanius67/Formgenerator)
 
----------
## Overview
This package can be used to create complex input forms. Multiple input fields can be arranged in rows or in 
several columns.

In addition to the 'usual' form elements, the package contains an element for entering color values 
and a range element to which a label can be assigned, in which the current selection of the slider is 
automatically displayed. 

Additionaly the package contains JavaScript-based pickers for 
- date fields
- time fields
- color fields

and integrated JS validation of
- date values
- time values
- numerical values (integer, floating point and currency fields).

The input formats can be configured to meet country-specific requirements.
The design can be completely adapted to your own needs using an individual stylesheet.

The form definition can be made directly in the PHP code or within a XML file (for this
purposes a XSD schema is provided with the package to make it easy to check the validation 
of the XML definition).

The package also contains an element that can be used to integrate the WYSIWYG editor 
'CKEditor 4' into a form. The complete configuration is done from the PHP side. A 
connector to the open source file manager 'RichFilemanager' is included for selecting 
graphics or to create links to other files on the server. 

## Documentation
As this package supports a lot of functionality and can be configured in a wide range,
a detailed documentation can be found in the [wiki of thos repository](https://github.com/Stefanius67/Formgenerator/wiki).

In the *example* directory you will find sample forms that show the use of all supported 
elements and currently provides two different themes.