@isset($filter_fields)
    <form action="" method="get">
        <div class="row no-gutters row-cols-2 row-cols-md-4">
            @foreach ($filter_fields as $filter_name => $filter_field)
                <div class="col px-1 mb-2">
                    @if($filter_field['type'] == 'checkbox')
                        <div class="form-check form-switch p-0 h-100 d-flex align-items-center">
                            @include('widgets.filter_fields.' . $filter_field['type'])
                            <label class="form-check-label d-flex mx-2">{{ $filter_field['title'] }}</label>
                        </div>
                    @else
                        <div class="form-label-group form-label-group-sm mb-0">
                            @include('widgets.filter_fields.' . $filter_field['type'])
                            <label>{{ $filter_field['title'] }}</label>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="w-100"></div>
        <div class="col px-1 mb-2">
            <button type="submit" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-filter"></i>
                {{ __('servicedeskTaskFilter.form.apply') }}
            </button>
        </div>

    </form>
@endisset
