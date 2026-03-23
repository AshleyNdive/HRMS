<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'department_id',
        'position_id',
        'employee_id',
        'phone',
        'date_of_birth',
        'hire_date',
        'employment_type',
        'status',
        'salary',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'salary' => 'decimal:2',
        ];
    }

    /* =======================
       Relationships
    ======================= */

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function performanceReviews(): HasMany
    {
        return $this->hasMany(PerformanceReview::class);
    }

    /* =======================
       Model Events
    ======================= */

        protected static function booted(): void
{
    static::creating(function ($employee) {
        if (empty($employee->employee_id)) {
            // Get the highest database ID (e.g. 5) and add 1
            $nextNumber = (static::max('id') ?? 0) + 1;

            // Generate the string: EMP-000006
            $employee->employee_id = 'EMP-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        }
    });
}

public function canAccessPanel(Panel $panel): bool
    {
        return true; 
    }


}
