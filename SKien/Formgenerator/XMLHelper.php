<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Trait containing some Helper to generate the Form from XML-File
 * @package Formgenerator
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 * @internal
 */
trait XMLHelper
{
    /**
     * Test if requested attribute is set.
     * @param \DOMElement $oXMLElement
     * @param string $strName
     * @return bool
     */
    static protected function hasAttrib(\DOMElement $oXMLElement, string $strName) : bool
    {
        return $oXMLElement->hasAttribute($strName);
    }

    /**
     * Get the string value of named attrib or return default value, if attrib not exist.
     * @param \DOMElement $oXMLElement
     * @param string $strName
     * @param string $strDefault
     * @return string
     */
    static protected function getAttribString(\DOMElement $oXMLElement, string $strName, string $strDefault = '') : string
    {
        if (!$oXMLElement->hasAttribute($strName)) {
            return $strDefault;
        }
        return $oXMLElement->getAttribute($strName);
    }

    /**
     * Get the integer value of named attrib or return default value, if attrib not exist.
     * @param \DOMElement $oXMLElement
     * @param string $strName
     * @param int $iDefault
     * @return int
     */
    static protected function getAttribInt(\DOMElement $oXMLElement, string $strName, int $iDefault = 0) : int
    {
        if (!$oXMLElement->hasAttribute($strName)) {
            return $iDefault;
        }
        return intval($oXMLElement->getAttribute($strName));
    }

    /**
     * Get the float value of named attrib or return default value, if attrib not exist.
     * @param \DOMElement $oXMLElement
     * @param string $strName
     * @param float $fltDefault
     * @return float
     */
    static protected function getAttribFloat(\DOMElement $oXMLElement, string $strName, float $fltDefault = 0.0) : float
    {
        if (!$oXMLElement->hasAttribute($strName)) {
            return $fltDefault;
        }
        return floatval($oXMLElement->getAttribute($strName));
    }

    /**
     * Get the boolean value of named attrib or return default value, if attrib not exist.
     * Legal values for boolean are true, false, 1 (which indicates true), and 0 (which indicates false).
     * @link https://www.w3schools.com/xml/schema_dtypes_misc.asp
     * @param \DOMElement $oXMLElement
     * @param string $strName
     * @param bool $bDefault
     * @return bool
     */
    static protected function getAttribBool(\DOMElement $oXMLElement, string $strName, bool $bDefault = false) : bool
    {
        if (!$oXMLElement->hasAttribute($strName)) {
            return $bDefault;
        }
        $strValue = $oXMLElement->getAttribute($strName);
        return ($strValue == 'true' || $strValue == '1');
    }

    /**
     * Get an array of string values of the named attrib.
     * The attrib must contain a list spearated by whitespace(s).
     * @param \DOMElement $oXMLElement
     * @param string $strName
     * @return array<string>
     */
    static protected function getAttribStringArray(\DOMElement $oXMLElement, string $strName) : array
    {
        $aValues = [];
        $strArray = $oXMLElement->getAttribute($strName);
        if (strlen($strArray) > 0) {
            // to make it validateable by XSD-schema, we use a whitespace-separated list since
            // there is no way to define the delimiter for xs:list in XSD...
            $aValues = self::splitWhitespaces($strArray);
        }
        return $aValues;
    }

    /**
     * Get an array of integer values of the named attrib.
     * The attrib must contain a list spearated by comma.
     * @param \DOMElement $oXMLElement
     * @param string $strName
     * @return array<int>
     */
    static protected function getAttribIntArray(\DOMElement $oXMLElement, string $strName) : array
    {
        $aValues = [];
        $strArray = $oXMLElement->getAttribute($strName);
        if (strlen($strArray) > 0) {
            $aValues = array_map('intval', explode(',', $strArray));
        }
        return $aValues;
    }

    /**
     * Get the flags specified by the named attrib.
     * The attrib must contain a list of FormFlag - constants spearated by any whitespace(s).
     * @param \DOMElement $oXMLElement
     * @return int
     */
    static protected function getAttribFlags(\DOMElement $oXMLElement) : int
    {
        $wFlags = 0;
        $strFlags = $oXMLElement->getAttribute('flags');
        if (strlen($strFlags) > 0) {
            // to make it validateable by XSD-schema, we use a whitespace-separated list since
            // there is no way to define the delimiter for xs:list in XSD...
            $aFlags = self::splitWhitespaces($strFlags);
            foreach ($aFlags as $strFlag) {
                $strConstName = __NAMESPACE__ . '\FormFlags::' . strtoupper($strFlag);
                if (defined($strConstName)) {
                    $wFlags += constant($strConstName);
                } else {
                    trigger_error('Unknown Constant [' . $strConstName . '] for the FormFlag property!', E_USER_ERROR);
                }
            }
        }
        return $wFlags;
    }

    /**
     * Read all known attributes that don't need any further processing.
     * @param \DOMElement $oXMLElement
     * @param array<string,string> $aAttributes
     * @return array<string,string>
     */
    protected function readElementAttributes(\DOMElement $oXMLElement, ?array $aAttributes) : array
    {
        $aAttributesToRead = [
            'onclick',
            'ondblclick',
            'onchange',
            'oninput',
            'onfocus',
            'onblur',
            'onkeydown',
            'onkeypress',
            'onkeyup',
            'title',
            'placeholder',
            'maxlength',
        ];
        if ($aAttributes == null) {
            $aAttributes = array();
        }
        foreach ($aAttributesToRead as $strAttribute) {
            if (self::hasAttrib($oXMLElement, $strAttribute)) {
                $aAttributes[$strAttribute] = self::getAttribString($oXMLElement, $strAttribute);
            }
        }
        return $aAttributes;
    }

    /**
     * Get the child with the given tag name.
     * The given parent must only contain one chuld with this name!
     * @param string $strName
     * @return \DOMElement|null
     */
    protected function getXMLChild(\DOMElement $oXMLElement, string $strName) : ?\DOMElement
    {
        $oList = $oXMLElement->getElementsByTagName($strName);
        if ($oList->count() === 1) {
            $oChild = $oList->item(0);
            if ($oChild instanceof \DOMElement) {
                return $oChild;
            }
        }
        return null;
    }

    /**
     * Split the given string by whitespaces.
     * @param string $strToSplit
     * @return array<string>
     */
    static protected function splitWhitespaces(string $strToSplit) : array
    {
        $aSplit = preg_split('/\s+/', trim($strToSplit));
        if ($aSplit !== false) {
            $aSplit = array_map('trim', $aSplit);
        } else {
            $aSplit = [];
        }
        return $aSplit;
    }
}
