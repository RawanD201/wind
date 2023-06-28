<?php

namespace LaraZeus\Wind\Models;

use Database\Factories\DepartmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * @property string $slug
 * @property bool $is_active
 * @property string $logo
 * @property string $name
 * @property string $user_id
 * @property int $ordering
 * @property string $desc
 * @property array $options
 * @property Carbon $start_date
 * @property Carbon $end_date
 */
class Department extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'name', 'is_active', 'user_id', 'ordering', 'desc', 'options', 'logo', 'start_date', 'end_date', 'slug',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'options' => 'array',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function newFactory(): DepartmentFactory
    {
        return DepartmentFactory::new();
    }

    /** @phpstan-return hasMany<Letter> */
    public function letters(): HasMany
    {
        return $this->hasMany(config('zeus-wind.models.letter'));
    }

    public function image(): ?string
    {
        if (str($this->logo)->startsWith('http')) {
            return $this->logo;
        }
        if ($this->logo !== null) {
            return Storage::disk(config('zeus-wind.uploads.disk', 'public'))
                ->url($this->logo);
        }

        return null;
    }
}
