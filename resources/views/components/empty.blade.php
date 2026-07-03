<div class="empty-state">
    <div class="empty-state-icon">
        <i class="fas fa-box-open"></i>
    </div>

    <div class="empty-state-title">
        {{ $title ?? 'Belum ada data' }}
    </div>

    <div class="empty-state-description">
        {{ $message ?? 'Silahkan tambahkan data untuk ditampilkan.' }}
    </div>

    @isset($action)
        <a href="{{ $action['url'] }}" class="btn btn-primary mt-2">
            <i class="{{ $action['icon'] }}"></i> {{ $action['label'] }}
        </a>
    @endisset
</div>
