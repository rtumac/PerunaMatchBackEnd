<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/projects', function ($request, $response, $args) {
	try {
		$sth = $this->db->prepare("SELECT projectID, name, tag, posterID FROM Projects");
   		$sth->execute();
   		$result = $sth->fetchAll();

		foreach($result as &$curr) {
			$curr['projectID'] = (int) $curr['projectID'];
			$curr['tag'] = json_decode($curr['tag']);
			$curr['posterID'] = (int) $curr['posterID'];
		}

	        return $response->withJson(["projects" => $result], 201)
				->withHeader('Content-Type', 'application/json')
                        	->withHeader('Location', '/projects');
	}
	catch(Exception $e) {
                return $response->withJson(["error" => "error.unauthorized"], 400)
                                ->withHeader('Content-Type', 'application/json');
        }
});

$app->get('/listing/{projectId}', function($request, $response, $args) {
	$sql = $this->db->prepare("SELECT * FROM Listings WHERE projectId = " . $args['projectId']);

	try{
		$sql->execute();
	}
	catch(Exception $e) {
		return $response->withJson(["error" => "error.unauthorized"], 400)
				->withHeader('Content-Type', 'application/Json');
	}

	$listings = $sql->fetchAll();

	return $this->response->withJson($listings);
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
    return $response->withStatus(200)->write('Hello!');
});

//student
$app->get('/student/{name}', function($request, $response, $args) {
	return $response->write("Student Profile for: " . $args['name']);
});

//professor
$app->get('/professor/{name}', function($request, $response, $args) {
        return $response->write("Professor Profile for: " . $args['name']);
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
