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
     * Get the string value of named attrib or return default value, if attrib not exist
     * @param \DOMElement $oXMLElement
     * @param string $strName
     * @param string $strDefault
     * @return string|null
     */
    static protected function getAttribString(\DOMElement $oXMLElement, string $strName, ?string $strDefault = null) : ?string
    {
        if (!$oXMLElement->hasAttribute($strName)) {
            return $strDefault;
        }
        return $oXMLElement->getAttribute($strName);
    }

    /**
     * Get the integer value of named attrib or return default value, if attrib not exist
     * @param \DOMElement $oXMLElement
     * @param string $strName
     * @param int $iDefault
     * @return int|null
     */
    static protected function getAttribInt(\DOMElement $oXMLElement, string $strName, ?int $iDefault = null) : ?int
    {
        if (!$oXMLElement->hasAttribute($strName)) {
            return $iDefault;
        }
        return intval($oXMLElement->getAttribute($strName));
    }

    /**
     * Get an array of string values of the named attrib.
     * The attrib must contain a list spearated by whitespace(s).
     * @param \DOMElement $oXMLElement
     * @param string $strName
     * @return array|null
     */
    static protected function getAttribStringArray(\DOMElement $oXMLElement, string $strName) : ?array
    {
        $aValues = null;
        $strArray = $oXMLElement->getAttribute($strName);
        if (strlen($strArray) > 0) {
            // to make it validateable by XSD-schema, we use a whitespace-separated list since
            // there is no way to define the delimiter for xs:list in XSD...
            $aValues = array_map('trim', preg_split('/\s+/', trim($strArray)));
        }
        return $aValues;
    }

    /**
     * Get an array of integer values of the named attrib.
     * The attrib must contain a list spearated by comma.
     * @param \DOMElement $oXMLElement
     * @param string $strName
     * @return array|null
     */
    static protected function getAttribIntArray(\DOMElement $oXMLElement, string $strName) : ?array
    {
        $aValues = null;
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
            $aFlags = array_map('trim', preg_split('/\s+/', trim($strFlags)));
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
            if (($strValue = self::getAttribString($oXMLElement, $strAttribute)) !== null) {
                $aAttributes[$strAttribute] = $strValue;
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
}
