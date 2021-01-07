# Umstellung auf den 'neuen' Formgenerator

## !!! Wichtig !!!
**zuerst die Datei im Notepad++ mit folgenden Makros konvertieren:**
- **toPSR (wenn nicht bereits erfolgt)**
	- **formatiert den Quellcode entsprechend den PSR Vorgaben und wandelt die Datei in UTF-8 um**
	- **Umlaute und 'ß' müssen ggf. manuell nachgearbeitet werden**
- **newFormgenerator**
	- **führt die unten aufgelisteten Ersetzungen automatisch durch**

## manuelle Änderungen am Quellcode
- einfügen von
	- include_once "../tools/AdminFormPage.php";
	- use system\page\AdminDialogPage;

- entfernen von 
	- include_once "../../lib/formgenerator.php";
	- include_once "../tools/ckeditor.php" ;

- einfügen von
	- use SKien\Formgenerator\FormElement as FE;
	- use SKien\Formgenerator\FormGenerator;
	- use SKien\Formgenerator\FormDiv;
	- use SKien\Formgenerator\FormInput;
	- use SKien\Formgenerator\FormInt;
	- use SKien\Formgenerator\FormCheck;
	- use SKien\Formgenerator\FormDate;
	- use SKien\Formgenerator\FormTime;
	- use SKien\Formgenerator\FormStatic;
	- use SKien\Formgenerator\FormSelect;
	- use SKien\Formgenerator\FormImage;
	- use SKien\Formgenerator\FormButton;
	- use SKien\Formgenerator\FormTextArea;
	- use SKien\Formgenerator\FormCKEdit;
	- use SKien\Formgenerator\FormDataGrid;
	- use SKien\Formgenerator\FormCanvas;
	- use SKien\Formgenerator\FormColor;
	
> nicht benötigte ´use´ werden angezeigt -> einfach wieder rauswerfen!

- supervisor - info im constructor angeben 
	- (-) $oFG = new FormGenerator();
	- (+) $oFG = new FormGenerator(Session::$oUser->isSupervisor());

- setDBTable entfällt
	- (-) $oFG->setDBTable(...);

- setRight() erstzen durch setReadOnly()
	- (-) $oFG->SetRight(RIGHT_xxx);
	- (+) $oFG->setReadOnly(!Session::hasRights('write', RIGHT_xxx));

- CFormPG ersetzen durch 
	- (-) $oPG = new CFormPG($oFG);
	- (+) $oPG = new AdminFormPage($oFG);
	- oder
	- (+) $oPG = new AdminDialogPage($oFG);

- wenn vorhanden entweder löschen oder durch methode des Elements ersetzen (wenn param != tbContent)
	- (-) CKEditor::SetToolbar(CKEditor::tbContent);
	- (+) $oEdit->setToolbar(FormCKEdit::TB_CONTENT);


## Ersetzungen, die durch das Makro newFormgenerator mit Notepadd++ durchgeführt werden
1. alle Methoden beginnen mit Kleinbuchstaben
```php
oFG->Add         ->   oFG->add
oFG->Set         ->   oFG->set
oFS->Add         ->   oFS->add
oFS->Set         ->   oFS->set
oFL->Add         ->   oFL->add
oFL->Set         ->   oFL->set
oDiv->Add        ->   oDiv->add
oDiv->Set        ->   oDiv->set
oEdit->Add       ->   oEdit->add
oEdit->Set       ->   oEdit->set
oSelect->Add     ->   oSelect->add
oSelect->Set     ->   oSelect->set
oCheck->Add      ->   oCheck->add
oCheck->Set      ->   oCheck->set
oImg->Add        ->   oImg->add
oImg->Set        ->   oImg->set
```
2. die Konstanten groß
```php
FormInput::AlignLeft        ->   FE::ALIGN_LEFT
FormInput::AlignRight       ->   FE::ALIGN_RIGHT
FormInput::AlignCenter      ->   FE::ALIGN_CENTER
FormInput::Mandatory        ->   FE::MANDATORY
FormInput::Hidden           ->   FE::HIDDEN
FormInput::ReadOnly         ->   FE::READ_ONLY
FormInput::AddDTU           ->   FE::ADD_DTU
FormInput::AddSelect        ->   FE::ADD_SELBTN
FormInput::SelectBtn        ->   FE::SELECT_BTN
FormInput::Disabled         ->   FE::DISABLED
FormInput::Info             ->   FE::INFO
FormInput::Bold             ->   FE::BOLD
FormInput::AddDatePicker    ->   FE::ADD_DATE_PICKER
FormInput::AddTimePicker    ->   FE::ADD_TIME_PICKER
FormInput::NoZero           ->   FE::NO_ZERO    
FormInput::Password         ->   FE::PASSWORD
FormInput::File             ->   FE::FILE
FormInput::AddEUR           ->   FE::ADD_EUR
FormInput::Trim             ->   FE::TRIM
FormInput::AddColorPicker   ->   FE::ADD_COLOR_PICKER
FormInput::SetJsonData      ->   FE::SET_JSON_DATA
FormInput::Replace_BR_CR    ->   FE::REPLACE_BR_CR
```
3. aus den Klassen CForm... werden Form...
```php
CForm            ->   Form
```
4. auch diese groß
```php
FormDiv::None           ->   FormDiv::NONE
FormDiv::Left           ->   FormDiv::LEFT
FormDiv::Right          ->   FormDiv::RIGHT
FormDiv::Clear          ->   FormDiv::CLEAR
```
