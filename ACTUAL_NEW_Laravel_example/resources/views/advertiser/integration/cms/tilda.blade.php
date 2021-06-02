@extends('layouts.app')

@section('title')
    {{ __('advertiser.integration.cms.index.app-title') }}
    <span> / </span>
    {{ __('advertiser.integration.cms.tilda.title') }}
@endsection

@section('content')

    <x-box>
        <x-slot name="title">{{ __('advertiser.integration.cms.tilda.title') }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route("advertiser.servicedeskadv.create") }}?type=technical&subject=%D0%98%D0%BD%D1%82%D0%B5%D0%B3%D1%80%D0%B0%D1%86%D0%B8%D1%8F%20%D0%BF%D0%BE%20API"
               class="btn btn-primary btn-sm">{{ __('advertiser.integration.api.right-button') }}</a>
        </x-slot>


        @if(!isset($_GET['projectid']))
            <p>Данная инструкция предназначена для партнёрской программы с оплатой за <strong>лиды</strong>. Изменить
                эту настройку можно на
                <a href="{{ route('advertiser.settings.company.index') }}"
                   target="_blank">странице настройки партнёрской программы</a> выбрав значение <strong>Лиды</strong>
                в поле <strong>За что платим партнёрам?</strong>. </p>
            <img src="/storage/cms/tilda/tilda-step-0.png" alt="step-0" width="100%">
            <br><br>

            <h6>Шаг 1</h6>
            <p>Зайти в свой аккаунт Tilda. Перейти во вкладку «Мои сайты».</p>
            <img src="/storage/cms/tilda/tilda-step-1.png" alt="step-1" width="100%">
            <br><br>

            <h6>Шаг 2</h6>
            <p>Нажать на ссылку «Редактировать сайт» того проекта, который хотим интегрировать.</p>
            <img src="/storage/cms/tilda/tilda-step-2.png" alt="step-2" width="100%">
            <br><br>

            <h6>Шаг 3</h6>
            <p>Копируем id проекта из адресной строки (цифры). И вставляем в поле ниже.</p>
            <img src="/storage/cms/tilda/tilda-step-3.png" alt="step-3" width="100%">
            <br><br>

            <form action="{{ route('advertiser.integration.cms.tilda') }}" method="GET">
                <span>projectid=</span>
                <input type="text" name="projectid">
                <input type="submit" class="btn btn-sm btn-primary" value="Активировать инструкцию">
            </form>
        @else
            <h6>Шаг 4</h6>
            <p>
                <button class="btn btn-sm btn-primary btn-copy-pixel-code" data-code="{{ $pixelCode }}">Скопировать
                </button>
                код, вставить его в поле ввода текста на
                <a href="https://tilda.cc/projects/editheadcode/?projectid={{ $_GET['projectid'] }}" target="_blank">странице</a>
                и там же нажать кнопку "Сохранить".
            </p>
            <img src="/storage/cms/tilda/tilda-step-4.png" alt="step-4" width="100%">
            <br><br>

            <h6>Шаг 5</h6>
            <p>На сайте <strong>gocpa.cloud</strong> создаём оффер. Для этого переходим на эту
                <a href="{{ route('advertiser.offers.create') }}" target="_blank">страницу</a>, заполняем поля и
                нажимаем кнопку «Cохранить».</p>
            <img src="/storage/cms/tilda/tilda-step-5.png" alt="step-5" width="100%">
            <br><br>

            <h6>Шаг 6</h6>
            <p>На открывшейся странице выбираем «Разрешённые источники трафика» и нажимаем кнопку
                <strong>«Сохранить»</strong>.</p>
            <img src="/storage/cms/tilda/tilda-step-6.png" alt="step-6" width="100%">
            <br><br>

            <h6 id="step-7">Шаг 7</h6>
            <p>Приступайте к этому шагу только после выполнения предыдущего.</p>
            <ul>
                <li>Сформируйте Webhook</li>
                <li>Скопируйте его</li>
                <li>Вставьте в поле ввода текста на эту
                    <a href="https://tilda.cc/projects/forms/add/?type=webhook&projectid={{ $_GET['projectid'] }}"
                       target="_blank">страницу</a> и нажмите кнопку «Добавить».
                </li>
            </ul>
            <form action="{{ route('advertiser.integration.cms.tilda', 'projectid=' . $_GET['projectid']) }}#step-7"
                  method="POST"
                  class="d-inline-block">
                @csrf
                <input type="hidden" name="createWebhook" value="true">
                <input type="hidden" name="projectid" value="{{ $_GET['projectid'] }}">
                <input type="submit" class="btn btn-sm btn-primary" value="Сформировать Webhook">
            </form>

            <button class="btn btn-sm btn-primary btn-copy-webhook-link" data-webhook="{{ $webhookLink }}">
                Скопировать
            </button>
            <img src="/storage/cms/tilda/tilda-step-7.png" alt="step-7" width="100%">
            <br><br>

            <h6>Шаг 8</h6>
            <p>Перейти <a
                    href="https://tilda.cc/projects/settings/?projectid={{ $_GET['projectid'] }}#tab=ss_menu_forms"
                    target="_blank">страницу</a> и в разделе «Подключённые сервисы» найти только что добавленный
                Webhook. Нажать кнопку «Настройки».</p>
            <img src="/storage/cms/tilda/tilda-step-8.png" alt="step-8" width="100%">
            <br><br>

            <h6>Шаг 9</h6>
            <p>В открывшемся окне ставим все галочки: посылать Cookies, посылать UTM, передавать данные по товарам в
                заказе - массивом. И нажимаем кнопку <strong>«Сохранить»</strong></p>
            <img src="/storage/cms/tilda/tilda-step-9.png" alt="step-9" width="100%">
            <br><br>

            <h6>Шаг 10</h6>
            <p>Найти у себя на сайте блок с корзиной и нажать кнопку «Контент»</p>
            <img src="/storage/cms/tilda/tilda-step-10.png" alt="step-10" width="100%">
            <br><br>

            <h6>Шаг 11</h6>
            <p>Поставить галочку с только что созданным Webhook. И нажать кнопку «Сохранить и закрыть»</p>
            <img src="/storage/cms/tilda/tilda-step-11.png" alt="step-11" width="100%">
            <br><br>

            <h6>Шаг 12</h6>
            <p>Опубликовать все <a
                    href="https://tilda.cc/projects/?projectid={{ $_GET['projectid'] }}"
                    target="_blank">страницы</a> после сохранения, чтобы изменения применились.</p>
            <img src="/storage/cms/tilda/tilda-step-12.png" alt="step-12" width="100%">
            <br><br>

            <h6>Шаг 13</h6>
            <p>Оставить заказ на своём сайте. Перейти на <a
                    href="{{ route('advertiser.report') }}"
                    target="_blank">страницу</a> и увидеть, что заказ в системе отобразился.</p>
            <img src="/storage/cms/tilda/tilda-step-13.png" alt="step-13" width="100%">
            <br><br>
        @endif

    </x-box>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"
            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script>
        $(function () {
            $(".btn-copy-pixel-code").click(function () {
                navigator.clipboard.writeText($(this).data("code")).then(function () {
                    alert('Код скопирован в буффер обмена!');
                }, function (err) {
                    alert('{{ __('partners.links.alert.error') }}: ', err);
                });
            });
            $(".btn-copy-webhook-link").click(function () {
                navigator.clipboard.writeText($(this).data("webhook")).then(function () {
                    alert('Webhook ссылка скопирована в буффер обмена!');
                }, function (err) {
                    alert('{{ __('partners.links.alert.error') }}: ', err);
                });
            });
        })
    </script>
@endsection

