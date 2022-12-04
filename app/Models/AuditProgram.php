<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditProgram extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public $timestamps = false;
    protected $connection = 'OfficeDB';

    public function procedures()
    {
        return $this->hasMany(AuditProgramProcedure::class, 'audit_program_id', 'id');
    }
}
