@props(['align' => 'right', 'width' => '48', 'contentClasses' => ''])

@php
$menuAlign = $align === 'left' ? '' : 'dropdown-menu-end';
@endphp

<div class="dropdown">
    <div data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
        {{ $trigger }}
    </div>

    <div class="dropdown-menu {{ $menuAlign }} shadow {{ $contentClasses }}">
        {{ $content }}
    </div>
</div>
