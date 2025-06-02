<?php 
// +------------------------------------------------------------------------+
// | @author        : Michael Arawole (Logad Networks)
// | @author_url    : https://logad.net
// | @author_email  : logadscripts@gmail.com
// | @date          : 20 Mar, 2022 10:18AM
// +------------------------------------------------------------------------+
// | Copyright (c) 2022 Logad Networks. All rights reserved.
// +------------------------------------------------------------------------+

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/sources/slim/autoload.php';
require __DIR__ . '/sources/psr-7/autoload.php';
require __DIR__ . '/core/functions.php';

header('Access-Control-Allow-Origin: *');

// Instantiate App
$api = AppFactory::create();

$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
if ($scriptDir == "/") $scriptDir = "";

// edit this if you're having issues
$api->setBasePath($scriptDir);

// Add body parsing
$api->addBodyParsingMiddleware();
// Add error middleware
$api->addErrorMiddleware(true, true, true);

// Add routes
$api->get('/', function (Request $request, Response $response) {
    $response->getBody()->write("API Endpoint");
    return $response;
});

// User login
$api->post('/login', function (Request $request, Response $response) {
	if (!haveEmptyParameter(array('QrPayUid'), $request, $response)) {
	    $request_data = $request->getParsedBody();
	    $post_data = cleanPostData($request_data);

	    // Check if uid is correct
	    $user = Users::ByUid($post_data->QrPayUid);
	    if (!empty($user)) {
            $response_data = array();
            $response_data['error'] = false;
            $response_data['message'] = "Login Successful";
            $response_data['data'] = Users::GetDetails($user->id);
            $response->getBody()->write(json_encode($response_data));
	    }
	    else {
	        $response_data = array();
	        $response_data['error'] = true;
	        $response_data['message'] = "Invalid UserID";
	        $response->getBody()->write(json_encode($response_data));
	    }
	}

    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});

// User register
$api->post('/register', function (Request $request, Response $response) {
	if (!haveEmptyParameter(array('name'), $request, $response)){
	    $request_data = $request->getParsedBody();
	   	$post_data = cleanPostData($request_data);

	    $result = Users::Create($post_data);
	    if ($result['status'] === true) {
            $response_data = array();
            $response_data['error'] = false;
            $response_data['message'] = $result['msg'];
            $response_data['data']['QrPayUid'] = $result['QrPayUid'];
            $response->getBody()->write(json_encode($response_data));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
	    }
	    else {
	        $response_data = array();
	        $response_data['error'] = true;
	        $response_data['message'] = $result['msg'];
	        $response->getBody()->write(json_encode($response_data));
	        return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
	    }
	}

    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});

// User Data
$api->post('/user-data', function (Request $request, Response $response) {
	if (!haveEmptyParameter(array('QrPayUid'), $request, $response)) {
	    $request_data = $request->getParsedBody();
	    $post_data = cleanPostData($request_data);

	    // Check if uid is correct
	    $user = Users::ByUid($post_data->QrPayUid);
	    if (!empty($user)) {
            $response_data = array();
            $response_data['error'] = false;
            $response_data['message'] = "Data fetch success";
            $response_data['data'] = Users::GetDetails($user->id);
            $response->getBody()->write(json_encode($response_data));
	    }
	    else {
	        $response_data = array();
	        $response_data['error'] = true;
	        $response_data['message'] = "Invalid UserID";
	        $response->getBody()->write(json_encode($response_data));
	    }
	}

    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});

// User Transactions
$api->post('/user-transactions', function (Request $request, Response $response) {
	if (!haveEmptyParameter(array('QrPayUid'), $request, $response)) {
	    $request_data = $request->getParsedBody();
	    $post_data = cleanPostData($request_data);

	    // Check if uid is correct
	    $user = Users::ByUid($post_data->QrPayUid);
	    if (!empty($user)) {
            $response_data = array();
            $response_data['error'] = false;
            $response_data['message'] = "success";
            $response_data['data'] = Transactions::ByUser($user->id);
            $response->getBody()->write(json_encode($response_data));
	    }
	    else {
	        $response_data = array();
	        $response_data['error'] = true;
	        $response_data['message'] = "Invalid UserID";
	        $response->getBody()->write(json_encode($response_data));
	    }
	}

    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});

// Send Money
$api->post('/send-money', function (Request $request, Response $response) {
	if (!haveEmptyParameter(array('QrPayUid', 'recipientUid', 'amount'), $request, $response)) {
	    $request_data = $request->getParsedBody();
	   	$post_data = cleanPostData($request_data);

	   	// Check if uid is correct
	   	$user = Users::ByUid($post_data->QrPayUid);
	   	if (!empty($user)) {
	   		$post_data->sender_id = $user->id;
		    $result = Transactions::SendMoney($post_data);
		    if ($result['status'] === true) {
	            $response_data = array();
	            $response_data['error'] = false;
	            $response_data['message'] = $result['msg'];
	            $response->getBody()->write(json_encode($response_data));
	            return $response
	                ->withHeader('Content-type', 'application/json')
	                ->withStatus(200);
		    }
		    else {
		        $response_data = array();
		        $response_data['error'] = true;
		        $response_data['message'] = $result['msg'];
		        $response->getBody()->write(json_encode($response_data));
		        return $response
	                ->withHeader('Content-type', 'application/json')
	                ->withStatus(200);
		    }
		} else {
	        $response_data = array();
	        $response_data['error'] = true;
	        $response_data['message'] = "Invalid UserID";
	        $response->getBody()->write(json_encode($response_data));
	    }
	}

    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});

function cleanPostData($post_data){
	global $app;
	$post = new stdClass();
	foreach ($post_data as $key => $value) {
		$post->$key = QrPay::clean($value);
	}
	return $post;
}
function haveEmptyParameter($required_params, $request, $response){ 
    $error = false;
    $error_params = '';
    $request_params = $request->getParsedBody();
    foreach ($required_params as $param) {
        if (!isset($request_params[$param]) || strlen($request_params[$param]) <= 0) {
            $error = true;
            $error_params .= $param . ', ';
        }
    }
    if ($error) {
        $error_detail = array();
        $error_detail['error'] = true;
        $error_detail['message'] = 'Required Parameters ' . substr($error_params, 0, -2) . ' are missing or empty';
        $response->getBody()->write(json_encode($error_detail));
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);
    }
    return $error;
}
$api->run();