<?php

namespace App\Models;

use App\Models\SSHConnection;
use App\src\Objects\Model;
use Exception;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use App\Traits\Validation;

class ExecuterModel extends Model
{
	protected array $connections = [];

	protected array $validators;

	use Validation;

	/**
	 * Connette tutti i server
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setConnections();
		$this->setValidators();
	}

	public function setValidators()
	{
		$this->validators = [
			"command" => new Length(["min"=>1]),
			"server" => new Choice(array_keys($this->connections))
		];
	}

	/**
	 * Crea le varie connessioni da usare e le salva nell'oggetto
	 *
	 * @return void
	 */
	protected function setConnections()
	{
		$connections = $this->load->config();

		foreach( $connections as $name => $connData )
		{
			$this->connections[$name] = new SSHConnection($connData);
		}
	}

	/**
	 * Esegue il comando di ricerca su un singolo server e ritorna i risultati sotto forma di stringa
	 *
	 * @param SSHConnection $connection Connessione al server SSH
	 * @param string $command Comando da eseguire
	 * @return string Striga di risultato o stringa di errore
	 */
	protected function exec( SSHConnection $connection, string $command ):string
	{
		return $connection->execute($command);
	}

	/**
	 * Esegue il comando su tutti i server
	 *
	 * @param string $command Comando da eseguire
	 * @return array Array dei risultati (un record per server)
	 */
	public function execOnAllServers( string $command ):array
	{
		$results = [];
		foreach( $this->connections as $server => $connection )
		{
			$results[$server] = $this->exec($connection, $command);
		}

		return $results;
	}

	/**
	 * Esegue il comando sui server desiderati
	 *
	 * @param string $command Comando
	 * @param array $servers Lista dei server su cui eseguire il comando
	 * @return array Lista dei risultati per server
	 */
	public function execOnServers( string $command, array $servers ):array
	{
		$results = [];

		foreach($servers as $server)
		{
			$results[$server] = $this->exec($this->connections[$server], $command);
		}

		return $results;
	}

	/**
	 * Esegue ogni comando in modo sequenziale su ogni server richiesto
	 *
	 * @param array $commands Array dei comandi da eseguire con la lista dei server su cui eseguirlo
	 * @return array Array con la lista dei comandi con e i risultati per i vari server
	 */
	public function multipleExecOnServers( array $commands ):array
	{
		$results = [];

		foreach( $commands as $command){
			$results[] = [
				"command" => $command["command"],
				"results" => $this->execOnServers($command["command"],$command["servers"])
			];
		}

		return $results;
	}
}