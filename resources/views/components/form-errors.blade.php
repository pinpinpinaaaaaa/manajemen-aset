@props(['title' => 'Ada yang perlu diperbaiki'])

@if ($errors->any())
    <div class="alert alert-danger" role="alert"
         style="background:#fff0f0;border:1px solid #f87171;border-radius:10px;padding:14px 18px;margin-bottom:1.25rem;color:#842029;">
        <strong style="display:block;margin-bottom:8px;">
            &#9888; {{ $title }}:
        </strong>
        <ul style="margin:0;padding-left:1.25rem;">
            @foreach ($errors->all() as $error)
                <li style="margin-bottom:3px;font-size:.88rem;">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
