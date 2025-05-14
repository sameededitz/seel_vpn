<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasSlug, HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'original_price',
        'discount_price',
        'duration',
        'duration_unit',
    ];
    protected $casts = [
        'original_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'duration' => 'integer',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
}
