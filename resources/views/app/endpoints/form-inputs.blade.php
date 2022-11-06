@php $editing = isset($endpoint) @endphp

<div class="flex flex-wrap">
    <x-inputs.group class="w-full">
        <x-inputs.select name="shop_id" label="Shop" required>
            @php $selected = old('shop_id', ($editing ? $endpoint->shop_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Shop</option>
            @foreach($shops as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.text
            name="name"
            label="Name"
            :value="old('name', ($editing ? $endpoint->name : ''))"
            maxlength="255"
            placeholder="Name"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.url
            name="url"
            label="Url"
            :value="old('url', ($editing ? $endpoint->url : ''))"
            maxlength="255"
            placeholder="Url"
            required
        ></x-inputs.url>
    </x-inputs.group>
</div>
