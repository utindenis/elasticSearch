<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>
<meta charset="UTF-8">
<h1>Elastic Search</h1>
<form method="post">
    <h3>Delete:</h3>
    <p>Delete index: <input type="text" name="delIndex"/></p>
    <p><input type="submit" value="Delete"/></p>
</form>
<form method="post">
    <h3>Update:</h3>
    <p>Id doc: <input type="text" name="idDocument"/></p>
    <p>Field: <input type="text" name="updateField"/></p>
    <p>Value: <input type="text" name="updateValue"/></p>
    <p><input type="submit" value="Update"/></p>
</form>
<form method="post">
    <h3>Add data:</h3>
    <p>Id: <input type="text" name="id"/></p>
    <p>Type of transaction: <input type="text" name="transactionType"/></p>
    <p>Type of building: <input type="text" name="typeBulding"/></p>
    <p>Cost: <input type="text" name="cost"/></p>
    <p>Square: <input type="text" name="square"/></p>
    <p>Rooms: <input type="number" name="rooms"/></p>
    <p>Finish: <input type="text" name="finish"/></p>
    <p>Trim: <input type="text" name="trim"/></p>
    <p>Fund: <input type="text" name="fund"/></p>
    <p>Format of accomodation: <input type="text" name="accomodationFormat"/></p>
    <p>Conditions of mandatory: <input type="text" name="mandatoryConditions"/></p>
    <p><input type="submit" value="Add"/></p>
</form>
</body>
</html>

<?php

require __DIR__ . '/vendor/autoload.php';

try {
    $el = new \Elastic\ElSearch();

    if (!empty($_POST['idDocument'])) {
        $idDocument = htmlspecialchars($_POST['idDocument']);
    }
    if (!empty($_POST['updateField'])) {
        $updateField = htmlspecialchars($_POST['updateField']);
    }
    if (!empty($_POST['updateValue'])) {
        $updateValue = htmlspecialchars($_POST['updateValue']);
    }
    if (!empty($_POST['searchAll'])) {
        $searchAll = htmlspecialchars($_POST['searchAll']);
    }

    $fieldsToAdd = [
        'id',
        'transactionType',
        'typeBulding',
        'cost',
        'square',
        'rooms',
        'finish',
        'trim',
        'fund',
        'accomodationFormat',
        'mandatoryConditions',
    ];

    $addStruct = [];

    $isFormValid = true;
    foreach ($fieldsToAdd as $fieldToAdd) {
        $isFormValid &= array_key_exists($fieldToAdd, $_POST);
        if ($isFormValid) {
            $addStruct[$fieldToAdd] = htmlspecialchars($_POST[$fieldToAdd]);
        }
    }

    if ($isFormValid) {
        $result = $el->create($addStruct);
        echo '<pre>', print_r($result, true), '</pre>';
    }
    if (!empty($_POST['delIndex'])) {
        $delIndex['delIndex'] = htmlspecialchars($_POST['delIndex']);
    }
    if (!empty($delIndex)) {
        echo '<h4>Result killed:</h4>';
        $result = $el->deleteByIndex($delIndex);
        echo '<pre>', print_r($result, true), '</pre>';
    }
    if (isset($idDocument, $updateField, $updateValue)) {
        echo '<h4>Result update:</h4>';
        $result = $el->updateById($idDocument, $updateField, $updateValue);
        echo '<pre>', print_r($result, true), '</pre>';
    }

} catch (Throwable $e) {
    var_dump($e);
}