@if(!empty($item))
@php
    $no = $no ?? 0;
@endphp
<tr id="oneItem-{{ $item->id }}">
    <td class="adminRedirect_table_number">
        {{ $no }}
    </td>
    <td class="adminRedirect_table_oldUrl">
        <a href="{{ $item->old_url }}" target="_blank" class="adminRedirect_table_url" title="{{ $item->old_url }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6M15 3h6v6M10 14L21 3"/>
            </svg>
            <span>{{ $item->old_url }}</span>
        </a>
    </td>
    <td class="adminRedirect_table_newUrl">
        <a href="{{ $item->new_url }}" target="_blank" class="adminRedirect_table_url" title="{{ $item->new_url }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6M15 3h6v6M10 14L21 3"/>
            </svg>
            <span>{{ $item->new_url }}</span>
        </a>
    </td>
    <td class="adminRedirect_table_actions">
        <button onclick="deleteItem({{ $item->id }})" class="adminButton adminButton--danger adminButton--sm" title="Xóa">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
            </svg>
            <span>Xóa</span>
        </button>
    </td>
</tr>
@endif
