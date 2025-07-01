<?php

namespace App\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Entity Model for TEST_PY_DB_CON2 table
 * Represents the database structure only - no business logic
 */
class TestPyDbCon extends Model
{
    protected $table = 'TEST_PY_DB_CON2';
    protected $connection = 'snowflake';
    
    // Disable timestamps as this is a view/table without created_at/updated_at
    public $timestamps = false;
    
    // Define the fillable fields
    protected $fillable = [
        'PAYERID',
        'CRDATEUNIQUE',
        'CONTYPE',
        'CONTYPES',
        'COSTTYPE',
        'VISITTYPE',
        'STATUSFLAG',
        'CO_TO',
        'CO_SP',
        'CO_OP',
        'CO_FP'
    ];
    
    // Define the primary key (assuming PAYERID might be the primary key)
    protected $primaryKey = 'PAYERID';
    
    // Disable auto-incrementing as this might be a view
    public $incrementing = false;
    
    // Define the key type
    protected $keyType = 'string';
    
    // Cast the date field
    protected $casts = [
        'CRDATEUNIQUE' => 'date',
        'CO_TO' => 'integer',
        'CO_SP' => 'decimal:2',
        'CO_OP' => 'decimal:2',
        'CO_FP' => 'decimal:2'
    ];
} 