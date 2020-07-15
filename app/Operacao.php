<?php

use Illuminate\Database\Eloquent\Model;

class Conta extends Model
{
    public $timestamps = false;
    public $table = 'operacoes';
    public $fillable = ['cpf', 'tipo', 'valor', 'date', 'observacao'];

    public function conta()
    {
        return $this->belongsTo(Conta::class);
    }
}