<div class="row mb-5" id="comment{{ $comment->id }}">
    <div class="col-12 col-md-1 text-center">
        @if ($comment->partner->role === 'manager')
            <i class="fas fa-user-graduate fa-3x fa-fw mt-4"></i>
            <div class="small">
                Служба поддержки
            </div>
        @elseif ($comment->partner->role === 'advertiser')
            <i class="fas fa-user-tie fa-3x fa-fw mt-4"></i>
            <div class="small">
                @if (auth()->user()->role === 'manager')
                    {!! $comment->partner->pp->short_name !!}<br/>
                    {!! $comment->partner->email !!}
                @else
                    Менеджер
                @endif
            </div>
        @else
            <i class="fas fa-user fa-3x fa-fw mt-4"></i>
            <div class="small">
                @if (auth()->user()->role === 'partner')
                {{ $comment->partner->email }}
                @else
                {!! $comment->partner->view_link !!}
                @endif
            </div>
        @endif
    </div>
    <div class="col-12 col-md-10">
        @if (auth()->user()->role !== 'partner' && $comment->partner->type !== 'partner' && !$comment->is_public)
        <div class="badge badge-dark">Не показывается партнеру</div>
        @endif

        <div class="small p-3 bg-light" style="min-height:100px;border-radius:5px">
            {!! nl2br(e($comment->body)) !!}
        </div>

        @if ($comment->attach)
        <div class="small">
            Прикрепленные файлы:
            @foreach ($comment->attach as $filename => $attach)
            @if (Storage::disk('public')->exists($attach))
            @php
                $mime = Storage::disk('public')->getMimeType($attach);
                $attach_link_attrs = Str::startsWith($mime, 'image/') ? 'data-fancybox' : 'download';
            @endphp
            <div>
                <a href="{{ Storage::disk('public')->url($attach) }}" {{ $attach_link_attrs }}>
                    {{ $filename }}
                </a>
            </div>
            @else
            <div>{{ $attach }}</div>
            @endif
            @endforeach
        </div>
        @endif
    </div>
    <div class="col-12 col-md-1 text-md-right">
        <div class="small text-secondary mb-4"><time>{{ Date::parse($comment->created_at)->format('j F Y H:i:s') }}</time></div>
        @if ($comment->edit_link)
            <a href="{{ $comment->edit_link }}" class="btn btn-sm btn-outline-primary">Редактировать</a>
        @endif
    </div>
</div>

