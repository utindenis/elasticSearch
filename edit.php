<!DOCTYPE html>
<html lang="en">
<head>
    <script type="text/javascript" src="src/js/main.js"></script>
</head>
<body>
<meta charset="UTF-8">
<h1>Elastic Search</h1>
<form method="post">
    <h3>Mapping</h3>
    <input type="submit" name="mapping" value="Create mapping" />
</form>
<form method="post">
    <h3>Delete:</h3>
    <p>Delete index: <input required type="text" name="delIndex" value="<?if(!empty($_POST["delIndex"])):?><?=$_POST["delIndex"]?><?endif;?>"/></p>
    <p><input type="submit" value="Delete"/></p>
    <p><input type='reset' value='Reset' name='reset' onclick="return resetForm(this.form);"></p>
</form>
<form method="post">
    <h3>Update:</h3>
    <p>Id doc: <input required type="text" name="idDocument" value="<?if(!empty($_POST["idDocument"])):?><?=$_POST["idDocument"]?><?endif;?>"/></p>
    <p>Field: <input required type="text" name="updateField" value="<?if(!empty($_POST["updateField"])):?><?=$_POST["updateField"]?><?endif;?>"/></p>
    <p>Value: <input required type="text" name="updateValue" value="<?if(!empty($_POST["updateValue"])):?><?=$_POST["updateValue"]?><?endif;?>"/></p>
    <p><input type="submit" value="Update"/></p>
    <p><input type='reset' value='Reset' name='reset' onclick="return resetForm(this.form);"></p>
</form>
<form method="post">
    <h3>Add data:</h3>
    <p>Id: <input required type="text" name="id" value="<?if(!empty($_POST["id"])):?><?=$_POST["id"]?><?endif;?>"/></p>
    <p>Type of transaction: <input required type="text" name="transactionType" value="<?if(!empty($_POST["transactionType"])):?><?=$_POST["transactionType"]?><?endif;?>"/></p>
    <p>Type of building: <input required type="text" name="typeBulding" value="<?if(!empty($_POST["typeBulding"])):?><?=$_POST["typeBulding"]?><?endif;?>"/></p>
    <p>Cost: <input required type="text" name="cost" value="<?if(!empty($_POST["cost"])):?><?=$_POST["cost"]?><?endif;?>"/></p>
    <p>Square: <input required type="text" name="square" value="<?if(!empty($_POST["square"])):?><?=$_POST["square"]?><?endif;?>"/></p>
    <p>Rooms: <input required type="number" name="rooms" value="<?if(!empty($_POST["rooms"])):?><?=$_POST["rooms"]?><?endif;?>"/></p>
    <p>Finish: <input required type="text" name="finish" value="<?if(!empty($_POST["finish"])):?><?=$_POST["finish"]?><?endif;?>"/></p>
    <p>Trim: <input required type="text" name="trim" value="<?if(!empty($_POST["trim"])):?><?=$_POST["trim"]?><?endif;?>"/></p>
    <p>Fund: <input required type="text" name="fund" value="<?if(!empty($_POST["fund"])):?><?=$_POST["fund"]?><?endif;?>"/></p>
    <p>Format of accomodation: <input required type="text" name="accomodationFormat" value="<?if(!empty($_POST["accomodationFormat"])):?><?=$_POST["accomodationFormat"]?><?endif;?>"/></p>
    <p>Conditions of mandatory: <input required type="text" name="mandatoryConditions" value="<?if(!empty($_POST["mandatoryConditions"])):?><?=$_POST["mandatoryConditions"]?><?endif;?>"/></p>
    <p>Name: <input required type="text" name="name" value="<?if(!empty($_POST["name"])):?><?=$_POST["name"]?><?endif;?>"/></p>
    <p>Description: <input required type="text" name="description" value="<?if(!empty($_POST["description"])):?><?=$_POST["description"]?><?endif;?>"/></p>
    <p><input type="submit" value="Add"/></p>
    <p><input type='reset' value='Reset' name='reset' onclick="return resetForm(this.form);"></p>
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
        'name',
        'description'
    ];

    $addStruct = [];

    $isFormValid = true;
    foreach ($fieldsToAdd as $fieldToAdd) {
        $isFormValid &= array_key_exists($fieldToAdd, $_POST);
        if ($isFormValid) {
            $addStruct[$fieldToAdd] = htmlspecialchars($_POST[$fieldToAdd]);
        }
    }

    if (isset($_POST['mapping'])) {
        $el->createMapping();
        echo 'Mapping create!';
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