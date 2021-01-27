# Umstieg auf die eigenständige Komponente

### bSupervisor im Constructor von FormGenerator entfällt wieder
```php
/* (-) */ $oFG = new FormGenerator(Session::$oUser->isSupervisor());
/* (+) */ $oFG = new FormGenerator();
```

### eigene Klasse von Formgenerator ableiten, um 
- das Konstrukt mit dem Referer dort einzubauen!!!
- das Konstrukt ActionReceiver + Action dort einzubauen
- die Lastmodified Zeile einzubauen

> Die Info wurde zur Migration eingebaut -> ist aber keine Allgemeingültige Eigenschaft!!!
> Die Info wurde nur in FormCFKEdit zum ein/ausblenden des 'HTML source' Button verwendet
> Der Button muss jetzt explizit über `oFormCKEdit->setToolbar( xxx | TB_SOURCE);` eingeblendet werden

### dem Konstructor DatenInterface übergeben

### dem Konstructor Form-ID übergeben (default geändert von 'auto_form' -> 'FormGenerator'

### Klasse FormHelper wurde durch Trait FormHelperFromSQL ersetzt
> `FormHelper` enthielt nur statische Hilfsfunktionen, die jetzt vom neuen Trait implementiert
> werden. Die Änderung sollte sich nicht auf die integration in nderen Projekten auswirken

### methode oFG->setSize() wurde durch $oFG->setFormWidth() ersetzt
> Die Höhe wurde an keiner Stelle gesetzt

### FormCKEditor
> oEdit->setContentsCss() muss jetzt explizit gesetzt werden

