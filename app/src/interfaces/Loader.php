<?php

namespace App\src\Interfaces;

interface Loader
{
	/**
	 * Carica un helper se non è già stato caricato
	 *
	 * @param string $helper nome dell'helper
	 * @return boolean true se il file esiste e/o viene caricato altrimenti false.
	 */
	public function helper( string $helper ):bool;

	/**
	 * Carica una view e ritorna true se la view è stata correttamente caricata
	 *
	 * @param string $view Nome della view
	 * @param array $data valori da utilizzare nella view
	 * @return boolean True le la view viene caricata altrimenti false
	 */
	public function view( string $view, array $data ):bool;

	/**
	 * Recupera la configurazione del model o controller desiderato
	 *
	 * @param string $config
	 * @return array
	 */
	public function config( ?string $config = null ):array;
}