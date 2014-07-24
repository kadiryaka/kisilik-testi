<?php

header('Access-Control-Allow-Origin: localhost');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');

require dirname(__FILE__) . '/third-party/Slim/Slim.php';
require dirname(__FILE__) . '/libs/main.php';

\Slim\Slim::registerAutoloader();

$app    = new \Slim\Slim();
$uup    = new uup();

// base router
$app->get('/', function() use ($uup) {

    echo json_encode($uup->status());

});

$app->get('/questions', function() use ($uup) {

    echo json_encode($uup->getAllQuestion());

});

$app->get('/questions/:id', function($id) use ($uup) {

    echo json_encode($uup->getQuestionById($id));

});

$app->get('/tests/:id', function($testId) use ($uup) {

    echo json_encode($uup->getQuestionsByTestId($testId));

});

$app->get('/role/:id', function($id) use ($uup) {

    echo json_encode($uup->getRoleTestQuestionById($id));

});

$app->get('/profile/:id', function($id) use ($uup) {

    echo json_encode($uup->getProfileTestByEpisodeId($id));

});

$app->post('/save', function() use ($app,$uup){

    $newArray = array();
    $questions = array();
    $adi = "";
    $soyadi = "";
    $array = (array) json_decode($app->request()->getBody());
    foreach($array as $i) {
        $newArray[] = get_object_vars($i);
    }
    foreach($newArray as $i) {
        if($i["adi"]) {
            $adi = $i["adi"];
        } else if($i["soyadi"]) {
            $soyadi = $i["soyadi"];
        } else {
            $questions[] = $i;
        }
    }
    echo json_encode($uup->save($adi,$soyadi,$questions));

});


$app->notFound(function () use ($uup) {

    echo json_encode($uup->errorNotFound());

});

$app->options('/', function() {

});

$app->run();