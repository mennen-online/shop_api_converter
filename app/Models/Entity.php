<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entity extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name', 'shop_id'];

    protected $searchableFields = ['*'];

    public function entityFields(): HasMany
    {
        return $this->hasMany(EntityField::class);
    }

    public function allShopData(): HasMany
    {
        return $this->hasMany(ShopData::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
}
