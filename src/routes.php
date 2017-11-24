<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/todos', function ($request, $response, $args) {
        $sth = $this->db->prepare("SELECT * FROM Users");
        $sth->execute();
        $todos = $sth->fetchAll();
        return $this->response->withJson($todos);
});


$app->put('/signup', function($request, $response, $args) {
	$parsedBody = $request->getParsedBody();
	$userID = $parsedBody['userID'];
        $username = $parsedBody['username'];
        $password = $parsedBody['password'];
        $email = $parsedBody['email'];
	$isProfessor = 'false';

	if( $parsedBody['isProfessor'] ) {
		$isProfessor = 'true';
	}


	$sql = $this->db->prepare(
		"INSERT INTO Users (userID, username, password, email, isProfessor)
		 VALUES ('{$userID}', '{$username}', '{$password}', '{$email}', '{$isProfessor}')"
	);

	try {
		$sql->execute();
	}
	catch(Exception $e) {
		return $response->withJson(["error" => "error"], 401)
				->withHeader('Content-Type', 'application/json');
	}

	$responseBody = ["token" => 12343, "userID" => $userID, "isProfessor" => $parsedBody['isProfessor']];

	return $response->withJson($responseBody, 201)
			->withHeader('Content-Type', 'application/json')
			->withHeader('Location', '/signup');
});


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

// default
/*$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});*/
