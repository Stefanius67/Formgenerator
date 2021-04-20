<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Class to handle all the different flags to control the form elements.
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
    const   ALIGN_LEFT              = 0x0000000;
    /** mandatory field. added to mandatory-list for JS validation    */
    const   MANDATORY               = 0x0000001;
    /** hidden field    */
    const   HIDDEN                  = 0x0000002;
    /** readonly field  */
    const   READ_ONLY               = 0x0000004;
    /** text-align:right    */
    const   ALIGN_RIGHT             = 0x0000008;
    /** field is extended with DTU (Date-Time-User) Button  */
    const   ADD_DTU                 = 0x0000010;
    /** field is extendet with selectbutton (click calls javascript function OnSelect(strField) with fieldname    */
    const   ADD_SELBTN              = 0x0000020;
    /** static field is displayed as hint (smaller font, darkgrey)  */
    const   HINT                    = 0x0000040;
    /** static field is displayed as error (red text)   */
    const   ERROR                   = 0x0000080;
    /** button with selectlist  */
    const   SELECT_BTN              = 0x0000100;
    /** button with selectlist  */
    const   SHOW_VALUE              = 0x0000100;
    /** connect edit/image with filemanager   */
    const   BROWSE_SERVER           = 0x0000200;
    /** field is disabled   */
    const   DISABLED                = 0x0000400;
    /** display static field as info (see stylesheet: .info)    */
    const   INFO                    = 0x0000800;
    /** text-align:center   */
    const   ALIGN_CENTER            = 0x0001000;
    /** field is extended with Cal-Button for datepicker    */
    const   ADD_DATE_PICKER         = 0x0002000;
    /** field is extended with Clock-Button for timepicker  */
    const   ADD_TIME_PICKER         = 0x0004000;
    /** suppress zero-values    */
    const   NO_ZERO                 = 0x0008000;
    /** suppress zero-values    */
    const   PASSWORD                = 0x0010000;
    /** file input  */
    const   FILE                    = 0x0020000;
    /** add Currency - Suffix    */
    const   ADD_CUR                 = 0x0040000;
    /** trim content (leading / trailing blanks)    */
    const   TRIM                    = 0x0080000;
    /** set data for CKEdit through JS  */
    const   SET_JSON_DATA           = 0x0200000;
    /** font-weight: bold   */
    const   BOLD                    = 0x0400000;
    /** replace '<br/>' / '<br> with CR */
    const   REPLACE_BR_CR           = 0x0800000;
    /** arrange radio - buttons horizontal in a row */
    const   HORZ_ARRANGE            = 0x1000000;
    
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
    
    /**
     * Get current flags.
     * @return int
     */
    public function getFlags() : int
    {
        return $this->wFlags;
    }
    
    /**
     * Add specified flags. 
     * @param int $wFlags
     */
    public function add(int $wFlags) : void
    {
        $this->wFlags |= $wFlags;
    }
    
    /**
     * Remove specified flag(s).
     * @param int $wFlags
     */
    public function remove(int $wFlags) : void
    {
        $this->wFlags &= ~$wFlags;
    }
    
    /**
     * Check if one of the requested flags is set.
     * @param int $wFlags
     * @return bool
     */
    public function isSet(int $wFlags) : bool
    {
        return ($this->wFlags & $wFlags) != 0;
    }
    
    /**
     * Check if one of the selectbutton - flags is specified.
     * @return int
     */
    public function getButtonFlags() : int
    {
        $wButtonFlags = FormFlags::ADD_DTU | FormFlags::ADD_TIME_PICKER | FormFlags::ADD_DATE_PICKER | FormFlags::ADD_SELBTN | FormFlags::BROWSE_SERVER;
        return ($this->wFlags & $wButtonFlags);
    }
}

