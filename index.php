<?php
header('Content-Type: application/json');
header("Cache-Control: no-cache, must-revalidate"); //No Caching
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

session_start(); //for connection monitoring

// Report all errors except E_NOTICE
// This is the default value set in php.ini
error_reporting(E_ALL ^ E_NOTICE);



/*

License--------------------------------------------------

* Copyright (c) 2014 Jason Burgess
* All rights reserved.
* http://www.jasonburgess.com

* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions are met:

* Redistributions of source code must retain the above copyright
* notice, this list of conditions and the following disclaimer.

* Redistributions in binary form must reproduce the above copyright
* notice, this list of conditions and the following disclaimer in the
* documentation and/or other materials provided with the distribution.

* The names of its contributors may NOT be used to endorse or promote
* products derived from this software without specific prior written
* permission.

*
* THIS SOFTWARE IS PROVIDED BY Jason Burgess "AS IS" AND ANY
* EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
* WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
* DISCLAIMED. IN NO EVENT SHALL Jason Burgess BE LIABLE FOR ANY
* DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
* (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
* ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
* (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
* SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

*/




/* CONFIG******************************************************************
***************************************************************************
***************************************************************************
***************************************************************************
***************************************************************************
**************************************************************************/

//load modules
require 'output.php';
require 'security.php';
require 'controller.php';

//*********************************

//default: uses the Epiphany PHP Micro Framework for routing (not included... you will need to download and install into /epi)
//you can use whatever you like for routing
//if you change it up, make sure to change the BUID ROUTES section below

//download it here: https://github.com/jmathai/epiphany

require './epi/Epi.php';

Epi::setPath('base', './epi');
Epi::init('route');

//*********************************

//API identifiers
const apiName = 'Testing API';
const apiVersion = '1.0';

//API key MUST be a COMPLEX and unique string - VERY IMPORTANT that you change this PER installation 
const apiKey = 'H3wuSg4wVunRkeXvEgtvNJUVeWnkCWSxzXXJVQPN7tXztKQkkaxDZFyn69M2dh8jCxwVP5EpKg9RKg6WeabmBScZacgGjpHZ9XPs';

//These salt values MUST be COMPLEX and unique strings - VERY IMPORTANT that you change PER installation
const salt = 'pN6MDHktfQpG25NEmbVNAuTkaTxyV5DfE3h2ecX2y56YFbSx2ahhHu3EFBvu7FtjQtVpawvKYz3Zw9ys5TNTqFjEsjNQ8UmJsJaT';
const saltUser = 'rA3YYcJJ6afRAP66zdtf933M2B7CqbJheUZBp7GhZDnxfDQMTxn274UUUSGFYfDJWt7yBs25Kn6ex4w';

//this is the default value (in seconds) for expiring user authentication keys
const keyExpire = 86400;

//db connection info -- you will need to change this 
const dbName = 'api';
const dbUser = 'user';
const dbPass = 'password';

const connectionTimer = 60; //60 seconds is our default connection timer
const connectionThresholdInt = 100; //100 connections per N



/* BUILD ROUTES************************************************************
***************************************************************************
***************************************************************************
***************************************************************************
***************************************************************************
**************************************************************************/

//default read routes
getRoute()->get('/', 'home');
getRoute()->get('.*', 'error404');

//these are action routes
getRoute()->post('/staff', 'staff');
getRoute()->post('/model', 'buildModel');
getRoute()->post('/auth', 'authenticate');

//start up
getRoute()->run();



/* READ ROUTE FUNCTIONS****************************************************
***************************************************************************
***************************************************************************
***************************************************************************
***************************************************************************
**************************************************************************/

function home() {

	$response = array(
		'api' => apiName, 
		'version' => apiVersion, 
		'status' => 'success', 
		'error' => 'false', 
		'msg' => 'welcome', 
		'results' => 'none'
	);
	
	respond($response);
}

function error404() {

	$response = array(
		'api' => apiName, 
		'version' => apiVersion, 
		'status' => 'fail', 
		'error' => 'true', 
		'msg' => 'Resource not available', 
		'results' => 'none'
	);
	
	respond($response);
}

?>
