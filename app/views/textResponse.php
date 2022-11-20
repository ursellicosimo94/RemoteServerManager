<?php

http_response_code($data["code"] ?? 200);

header('Content-Type: text/plain');

if( array_key_exists( "payload", $data ) )
{
	print_r($data);
}