<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes


$app->get('/todos', function ($request, $response, $args) {
         $sth = $this->db->prepare("SELECT * FROM tasks ORDER BY task");
        $sth->execute();
        $todos = $sth->fetchAll();
        return $this->response->withJson($todos);
    });
//routes:
//welcome
$app->get('/welcome', function ($request, $response, $args) {
    return $response->withStatus(200)->write('Welcome to Peruna Projects!');
});
//student
$app->get('/student/{name}', function($request, $response, $args) {
	return $response->write("Student Profile for: " . $args['name']);
});
//professor
$app->get('/professor/{name}', function($request, $response, $args) {
        return $response->write("Professor Profile for: " . $args['name']);
});
//listings
$app->get('/listings', function ($request, $response, $args) {
    return $response->withStatus(200)->write("My Listings");
});
//settings
$app->get('/{name}/settings', function($request, $response, $args) {
        return $response->write("Settings for user: " . $args['name']);
});


$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});
