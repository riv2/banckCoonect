<li class="nav-item dropdown">
    <a id="navbarDropdown" class="nav-link dropdown-toggle text-uppercase" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
        {{ App()->getLocale() }} <span class="caret"></span>
    </a>
    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href="{{ route('locale', ['locale' => 'en']) }}?redirect={{ request()->url() }}">EN</a>
        <a class="dropdown-item" href="{{ route('locale', ['locale' => 'ru']) }}?redirect={{ request()->url() }}">RU</a>
    </div>
</li>


