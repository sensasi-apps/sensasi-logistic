<div class="alert alert-{{ $color ?? 'danger' }} alert-dismissible fade show">
    <div class="alert-body">
        <button class="close" data-dismiss="alert">
            <span>×</span>
        </button>
        {!! $message !!}
    </div>
</div>
