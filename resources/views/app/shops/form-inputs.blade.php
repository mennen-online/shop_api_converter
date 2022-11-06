@php $editing = isset($shop) @endphp

<div class="flex flex-wrap">
    <x-inputs.group class="w-full">
        <x-inputs.text
            name="name"
            label="Name"
            :value="old('name', ($editing ? $shop->name : ''))"
            maxlength="255"
            placeholder="Name"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.url
            name="url"
            label="Url"
            :value="old('url', ($editing ? $shop->url : ''))"
            maxlength="255"
            placeholder="Url"
            required
        ></x-inputs.url>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.textarea
            name="credentials"
            label="Credentials"
            maxlength="255"
            required
            >{{ old('credentials', ($editing ? json_encode($shop->credentials) :
            '')) }}</x-inputs.textarea
        >
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.select name="user_id" label="User" required>
            @php $selected = old('user_id', ($editing ? $shop->user_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the User</option>
            @foreach($users as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
</div>
