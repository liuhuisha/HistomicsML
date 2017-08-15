<?php

//
//	Copyright (c) 2014-2015, Emory University
//	All rights reserved.
//
//	Redistribution and use in source and binary forms, with or without modification, are
//	permitted provided that the following conditions are met:
//
//	1. Redistributions of source code must retain the above copyright notice, this list of
//	conditions and the following disclaimer.
//
//	2. Redistributions in binary form must reproduce the above copyright notice, this list 
// 	of conditions and the following disclaimer in the documentation and/or other materials
//	provided with the distribution.
//
//	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
//	EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
//	OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT
//	SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
//	INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED 
//	TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR
//	BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
//	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY
//	WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH
//	DAMAGE.
//
//

	require 'hostspecs.php';
	require '../db/logging.php';
	session_start();
	
	$prog = true;
	
	// Send finalize command to al server to save the training set and
	// get the pertinent info to save in the database
	//
	$end_data =  array( "command" => "viewerEnd", 
			 	   		"uid" => $_SESSION['uid']);
	$end_data = json_encode($end_data);
			 	   

	$addr = gethostbyname($host);
	set_time_limit(0);
	
	$socket = socket_create(AF_INET, SOCK_STREAM, 0);
	if( $socket === false ) {
		log_error("socket_create failed: ".socket_strerror(socket_last_error()));
		$prog = false;
	}
	
	if( $prog ) {
		$result = socket_connect($socket, $addr, $port);
		if( !$result ) {
			log_error("socket_connect failed: ".socket_strerror(socket_last_error()));
			$prog = false;
		}
	}
	
	if( $prog ) {
		socket_write($socket, $end_data, strlen($end_data));

		$response = socket_read($socket, 8192);
		$additional = socket_read($socket, 8192);
		while( $additional != false ) {
			$response = $response.$additional;
			$additional = socket_read($socket, 8192);
		}
		socket_close($socket);
	}

	if( $prog ) {
		echo "PASS";
	}
	
	if( $prog == false ) {
		echo "FAIL";
	}
		
	// Cleanup session variables
	//
	session_destroy();	
?>
