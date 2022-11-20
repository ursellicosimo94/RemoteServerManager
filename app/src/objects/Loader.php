<?php

namespace App\src\Objects;

use App\src\Interfaces\Controller as ControllerInterface;
use App\src\Interfaces\Model as ModelInterface;

class Loader
{
	protected $configPrefix = "";
	protected $father = "";

	public function __construct(object $father)
	{
		$namespaceParts = explode("\\", (get_class($father)));
		$this->father = end($namespaceParts);

		if ( $father instanceof ControllerInterface )
		{
			$this->configPrefix = "controllers/";
		}
		elseif ( $father instanceof ModelInterface )
		{
			$this->configPrefix = "models/";
		}
	}

	/**
	 * Carica un helper se non è già stato caricato
	 *
	 * @param string $helper nome dell'helper
	 * @return boolean true se il file esiste e/o viene caricato altrimenti false.
	 */
	public function helper( string $helper ):bool
	{
		$path = HELPERS . $helper . ".php";

		if( file_exists( $path ) )
		{
			require_once( $path );
			return true;
		}

		return false;
	}

	/**
	 * Carica una view e ritorna true se la view è stata correttamente caricata
	 *
	 * @param string $view Nome della view
	 * @param array $data valori da utilizzare nella view
	 * @return boolean True le la view viene caricata altrimenti false
	 */
	public function view( string $view, array $data ):bool
	{
		ob_clean();
		
		$path = VIEWS . $view . ".php";

		if( file_exists( $path ) )
		{
			require( $path );
			return true;
		}

		return false;
	}

	/**
	 * Recupera un config, se non specificato recupera quello instanziato alla creazione
	 *
	 * @param string|null $config
	 * @return array
	 */
	public function config( ?string $config = null ):array
	{
		return getProvider("config")->get( $config ?? $this->configPrefix . $this->father );
	}
}