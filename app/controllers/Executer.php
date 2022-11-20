<?php

namespace App\Controllers;

use App\src\Objects\Controller;
use App\Models\ExecuterModel;
use Exception;

class Executer extends Controller
{
    protected object $executer;

    public function __construct()
    {
        parent::__construct();
        $this->executer = new ExecuterModel();
    }

    public function indexPost( string $command )
    {
        $this->response(
            ["payload" => $this->executer->execOnAllServers($command)],
            200
        );
    }

    public function execOnServersPost( string $command, array $servers )
    {
        $this->response(
            ["payload" => $this->executer->execOnServers($command, $servers)],
            200
        );
    }

    public function multipleExecOnServersPost( array $list )
    {
        $this->response(
            ["payload" => $this->executer->multipleExecOnServers($list)],
            200
        );
    }
}