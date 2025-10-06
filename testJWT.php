<?php

declare(strict_types=1);

include_once 'autoloader.php';

use SKien\Formgenerator\FormSecrets;

$oSecretsWrite = new FormSecrets('jwtData');
$oSecretsWrite->loadPrivateKey('key/private.pem');
$oSecretsWrite->add('myPayload', ['a' => 'testA', 'b' => 'testB']);

$strJWT = $oSecretsWrite->getData();

$oSecretsRead = new FormSecrets();
$oSecretsRead->loadPublicKey('key/public.pem');
$oSecretsRead->setData($strJWT);

echo '<pre>';
echo 'JWT<br>';
echo $strJWT;
echo '<br>';
echo '<br>';

echo 'my Payload:<br>';
var_dump($oSecretsRead->get('myPayload'));
echo '<br>';

echo '</pre>';

echo xdebug_info();
