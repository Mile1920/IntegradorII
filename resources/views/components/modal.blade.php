@props([ 'id', 'label' => null ])

@php
    $label = $label ?? ($id . 'Label');
@endphp

<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $label }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{ $slot }}
        </div>
    </div>
</div>
