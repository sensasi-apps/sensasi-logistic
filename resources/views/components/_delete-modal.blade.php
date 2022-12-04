<x-_modal id="{{ $id ?? ($id = null) }}" class="{{ $class ?? null }}" title="{{ __('Are you sure') }}?" color="danger">
    {{ __('This action can not be undone') }}.
    {{ __('Do you still want to delete') }} <b style="font-size: 1.2rem"></b>
    <form method="POST" id="{{ $formId ?? $formId = uniqid() }}">
        @csrf
        @method('delete')
    </form>

    @slot('footer')
        <button type="submit" form="{{ $formId }}" class="btn btn-danger">{{ __('Yes') }}</button>
        <button data-dismiss="modal" class="btn btn-secondary">{{ __('Cancel') }}</button>
    @endslot
</x-_modal>
