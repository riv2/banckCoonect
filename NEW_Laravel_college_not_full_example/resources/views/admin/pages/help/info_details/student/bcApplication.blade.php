<div class="col-md-12">
    <h4>bcApplication</h4>
    <hr>
</div>
<div class="col-md-12">
    <div class="col-md-3"><label>Национальность</label></div>
    <div class="col-md-9">{{ $help->user->bcApplication->nationality->name_ru }}</div>
</div>
<div class="col-md-12">
    <div class="col-md-3"><label>Гражданство</label></div>
    <div class="col-md-9">{{ $help->user->bcApplication->citizenship->name }}</div>
</div>
<div class="col-md-12">
    <div class="col-md-3"><label>Состоит в браке</label></div>
    <div class="col-md-9">{{ $help->user->bcApplication->family_status ? 'Да' : 'Нет'}}</div>
</div>

<div class="col-md-12"><hr>
    <div class="col-md-3"><label>Область</label></div>
    <div class="col-md-9">{{ $help->user->bcApplication->region->name }}</div>
</div>
<div class="col-md-12">
    <div class="col-md-3"><label>Населенный пункт</label></div>
    <div class="col-md-9">{{ $help->user->bcApplication->city->name ?? '' }}</div>
</div>
<div class="col-md-12">
    <div class="col-md-3"><label>Улица</label></div>
    <div class="col-md-9">{{ $help->user->bcApplication->street }}</div>
</div>
<div class="col-md-12">
    <div class="col-md-3"><label>Номер дома</label></div>
    <div class="col-md-9">{{ $help->user->bcApplication->building_number }}</div>
</div>
<div class="col-md-12">
    <div class="col-md-3"><label>Квартира</label></div>
    <div class="col-md-9">{{ $help->user->bcApplication->apartment_number }}</div>
</div>

<div class="col-md-12"><hr>
    <div class="col-md-3"><label>Сертификат ЕНТ</label></div>
    <div class="col-md-9">{{ $help->user->bcApplication->ent_total ? 'Есть' : 'Нету' }}</div>
</div>

@if($help->user->bcApplication)
<div class="col-md-12"><hr>
    <div class="col-md-3"><label>Образование:</label></div>
    <div class="col-md-9">

        @if($help->user->bcApplication->bceducation == 'high_school')
            Среднее образование
        @endif
        @if($help->user->bcApplication->bceducation == 'vocational_education')
            Средне-специальное образование
        @endif
        @if($help->user->bcApplication->bceducation == 'bachelor')
            Высшее образование
        @endif
    </div>
</div>
<div class="col-md-12">
    <div class="col-md-3"><label>Серия</label></div>
    <div class="col-md-9">{{ $help->user->bcApplication->sereducation }}</div>
</div>
<div class="col-md-12">
    <div class="col-md-3"><label>Номер</label></div>
    <div class="col-md-9">{{ $help->user->bcApplication->numeducation }}</div>
</div>
<div class="col-md-12">
    <div class="col-md-3"><label>Наименование учреждения</label></div>
    <div class="col-md-9">{{ $help->user->bcApplication->nameeducation }}</div>
</div>
<div class="col-md-12">
    <div class="col-md-3"><label>Дата выдачи</label></div>
    <div class="col-md-9">{{ \Carbon\Carbon::make($help->user->bcApplication->dateeducation)->format('d.m.Y') }}</div>
</div>
<div class="col-md-12">
    <div class="col-md-3"><label>Город выдачи</label></div>
    <div class="col-md-9">{{ $help->user->bcApplication->cityeducation }}</div>
</div>
<div class="col-md-12">
    <div class="col-md-3"><label>Специализация</label></div>
    <div class="col-md-9">{{ $help->user->bcApplication->eduspecialization }}</div>
</div>
<div class="col-md-12">
    <div class="col-md-3"><label>Выдан в Казахстане</label></div>
    <div class="col-md-9">{{ $help->user->bcApplication->kzornot ? 'Да' : 'Нет' }}</div>
</div>
@if($help->user->bcApplication->kzornot == false)
    <div class="col-md-12">
        <div class="col-md-3"><label>Данные о нострификации</label></div>
        <div class="col-md-9">{{ $help->user->bcApplication->nostrfication }}</div>
    </div>
@endif
@endif