<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Filament\Pages\Auth\EditProfile;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasRoles,HasPanelShield;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
 

    public function operators()
    {
        return $this->hasMany(Operator::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }
    public function orderss()
    {
        return $this->hasMany(Order::class, 'manager_id');
    }
    public function salesGoals()
    {
        return $this->hasMany(SalesGoal::class);
    }
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
    public function member()
    {
        return $this->belongsTo(TeamMember::class);
    }
    public function discounts()
    {
        return $this->hasMany(discount::class);
    }
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function proddesign()
    {
        return $this->hasMany(ProdDesign::class);
    }
}
