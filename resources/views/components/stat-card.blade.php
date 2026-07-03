@props([
    'label' => '',
    'value' => '',
    'id'    => null,
])

<div class="stat-card-unified">
    <div class="stat-card-label">{{ $label }}</div>
    <div class="stat-card-value" @if($id) id="{{ $id }}" @endif>{{ $value }}</div>
</div>
