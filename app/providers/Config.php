<?php

namespace App\Providers;

class Config
{
	/**
	 * Recupera un file di config e ritorna la configurazione
	 *
	 * @param string $configFile nome/path sotto app/configs del file di config
	 * @return array [] se non trovato altrimenti la configurazione trovata
	 */
	public function get( string $configFile ):mixed
	{
		$path = CONFIGS . $configFile . ".php";

		if( file_exists( $path ) )
		{
			include( $path );

			return $config ?? [];
		}

		return [];
	}
}