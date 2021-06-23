<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Class to handle all the different flags to control the form elements.
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormFlags
{
    /** mandatory/required field. Attribute 'required' is set    */
    const   MANDATORY               = 0x0000001;
    /** mandatory/required field. Attribute 'required' is set    */
    const   REQUIRED                = 0x0000001;
    /** hidden field    */
    const   HIDDEN                  = 0x0000002;
    /** readonly field  */
    const   READ_ONLY               = 0x0000004;
    /** right text-align (buttons: align of the button)   */
    const   ALIGN_RIGHT             = 0x0000008;
    /** field is extended with DTU (Date-Time-User) Button  */
    const   ADD_DTU                 = 0x0000010;
    /** field is extendet with selectbutton (click calls javascript function OnSelect(strField) with fieldname    */
    const   ADD_SELBTN              = 0x0000020;
    /** static field is displayed as hint (set CSS class `.hint`)  */
    const   HINT                    = 0x0000040;
    /** static field is displayed as error (set CSS class `.error`)   */
    const   ERROR                   = 0x0000080;
    /** button with selectlist  */
    const   SELECT_BTN              = 0x0000100;
    /** display value for range/progress/meter element  */
    const   SHOW_VALUE              = 0x0000100;
    /** connect edit/image with filemanager   */
    const   BROWSE_SERVER           = 0x0000200;
    /** field is disabled   */
    const   DISABLED                = 0x0000400;
    /** display static field as info (set CSS class `.info`)    */
    const   INFO                    = 0x0000800;
    /** center text-align (buttons: align of the button)   */
    const   ALIGN_CENTER            = 0x0001000;
    /** field is extended with Calendar-Button for datepicker    */
    const   ADD_DATE_PICKER         = 0x0002000;
    /** field is extended with Clock-Button for timepicker  */
    const   ADD_TIME_PICKER         = 0x0004000;
    /** suppress zero-values    */
    const   NO_ZERO                 = 0x0008000;
    /** input for password    */
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
    /** replace '&lt;br/&gt;' / '&lt;br&gt; with CR */
    const   REPLACE_BR_CR           = 0x0800000;
    /** arrange radio - buttons horizontal in a row */
    const   HORZ_ARRANGE            = 0x1000000;

    /** @var integer the flags specified     */
    protected int $wFlags = 0;

    /**
     * create a flag object
     * @param int $wFlags   any combination of FormFlag constants
     */
    public function __construct(int $wFlags = 0)
    {
        $this->wFlags = $wFlags;
    }

    /**
     * Get current flags.
     * @return int
     * @internal
     */
    public function getFlags() : int
    {
        return $this->wFlags;
    }

    /**
     * Add specified flags.
     * @param int $wFlags
     * @internal
     */
    public function add(int $wFlags) : void
    {
        $this->wFlags |= $wFlags;
    }

    /**
     * Remove specified flag(s).
     * @param int $wFlags
     * @internal
     */
    public function remove(int $wFlags) : void
    {
        $this->wFlags &= ~$wFlags;
    }

    /**
     * Check if one of the requested flags is set.
     * @param int $wFlags
     * @return bool
     * @internal
     */
    public function isSet(int $wFlags) : bool
    {
        return ($this->wFlags & $wFlags) != 0;
    }

    /**
     * Check if one of the selectbutton - flags is specified.
     * @return int
     * @internal
     */
    public function getButtonFlags() : int
    {
        $wButtonFlags = FormFlags::ADD_DTU | FormFlags::ADD_TIME_PICKER | FormFlags::ADD_DATE_PICKER | FormFlags::ADD_SELBTN | FormFlags::BROWSE_SERVER;
        return ($this->wFlags & $wButtonFlags);
    }
}

