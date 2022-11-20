<?php

http_response_code($data["code"] ?? 200);

header('Content-Type: application/json');

if( array_key_exists( "payload", $data ) )
{
	echo json_encode( $data["payload"] );
}