<div class="container-fluid mb-3 py-3 bg-white border">
    <form method="get">
        <div class="row row-cols-5">
            @foreach($fields as $fieldID=>$field)
                <div class="col">
                    <div class="form-label-group mb-3">
                        @if($field["type"] == "select")
                            <select class="form-control form-control-sm" name="{{ $fieldID }}">
                                <option @if ("" === request($fieldID)) selected
                                        @endif value="">{{ __('helpers.filter.select') }}...
                                </option>
                                @foreach($field["options"] as $a=>$b)
                                    <option
                                        @if ($a == request($fieldID)) selected @endif
                                    value="{{ $a }}">{{ $b }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="{{ $field["type"] }}" class="form-control form-control-sm"
                                   name="{{ $fieldID }}" value="{{ request($fieldID) }}">
                        @endif
                        <label>{{ $field["title"] }}</label>
                    </div>
                </div>
            @endforeach
            <div class="col">
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-filter"></i>
                    {{ __('helpers.filter.apply') }}
                </button>
            </div>
        </div>
    </form>
</div>
