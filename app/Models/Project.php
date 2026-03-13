<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['nom', 'description', 'contrat_id'];

    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }

    public function tickets()
    {
        // on précise projet_id car sinon Laravel va chercher project_id (en anglais) par défaut
        return $this->hasMany(Ticket::class, 'projet_id');
    }
}
