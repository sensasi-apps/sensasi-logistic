<x-_modal {{ $attributes->except(['id', 'class', 'formId']) }} id="{{ $id ?? ($id = null) }}"
    class="{{ $class ?? null }}" title="{{ __('Are you sure') }}?" color="danger" centered>
    {{ __('This action can not be undone') }}.
    {{ __('Do you still want to delete') }} <b style="font-size: 1.2rem"></b>
    <form method="POST"
        {{ $attributes->has(':formId') ? ':' : '' }}id="{{ $formId ?? ($formId = $attributes->get(':formId') ?? uniqid()) }}">
        @csrf
        @method('delete')
    </form>

    @slot('footer')
        @if (isset($footer))
            {{ $footer }}
        @else
        <button type="submit" {{ $attributes->has(':formId') ? ':' : '' }}form="{{ $formId }}"
            class="btn btn-secondary">{{ __('Yes') }}</button>
        @endif
    @endslot
</x-_modal>
