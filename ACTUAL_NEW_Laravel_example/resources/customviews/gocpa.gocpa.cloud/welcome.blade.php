<?php
$page = [];
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Партнерская программа GoCPA Cloud</title>
    <meta name="Description" content="Партнерская программа GoCPA Cloud">
    <meta name="Keywords" content="Партнерская программа GoCPA Cloud">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script>
        new WOW({
            offset:       100,
        }).init();
    </script>
    <style>
        body {
            font-family:Poppins, sans-serif;
        }
        .logo {
            width: 200px;
            height: 40px;
            background: url('https://gocpa.ru/img/logo_text_dark.png');
            cursor: pointer;
        }
        .btn_theme {
            color: #fff;
            margin-top: 0;
            border: 1px solid #5d26d9;
            padding: 12px 35px;
            background-color: #5d26d9;
            border-radius: 5px;
        }
        .btn_theme:hover {
            color: #fff;
            text-decoration: none;
        }
        a {
            color: #5d26d9;
        }
        .banner {
            height:210px;
            width:100%;
            background-image: url('https://gocpa.ru/img/cloud/bg1.png');
        }
        .aboutblok2 {
            height:400px;
            width:100%;
            background-image: url('https://gocpa.ru/img/cloud/bg2.jpg');
        }

        .aboutblok4 {
            background: url('https://gocpa.ru/img/cloud/bg2.png') top repeat-x;
            background-color:#ededed;
        }
        .aboutblok5 {
            height:400px;
            width:100%;
            background: #fff;
            background-image: url('https://gocpa.ru/img/cloud/bg3.jpg');
            background-size: cover;
        }
        .txtgreen {
            color: #0abc5f;
        }
        .text-sm {font-size:0.95rem}
        h1 {font-size:1.6rem;} /*1rem = 16px*/
        h2 {font-size:1.4rem;} /*1rem = 16px*/
        h5 {font-size:0.9rem;font-weight:600} /*1rem = 16px*/

        @media (min-width: 544px) {
            h1 {font-size:1.6rem;} /*1rem = 16px*/
            h2 {font-size:1.4rem;} /*1rem = 16px*/
            h5 {font-size:1rem;} /*1rem = 16px*/
        }
        @media (min-width: 768px) {
            h1 {font-size:2rem;} /*1rem = 16px*/
            h2 {font-size:1.5rem;} /*1rem = 16px*/
            h5 {font-size:1rem;} /*1rem = 16px*/
            .aboutblok3 {margin-top:200px}
        }
        @media (min-width: 992px) {
            h1 {font-size:2.4rem;} /*1rem = 16px*/
            h2 {font-size:2rem;} /*1rem = 16px*/
            h5 {font-size:1.1rem;} /*1rem = 16px*/
        }
        @media (min-width: 1200px) {
            h1 {font-size:2.6rem;} /*1rem = 16px*/
            h2 {font-size:2.2rem;} /*1rem = 16px*/
            h5 {font-size:1.2rem;} /*1rem = 16px*/
        }



        .tml.element-two>.tml-item {
            position: relative;
            padding-top: 60px
        }

        .tml.element-two>.tml-item>.tml-item-line {
            display: block;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 50%;
            z-index: 0;
            width: 2px;
            margin-left: -1px;
            background-color: #cacdd4
        }

        .tml.element-two>.tml-item>.tml-item-line:before,
        .tml.element-two>.tml-item>.tml-item-line:after {
            position: absolute;
            display: block;
            content: " ";
            width: 10px;
            height: 10px;
            margin-left: -4px;
            border-radius: 50%
        }

        .tml.element-two>.tml-item>.tml-item-line:before {
            top: 0;
            background-color: #cacdd4
        }

        .tml.element-two>.tml-item>.tml-item-line:after {
            bottom: 0
        }

        .tml.element-two>.tml-item:not(:first-child)>.tml-item-line:before,
        .tml.element-two>.tml-item:not(:last-child)>.tml-item-line:after {
            display: none
        }

        .tml.element-two > .tml-item > .tml-item-line:after{
            background-color: #0abc5f ;
        }

        .tml.element-two>.tml-item>.tml-item-dot {
            display: block;
            position: absolute;
            top: 70px;
            left: 50%;
            z-index: 1;
            content: " ";
            width: 20px;
            height: 20px;
            margin-left: -10px;
            background-color: #fff;
            border-width: 5px;
            border-style: solid;
            border-radius: 50%;
            border-color: #0abc5f;
        }

        .tml.element-two>.tml-item>.row {
            margin-left: -35px;
            margin-right: -35px
        }

        .tml.element-two>.tml-item>.row>[class*='col-'] {
            padding-left: 35px;
            padding-right: 35px
        }

        .tml.element-two>.tml-item:nth-child(even)>.row>[class*='col-']:first-child {
            float: right
        }

        .tml.element-two>.tml-item:nth-child(even)>.row>[class*='col-']:last-child {
            float: left
        }

        .tml.element-two>.tml-item .tml-item-datestamp {
            margin-bottom: 30px;
            padding-left: 10px;
            padding-right: 10px;
            text-align: right
        }

        .tml.element-two>.tml-item:nth-child(even) .tml-item-datestamp {
            text-align: left
        }

        .tml.element-two>.tml-item .tml-item-datestamp>.holder {
            position: relative;
            display: inline-block;
            vertical-align: top;
            padding: 7px 15px 7px 15px;
            background-color: #cacdd4;
            font-weight: 700;
            font-size: 15px;
            color: #030712;
            line-height: 28px
        }

        .tml.element-two>.tml-item .tml-item-datestamp>.holder:after {
            position: absolute;
            top: 50%;
            left: 100%;
            margin-top: -10px;
            width: 0;
            height: 0;
            content: " ";
            border: solid transparent;
            border-color: transparent;
            border-left-color: #cacdd4;
            border-width: 10px;
            pointer-events: none
        }

        .tml.element-two>.tml-item:nth-child(even) .tml-item-datestamp>.holder:after {
            right: 100%;
            left: auto;
            border-right-color: #cacdd4;
            border-left-color: transparent
        }

        .tml.element-two>.tml-item .tml-item-datestamp>.holder strong {
            font-weight: inherit
        }

        .tml.element-two>.tml-item .tml-item-pic {
            margin-bottom: 22px;
            text-align: right
        }

        .tml.element-two>.tml-item:nth-child(even) .tml-item-pic {
            text-align: left
        }

        .tml.element-two>.tml-item .tml-item-data {
            text-align: left
        }

        .tml.element-two>.tml-item:nth-child(even) .tml-item-data {
            text-align: right
        }

        .tml.element-two>.tml-item .tml-item-data>*:last-child {
            margin-bottom: 0 !important
        }

        .tml.element-two>.tml-item .tml-item-data .title {
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 20px;
            color: #171c30;
            line-height: 30px
        }

        .tml.element-two>.tml-item .tml-item-data p {
            margin-bottom: 20px
        }

        @media screen and (min-width: 480px) and (max-width: 767px) {
            .tml.element-two>.tml-item>.tml-item-line,
            .tml.element-two>.tml-item>.tml-item-dot {
                display: none
            }
            .tml.element-two>.tml-item .tml-item-datestamp,
            .tml.element-two>.tml-item:nth-child(even) .tml-item-datestamp,
            .tml.element-two>.tml-item .tml-item-data,
            .tml.element-two>.tml-item:nth-child(even) .tml-item-data {
                text-align: center
            }
            .tml.element-two>.tml-item .tml-item-datestamp>.holder:after,
            .tml.element-two>.tml-item:nth-child(even) .tml-item-datestamp>.holder:after {
                display: none
            }
        }

        @media screen and (min-width: 321px) and (max-width: 479px) {
            .tml.element-two>.tml-item>.tml-item-line,
            .tml.element-two>.tml-item>.tml-item-dot {
                display: none
            }
            .tml.element-two>.tml-item .tml-item-datestamp,
            .tml.element-two>.tml-item:nth-child(even) .tml-item-datestamp,
            .tml.element-two>.tml-item .tml-item-data,
            .tml.element-two>.tml-item:nth-child(even) .tml-item-data {
                text-align: center
            }
            .tml.element-two>.tml-item .tml-item-datestamp>.holder:after,
            .tml.element-two>.tml-item:nth-child(even) .tml-item-datestamp>.holder:after {
                display: none
            }
        }

        @media screen and (max-width: 320px) {
            .tml.element-two > .tml-item > .tml-item-line,
            .tml.element-two > .tml-item > .tml-item-dot {
                display: none
            }

            .tml.element-two > .tml-item .tml-item-datestamp,
            .tml.element-two > .tml-item:nth-child(even) .tml-item-datestamp,
            .tml.element-two > .tml-item .tml-item-data,
            .tml.element-two > .tml-item:nth-child(even) .tml-item-data {
                text-align: center
            }

            .tml.element-two > .tml-item .tml-item-datestamp > .holder:after,
            .tml.element-two > .tml-item:nth-child(even) .tml-item-datestamp > .holder:after {
                display: none
            }
        }

    </style>
