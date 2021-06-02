@if (Route::has(auth()->user()->role . '.users.show'))
    <span>
        #{{ $user->id }} <a href="{{ route(auth()->user()->role . '.users.show', $user) }}" class="js-click-tr" title="{{ __('manager.report.authorise.owner-pp') }}">{{ $user->email }}</a>
    </span>
@else
    {{ $user->email }}
@endif
