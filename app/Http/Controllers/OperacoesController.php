<?php
namespace App\Http\Controllers;

use Throwable;
use App\Operacao;
use Illuminate\Http\Request;

abstract class OperacoesController
{
    protected $object;

    public function debit(Request $request)
    {        
        $data = $this->operationAccount($request->cpf, floatval($request->valor), "Debito");
        return response()->json(['message' => $data['message']], $data['statusCode']);
    }
    
    public function credit(Request $request)
    {
        $return = $this->operationAccount($request->cpf, $request->valor, "Credito");
        return response()->json(['message' => $return['message']], $return['statusCode']);
    }
    
    public function extract(Request $request)
    {
        
    }
    
    public function transfer(Request $request)
    {
        
    }

    private function operationAccount(String $cpf, Float $value, String $type, String $note = '')
    {
        $account = $this->object::where(['cpf' => $cpf])->get();
        $currentBalance = $account[0]['saldo'];

        if ( ($currentBalance < $value) && ($type == 'Debito') ) {
            return ["message" => "Saldo insuficiente",
                    "statusCode" => 404
            ];
        }

        if ($type == 'Debito') {
            $newBalance = $currentBalance - $value;
        }else {
            $newBalance = $currentBalance + $value;
        }

        try {
            $this->updateBalance($account[0]['id'], $newBalance);
        } catch (\Throwable $th) {
            return ["message" => "Error: ".$th->getMessage(),
                    "statusCode" => 404
            ];
        }

        $this->saveRegistry($cpf, $value, $type, $note);
        return ["message" => "{$type} realizado com sucesso",
                "statusCode" => 200
        ];
    }

    private function updateBalance(int $id, Float $balance)
    {
        $account = $this->object::find($id);
        $account->saldo = $balance;
        $account->save();
    }

    private function saveRegistry(String $cpf, Float $value, String $type, String $note = '')
    {
        Operacao::create([
            'cpf' => $cpf,
            'tipo' => $type,
            'valor' => $value,
            'data' => date('Y-m-d'),
            'observacao' => $note
        ]);
    }    
}