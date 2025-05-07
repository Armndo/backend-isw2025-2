<?php
namespace Models;

use Core\Model;

class Sign extends Model //Cartel
{
    protected $fillable = [
        "id",
        "sign",
        "printed"
    ];
    
    public function markAsPrinted() {
        $this->printed = true;
        $this->save();
    }

    //public function project() { //Metodo para obtener el proyecto relacionado
        //return Project::find($this->project_id);
    //}
}