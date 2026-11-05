@props(['name' => 'arrow-right', 'size' => 20])
<svg aria-hidden="true" focusable="false" width="{{ $size }}" height="{{ $size }}"
     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"
     stroke-linecap="round" stroke-linejoin="round" {{ $attributes }}>
    @switch($name)
        @case('search')<circle cx="11" cy="11" r="7.5"/><path d="m16.5 16.5 4 4"/>@break
        @case('cart')<path d="M3 4h2l2.5 11h11.2l2.1-8H6"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/>@break
        @case('chevron-down')<path d="m6 9 6 6 6-6"/>@break
        @case('chevron-right')<path d="m9 5 7 7-7 7"/>@break
        @case('menu')<path d="M3 6h18M3 12h18M3 18h18"/>@break
        @case('close')<path d="M5 5l14 14M19 5 5 19"/>@break
        @case('shield')<path d="M12 2.5 20 6v6c0 5.3-3.7 8.2-8 10-4.3-1.8-8-4.7-8-10V6z"/><path d="m8.5 12 2.3 2.3 4.7-4.9"/>@break
        @case('check')<path d="m4 12 5 5L20 6"/>@break
        @case('package')<path d="m3 7 9-4 9 4v10l-9 4-9-4zM3 7l9 4 9-4M12 11v10"/>@break
        @case('file')<path d="M7 3h7l4 4v14H7zM14 3v5h4M10 12h5M10 16h5"/>@break
        @case('phone')<path d="M7 3H5c-1.5 0-2 1-2 2 0 9 7 16 16 16 1 0 2-.5 2-2v-2l-5-3-3 3c-3-1.5-5-3.5-6-6l3-3z"/>@break
        @case('filter')<path d="M4 6h16M7 12h10M10 18h4"/>@break
        @case('arrow-left')<path d="m11 5-7 7 7 7M4 12h16"/>@break
        @default <path d="M4 12h16m-6-6 6 6-6 6"/>
    @endswitch
</svg>
