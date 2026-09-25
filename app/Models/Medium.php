<?php

namespace App\Models;

use App\Traits\HasTimestamps;
use Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Medium extends Model
{
    use HasTimestamps;

    // PROPS
    protected $fillable = ['type', 'mediable_type', 'mediable_id'];

    // Static media type resolver
    public static array $typeMap = [
        'image' => ['class' => Image::class, 'relation' => 'images'],
        'image-list' => ['class' => Image::class, 'relation' => 'images'],
        'carousel' => ['class' => Image::class, 'relation' => 'images'],
        'video' => ['class' => Video::class, 'relation' => 'videos'],
        'gifv' => ['class' => Video::class, 'relation' => 'videos'],
        '3d-model' => ['class' => Model3D::class, 'relation' => 'models3D'],
    ];

    public static function booted(): void
    {
        // When updating: if type has changed, purge content
        static::updating(function (Medium $medium) {
            if ($medium->isDirty('type')) {
                $oldType = $medium->getOriginal('type');

                // Temporarily switch back to old type to resolve old relation content
                $medium->type = $oldType;
                $medium->purgeContent();

                // Restore new type
                $medium->type = $medium->getAttribute('type');
            }
        });

        // Remove all associated media items before the delete cascade occurs
        static::deleting(function (Medium $medium) {
            $medium->images()->get()->each->delete();
            $medium->videos()->get()->each->delete();
            $medium->models3D()->get()->each->delete();
        });
    }
    // Process form input and return a structured response with validated path
    public static function processInput(array $input)
    {
        $type = $input['type'];

        if ($type == 'none' || !isset(static::$typeMap[$type]))
            return ['type' => 'none'];


        $processorClass = static::$typeMap[$type]['class'];
        $files = [];
        $safe_urls = []; // Safe urls is a shortcut to final file path; used later on when updating the DB

        foreach ($input['files'] ?? [] as $file) {
            $processed = $processorClass::processFile($file);
            $files[] = $processed;
            $safe_urls[] = $processed['path'];
        }

        return [
            'type' => $type,
            'files' => $files,
            'safe_urls' => $safe_urls
        ];
    }

    // DB update logic that links files to relevant tables
    // POSSIBLE FUTURES
    // Scenario 1. file is same, meta data changed (for example alt)
    // Scenario 2. file is new and needs to be registered
    // Scenario 3. file is old and needs to be removed
    public function resolveFiles(array $data)
    {
        $new_files = $data['files'] ?? [];
        $safe_urls = $data['safe_urls'] ?? [];
        $old_files = $this->content;

        // First do scenario 3: file was not resubmit, so file is no longer fresh
        // Delete the old file
        foreach ($old_files as $old_file) {
            if (!in_array($old_file->path, $safe_urls)) {
                $old_file->delete();
            }
        }

        // Then do scenario 1 & 2: file is new or was resubmit, so still fresh
        $relation = static::$typeMap[$this->type]['relation'] ?? null;
        if (!$relation || empty($new_files))
            return;

        // Create payload depending on type of record
        foreach ($new_files as $file) {
            $payload = match ($relation) {
                'images' => Arr::only($file, ['path', 'name', 'alt']),
                'videos' => array_merge(
                    Arr::only($file, ['path', 'name', 'title', 'description', 'poster_path', 'has_audio']),
                    ['loop' => $this->type == "gifv"]
                ),
                'models3D' => Arr::only($file, ['path', 'name', 'alt', 'poster_path'])
            };
            // Update or create (path is unique)
            $this->{$relation}()->updateOrCreate(
                ['path' => $file['path']],
                $payload
            );
        }
    }

    // Has a polymorphic relation: every medium can be linked to different models
    public function mediable()
    {
        return $this->morphTo();
    }
    protected function mediableName(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->mediable_type ? class_basename($this->mediable_type) : null,
        );
    }

    // Content returns relevant collection based on type
    // Returns empty collection if no relation found for type
    public function getContentAttribute()
    {
        $relation = static::$typeMap[$this->type]['relation'] ?? null;
        return $relation ? $this->{$relation} : collect();
    }

    // Purge content, triggering their static deleting hooks for file cleaning
    public function purgeContent(): void
    {
        if ($this->content->isNotEmpty()) {
            $this->content->each->delete();
        }
    }

    // Medium can have a collection of images attached
    public function images()
    {
        return $this->hasMany(Image::class);
    }

    // Medium can have a collection of videos attached
    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    // Medium can have a collection of 3D models attached
    public function models3D()
    {
        return $this->hasMany(Model3D::class);
    }

    // Eager load all files
    public function scopeWithFiles($query)
    {
        return $query->with(['images', 'videos', 'models3d']);
    }

    // Secondary or derived fields
    public function name(): Attribute
    {
        return Attribute::make(
            get: fn() => __('media.name', ['id' => $this->id, 'type' => $this->type]),
        );
    }
    public function icon(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->type) {
                'image' => 'styles/icons/image_icon.svg',
                'image-list' => 'styles/icons/gallery_icon.svg',
                'carousel' => 'styles/icons/carousel_icon.svg',
                'video' => 'styles/icons/video_icon.svg',
                'gifv' => 'styles/icons/gifv_icon.svg',
                '3d-model' => 'styles/icons/3d-model_icon.svg',
                default => 'styles/icons/image_icon.svg'
            }
        );
    }
}
