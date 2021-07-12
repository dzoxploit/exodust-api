<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GitlabUser extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    protected $table = "github_user";

    protected $fillable = [
        'username', 'name', 'followers', 'following', 'average_number_per_followers'
    ];

    protected $timestamp = [
        'created_at',
        'updated_at'
    ];
}
