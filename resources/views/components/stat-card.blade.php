@props([
    'label' => '',
    'value' => '',
    'id'    => null,
    'href'  => null,
])

@if ($href)
<a href="{{ $href }}" class="stat-card-unified stat-card-link" style="text-decoration:none;display:block;">
@else
<div class="stat-card-unified">
@endif
    <div class="stat-card-label">{{ $label }}</div>
    <div class="stat-card-value" @if($id) id="{{ $id }}" @endif>{{ $value }}</div>
@if ($href)
</a>
@else
</div>
@endif
