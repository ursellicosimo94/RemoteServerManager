<?php

namespace App\src\Objects;

use App\src\Interfaces\Model as InterfacesModel;
use App\src\Objects\Loader;

class Model implements InterfacesModel
{
	public object $load;

	/**
	 * Carica il loder e inizializza il buffer
	 */
	public function __construct()
	{
		$this->load = new Loader($this);
	}
}