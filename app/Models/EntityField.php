<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EntityField extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['entity_id', 'name'];

    protected $searchableFields = ['*'];

    protected $table = 'entity_fields';

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    public function endpoints(): BelongsToMany
    {
        return $this->belongsToMany(Endpoint::class);
    }
}
