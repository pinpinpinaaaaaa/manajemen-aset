<form method="GET" action="{{ request()->url() }}" class="d-inline-flex align-items-center gap-2">
    @foreach(request()->except('per_page', 'page') as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach
    <span class="text-muted small">Tampilkan</span>
    <select name="per_page" onchange="this.form.submit()" class="form-select form-select-sm" style="width:auto">
        @foreach([50, 100, 200] as $opt)
            <option value="{{ $opt }}" {{ ($perPage ?? 50) == $opt ? 'selected' : '' }}>{{ $opt }}</option>
        @endforeach
    </select>
    <span class="text-muted small">data</span>
</form>
