<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    protected $logicaNegocio;

    protected function __construct($logicaNegocio)
    {
        $this->logicaNegocio = $logicaNegocio;
    }

    public function index(Request $request)
    {
        return $this->logicaNegocio->index($request);
    }

    public function store(Request $request)
    {
        return $this->logicaNegocio->store($request);
    }

    public function show($id)
    {
        return $this->logicaNegocio->store($id);
    }

    public function update(Request $request, $id)
    {
        return $this->logicaNegocio->update($request, $id);
    }

    public function destroy($id)
    {
        return $this->logicaNegocio->destroy($id);
    }

    public function mail($usuario) {
        return $this->logicaNegocio->mail($usuario);
    }
}
