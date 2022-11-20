<?php

function ExceptionHandler( Throwable $exception )
{
	ob_clean();
	
	$code = $exception->getCode();
	$code = $code ?: 500;

	$data = [
		"code" => $code,
		"payload" => [
			"code" => $code,
			"message" => $exception->getMessage(),
			"trace" => $exception->getTrace()
		]
	];

	$loader = new App\src\Objects\Loader($exception);

	$loader->view(getViewByHeaders(), $data);
}

function getHeader( string $header ):?string
{
	$headers = apache_request_headers() ?: [];

	return array_key_exists( $header, $headers ) ? $headers[$header] : null;
}

function getViewByHeaders( $default = "jsonResponse" )
{
	$responseView = [
		"application/json" => "jsonResponse",
		"application/xml" => "xmlResponse",
		"text/xml" => "xmlResponse",
		"text/html" => "htmlResponse",
		"text/plain" => "textResponse",
		"text/markdown" => "markdownResponse"
	];

	$header = getHeader("Accept");

	return array_key_exists($header, $responseView) ? $responseView[$header] : $default;
}

/**
 * Recupera il provider richiesto
 *
 * @param string $providerName Nome del provider da cercare
 * @return object
 */
function getProvider( string $providerName ):object
{
	$providerName = ucfirst($providerName);
	$namespace = "App\\Providers\\{$providerName}";

	if(!class_exists($namespace))
	{
		throw new Exception("Provider non trovato: {$providerName}", 500);
	}

	return new $namespace();
}