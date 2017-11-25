<?php

use Slim\Http\Request;
use Slim\Http\Response;


$app->get('/todos', function ($request, $response, $args) {
         $sth = $this->db->prepare("SELECT * FROM Users");
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
