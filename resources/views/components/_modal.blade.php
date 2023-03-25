<div {{ $attributes->except(['size', 'class', 'id', 'centered', 'color', 'title']) }}
    class="modal fade {{ $class ?? null }}" id="{{ $id ?? ($id = uniqid()) }}" tabindex="-1" role="dialog"
    aria-labelledby="{{ $id }}-label" aria-hidden="true">

    <div class="modal-dialog{{ $centered ?? false ? ' modal-dialog-centered' : null }}{{ $size ?? false ? " modal-$size" : null }}"
        role="document">
        <div class="modal-content">
            <div class="modal-header bg-{{ $color ?? 'primary' }} text-white text-capitalize">
                <h5 class="modal-title" id="{{ $id }}-label">{{ $title ?? null }}</h5>
                <button type="button" tabindex="-1" class="close text-white"
                    onclick="$(this.closest('.modal')).modal('hide')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-0">
                {{ $slot }}
            </div>

            <div class="modal-footer justify-content-between">
                @if (isset($footer))
                    {{ $footer }}
                @endif
            </div>
        </div>
    </div>
</div>
