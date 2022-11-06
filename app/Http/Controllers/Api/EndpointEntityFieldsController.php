<?php
namespace App\Http\Controllers\Api;

use App\Models\Endpoint;
use App\Models\EntityField;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\EntityFieldCollection;

class EndpointEntityFieldsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Endpoint $endpoint
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Endpoint $endpoint)
    {
        $this->authorize('view', $endpoint);

        $search = $request->get('search', '');

        $entityFields = $endpoint
            ->entityFields()
            ->search($search)
            ->latest()
            ->paginate();

        return new EntityFieldCollection($entityFields);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Endpoint $endpoint
     * @param \App\Models\EntityField $entityField
     * @return \Illuminate\Http\Response
     */
    public function store(
        Request $request,
        Endpoint $endpoint,
        EntityField $entityField
    ) {
        $this->authorize('update', $endpoint);

        $endpoint->entityFields()->syncWithoutDetaching([$entityField->id]);

        return response()->noContent();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Endpoint $endpoint
     * @param \App\Models\EntityField $entityField
     * @return \Illuminate\Http\Response
     */
    public function destroy(
        Request $request,
        Endpoint $endpoint,
        EntityField $entityField
    ) {
        $this->authorize('update', $endpoint);

        $endpoint->entityFields()->detach($entityField);

        return response()->noContent();
    }
}
