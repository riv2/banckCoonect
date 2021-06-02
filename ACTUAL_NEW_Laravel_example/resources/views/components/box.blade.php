<div {{ $attributes->merge(['class' => 'mb-4']) }}>
    <div class="box">
        @if (isset($title) || isset($helptext))
        <div class="row border-bottom g-2 p-2 mx-0 justify-content-between @empty($helptext) align-items-center @endempty">
            <div class="col">
                @isset ($title)
                <h3>{{ $title }}</h3>
                @endisset
                @isset ($helptext)
                <div class="small">
                    {!! $helptext !!}
                </div>
                @endisset
            </div>
            @isset($rightblock)
            <div class="col-auto text-end small">
                {!! $rightblock !!}
            </div>
            @endisset
        </div>
        @endif
        <div class="box__content p-3">
            {{ $slot }}
        </div>
    </div>
</div>
