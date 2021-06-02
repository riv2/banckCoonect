<form method="post" action="{{ $route }}" enctype="multipart/form-data">
    @method('delete')
    @csrf
    <button type="submit" class="btn btn-primary btn-sm">{{ __('faq_category.delete') }}</button>
</form>
