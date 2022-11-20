<?php

namespace App\src\Interfaces;

interface Controller
{
	/**
	 * Stampa la risposta nel formato richiesto dalla chiamata
	 *
	 * @param mixed $payload payload della risposta
	 * @param integer $code HTTP code della risposta
	 * @return void
	 */
	public function response(mixed $payload = null, int $code = 200);
}