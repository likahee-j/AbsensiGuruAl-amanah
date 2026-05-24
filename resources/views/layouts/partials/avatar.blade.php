@php($size = $size ?? 40)
@if($user?->photo)
    <img src="{{ asset('storage/'.$user->photo) }}" alt="{{ $user->name }}"
         class="rounded-circle object-fit-cover" style="width:{{ $size }}px;height:{{ $size }}px;">
@else
    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-secondary"
          style="width:{{ $size }}px;height:{{ $size }}px;">
        <i class="bi bi-person-fill" style="font-size:{{ round($size * 0.55) }}px;"></i>
    </span>
@endif
