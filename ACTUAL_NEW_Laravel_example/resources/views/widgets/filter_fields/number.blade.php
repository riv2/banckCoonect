<input type="number" min="0" step="1" name="{{ $filter_name }}" class="form-control form-control-sm" value="{{ request()->get($filter_name, ($filter_field['default'] ?? null)) }}" placeholder="{{ $filter_field['title'] }}">
