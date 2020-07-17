<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Operacao extends Model
{
    public $timestamps = false;
    public $table = 'operacoes';
    public $fillable = ['cpf', 'tipo', 'valor', 'data', 'observacao'];

    public function conta()
    {
        return $this->belongsTo(Conta::class);
    }
}