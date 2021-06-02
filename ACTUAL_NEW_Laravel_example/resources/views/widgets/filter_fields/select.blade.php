<select class="form-select form-select-sm" name="{{ $filter_name }}">
    @if (empty($filter_field['default']))
        <option value="">Выберите вариант</option>
    @endif
    @foreach ($filter_field['options'] as $option_id => $option_value)
        <option
            value="{{ $option_id }}" {{ request()->get($filter_name, ($filter_field['default'] ?? null)) == $option_id ? 'selected' : '' }}>{{ $option_value }}</option>
    @endforeach
</select>
