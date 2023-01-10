<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shop extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name', 'type', 'status', 'url', 'credentials', 'user_id'];

    protected $searchableFields = ['*'];

    protected $casts = [
        'credentials' => 'encrypted:object',
    ];

    protected $appends = [
        'summary'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function allShopData(): HasMany
    {
        return $this->hasMany(ShopData::class);
    }

    public function endpoints(): HasMany
    {
        return $this->hasMany(Endpoint::class);
    }

    public function entities(): HasMany
    {
        return $this->hasMany(Entity::class);
    }

    public function getSummaryAttribute(): array {
        return $this->entities->mapWithKeys(function(Entity $entity) {
            return [
                str($entity->name)->title()->replace('_', ' ')->toString() => $this->allShopData()->byEntity($entity)->count()
            ];
        })->toArray();
    }
}
