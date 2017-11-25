<?php

use Slim\Http\Request;
use Slim\Http\Response;

//routes:
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

//log in
$app->post('/login', function($request, $response, $args) {
	//get the body
	$data = $request->getParsedBody();
	//make variables for username and password
	$userID = filter_var($data['username'], FILTER_SANITIZE_STRING);
	$passCode = filter_var($data['password'], FILTER_SANITIZE_STRING);
	
	//check mysql for the username and password
	$query = $this->db->prepare("SELECT * FROM Users WHERE username = '".$userID."' AND password = '".$passCode."'");
	$query->execute();

	//get the count of the numebr of rows affected by the query
	$count = $query->rowCount();

	//now check if the number of rows is 1 (match) or 0 (no match)
	if($count == 1)
	{
		//get the values from the array:
		$uToken = 1234; //default for now

		//get the user's id:
		$queryID = $this->db->prepare("SELECT * FROM Users WHERE username = '".$userID."' AND password = '".$passCode."'");
		$queryID->execute();
		$uId = $queryID->fetchColumn(); //get the first column for the user id

		//get the boolean for whether the user is a professor
		$queryIsProf = $this->db->prepare("SELECT * FROM Users WHERE username = '".$userID."' AND password = '".$passCode."'");
		$queryIsProf->execute();
                $isProf = $queryIsProf->fetchColumn(4); //get the professor boolean

		//make an array for outputting to json
		$success = array("token" => $uToken, "userId" => $uId, "isProfessor" => $isProf);
		return $response->withJson($success, 201);

	}
	else //no match
	{
		$error = array("error" => "error.unauthorized");
		return $response->withJson($error, 401);
	}

});
