<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'username',
        'role_id',
        'jurusan_id',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class, 'user_id', 'id');
    }

    public function asesor()
    {
        return $this->hasOne(Asesor::class, 'user_id', 'id');
    }

    public function adminJurusan()
    {
        return $this->hasOne(AdminJurusan::class, 'user_id');
    }

    public function getJurusanAdminAttribute()
    {
        return $this->adminJurusan?->jurusan_id;
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }
}
