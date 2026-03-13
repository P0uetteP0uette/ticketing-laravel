<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    // Autorise l'insertion dans ces colonnes
    protected $fillable = ['nom_entreprise'];

    // Un client a plusieurs contrats
    public function clients()
    {
        return $this->hasMany(Contrat::class);
    }
}
