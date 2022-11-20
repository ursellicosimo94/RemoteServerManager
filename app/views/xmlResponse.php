<?php

require_once ('app/src/xmlUtils.php');

http_response_code($data["code"] ?? 200);

header('Content-Type: application/xml');

if( array_key_exists( "payload", $data ) )
{
	echo toXML( $data["payload"], "response" );
}