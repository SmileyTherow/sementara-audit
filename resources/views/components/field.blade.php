@props([
    'name',
    'rules' => 'nullable|string',
    'value' => null,
    'options' => null,
    'label' => null,
])

@php
    $id = $name . '-' . \Illuminate\Support\Str::random(6);

    $value = old($name, $value);

    $required = str_starts_with($rules, 'required');
    $isFile = str_contains($rules, 'image');
    $isBool = str_contains($rules, 'boolean');
    $isDate = str_contains($rules, 'date');
    $isNumeric = str_contains($rules, 'numeric')
        || str_contains($rules, 'integer');
    $isEmail = str_contains($rules, 'email');
    $isPassword = $name === 'password'
        || str_contains($name, 'password');

    if ($isBool && $value === null) {
        $value = '1';
    }

    if (
        $options === null
        && preg_match('/(?:^|\|)in:([^|]+)/', $rules, $match)
    ) {
        $values = explode(',', $match[1]);

        $options = collect($values)
            ->mapWithKeys(function (string $item) {
                return [
                    $item => match ($item) {
                        'planned' => 'Planned',
                        'planted' => 'Planted',
                        'growing' => 'Growing',
                        'ready_to_harvest' => 'Siap Panen',
                        'harvested' => 'Sudah Dipanen',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        'active' => 'Aktif',
                        'inactive' => 'Nonaktif',
                        'super_admin' => 'Super Admin',
                        'operator_kwt' => 'Operator KWT',
                        'koperasi' => 'Koperasi',
                        default => ucfirst(
                            str_replace('_', ' ', $item)
                        ),
                    },
                ];
            })
            ->all();
    }

    $longFields = [
        'description',
        'content',
        'notes',
        'excerpt',
        'address',
        'cooperative_address',
        'site_description',
        'general_notes',
        'treatment',
        'fertilizer_notes',
        'pest_notes',
        'disease_notes',
        'status_reason',
        'whatsapp_note',
    ];

    $isLong = in_array($name, $longFields, true);

    $type = match (true) {
        $isFile => 'file',
        $isDate => 'date',
        $isNumeric => 'number',
        $isPassword => 'password',
        $isEmail => 'email',
        default => 'text',
    };

    $fieldLabel = $label
        ?? \App\Support\Resources::label($name);
@endphp

<div class="field {{ $isLong ? 'field-wide' : '' }}">
    <label for="{{ $id }}">
        {{ $fieldLabel }}

        @if($required)
            <span class="required">*</span>
        @endif
    </label>

    @if($isBool)
        <select
            id="{{ $id }}"
            name="{{ $name }}"
            @required($required)
        >
            <option
                value="1"
                @selected((string) $value === '1')
            >
                Aktif
            </option>

            <option
                value="0"
                @selected((string) $value === '0')
            >
                Nonaktif
            </option>
        </select>

    @elseif($options !== null)
        <select
            id="{{ $id }}"
            name="{{ $name }}"
            @required($required)
        >
            <option value="">
                Pilih {{ strtolower($fieldLabel) }}
            </option>

            @foreach($options as $key => $text)
                <option
                    value="{{ $key }}"
                    @selected((string) $value === (string) $key)
                >
                    {{ $text }}
                </option>
            @endforeach
        </select>

    @elseif($isLong)
        <textarea
            id="{{ $id }}"
            name="{{ $name }}"
            rows="4"
            @required($required)
        >{{ $value }}</textarea>

    @else
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="{{ $type }}"
            @if(!$isFile)
                value="{{
                    $value instanceof \DateTimeInterface
                        ? $value->format('Y-m-d')
                        : ($type === 'password' ? '' : $value)
                }}"
            @else
                accept="image/jpeg,image/png,image/webp"
            @endif

            @if($type === 'number')
                step="{{ str_contains($rules, 'integer') ? '1' : 'any' }}"

                @if($name === 'latitude')
                    min="-90"
                    max="90"
                @elseif($name === 'longitude')
                    min="-180"
                    max="180"
                @else
                    min="0"
                @endif
            @endif

            @required($required)
        >
    @endif

    @if($isFile)
        <small>JPG, PNG, WebP · Maksimal 4 MB</small>

        @if($value)
            <img
                class="field-preview"
                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($value) }}"
                alt="Foto saat ini"
            >
        @endif
    @endif

    @error($name)
        <small class="field-error">{{ $message }}</small>
    @enderror
</div>