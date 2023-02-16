<button {{ $attributes->except(['class', 'type', 'title']) }} tabindex="-1" type="{{ $type ?? 'button' }}"
    class="btn btn-secondary disabled {{ $class ?? null }}" title="{{ $title ?? __('Cannot delete this') }}">
    <i class="fas fa-trash"></i>
</button>
