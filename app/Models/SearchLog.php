<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchLog extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $table = "search_logs";

    protected $fillable = [
        'id_user', 'searching'
    ];

    protected $timestamp = [
        'created_at',
        'updated_at'
    ];
}
