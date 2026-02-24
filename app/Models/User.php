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
     *
     * Main roles for users: 'freelancer', 'company', 'admin', 'other'
     */
    protected $fillable = [
        'name',
        'legal_name',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'phone',
        'email',
        'website',
        'tax_number',
        'vat_id',
        'commercial_register_number',
        'currency',
        'financial_year_start',
        'financial_year_end',
        'logo_path',
        'is_active',
        'settings',
        'password',
        'role',
        'role_alias',
        'company_id',
        'menu_permissions',
        'languages',
        'years_of_experience',
        'availability',
        'preferred_shift',
        'specializations',
        'certifications',
        'bio',
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
            'is_active' => 'boolean',
            'settings' => 'array',
            'menu_permissions' => 'array',
            'financial_year_start' => 'date',
            'financial_year_end' => 'date',
            'languages' => 'array',
            'specializations' => 'array',
            'certifications' => 'array',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin'], true);
    }

    public function isRegularUser(): bool
    {
        return in_array($this->role, ['freelancer', 'company', 'other']);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

    public function projectApplications()
    {
        return $this->hasMany(ProjectApplication::class);
    }


    public function conversationParticipants()
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(ConversationMessage::class, 'sender_id');
    }

    public function presence()
    {
        return $this->hasOne(UserPresence::class);
    }

    public function chartOfAccounts()
    {
        return $this->hasMany(ChartOfAccount::class);
    }


    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function canAccessPortal(string $portal): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return match ($portal) {
            'admin' => $this->role === 'admin',
            'company' => $this->role === 'company',
            'freelancer' => $this->role === 'freelancer',
            default => false,
        };
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (is_array($this->menu_permissions) && in_array($permission, $this->menu_permissions, true)) {
            return true;
        }

        return $this->relationLoaded('permissions')
            ? $this->permissions->contains('slug', $permission)
            : $this->permissions()->where('slug', $permission)->exists();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
