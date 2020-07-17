<?php

namespace App\Http\Controllers;

use App\Conta;
use Illuminate\Http\Request;
use App\Http\Controllers\OperacoesController;

class ContaController extends OperacoesController
{
    public function __construct()
    {
        $this->object = Conta::class;
    }

    public function create(Request $request)
    {
        if ($this->existRegisteredAccount($request->cpf)) {
            return response()->json(['message' => "Conta ja existente"], 203);
        }

        return response()->json(Conta::create($request->all()), 201);
    }

    public function accountsBalance()
    {
        return Conta::all();
    }

    public function balance(String $cpf)
    {
        return response()->json(Conta::where(['cpf' => $cpf])->get());
    }

    private function existRegisteredAccount(String $cpf)
    {
        return Conta::where(['cpf' => $cpf])->count();
    }


}