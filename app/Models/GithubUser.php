<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GithubUser extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'id';

    protected $table = "github_users";

    protected $fillable = [
        'username', 'name', 'company', 'organization', 'followers', 'following', 'average_number_per_followers'
    ];

    protected $timestamp = [
        'created_at',
        'updated_at'
    ];
}
