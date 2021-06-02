@if (Route::has(auth()->user()->role . '.users.show'))
<a href="{{ route(auth()->user()->role . '.users.show', $user) }}" class="js-click-tr">{{ $user->email }}</a>
@else
{{ $user->email }}
@endif
