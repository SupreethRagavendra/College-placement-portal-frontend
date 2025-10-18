<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;

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
        'name',
        'register_number',
        'email',
        'password',
        'role',
        'supabase_id',
        'access_token',
        'is_verified',
        'is_approved',
        'email_verified_at',
        'admin_approved_at',
        'admin_rejected_at',
        'status',
        'rejection_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'access_token',
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
            'admin_approved_at' => 'datetime',
            'admin_rejected_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'is_approved' => 'boolean',
        ];
    }

    /**
     * Check if user is admin (cached)
     */
    public function isAdmin()
    {
        return Cache::remember("user_is_admin_{$this->id}", 300, function() {
            return $this->role === 'admin';
        });
    }

    /**
     * Check if user is student (cached)
     */
    public function isStudent()
    {
        return Cache::remember("user_is_student_{$this->id}", 300, function() {
            return $this->role === 'student';
        });
    }

    /**
     * Check if student is approved by admin (cached)
     */
    public function isApproved()
    {
        return Cache::remember("user_is_approved_{$this->id}", 300, function() {
            return $this->isStudent() && 
                   $this->is_verified && 
                   $this->is_approved;
        });
    }

    /**
     * Check if user can login (admin or approved student) (cached)
     */
    public function canLogin()
    {
        return Cache::remember("user_can_login_{$this->id}", 300, function() {
            return $this->isAdmin() || $this->isApproved();
        });
    }

    /**
     * Check if student is pending approval (cached)
     */
    public function isPendingApproval()
    {
        return Cache::remember("user_is_pending_{$this->id}", 300, function() {
            return $this->isStudent() && 
                   $this->is_verified && 
                   !$this->is_approved;
        });
    }

    /**
     * Check if student is rejected (cached)
     */
    public function isRejected()
    {
        return Cache::remember("user_is_rejected_{$this->id}", 300, function() {
            return $this->isStudent() && $this->admin_rejected_at;
        });
    }

    /**
     * Scope for approved students
     */
    public function scopeApproved($query)
    {
        return $query->where('role', 'student')
                    ->where('is_verified', true)
                    ->where('is_approved', true);
    }

    /**
     * Scope for pending students
     */
    public function scopePending($query)
    {
        return $query->where('role', 'student')
                    ->where('is_verified', true)
                    ->where('is_approved', false)
                    ->whereNull('admin_rejected_at');
    }

    /**
     * Scope for rejected students
     */
    public function scopeRejected($query)
    {
        return $query->where('role', 'student')
                    ->whereNotNull('admin_rejected_at');
    }

    /**
     * Get student results for this user
     */
    public function studentResults()
    {
        return $this->hasMany(StudentResult::class, 'student_id');
    }
}