<?php

use Illuminate\Database\Eloquent\Model;

class Conta extends Model
{
    public $timestamps = false;
    public $fillable = ['cpf', 'saldo'];

    public function operacoes()
    {
        return $this->HasMany(Operacao::class);
    }
}