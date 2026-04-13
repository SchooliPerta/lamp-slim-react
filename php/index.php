<?php
use Slim\Factory\AppFactory;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/controllers/AlunniController.php';

$app = AppFactory::create();

$app->get('/alunni', 'AlunniController:index');
$app->get('/alunni/{id}', 'AlunniController:show');
$app->post('/alunni', 'AlunniController:create');
$app->put('/alunni/{id}', 'AlunniController:update');
$app->delete('/alunni/{id}', 'AlunniController:destroy');
                   
$app->get('/certificazione', 'CertificazioniController:index');
$app->get('/certificazione/{id}', 'CertificazioniController:show');
$app->post('/certificazione', 'CertificazioniController:create');
$app->put('/certificazione/{id}', 'CertificazioniController:update');
$app->delete('/certificazione/{id}', 'CertificazioniController:destroy');

$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    return $response;
});

$app->run();
