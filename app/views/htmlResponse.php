<?php

http_response_code($data["code"] ?? 200);

header('Content-Type: text/html');

if( array_key_exists( "payload", $data ) )
{
	printIteration($data["payload"]);
}

function printIteration( $data )
{
	echo "<table>";
	foreach ( $data as $key => $value )
	{
		echo "<tr><th>{$key}</th><td>";
		if(is_array($value) OR is_object($value))
		{
			echo printIteration($value);
		}
		else
		{
			echo $value;
		}

		echo "</td></tr>";
	}
	echo "</table>";
}