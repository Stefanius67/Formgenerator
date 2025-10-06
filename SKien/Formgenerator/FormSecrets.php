<?php

declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 *
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormSecrets extends FormInput
{
    protected string $strPrivateKey;
    protected string $strPublicKey;
    protected bool $bUseJWT = false;
    protected array $aSecrets = [];

    public function __construct(string $strName = '')
    {
        parent::__construct($strName, -1, FormFlags::HIDDEN);
    }

    public function setData(string $strData) : bool
    {
        $aSecrets = false;
        if ($this->bUseJWT) {
            $aSecrets = $this->verifyRS256JWT($strData);
        } else {
            $aSecrets = $this->decodeJSON($strData);
        }
        if ($aSecrets !== false) {
            $this->aSecrets = $aSecrets;
        }
        return $aSecrets !== false;
    }

    public function getData() : string
    {
        $strData = '';
        if ($this->bUseJWT) {
            $strData = $this->encodeRS256JWT($this->aSecrets);
        } else {
            $strData = $this->encodeJSON($this->aSecrets);
        }
        return $strData;
    }

    public function add(string $strName, mixed $value) : void
    {
        $this->aSecrets[$strName] = $value;
    }

    public function get(string $strName) : mixed
    {
        return $this->aSecrets[$strName] ?? null;
    }

    public function loadPrivateKey(string $strPrivateKeyFile) : void
    {
        if (is_readable($strPrivateKeyFile)) {
            $this->strPrivateKey = file_get_contents($strPrivateKeyFile);
            $this->bUseJWT = true;
        } else {
            trigger_error("The file [$strPrivateKeyFile] doesn't exist or is not readable!", E_USER_ERROR);
        }
    }

    public function loadPublicKey(string $strPublicKeyFile) : void
    {
        if (is_readable($strPublicKeyFile)) {
            $this->strPublicKey = file_get_contents($strPublicKeyFile);
            $this->bUseJWT = true;
        } else {
            trigger_error("The file [$strPublicKeyFile] doesn't exist or is not readable!", E_USER_ERROR);
        }
    }

    public function setPrivateKey(string $strPrivateKey) : void
    {
        $this->strPrivateKey = $strPrivateKey;
    }

    public function setPublicKey(string $strPublicKey) : void
    {
        $this->strPublicKey = $strPublicKey;
    }

    public function enableEncoding(bool $bEncode) : void
    {
        $this->bUseJWT = $bEncode;
    }

    /**
     * Builds the RS256 encoded JWT with given payload and private key.
     * @return string
     */
    public function encodeRS256JWT(array $aPayload) : string
    {
        // 1. build and encode Header
        $aJwtHeader = ["typ" => "JWT", "alg" => "RS256"];
        $strJwtHeader = $this->encodeJSON($aJwtHeader);
        $strJwtHeader64 = $this->encodeBase64Url($strJwtHeader);

        // 2. encode the payload / claim
        $strJwtPayload = $this->encodeJSON($aPayload);
        $strJwtPayload64 = $this->encodeBase64Url($strJwtPayload);

        // 3. sign both values for the signature
        $strJwtSignature = '';
        openssl_sign(
            $strJwtHeader64 . '.' . $strJwtPayload64,
            $strJwtSignature,
            $this->strPrivateKey,
            OPENSSL_ALGO_SHA256
            );
        $strJwtSignature64 = $this->encodeBase64Url($strJwtSignature);

        return implode('.', [$strJwtHeader64, $strJwtPayload64, $strJwtSignature64]);
    }

    /**
     * Verifies the given JWT and returns the extracted payload in case of success.
     * @param string $strJWT
     * @param string $strPrivateKey
     * @return array|false
     */
    public function verifyRS256JWT(string $strJWT) : array|false
    {
        // 1. split JWT into header, payload and signature
        list($strJwtHeader64, $strJwtPayload64, $strJwtSignature64) = explode('.', $strJWT);

        // 2. decode the signature
        $strJwtSignature = $this->decodeBase64Url($strJwtSignature64);

        // 3. verify the data and signature with the public key
        $iVerfied = openssl_verify(
            $strJwtHeader64 . '.' . $strJwtPayload64,
            $strJwtSignature,
            $this->strPublicKey,
            OPENSSL_ALGO_SHA256
            );
        $aPayload = false;
        if ($iVerfied === -1) {
            trigger_error('Error verifying the JWT: ' . openssl_error_string(), E_USER_ERROR);
        } else if ($iVerfied === 1) {
            // 4. JWT is verified - decode the payload
            $strJwtPayload = $this->decodeBase64Url($strJwtPayload64);
            $aPayload = $this->decodeJSON($strJwtPayload);
        }
        return $aPayload;
    }

    /**
     * Returns a string containing the JSON representation of a value.
     * @param mixed $value
     * @return string
     */
    protected function encodeJSON($value) : string
    {
        $strEncoded = json_encode($value, JSON_UNESCAPED_SLASHES);
        if ($strEncoded === false) {
            throw new \RuntimeException('Error encoding value to JSO: ' . json_last_error_msg());
        }
        return $strEncoded;
    }

    /**
     * Encodes string to save base64URL
     * @param string $strInput
     * @return string
     */
    protected function encodeBase64Url(string $strInput) : string
    {
        return str_replace('=', '', strtr(base64_encode($strInput), '+/', '-_'));
    }

    /**
     * Returns a decoded JSON string as array .
     * @param string $strJSON
     * @return array|false
     */
    protected function decodeJSON(string $strJSON) : array|false
    {
        $decoded = json_decode($strJSON, true, 64, JSON_INVALID_UTF8_IGNORE);
        if ($decoded === null) {
            throw new \RuntimeException('Error decoding JSON: ' . json_last_error_msg());
        }
        return $decoded;
    }

    /**
     * Decodes save base64URL to string.
     * @param string $strInput
     * @return string
     */
    protected function decodeBase64Url(string $strInput) : string
    {
        return base64_decode(strtr($strInput, '-_,', '+/='));
    }
}