</head>
<body>
<header>
    <div class="container">
        <div class="row">
            <div class="col">
                <nav id="menu" class="navbar navbar-expand-lg navbar-light p-0 pt-3 pb-3">
                    <div class="navbar-brand logo" onclick="location.href='/'"></div>
                    <span class="text-uppercase" style="color:#624ae5;">партнерская<br/>программа</span>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <button class="btn_theme" style="padding:6px 12px" data-toggle="modal" data-target="#exampleModal">Войти</button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</header>

<!--
<section class="banner mb-5">
    <div class="container">
        <div class="row p-5">
            <div class="col text-center text-white p-4">
                <h1 class="font-weight-bold">Партнерская программа GoCPA</h1>
            </div>
        </div>
    </div>
</section>
-->

<section class="aboutblok mb-5">
    <div class="container">
        <div class="row mb-5 pt-4 pb-4">
            <div class="col-12 col-md-7 pt-3">
                <p class="wow slideInLeft text-uppercase font-weight-bold">ЗАРАБАТЫВАЙТЕ ВМЕСТЕ С НАМИ</p>
                <h2 class="wow slideInLeft font-weight-bold">Регистрируйтесь в партнёрской программе и получайте вознаграждение за продажи платных тарифов</h2>
                <p class="wow slideInLeft text-sm">
                    Наша партнёрская программа подойдёт всем, кто готов продвигать b2b-продукты. Целевая аудитория: владельцы бизнеса, руководители направлений маркетинга и продаж, менеджеры, отвечающие за продвижение и рост, и все, кто продаёт что-либо в онлайне: от интернет-магазинов до страховок и консультаций.
                </p>
                <p class="wow slideInLeft text-sm">
                    Работаете с бизнесами, обучаете маркетологов или занимаетесь привлечением трафика? Рекомендуйте своим пользователям платформу для партнёрского маркетинга GoCPA и зарабатывайте вместе с нами.
                </p>
                <br/>
                <a href="/register" class="btn_theme">Стать партнером</a>
            </div>
            <div class="d-none d-md-block col-md-5 wow slideInRight pt-5">
                <img src="https://gocpa.ru/img/cloud/s1.png" class="img-fluid">
            </div>
        </div>
    </div>
