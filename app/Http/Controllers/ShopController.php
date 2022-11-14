<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShopStoreRequest;
use App\Http\Requests\ShopUpdateRequest;
use App\Models\Shop;
use App\Models\User;
use App\Policies\ShopPolicy;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Shop::class);

        $search = $request->get('search', '');

        $shops = Shop::search($search)
            ->latest()
            ->select('id', 'name', 'url', 'status', 'created_at', 'updated_at')
            ->paginate(15)
            ->toArray();



        return Inertia::render('Dashboard', ['shops' => $shops]);
//        return view('app.shops.index', compact('shops', 'search'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Shop::class);

        $users = User::pluck('name', 'id');

        return view('app.shops.create', compact('users'));
    }

    /**
     * @param \App\Http\Requests\ShopStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ShopStoreRequest $request)
    {
        $this->authorize('create', Shop::class);

        $validated = $request->validated();

        $shop = Shop::create($validated);

        return redirect()
            ->route('shops.edit', $shop)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Shop $shop
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Shop $shop)
    {
        $this->authorize('view', $shop);

        return view('app.shops.show', compact('shop'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Shop $shop
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Shop $shop)
    {
        $this->authorize('update', $shop);

        $users = User::pluck('name', 'id');

        return view('app.shops.edit', compact('shop', 'users'));
    }

    /**
     * @param \App\Http\Requests\ShopUpdateRequest $request
     * @param \App\Models\Shop $shop
     * @return \Illuminate\Http\Response
     */
    public function update(ShopUpdateRequest $request, Shop $shop)
    {
        $this->authorize('update', $shop);

        $validated = $request->validated();
        $validated['credentials'] = json_decode(
            $validated['credentials'],
            true
        );

        $shop->update($validated);

        return redirect()
            ->route('shops.edit', $shop)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Shop $shop
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Shop $shop)
    {
        $this->authorize('delete', $shop);

        $shop->delete();

        return redirect()
            ->route('shops.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
