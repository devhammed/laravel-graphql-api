<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'size',
        'disk',
        'path',
        'type',
        'meta',
        'user_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'disk',
        'path',
    ];

    /**
     * The attributes to be appended to the model's array form.
     */
    protected $appends = [
        'url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'meta' => 'object',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleted(
            fn(self $file) => Storage::disk($file->disk)->delete($file->path),
        );
    }

    /**
     * Get the user that owns the file.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the owning fileable model.
     */
    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the file's public url.
     */
    protected function url(): Attribute
    {
        return Attribute::get(
            fn(): string => Storage::disk($this->disk)->url($this->path),
        );
    }

    /**
     * Create a new File from uploaded file.
     *
     * @throws Exception
     */
    public static function fromUploadedFile(
        UploadedFile $file,
        string $folder = 'uploads',
        string $disk = 'public',
    ): self {
        if ($file->getError() !== UPLOAD_ERR_OK) {
            throw new Exception('File not uploaded.');
        }

        if (!($path = $file->store($folder, $disk))) {
            throw new Exception('File not stored.');
        }

        if (!($size = $file->getSize())) {
            $size = Storage::disk($disk)->size($path);
        }

        if (!($type = $file->getMimeType())) {
            $type = Storage::disk($disk)->mimeType($path);
        }

        if (!$type) {
            $type = 'application/octet-stream';
        }

        $attributes = [
            'disk' => $disk,
            'path' => $path,
            'size' => $size,
            'type' => $type,
            'user_id' => Auth::id(),
            'name' => $file->hashName(),
        ];

        if (str_starts_with($type, 'image/')) {
            $dimensions = $file->dimensions();

            $attributes['meta'] = array_merge(
                $attributes['meta'] ?? [],
                $dimensions ? ['width' => $dimensions[0], 'height' => $dimensions[1]] : [],
            );
        }

        return (new self())->fill($attributes);
    }
}
