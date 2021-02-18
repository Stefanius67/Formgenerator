<?php
declare(strict_types=1);

require_once '../autoloader.php';

foreach ($_POST as $key => $value) {
    echo $key . ' => ' . $value . '<br/>';
}