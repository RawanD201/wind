<?php

namespace LaraZeus\Wind\Models;

use Database\Factories\LetterFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $name
 * @property string $email
 * @property int $department_id
 * @property string $title
 * @property string $message
 * @property string $status
 * @property string $reply_message
 * @property string $reply_title
 */
class Letter extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'department_id', 'title', 'message', 'status', 'reply_message', 'reply_title',
    ];

    protected static function newFactory(): LetterFactory
    {
        return LetterFactory::new();
    }

    /** @return BelongsTo<Letter, Department> */
    public function department(): BelongsTo
    {
        return $this->belongsTo(config('zeus-wind.models.department'));
    }

    public function getReplyTitleAttribute(): string
    {
        return $this->reply_title ?? __('re') . ': ' . $this->title;
    }
}
