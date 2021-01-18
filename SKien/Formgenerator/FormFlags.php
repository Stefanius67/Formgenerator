<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Class to handle all the differnet flags to control the form elements.
 *
 * #### History
 * - *2021-01-18*   initial version
 *
 * @package Formgenerator
 * @version 1.1.0
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormFlags
{
    /** text-align:left (default)   */
    const   ALIGN_LEFT              = 0x000000;
    /** mandatory field. added to mandatory-list for JS validation    */
    const   MANDATORY               = 0x000001;
    /** hidden field    */
    const   HIDDEN                  = 0x000002;
    /** readonly field  */
    const   READ_ONLY               = 0x000004;
    /** text-align:right    */
    const   ALIGN_RIGHT             = 0x000008;
    /** field is extended with DTU (Date-Time-User) Button  */
    const   ADD_DTU                 = 0x000010;
    /** field is extendet with selectbutton (click calls javascript function OnSelect(strField) with fieldname    */
    const   ADD_SELBTN              = 0x000020;
    /** static field is displayed as hint (smaller font, darkgrey)  */
    const   HINT                    = 0x000040;
    /** static field is displayed as error (red text)   */
    const   ERROR                   = 0x000080;
    /** button with selectlist  */
    const   SELECT_BTN              = 0x000100;
    /** field is disabled   */
    const   DISABLED                = 0x000400;
    /** display static field as info (see stylesheet: .info)    */
    const   INFO                    = 0x000800;
    /** text-align:center   */
    const   ALIGN_CENTER            = 0x001000;
    /** field is extended with Cal-Button for datepicker    */
    const   ADD_DATE_PICKER         = 0x002000;
    /** field is extended with Clock-Button for timepicker  */
    const   ADD_TIME_PICKER         = 0x004000;
    /** suppress zero-values    */
    const   NO_ZERO                 = 0x008000;
    /** suppress zero-values    */
    const   PASSWORD                = 0x010000;
    /** file input  */
    const   FILE                    = 0x020000;
    /** add EUR - Suffix    */
    const   ADD_EUR                 = 0x040000;
    /** trim content (leading / trailing blanks)    */
    const   TRIM                    = 0x080000;
    /** field invokes color picker on click */
    const   ADD_COLOR_PICKER        = 0x0100000;
    /** set data for CKEdit through JS  */
    const   SET_JSON_DATA           = 0x0200000;
    /** font-weight: bold   */
    const   BOLD                    = 0x0400000;
    /** replace '<br/>' / '<br> with CR */
    const   REPLACE_BR_CR           = 0x0800000;
    
    /** @var integer the flags specified     */
    protected int $wFlags = 0;

    /**
     * create a flag object  
     * @param int $wFlags
     */
    public function __construct(int $wFlags = 0)
    {
        $this->wFlags = $wFlags;
    }
    
    public function getFlags() : int
    {
        return $this->wFlags;
    }
    
    public function add(int $wFlags) : void
    {
        $this->wFlags |= $wFlags;
    }
    
    public function remove(int $wFlags) : void
    {
        $this->wFlags &= ~$wFlags;
    }
    
    public function isSet(int $wFlags) : bool
    {
        return ($this->wFlags & $wFlags) != 0;
    }
}

