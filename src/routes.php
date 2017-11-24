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
    return $response->withStatus(200)->write('Welcome to Peruna Projects! This is a test');
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


//log in
$app->post('/login', function($request, $response, $args) {
	//get the body
	$data = $request->getParsedBody();
	//make variables for username and password
	$userID = filter_var($data['username'], FILTER_SANITIZE_STRING);
	$passCode = filter_var($data['password'], FILTER_SANITIZE_STRING);

	
	//check mysql for the username and password


	
	//check to see if the username and password are correct
	if($userID == "nhidang" and $passCode == "welcome")
	{
		$success = array("token" => 1234, "userId" => 01, "isProfessor" => true);
		return $response->withJson($success, 201);

		//return $response->withStatus(201)->write('success');
	}
	else
	{
		$error = array("error" => "error.unauthorized");
		return $response->withJson($error, 401);
	}
	

	//return $response->withStatus(201)->write('success');
});


$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});
