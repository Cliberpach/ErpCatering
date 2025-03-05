<?php
namespace App\Models\Registros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ColaboradorProyecto extends Model {
    use HasFactory;

    protected $table = 'colaborador_proyecto';

    protected $fillable = ['colaborador_id', 'regimen_id', 'horario_id'];

    public function colaborador() {
        return $this->belongsTo(Colaborador::class);
    }

 

    public function regimen() {
        return $this->belongsTo(Regimen::class);
    }

    public function horario() {
        return $this->belongsTo(Horario::class);
    }
}
