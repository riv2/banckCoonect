<div class="input-group">
    <input type="date" name="{{ $filter_name }}[gt]" class="form-control form-control-sm" value="{{ (request()->get($filter_name, ($filter_field['default'] ?? null))['gt'] ?? null) }}" max="{{ now()->toDateString() }}">
    <input type="date" name="{{ $filter_name }}[lt]" class="form-control form-control-sm" value="{{ (request()->get($filter_name, ($filter_field['default'] ?? null))['lt'] ?? null) }}" max="{{ now()->toDateString() }}">
</div>
