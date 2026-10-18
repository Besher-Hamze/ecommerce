@php
    $value = (float) ($rating ?? 0);
    $count = (int) ($count ?? 0);
@endphp
<span class="noor-stars" aria-label="{{ number_format($value, 1) }} out of 5">
    @for ($i = 1; $i <= 5; $i++)
        <i class="fa fa-star {{ $i <= round($value) ? '' : 'muted' }}"></i>
    @endfor
    @if($count > 0)
        <span class="text-muted small ms-1">({{ $count }})</span>
    @endif
</span>
