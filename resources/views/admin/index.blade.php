<x-layout :admin="true" :title="$title">
    <div class="admin-title">
        <div>
            <span class="eyebrow">KEBUN & KOMUNITAS</span>
            <h1>{{ $title }}</h1>
            <p>Data terkelola, komunitas semakin berdaya.</p>
        </div>

        @php
            $user = auth()->user();

            $canCreate = $user
                && ! (
                    $user->isOperatorKwt()
                    && $resource === 'kwts'
                )
                && ! (
                    $user->isKoperasi()
                    && $resource !== 'products'
                );
        @endphp

        @if($canCreate)
            <a
                href="{{ url('/admin/manage/' . $resource . '/create') }}"
                class="btn"
            >
                +
                {{ $resource === 'cycles'
                    ? 'Mulai Budidaya'
                    : 'Tambah Data' }}
            </a>
        @endif
    </div>

    <x-filters
        :status="$resource === 'cycles'
            ? true
            : ($resource === 'kwts'
                ? ['active', 'inactive']
                : false)"
        :period="$resource === 'cycles'"
        :commodity="$resource === 'cycles'"
        :location="$resource === 'kwts'"
        :kwt="in_array($resource, ['cycles', 'products'], true)"
    />

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>
                        {{ $resource === 'cycles'
                            ? 'Siklus / Komoditas'
                            : 'Nama / Judul' }}
                    </th>

                    @if(! in_array($resource, [
                        'kwts',
                        'commodities',
                        'users',
                        'commodity-categories',
                        'product-categories',
                    ], true))
                        <th>KWT</th>
                    @endif

                    <th>Informasi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($records as $record)
                    @php
                        $status = match ($resource) {
                            'kwts', 'commodities' => $record->is_active
                                ? 'active'
                                : 'inactive',

                            'products' => $record->is_available
                                ? 'available'
                                : 'not_available',

                            default => $record->status ?? 'active',
                        };

                        $canEdit = $resource === 'users'
                            || str_ends_with($resource, 'categories')
                            || auth()->user()->can('update', $record);
                    @endphp

                    <tr>
                        <td>
                            <strong>
                                {{ $record->name
                                    ?? $record->title
                                    ?? $record->code }}
                            </strong>

                            @if($resource === 'cycles')
                                <br>

                                <span class="muted">
                                    {{ $record->commodity?->name ?? '—' }}
                                </span>
                            @endif
                        </td>

                        @if(! in_array($resource, [
                            'kwts',
                            'commodities',
                            'users',
                            'commodity-categories',
                            'product-categories',
                        ], true))
                            <td>
                                {{ $record->kwt?->name ?? '—' }}
                            </td>
                        @endif

                        <td>
                            @switch($resource)
                                @case('kwts')
                                    {{ $record->district ?? '—' }}
                                    ·
                                    {{ $record->village ?? '—' }}
                                    <br>
                                    Ketua:
                                    {{ $record->leader_name ?? '—' }}
                                    @break

                                @case('commodities')
                                    Kode:
                                    {{ $record->code ?? '—' }}
                                    <br>
                                    Masa tumbuh:
                                    {{ $record->default_growth_days ?? '—' }}
                                    hari
                                    @break

                                @case('cycles')
                                    Panen:
                                    {{ $record->estimated_harvest_date?->format('d M Y') ?? '—' }}
                                    <br>
                                    Target:
                                    {{ $record->target_quantity !== null
                                        ? number_format(
                                            $record->target_quantity,
                                            2,
                                            ',',
                                            '.'
                                        )
                                        : '—' }}
                                    {{ $record->target_unit ?? '' }}
                                    @break

                                @case('products')
                                    Stok:
                                    {{ $record->stock !== null
                                        ? number_format(
                                            $record->stock,
                                            2,
                                            ',',
                                            '.'
                                        )
                                        : '—' }}
                                    {{ $record->unit ?? '' }}
                                    @break

                                @case('users')
                                    {{ $record->email }}
                                    <br>

                                    {{ $record->roles
                                        ->pluck('name')
                                        ->implode(', ') ?: '—' }}
                                    @break

                                @case('commodity-categories')
                                @case('product-categories')
                                    Kategori
                                    @break

                                @default
                                    {{ $record->created_at?->format('d M Y') ?? '—' }}
                            @endswitch
                        </td>

                        <td>
                            <x-badge :status="$status" />
                        </td>

                        <td>
                            <div class="actions">
                                @if($resource === 'cycles')
                                    <a
                                        href="{{ url('/admin/cycles/' . $record->id) }}"
                                    >
                                        Lihat detail ↗
                                    </a>
                                @endif

                                @if($canEdit)
                                    <a
                                        href="{{ url(
                                            '/admin/manage/'
                                            . $resource
                                            . '/'
                                            . $record->id
                                            . '/edit'
                                        ) }}"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        action="{{ url(
                                            '/admin/manage/'
                                            . $resource
                                            . '/'
                                            . $record->id
                                        ) }}"
                                        method="POST"
                                        data-confirm="Hapus atau nonaktifkan data ini?"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="text-button danger"
                                        >
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td
                            colspan="5"
                            class="empty"
                        >
                            Belum ada data.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $records->links() }}
</x-layout>