</section>

<section class="aboutblok2 mb-5 d-none d-md-block">
    <div class="container">
        <div class="row p-5">
            <div class="col text-center text-white p-4">
                <h1 class="font-weight-bold">Почему стоит работать с нами</h1>
                <br/>
                <p class="text-sm">Начните зарабатывать с GoCPA, разместив информацию о нашем продукте на своём сайте, в блоге, видео, странице в соцсети или другом ресурсе.</p>
            </div>
        </div>
        <div class="row p-5" style="margin-top:-100px;">
            <div class="col p-2">
                <div class="col text-center bg-white shadow p-4 wow slideInLeft">
                    <img src="https://gocpa.ru/img/cloud/sminfo1.png"><br/><br/>
                    <h5>Быстрый старт</h5>
                    <p class="text-sm">Зарегистрируйтесь, выберите промо-материал, скопируйте свою партнёрскую ссылку и начните зарабатывать</p>
                </div>
            </div>
            <div class="col p-2">
                <div class="col text-center bg-white shadow p-4 wow slideInUp">
                    <img src="https://gocpa.ru/img/cloud/sminfo2.png"><br/><br/>
                    <h5>Удобные выплаты</h5>
                    <p class="text-sm">Минимальная сумма для выплаты начинается от 5 000 рублей, мы отправляем вознаграждение 2 раза в месяц</p>
                </div>
            </div>
            <div class="col p-2">
                <div class="col text-center bg-white shadow p-4 wow slideInRight">
                    <img src="https://gocpa.ru/img/cloud/sminfo3.png"><br/><br/>
                    <h5>Прозрачная статистика</h5>
                    <p class="text-sm">Отслеживайте посетителей, регистрации и оплаты в личном кабинете, данные передаются в реальном времени</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="aboutblok3 mb-5">
    <div class="container">
        <div class="row mb-5 pt-4 pb-4">
            <div class="d-none d-md-block col-md-5 wow slideInLeft pt-5">
                <img src="https://gocpa.ru/img/cloud/s2.png" class="img-fluid">
            </div>

            <div class="col-12 col-md-7 pt-3">
                <h2 class="wow slideInRight font-weight-bold">Быть партнёром GoCPA — выгодно</h2>
                <div class="row mt-5 mb-3">
                    <div class="col wow slideInRight">
                        <span class="font-weight-bold"><span class="txtgreen">01.</span> Минимальный гарантированный доход 30%</span><br/>
                        <p class="text-sm pt-2">Гарантированное вознаграждение для всех партнёров начинается от 30% и растёт по мере выполнения KPI</p>
                    </div>
                    <div class="col wow slideInRight">
                        <span class="font-weight-bold"><span class="txtgreen">02.</span> Высокий средний чек</span><br/>
                        <p class="text-sm pt-2">Наш популярный тариф - Professional - стоит 50 000 рублей, это означает, что при его оплате вы заработаете минимум 15 000 рублей</p>
                    </div>
                </div>
                <div class="row mb-5">
                    <div class="col wow slideInRight">
                        <span class="font-weight-bold"><span class="txtgreen">03.</span> Cookie - 90 дней</span><br/>
                        <p class="text-sm pt-2">Решение о переходе на платный тариф может занять у потенциального клиента много времени, поэтому мы долго храним данные атрибуции.</p>
                    </div>
                    <div class="col wow slideInRight">
                        <span class="font-weight-bold"><span class="txtgreen">04.</span> Неограниченный объём</span><br/>
                        <p class="text-sm pt-2">Чем больше — тем лучше! Приводите к нам пользователей в любом количестве, мы заплатим за каждого клиента, оформившего платный тариф.</p>
                    </div>
                </div>
                <a href="/register" class="btn_theme">Стать партнером</a>
            </div>
        </div>
    </div>
