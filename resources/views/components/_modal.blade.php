<div class="modal fade {{ $class ?? null }}" id="{{ $id ?? $id=null }}" tabindex="-1" role="dialog" aria-labelledby="{{ $id }}-label" aria-hidden="">
    <div class="modal-dialog {{ $centered ?? false ? 'modal-dialog-centered' : null }}{{ $size ?? false ? "modal-$size" : null }}" role="document">
        <div class="modal-content">
            <div class="modal-header bg-{{ $color ?? 'primary' }} text-white">
                <h5 class="modal-title" id="{{ $id }}-label">{{ $title ?? null }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-0">
                {{ $slot }}
            </div>
            <div class="modal-footer justify-content-between">
                {{ $footer }}
            </div>
        </div>
    </div>
</div>
