<?php

namespace App\src\Objects;

use Exception;
use ReflectionClass;
use ReflectionException;

class RequestHandler
{
    protected $typeValidators = [
        "array" => "is_array",
        "float" => "is_float",
        "string" => "is_string",
        "int" => "is_int",
        "bool" => "is_bool"
    ];
    protected $controller;
    protected $method;
    protected $parameters = [];
    
    /**
     * Inizializza la classe per le chiamate dinamiche
     * @throws Exception Eccezione dell'errore
     */
    public function __construct()
    {
        #check url
        $urlCheck = preg_match( "/^\/([\w\d]+)(\/([\w\d]+))?\/?$/", $_SERVER["REQUEST_URI"], $matches );

        #url non valido
        if ( !$urlCheck )
        {
            throw new Exception("Richiesta malformata: controller assente!",400);
        }

        #Composizione del namespace del controller da inizializzare
        $controller = CONTROLLER_NAMESPACE . ucfirst($matches[1]);
        #Composizione e normalizzazione del metodo
        $method = lcfirst( $matches[3] ?? "index" ) . ucfirst(strtolower($_SERVER['REQUEST_METHOD']));
        #Body della richiesta
        $requestBody = json_decode( file_get_contents('php://input'), true );
        
        $requestBody = is_array($requestBody) ? $requestBody : [];

        $this->setController( $controller );
        $this->setMethod( $method );
        $this->setParameters( $requestBody );

    }

    /**
     * Imposta i parametri nell'ordine in cui devono essere passati al metodo della funzione
     *
     * @param array $params Array dei parametri inviati
     * @return void
     * @throws Exception Errore nei campi (campo necessario assente o tipo non valido)
     */
    protected function setParameters( array $params = [] ) {
        $parameters = [];

        $paramSettings = $this->methodParametersSettings();

        foreach( $paramSettings as $setting ) {
            if( array_key_exists( $setting["name"], $params ) ){
                $param = $params[ $setting[ "name" ] ];
            } elseif( $setting["hasDefault"] ) {
                $param = $setting["default"];
            } else {
                throw new Exception( "Impossibile trovare campo: {$setting["name"]}",400 );
            }

            if ( $setting["hasType"] ) {
                if ( array_key_exists($setting["type"], $this->typeValidators) ){
                    $function = $this->typeValidators[$setting["type"]];
                } else {
                    $setting["type"] = "object";
                    $function = "is_object";
                }

                if( !$function( $param ) ) {
                    throw new Exception( "Il tipo del valore {$setting[ "name" ]} non è valido: tipo atteso {$setting["type"]}", 400 );
                }
            }

            $parameters[] = $param;
        }

        $this->parameters = $parameters;
    }

    /**
     * Imposta il metodo da chiamare
     *
     * @param string $method Nome del metodo da chiamare
     * @return void 
     * @throws Exception Metodo inesistente
     */
    protected function setMethod( string $method )
    {
        if ( !method_exists( $this->controller, $method ) ) {
            throw new Exception("Metodo inesistente",501);
        }
        
        $this->method = $method;
    }

    /**
     * Imposta il controller
     *
     * @param string $controller Namespace del controller
     * @return void
     * @throws Exception Controller inesistente
     */
    protected function setController( string $controller )
    {
        #Composizione e normalizzazione del controller
        if( !class_exists($controller) )
        {
            throw new Exception("Risorsa non trovata",404);
        }

        $this->controller = new $controller();
    }

    /**
     * Recupera le informazioni sui parametri del metodo da chiamare
     *
     * @return array Parametri del metodo impostato
     * @throws ReflectionException Eventuali eccezioni della classe di reflection
     */
    protected function methodParametersSettings ():array {
        $reflector = new ReflectionClass( get_class( $this->controller ) );

        //Get the parameters of a method
        $parameters = $reflector->getMethod( $this->method )->getParameters();

        $array = [];
        foreach($parameters as $parameter)
        {
            $param = array();
            
            $param["name"] = $parameter->name;
            $param["hasType"] = $parameter->hasType(); 
            $param["type"] = (string) $parameter->getType();
            $param["hasDefault"] = $parameter->isDefaultValueAvailable();
            $param["default"] = ( $param["hasDefault"] ? $parameter->getDefaultValue() : null );

            $array[] = $param;
        }
        
        return $array;
    }

    /**
     * Call the selected controller method with the set parameters and return the result.
     * In case of exceptions, a response is returned with the exception message and the http 500 code
     *
     * @return mixed method results or \Illuminate\Http\Response with exception message
     */
    public function call(){
        return call_user_func_array([$this->controller,$this->method], $this->parameters);
    }
}