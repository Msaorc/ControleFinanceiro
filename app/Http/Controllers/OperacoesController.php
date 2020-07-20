<?php
namespace App\Http\Controllers;

use Throwable;
use App\Operacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

abstract class OperacoesController
{
    /**
     * @param Object $objetct - Objeto passado pela classe filha
     */
    protected $object;

    /**
     * Metodo para realizar debitos
     * 
     * @param Request $request Dados para a realização do debito
     * @return Json Mensagem e status
     */
    public function debit(Request $request)
    {   
        if (!$this->existRegisteredAccount($request->cpf)) {
            return response()->json(["sucess" => false,
                                     "message" => "Conta não existente"], 404);
        }

        $debitResponse = $this->operationAccount($request->cpf, floatval($request->valor), "Debito");
        return response()->json(["sucess" => $debitResponse['sucess'],
                                 "message" => $debitResponse['message']], $debitResponse['statusCode']);
    }
    
    /**
     * Metodo para realizar creditos
     * 
     * @param Request $request Dados para a realização do debito
     * @return Json Mensagem e status
     */    
    public function credit(Request $request)
    {
        if (!$this->existRegisteredAccount($request->cpf)) {
            return response()->json(["sucess" => false,
                                     "message" => "Conta não existente"], 404);
        }

        $creditResponse = $this->operationAccount($request->cpf, $request->valor, "Credito");
        return response()->json(["sucess" => $creditResponse['sucess'],
                                 "message" => $creditResponse['message']], $creditResponse['statusCode']);
    }

    /**
     * Metodo para consulta do extrato de uma conta
     * 
     * @param String $cpf cpf da conta
     * @return Json extrato da conta
     */
    public function extract(String $cpf)
    {
        if (!$this->existRegisteredAccount($cpf)) {
            return response()->json(["sucess" => false,
                                     "message" => "Conta não existente"], 404);
        }

        return response()->json(Operacao::where(['cpf' => $cpf])->get());
    }

    /**
     * Metodo para realizar transações
     * 
     * @param Request $request Dados para a realização da transação
     * @return Json Mensagem e status
     */
    public function transfer(Request $request)
    {
        if ($request->cpf_origem == $request->cpf_destino) {
            return response()->json(["sucess" => false,
                                     "message" => "Contas de origem e destino são iguais"], 404);            
        }
        if (!$this->existRegisteredAccount($request->cpf_origem)) {
            return response()->json(["sucess" => false,
                                     "message" => "Conta de origem não existente"], 404);
        }

        if (!$this->existRegisteredAccount($request->cpf_destino)) {
            return response()->json(["sucess" => false,
                                     "message" => "Conta de destino não existente"], 404);
        }        

        $transferResponse = $this->transferAccountValue($request->cpf_origem, $request->cpf_destino, $request->valor);
        return response()->json(["sucess" => $transferResponse['sucess'],
                                 "message" => $transferResponse['message']], $transferResponse['statusCode']);        
    }

    /**
     * Metodo que realiza as operações
     * 
     * @param String $cpf - cpf da conta para a realização da transação
     * @param Float $value - valor a ser aplicado na operação
     * @param String $type - tipo da operação que sera realizada
     * @param String $note - Observação da operação
     * @return Json Mensagem e status
     */
    private function operationAccount(String $cpf, Float $value, String $type, String $note = '')
    {
        $account = $this->object::where(['cpf' => $cpf])->get();
        $currentBalance = $account[0]['saldo'];

        if ( ($currentBalance < $value) && ($type == 'Debito') ) {
            return ["sucess" => false,
                    "message" => "Saldo insuficiente",
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
            return ["sucess" => false,
                    "message" => "Error: ".$th->getMessage(),
                    "statusCode" => 404
            ];
        }

        $this->saveRegistry($cpf, $value, $type, $note);
        return ["sucess" => true,
                "message" => "{$type} realizado com sucesso",
                "statusCode" => 200
        ];
    }

    /**
     * Metodo para atualizar o saldo
     * 
     * @param int $id - numero da conta
     * @param Float $balance - novo saldo da conta
     * @return void
     */
    private function updateBalance(int $id, Float $balance)
    {
        $account = $this->object::find($id);
        $account->saldo = $balance;
        $account->save();
    }

    /**
     * Metodo para salvar o registro das operações realizadas
     * 
     * @param String $cpf - cpf referente a conta da transação realizada
     * @param Float $value - valor da operação realizada
     * @param String $type - tipo da operação realizada
     * @param String $note - Observação da operação
     * @return void
     */    
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

    /**
     * Metodo para realizar transferencias
     * 
     * @param String $cpfOrigin - cpf da conta de origem
     * @param String $cpfDestiny - cpf da conta de destino
     * @param Float $value - valor da transferencia
     * @return Json Mensagem e status
     */    
    private function transferAccountValue($cpfOrigin, $cpfDestiny, $value)
    {
        DB::beginTransaction();
        $debitResponse = $this->operationAccount($cpfOrigin, floatval($value), "Debito", "Transferecia realizada para {$cpfDestiny} no valor {$value}");
        $creditResponse = $this->operationAccount($cpfDestiny, floatval($value), "Credito", "Transferecia recebida de {$cpfOrigin} no valor {$value}");
        
        if ($debitResponse['statusCode'] <> 200) {
            DB::rollBack();
            return ["sucess" => false,
                    "message" => "Erro ao realizar tranferencia. ".$debitResponse['message'],
                    "statusCode" => 404
            ];  
        }

        $creditResponse = $this->operationAccount($cpfDestiny, floatval($value), "Credito", "Transferecia recebida de {$cpfOrigin} no valor {$value}");        
        if ($creditResponse['statusCode'] <> 200) {
            DB::rollBack();
            return ["sucess" => false,
                    "message" => "Erro ao realizar tranferencia. ".$creditResponse['message'],
                    "statusCode" => 404
            ];  
        }
        DB::commit();

        return ["sucess" => true,
                "message" => "Transferencia realizada com sucesso",
                "statusCode" => 200
        ];        
    }

    /**
     * Metodo para realizar debitos
     * 
     * @param String $cpf - cpf para verificar se existe a conta
     * @return int - quantidade de contas cadastradas
     */    
    protected function existRegisteredAccount(String $cpf)
    {
        return $this->object::where(['cpf' => $cpf])->count();
    }
}