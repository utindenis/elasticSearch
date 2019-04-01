<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>
<meta charset="UTF-8">
<h1>Elastic Search</h1>
<form method="post">
    <h3>Search:</h3>
    <p>Search in some document lines: <input type="text" name="searchAll" value="<?if(!empty($_POST["searchAll"])):?><?=$_POST["searchAll"]?><?endif;?>"/></p>
    <p><input type="submit" value="Search"/></p>
    <p><input type='reset' value='Reset' name='reset' onclick="return resetForm(this.form);"></p>
</form>
<form method="post">
    <h3>Cost:</h3>
    <p>From: <input type="text" name="from" value="<?if(!empty($_POST["from"])):?><?=$_POST["from"]?><?endif;?>"/></p>
    <p>To: <input type="text" name="to" value="<?if(!empty($_POST["to"])):?><?=$_POST["to"]?><?endif;?>"/></p>
    <p><input type="submit" value="Search cost"/></p>
    <p><input type='reset' value='Reset' name='reset' onclick="return resetForm(this.form);"></p>
</form>
<form method="post">
    <h3>Search by fields:</h3>
    <p>Type bulding: <input type="text" name="typeBulding" value="<?if(!empty($_POST["typeBulding"])):?><?=$_POST["typeBulding"]?><?endif;?>"/></p>
    <p>Rooms: <input type="number" name="rooms" value="<?if(!empty($_POST["rooms"])):?><?=$_POST["rooms"]?><?endif;?>"/></p>
    <p>Accomodation format: <input type="text" name="accomodationFormat" value="<?if(!empty($_POST["accomodationFormat"])):?><?=$_POST["accomodationFormat"]?><?endif;?>"/></p>
    <p><input type="submit" value="Search"/></p>
    <p><input type='reset' value='Reset' name='reset' onclick="return resetForm(this.form);"></p>
</form>
</body>
</html>

<?php

require __DIR__ . '/vendor/autoload.php';

try {
    $el = new \Elastic\ElSearch();
    $searchStruct = [];
    $searchAll = [];
    $from = [];
    $to = [];

    if (!empty($_POST['typeBulding'])) {
        $searchStruct['typeBulding'] = htmlspecialchars($_POST['typeBulding']);
    }
    if (!empty($_POST['accomodationFormat'])) {
        $searchStruct['accomodationFormat'] = htmlspecialchars($_POST['accomodationFormat']);
    }
    if (!empty($_POST['rooms']) && is_numeric($_POST['rooms'])) {
        $searchStruct['rooms'] = $_POST['rooms'];
    }
    if (!empty($_POST['searchAll'])) {
        $searchAll = htmlspecialchars($_POST['searchAll']);
    }
    if (!empty($_POST['from'])) {
        $from = htmlspecialchars($_POST['from']);
    }
    if (!empty($_POST['to'])) {
        $to = htmlspecialchars($_POST['to']);
    }

    if (!empty($searchStruct)) {
        echo '<h4>Result search:</h4>';
        $result = $el->search($searchStruct);
        echo '<pre>', print_r($result, true), '</pre>';
    }
    if (!empty($searchAll)) {
        echo '<h4>Result search all:</h4>';
        $result = $el->searchAllbyFields($searchAll);
        echo '<pre>', print_r($result, true), '</pre>';
    }
    if (!empty($from) || !empty($to)) {
        echo '<h4>Result search cost:</h4>';
        $result = $el->searchByCost($from, $to);
        echo '<pre>', print_r($result, true), '</pre>';
    }

} catch (Throwable $e) {
    var_dump($e);
}

?>