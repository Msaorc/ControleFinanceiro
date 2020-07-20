<?php

namespace App\Http\Controllers;

use App\Conta;
use Illuminate\Http\Request;
use App\Http\Controllers\OperacoesController;

class ContaController extends OperacoesController
{
     /**
     * Construtor da classe
     * 
     * @return void 
     */
    public function __construct()
    {
        $this->object = Conta::class;
    }
 
    /**
     * Metodo para criar contas
     * 
     * @param Request $request Dados para a criação da conta
     * @return Json dados da conta criada
     */
    public function create(Request $request)
    {
        if ($this->existRegisteredAccount($request->cpf)) {
            return response()->json(["sucess" => false,
                                     "message" => "Conta ja existente"], 203);
        }

        return response()->json(Conta::create($request->all()), 201);
    }

    /**
     * Metodo de consulta de todas as contas
     * 
     * @return Json retorna todas as contas existentes
     */
    public function accountsBalance()
    {
        return Conta::all();
    }

    /**
     * Metodo de consulta de conta
     * 
     * @param String cpf para consultar a conta
     * @return Json retorna todas as contas existentes
     */
    public function balance(String $cpf)
    {
        return response()->json(Conta::where(['cpf' => $cpf])->get());
    }
}