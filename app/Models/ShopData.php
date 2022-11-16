<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class ShopData extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['shop_id', 'entity_id', 'content'];

    protected $searchableFields = ['*'];

    protected $table = 'shop_data';

    protected $casts = [
        'content' => 'object',
    ];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function getContentAttribute()
    {
        $shop = str($this->shop->type)->camel()->ucfirst()->toString();
        $namespace = 'App\Services\\ShopData\\'.$shop.'SyncService\\Models\\';
        $entity = $this->entity;
        $model = $namespace.str($entity->name)->lower()->camel()->ucfirst()->toString();
        if (! class_exists($model)) {
            Log::info("$model for $shop not found");

            return json_decode($this->attributes['content']);
        }

        return new $model(json_decode($this->attributes['content']));
    }
}
