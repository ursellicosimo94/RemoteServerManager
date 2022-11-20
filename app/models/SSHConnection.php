<?php

namespace App\Models;

use App\src\Objects\Model;

use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\Hostname;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Choice;

use Exception;

class SSHConnection extends Model
{
    protected mixed $connection = false;
    protected bool $isAuthorized = false;
    protected array $validators = [];

    /**
     * Tenta la connessione e l'autorizzazione al server
     *
     * @param array $authData parametri di connessione e autorizzazione
     */
    public function __construct( array $authData )
    {
        $this->setValidators();

        $this->validateFields(
            $authData,
            [ "hostname", "port", "methods", "callbacks" ]
        );

        $check = $this->connect(
            $authData["hostname"],
            $authData["port"],
            array_key_exists("methods",$authData) ? $authData["methods"] : null,
            array_key_exists("callbacks",$authData) ? $authData["callbacks"] : null
        );

        if ( $check )
        {
            $this->authtorize( $authData );
        }
    }

    /**
     * Imposta le regole di validazione degli input
     *
     * @return void
     */
    public function setValidators()
    {
        $this->validators = [
            "hostname" => new Required([new Hostname([
                'message' => 'Hostname non valido',
            ])]),
            "port" => new Required([new Range([
                'min' => 0,
                'max' => 65535,
                'notInRangeMessage' => 'La porta deve essere tra {{ min }} e {{ max }}',
            ])]),
            "username" => new Required([new Length([
                'min' => 2,
                'max' => 256,
                'minMessage' => 'Lo username deve essere lungo almeno {{ limit }} caratteri',
                'maxMessage' => 'Lo username deve essere lungo massimo {{ limit }} caratteri',
            ])]),
            "password" => new Required([new Length([
                'min' => 1,
                'max' => 512,
                'minMessage' => 'La password deve essere lungo almeno {{ limit }} carattere',
                'maxMessage' => 'La password deve essere lungo massimo {{ limit }} caratteri',
            ])]),
            "publicKey" => new Required([new File([
                'maxSize' => '1m',
            ])]),
            "privateKey" => new Required([new File([
                'maxSize' => '2m',
            ])]),
            "authMode" => new Required([new Choice([
                'choices' => ['withPassword', 'withCertificate'],
                'message' => 'Metodo di autorizzazione scelto non valido',
            ])]),
            "localUsername" => new Optional([new Length([
                'min' => 2,
                'max' => 256,
                'minMessage' => 'Lo username locale deve essere lungo almeno {{ limit }} caratteri',
                'maxMessage' => 'Lo username locale deve essere lungo massimo {{ limit }} caratteri',
            ])])
        ];
    }

    /**
     * Valida l'array in input, in caso di errori genera un'eccezione
     *
     * @param array $array Array associativo dei campi da validare
     * @param array $fields Array dei campi da validare
     * @param boolean $allowExtraFields True se $array può contenere anche altri campi, altrimenti false
     * @return void 
     */
    protected function validateFields( array $array, array $fields, bool $allowExtraFields = true ):void
    {
        $validator = Validation::createValidator();

        $fieldsValidations = [];

        $toValidate = [];
        foreach( $fields as $field )
        {
            $toValidate[$field] =  array_key_exists( $field, $array ) ? $array[$field] : null;
            if( array_key_exists( $field, $this->validators ) )
            {
                $fieldsValidations[$field] = $this->validators[$field];
            }
        }

        $violations = $validator->validate(
            $allowExtraFields ? $toValidate : $array,
            $fieldsValidations
        );

        if ( count( $violations ) ) {
            $errors = "";
            // there are errors, now you can show them
            foreach ($violations as $violation) {
                $errors .= $violation->getMessage().'\n';
            }

            throw new Exception( $errors );
        }
    }

    /**
     * Stabilisce una connessione verso il server
     *
     * @param string $hostname
     * @param integer $port
     * @param array|null $methods
     * @param array|null $callbacks
     * @return boolean True se riesce a stabilire una connessione altrimenti false
     */
    public function connect ( string $hostname, int $port = 22, ?array $methods = null, ?array $callbacks = null ):bool
    {   
        $this->validateFields(
            [ $hostname, $port ],
            [ "hostname", "port" ]
        );

        if ( !$this->disconnect() )
        {
            throw new Exception( "Impossibile interrompere connessione attuale" );
        }

        $this->connection =  new SSH2( $hostname, $port );

        return (bool) $this->connection;
    }

