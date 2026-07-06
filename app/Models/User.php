<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'promoted_by',
        'level',
        'last_sales_check',
        'path',
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
            'last_sales_check' => 'datetime',
        ];
    }

    // ===== RELATIONSHIPS =====

    public function promoter()
    {
        return $this->belongsTo(User::class, 'promoted_by');
    }

    public function downlines()
    {
        return $this->hasMany(User::class, 'promoted_by');
    }

    public function allDownlines()
    {
        return $this->downlines()->with('allDownlines');
    }

    /**
     * Promote requests sent by this user
     */
    public function sentPromoteRequests()
    {
        return $this->hasMany(PromoteRequest::class, 'promoter_id');
    }

    /**
     * Promote requests received by this user
     */
    public function receivedPromoteRequests()
    {
        return $this->hasMany(PromoteRequest::class, 'target_id');
    }

    /**
     * Unlink requests sent by this user
     */
    public function sentUnlinkRequests()
    {
        return $this->hasMany(UnlinkRequest::class, 'requester_id');
    }

    /**
     * Unlink requests received by this user
     */
    public function receivedUnlinkRequests()
    {
        return $this->hasMany(UnlinkRequest::class, 'target_id');
    }

    // ===== LEVEL SYSTEM =====

    /**
     * Get level name based on level number
     */
    public function getLevelNameAttribute()
    {
        $levels = [
            0 => 'Member',
            1 => 'Bronze',
            2 => 'Silver',
            3 => 'Gold',
            4 => 'Platinum',
            5 => 'Diamond',
            6 => 'Sapphire',
            7 => 'Ruby',
            8 => 'Emerald',
            9 => 'Pearl',
            10 => 'Legend',
        ];

        return $levels[$this->level] ?? 'Unknown';
    }

    /**
     * Get level color badge
     */
    public function getLevelColorAttribute()
    {
        $colors = [
            0 => 'secondary',
            1 => '#CD7F32',  // Bronze
            2 => '#C0C0C0',  // Silver
            3 => '#FFD700',  // Gold
            4 => '#E5E4E2',  // Platinum
            5 => '#B9F2FF',  // Diamond
            6 => '#0F52BA',  // Sapphire
            7 => '#E0115F',  // Ruby
            8 => '#50C878',  // Emerald
            9 => '#F5F5F5',  // Pearl
            10 => '#FFD700', // Legend (Gold)
        ];

        return $colors[$this->level] ?? 'secondary';
    }

    /**
     * Get level emoji
     */
    public function getLevelEmojiAttribute()
    {
        $emojis = [
            0 => '👤',
            1 => '🥉',
            2 => '🥈',
            3 => '🥇',
            4 => '💎',
            5 => '⭐',
            6 => '🔷',
            7 => '❤️',
            8 => '💚',
            9 => '⚪',
            10 => '👑',
        ];

        return $emojis[$this->level] ?? '👤';
    }

    /**
     * Get minimum sales required for current level
     */
    public function getLevelMinSalesAttribute()
    {
        return $this->getLevelSalesRequirement($this->level);
    }

    /**
     * Get maximum sales for current level
     */
    public function getLevelMaxSalesAttribute()
    {
        return $this->getLevelSalesRequirement($this->level + 1) - 0.01;
    }

    /**
     * Get sales requirement for a specific level
     */
    public static function getLevelSalesRequirement($level)
    {
        $requirements = [
            0 => 0,
            1 => 0,
            2 => 1001,
            3 => 5001,
            4 => 15001,
            5 => 30001,
            6 => 50001,
            7 => 100001,
            8 => 200001,
            9 => 500001,
            10 => 1000001,
        ];

        return $requirements[$level] ?? PHP_INT_MAX;
    }

    public function getGroupSalesAttribute()
    {
        $total = $this->getPersonalSalesAttribute();

        foreach ($this->downlines as $downline) {
            $total += $downline->group_sales;
        }

        return $total;
    }

    /**
     * Get personal sales (from orders)
     */
    public function getPersonalSalesAttribute()
    {
        return Order::where('user_id', $this->id)
            ->where('status', 'completed')
            ->sum('total_amount') ?? 0;
    }

    /**
     * Calculate level based on group sales
     */
    public function calculateLevel()
    {
        $sales = $this->group_sales;

        if ($sales >= 1000001) return 10;
        if ($sales >= 500001) return 9;
        if ($sales >= 200001) return 8;
        if ($sales >= 100001) return 7;
        if ($sales >= 50001) return 6;
        if ($sales >= 30001) return 5;
        if ($sales >= 15001) return 4;
        if ($sales >= 5001) return 3;
        if ($sales >= 1001) return 2;
        if ($sales >= 0 && $this->role == 1) return 1;

        return 0;
    }

    public function canPromote(User $target)
    {
        if ($this->id === $target->id) {
            return false;
        }

        if ($this->role !== 1) {
            return false;
        }

        if ($target->promoted_by !== null) {
            return false;
        }

        // Only allow promoting users with strictly lower level than the promoter
        // This prevents promoting users of the same or higher level.
        if ($this->level <= $target->level) {
            return false;
        }

        return true;
    }

    public function canUnlink()
    {
        if ($this->promoted_by === null) {
            return false;
        }

        return true;
    }

    public function getDownlinesTree()
    {
        return $this->buildTree($this->id);
    }

    private function buildTree($userId)
    {
        $user = User::with('downlines')->find($userId);
        $tree = [];

        foreach ($user->downlines as $downline) {
            $tree[] = [
                'user' => $downline,
                'children' => $this->buildTree($downline->id),
            ];
        }

        return $tree;
    }

    public function updatePath($promoterId)
    {
        $promoter = User::find($promoterId);

        $prefix = ($promoter && $promoter->path) ? $promoter->path : '.';
        $this->path = $prefix . $this->id . '.';
        $this->save();

        // Recalculate for children
        foreach ($this->downlines as $downline) {
            $downline->updatePath($this->id);
        }
    }
}