<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Endpoint extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['shop_id', 'entity_id', 'entity_field_id', 'name', 'url'];

    protected $searchableFields = ['*'];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function entityFields(): BelongsToMany
    {
        return $this->belongsToMany(EntityField::class);
    }

    public function mainEntity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    public function mainEntityField(): BelongsTo
    {
        return $this->belongsTo(EntityField::class);
    }
}
