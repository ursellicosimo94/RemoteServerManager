<?php

namespace App\src\Objects;

use App\src\Interfaces\Controller as InterfacesController;
use App\src\Objects\Loader;

class Controller implements InterfacesController
{
	public object $load;

	/**
	 * Carica il loder e inizializza il buffer
	 */
	public function __construct()
	{
		$this->load = new Loader($this);
	}

	/**
	 * Stampa la risposta in json
	 *
	 * @param mixed $payload payload della risposta
	 * @param integer $code HTTP code della risposta
	 * @return void
	 */
	public function response(mixed $payload = null, int $code = 200)
	{
		$this->load->view(
			getViewByHeaders(),
			[
				"code" => $code,
				"payload" => $payload
			]
		);
	}
}