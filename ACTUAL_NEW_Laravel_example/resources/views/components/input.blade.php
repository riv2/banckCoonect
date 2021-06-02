@php
    $type = $attributes->get('type');
    $name = $attributes->get('name');
    $value = old($name, $attributes->get('value'));
    $errorclass = ($errors->has($name)) ? ' is-invalid' : '';

@endphp
<div class="row">
    <div class="col-md-6 mb-3">
        <div class="form-label-group {{$type === 'file' ? 'form-label-group-file' : ''}}">

            @if ($slot)
                <label class="form-label">{!! $slot !!}</label>
            @endif

            @if ($type == 'file')
                <input {{ $attributes->merge(['class' => 'form-control ' . $errorclass, 'id' => 'input_upload_' . $name]) }}>

                <div class="my-2 w-25">
                    <img src="{{ $attributes->get('value') }}" alt="{{ strip_tags($help) }}"
                         class="img-fluid img-thumbnail @if (!$attributes->get('value')) d-none @endif">
                </div>
            @elseif ($type == 'textarea')
                <textarea {{ $attributes->except(['value'])->merge(['class' => 'form-control' . $errorclass]) }}>{!! $value !!}</textarea>
            @elseif ($type == 'select')
                <select {{ $attributes->except(['data-nullvalue', 'value', 'options'])->merge(['class' => 'form-select' . $errorclass]) }}>
                    @if ($attributes->offsetExists('data-nullvalue'))
                        @php
                            $optv = $attributes->get('data-nullvalue');
                            if (is_bool($optv)) {
                                $optv = '[выберите вариант]';
                            }
                        @endphp
                        <option value="">{{ $optv }}</option>
                    @endif

                    @foreach ($attributes->get('options', []) as $optk => $optv)
                        <option value="{{ $optk }}" @if ($optk == $value) selected @endif>{{ $optv }}</option>
                    @endforeach
                </select>
            @else
                <input {{ $attributes->merge(['class' => 'form-control' . $errorclass]) }}>
            @endif
            @if ($errors->has($name))
                <div class="invalid-feedback">
                    {{ $errors->first($name) }}
                </div>
            @endif
        </div>
    </div>
    @isset ($help)
        <div class="col-md-6 small">
            <section class="pt-md-2">
                {!! $help !!}
            </section>
        </div>
    @endisset
</div>