    /**
     * Chiude la connessione verso il server remoto
     *
     * @return bool True se non è necessario chiudere la connessione (non connesso) o la connessione è stata chiusa, altrimenti false
     */
    public function disconnect():bool
    {
        $this->isAuthorized = false;

        $this->connection = null;

        return true;
    }

    /**
     * Controlla che la connessione sia impostata e chiama il metodo di autenticazione richiesto
     *
     * @param array $authData Array contenente tutte le informazioni per la connessione
     * @return bool True per autorizzazione riuscita, altrimenti False 
     */
    public function authtorize( array $authData ):bool
    {
        if ( !$this->connection )
        {
            throw new Exception("Tentativo di autorizzazione su connessione inesistente");
        }

        $this->validateFields(
            $authData,
            [ "authMode" ]
        );

        $this->isAuthorized = $this->{$authData["authMode"]}( $authData );

        if(!$this->isAuthorized)
        {
            throw new Exception("Impossibile connettersi al server {$authData["nome"]}",500);
        }

        return $this->isAuthorized;
    }

    /**
     * Controlla i campi necessari e tenta l'autorizzazione con username e password
     *
     * @param array $params array dei parametri di connessione
     * @return boolean True se la connessione è riuscita, altrimenti false
     */
    protected function authWithPassword( array $params ):bool
    {
        $this->validateFields(
            $params,
            [ "username", "password" ]
        );

        return (bool) $this->connection->login($params['username'], $params['password']);
    }

    /**
     * Controlla i campi per l'autorizzazione tramite public key e si connette al server
     *
     * @param array $params array dei parametri
     * @return boolean True se l'autorizzazione è andata a buon fine, altrimenti false
     */
    protected function authWithCertificate( array $params ):bool
    {
        $this->validateFields(
            $params,
            [ "username", "publicKey", "certificatePassword","password" ]
        );

        $password = array_key_exists( "certificatePassword", $params ) ? $params["certificatePassword"] : null;

        $keyFile = file_get_contents($params["publicKey"]);
        
        if( $password !== null )
        {
            $key = PublicKeyLoader::load($keyFile, $params["certificatePassword"]);
        }
        else
        {
            $key = PublicKeyLoader::load($keyFile);
        }

        if(isset($params["password"]))
        {
            return (bool)$this->connection->login($params["username"], $key, $params["password"]);
        } 

        return (bool)$this->connection->login($params["username"], $key );
    }

    /**
     * Se l'oggetto non è pronto per eseguire chiamate sul server remoto viene generata un'eccezione
     *
     * @return void
     */
    public function connectedAndAuthorized()
    {
        if ( !$this->connection OR !$this->isAuthorized )
        {
            throw new Exception( "Connessione non stabilita o host non autorizzato" );
        }
    }

    /**
     * Esegue un comando sul server remoto
     *
     * @param string $command Comando da eseguire
     * @param string|null $pty Nome dell'emulazione pty, se non necessario lasciare a null
     * @param array|null $env Può essere passato come una matrice associativa di coppie nome/valore da impostare nell'ambiente di destinazione.
     * @param integer $width Larghezza del terminale virtuale.
     * @param integer $height Altezza del terminale virtuale.
     * @param int $width_height_type Può essere uno tra SSH2_TERM_UNIT_CHARS o SSH2_TERM_UNIT_PIXELS.
     * @return string risultato comando
     */
    public function execute( string $command, ?string $pty = null, ?array $env = null, int $width = 80, int $height = 25, int $width_height_type = SSH2_TERM_UNIT_CHARS ):string
    {
        $this->connectedAndAuthorized();

        return $this->connection->exec($command);
    }

    /**
     * Chiude la connessione se impostata, se fallisce genera eccezione
     */
    public function __destruct()
    {
        if ( !$this->disconnect() )
        {
            throw new Exception("Impossibile chiudere correttamente connessione con il server!");
        }
    }
}