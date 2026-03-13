<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrat extends Model
{
    protected $fillable = ['client_id', 'heures_incluses', 'taux_horaire'];

    // Un contrat appartient à un client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Un contrat possède plusieurs projets
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
