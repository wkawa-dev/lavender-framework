<?php

namespace App\Models;

use Database\Factories\AgentClientFactory;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

/**
 * An agent registered through /oauth/register (RFC 7591). It trades its
 * client_id and secret for short-lived Sanctum bearer tokens at /oauth/token.
 */
class AgentClient extends Model implements AuthenticatableContract
{
    /** @use HasFactory<AgentClientFactory> */
    use Authenticatable, HasApiTokens, HasFactory;

    protected $fillable = ['name', 'client_id', 'secret'];

    protected $hidden = ['secret'];

    // Authenticatable's password column; agents never log in with it.
    public function getAuthPasswordName(): string
    {
        return 'secret';
    }

    protected function casts(): array
    {
        return ['secret' => 'hashed'];
    }
}
