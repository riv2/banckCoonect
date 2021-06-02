@if ($data && count($data))
<div class="table-responsive">
    <table class="table table-striped table-hover js-has-clickable-tr">
        <thead>
            @if (isset($thead) && $thead)
                {!! $thead !!}
            @elseif (isset($columns) && $columns)
            <tr>
                @foreach ($columns as $column)
                <th>{{ $column->title }}</th>
                @endforeach
            </tr>
            @endif
        </thead>
        <tbody>
            @foreach ($data as $row)
            <tr>
                @foreach ($format as $method => $format_type)
                <td class="">
                    @isset ($row->{$method})
                    @switch($format_type)
                        @case('image')
                            <img src="{{ $row->{$method} }}" alt="" class="img-thumbnail" style="max-height:100px">
                            @break

                        @case('number')
                            @number($row->{$method})
                            @break

                        @case('money')
                            @money($row->{$method})
                            @break

                        @case('format.datetime')
                            <x-format.datetime :value="$row->{$method}" format="d F Y H:i:s"/>
                            @break

                        @case('format.user-link')
                            <x-format.user-link :value="$row->{$method}" />
                            @break

                        @case('format.offer-link')
                            <x-format.offer-link :value="$row->{$method}" />
                            @break

                        @case('format.pp-link')
                            <x-format.pp-link :value="$row->{$method}" />
                            @break

                        @case('format.link-link')
                            <x-format.link-link :value="$row->{$method}" />
                            @break

                        @case('format.json')
                            <x-format.json :value="$row->{$method}" />
                            @break

                        @case('format.fee')
                            <x-format.fee :value="$row->{$method}" />
                            @break

                        @case('format.percentage')
                            <x-format.percentage :value="$row->{$method}" />
                            @break

                        @case('html')
                            {!! $row->{$method} !!}
                            @break

                        @case('html.banned-link')
                            <div class="banned-link">
                                {!! $row->{$method} !!}
                            </div>
                            @break

                        @default
                            {{ $row->{$method} }}
                            @break
                    @endswitch
                    @else
                    {{-- method_exists($row, $method) --}}
                    {{-- nf:{{ $method }} --}}
                    @endisset
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@if ($data instanceof Illuminate\Pagination\AbstractPaginator)
{{ $data->appends(request()->except('page'))->links() }}
@endif

@else
<div class="text-center text-secondary">
    <i class="far fa-lightbulb fa-7x mb-3"></i>
    <div>
        {!! $empty ?? __('components.table.no-records-found') . '!' !!}
    </div>
</div>
@endif



{{--
@if (Illuminate\Support\Str::startsWith($tc->type, 'format.'))
    <x-dynamic-component :component="$tc->type" :value="$row->{$method}" />
@endif
--}}
