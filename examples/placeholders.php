<?php
// define available placeholders
$placeholders = [[
        "text"  => "First Placeholder",
        "value" => "placeholder1"
    ], [
        "text"  => "Second Placeholder",
        "value" => "placeholder2"
    ], [
        "text"  => "Third Placeholder",
        "value" => "placeholder3"
    ],
];
/*
$placeholders = [
    "placeholder1",
    "placeholder2",
    "placeholder3",
];
*/
header('Content-Type: application/json');
echo json_encode($placeholders);

