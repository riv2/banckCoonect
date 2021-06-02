<?php
?>

<h2>Phantom</h2>

<div class="box">
    <div class="box-header with-border">
        <strong>Селекторы</strong>
    </div>
    <div class="box-body">
        <p>
             <a href="https://developer.mozilla.org/ru/docs/Web/CSS/CSS_%D0%A1%D0%B5%D0%BB%D0%B5%D0%BA%D1%82%D0%BE%D1%80%D1%8B">Документаия по селекторам</a>
        </p>
    </div>
</div>

<div class="box">
    <div class="box-header with-border">
        <strong>Значения поля "Цель (html или аттрибут)"</strong>
    </div>
    <table class="box-body table table-bordered table-striped">
        <tbody>
            <tr>
                <th colspan="2" class="text-center">Определение источник (места откуда парсить)</th>
            </tr>
            <tr>
                <th style="width: 20%;">Значение</th>
                <th>Описание</th>
            </tr>
            <tr>
                <td>[пусто]</td>
                <td>Внутренний HTML тега</td>
            </tr>
            <tr>
                <td>[название аттрибута]</td>
                <td>Значение аттрибута тега</td>
            </tr>
            <tr>
                <th colspan="2" class="text-center">Модификаторы значения (спарсенного)</th>
            </tr>
            <tr>
                <th>Символ</th>
                <th>Описание</th>
            </tr>
            <tr>
                <td>^</td>
                <td>убрать теги</td>
            </tr>
            <tr>
                <td>=</td>
                <td>убрать всё кроме цифр</td>
            </tr>
            <tr>
                <td>.</td>
                <td>убрать точки</td>
            </tr>
            <tr>
                <td>,</td>
                <td>убрать запятые</td>
            </tr>
            <tr>
                <td>~</td>
                <td>убрать пробелы</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="box">
    <div class="box-header with-border">
        <strong>Антикапча</strong>
    </div>
    <div class="box-body">
        <p>Ключ антикапчи находится в <a href="http://pricing.vseinstrumenti.ru/robot/update?id=10.254.12.180">настройках</a> каждого из роботов.</p>
        <p>Отчет по работе антикапчи можно посмотреть <a href="http://pricing.vseinstrumenti.ru/crud-anti-captcha-task?sort=-created_at">тут</a>.</p>
        <p>
            Для того чтобы добавить в маску антикапчу, нужно добавить доп. маску типа "Скрипт" и вписать туда следующий код:
        </p>
        <p class="well well-sm">
            return window.standartCaptcha('<b>img.class</b>','<b>input.class</b>','<b>button.class</b>');
        </p>
        <div>где</div>
        <ul>
            <li><b>img.class</b> - селектор картинки капчи (элемента  img)</li>
            <li><b>input.class</b> - селектор текстового поля для вставки отвта</li>
            <li><b>button.class</b> - селектор кнопки, на которую надо нажать после ввода капчи</li>
        </ul>
        <p>
            Для яндекс.маркета данные параметры можно опустить.
        </p>
        <p class="well well-sm">
            return window.standartCaptcha();
        </p>

    </div>
</div>

<h2>Простые маски</h2>
<div class="box">
    <div class="box-header with-border">
        Служебные символы
    </div>
        <table class="box-body table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Символ</th>
                    <th>Описание</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>***</td>
                    <td>Последовательность <b>любых символов</b> длинной от <b>0</b> до &infin;</td>
                </tr>
                <tr>
                    <td>**</td>
                    <td>Последовательность <b>пробелов</b>  длинной от <b>0</b> до &infin;</td>
                </tr>
                <tr>
                    <td>+++</td>
                    <td>Последовательность <b>любых символов</b> длинной от <b>1</b> до &infin;</td>
                </tr>
                <tr>
                    <td>++</td>
                    <td>Последовательность <b>пробелов</b>  длинной от <b>1</b> до &infin;</td>
                </tr>
            </tbody>
        </table>
</div>