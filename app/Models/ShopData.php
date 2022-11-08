<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
