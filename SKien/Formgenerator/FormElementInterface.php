<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Interface all elements of a form have to implement.
 *
 * @package Formgenerator
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
interface FormElementInterface
{
    /**
     * Return the FormGenerator this element belongs to.
     * @return FormGenerator
     */
    public function getFG() : ?FormGenerator;
    
    /**
     * Set the parent of this element.
     * The Formgenerator of the parent is adopted for this element. 
     * If there are global flags set for the FormGenerator, this flags are added.
     * @param FormCollection $oParent
     */
    public function setParent(FormCollection $oParent) : void; 
    
    /**
     * Set the current col.
     * If this element is added as a child of a FormLine, the column is set in 
     * order to be able to calculate the correct width of the element.
     * @param int $iCol
     */
    public function setCol(int $iCol) : void;
    
    /**
     * Set the tab index of the element.
     * Method is called from the PageGenerator after an element is added to the form.
     * @param int $iTabindex
     * @return int the number of indexes, the element needs
     */
    public function setTabindex(int $iTabindex) : int;
    
    /**
     * Get styles related to this element.
     * This method gives each element the chance to add special styles to the current page. <br/>
     * <b>This method is only called for elements having member bCreateStyle set to true!</b>
     * @return string
     */
    public function getStyle() : string;

    /**
     * Get the HTML marlup for the element
     * @return string
     */
    public function getHTML() : string;
}

