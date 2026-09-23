<x-layout :admin="true" :title="$title">
    <div class="admin-title">
        <div>
            <span class="eyebrow">PENGELOLAAN DATA</span>
            <h1>{{ $record->exists ? 'Edit' : 'Tambah' }} {{ $title }}</h1>
            <p>Lengkapi isian berikut. Tanda * wajib diisi.</p>
        </div>

        <a class="btn btn-light" href="/admin/manage/{{ $resource }}">
            ← Kembali
        </a>
    </div>

    <form class="panel" method="post" enctype="multipart/form-data"
        action="/admin/manage/{{ $resource }}{{ $record->exists ? '/' . $record->id : '' }}">
        @csrf

        @if ($record->exists)
            @method('PUT')
        @endif

        <div class="form-grid">
            @foreach ($fields as $name => $rules)
                @php
                    $options = match ($name) {
                        'kwt_id' => $kwts->pluck('name', 'id')->all(),

                        'commodity_id' => $commodities
                            ->mapWithKeys(
                                fn($commodity) => [
                                    $commodity->id => $commodity->name,
                                ],
                            )
                            ->all(),

                        'commodity_category_id', 'category_id' => $categories->pluck('name', 'id')->all(),

                        'processed_product_category_id' => $categories->pluck('name', 'id')->all(),

                        'role' => [
                            'super_admin' => 'Super Admin',
                            'operator_kwt' => 'Operator KWT',
                            'koperasi' => 'Koperasi',
                        ],

                        default => null,
                    };

                    $defaults = [
                        'is_active' => 1,
                        'is_available' => 1,
                        'status' => $resource === 'cycles' ? 'planned' : null,
                        'planting_date' => now()->toDateString(),
                        'target_unit' => 'kg',
                        'default_unit' => 'kg',
                        'unit' => 'kg',
                    ];

                    $value = old($name, $record->{$name} ?? ($defaults[$name] ?? null));

                    $fieldRules = is_array($rules) ? implode('|', $rules) : $rules;
                @endphp

                <x-field :name="$name" :rules="$fieldRules" :value="$value" :options="$options" :label="\App\Support\Resources::label($name)" />
            @endforeach
        </div>

        <div class="actions">
            <button class="btn">
                Simpan {{ $resource === 'cycles' ? 'Budidaya' : 'Data' }} ✓
            </button>

            <a class="text-button" href="/admin/manage/{{ $resource }}">
                Batal
            </a>
        </div>
    </form>
</x-layout>