</section>

<section class="aboutblok4">
    <div class="container">
        <div class="row" style="padding-top:200px">
            <div class="col text-center">
                <p class="wow slideInUp text-uppercase font-weight-bold">КАК НАЧАТЬ ЗАРАБАТЫВАТЬ</p>
                <h2 class="wow slideInUp font-weight-bold">3 простых шага:</h2>
            </div>
        </div>
        <div class="row text-center">
            <div class="tml element-two p-5">
                <!-- tml-item -->
                <div class="tml-item">
                    <div class="tml-item-line"></div>
                    <div class="tml-item-dot"></div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 wow slideInLeft">
                            <div class="tml-item-datestamp">
                                <div class="holder">Шаг 1</div>
                            </div>
                        </div><div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 wow slideInRight">
                            <div class="tml-item-data">
                                <h4 class="title">Регистрация</h4>
                                <p>Заполните короткую форму и станьте участником партнерской программы GoCPA</p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <!-- tml-item -->
                <!-- tml-item -->
                <div class="tml-item">
                    <div class="tml-item-line"></div>
                    <div class="tml-item-dot"></div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 wow slideInLeft">
                            <div class="tml-item-data">
                                <h4 class="title">Получение ссылки</h4>
                                <p>Получите ссылку на посадочную страницу и разместите на своём ресурсе</p>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 wow slideInRight">
                            <div class="tml-item-datestamp">
                                <div class="holder">Шаг 2</div>
                            </div>
                        </div>

                        <div class="clearfix"></div>
                    </div>
                </div>
                <!-- tml-item -->
                <!-- tml-item -->
                <div class="tml-item">
                    <div class="tml-item-line"></div>
                    <div class="tml-item-dot"></div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 wow slideInLeft">
                            <div class="tml-item-datestamp">
                                <div class="holder">Шаг 3</div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 wow slideInRight">
                            <div class="tml-item-data">
                                <h4 class="title">Получайте вознаграждение</h4>
                                <p>Следите за статистикой и забирайте вознаграждение за пользователей, которые оформили платный тариф</p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <!-- tml-item -->
            </div>

        </div>
    </div>
</section>

<section class="aboutblok5 mb-5">
    <div class="container">
        <div class="row pt-5">
            <div class="col-12 col-md-9 pt-5">
                <h2 class="font-weight-bold text-white">Зарегистрируйтесь сейчас и начните зарабатывать</h2>
                <br/>
                <a href="/register" class="btn_theme" style="background: #0abc5f">Стать партнером</a>
            </div>
            <div class="col-3 d-none d-md-block wow slideInUp">
                <img src="https://gocpa.ru/img/cloud/rocket.png">
            </div>
        </div>

    </div>
</section>

<footer>
    <div class="container">
        <div class="row pb-5">
            <div class="col">
                <div class="logo" onclick="location.href='/'"></div>
            </div>
            <div class="col text-right">
                © 2017 - 2021
            </div>
        </div>
    </div>
</footer>


<div class="modal fade mt-5" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Вход для партнеров</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group row">
                        <label for="email" class="col-md-4 col-form-label text-md-right">Email</label>

                        <div class="col-md-6">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password" class="col-md-4 col-form-label text-md-right">{{ __("Пароль") }}</label>

                        <div class="col-md-6">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6 offset-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                <label class="form-check-label" for="remember">
                                    {{ __("Запомнить меня") }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-8 offset-md-4">
                            <button type="submit" class="btn btn-primary">
                                {{ __("Вход") }}
                            </button>

                            @if (Route::has('password.request'))
                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    {{ __("Забыли пароль?") }}
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

