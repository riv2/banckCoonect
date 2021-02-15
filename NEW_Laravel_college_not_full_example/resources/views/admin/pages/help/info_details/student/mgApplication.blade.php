<div class="col-md-12">
    <h4>mgApplication</h4>
    <hr>
</div>
<div class="col-md-12">
    <div class="col-md-3"><label>Национальность</label></div>
    <div class="col-md-9">{{ $help->user->mgApplication->nationality->name_ru }}</div>
</div>
<div class="col-md-12">
    <div class="col-md-3"><label>Гражданство</label></div>
    <div class="col-md-9">{{ $help->user->mgApplication->citizenship->name }}</div>
</div>
<div class="col-md-12">
    <div class="col-md-3"><label>Состоит в браке</label></div>
    <div class="col-md-9">{{ $help->user->mgApplication->family_status ? 'Да' : 'Нет'}}</div>
</div>

<div class="col-md-12"><hr>
    <div class="col-md-3"><label>Область</label></div>
    <div class="col-md-9">{{ $help->user->mgApplication->region->name }}</div>
</div>
<div class="col-md-12">
    <div class="col-md-3"><label>Населенный пункт</label></div>
    <div class="col-md-9">{{ $help->user->mgApplication->city->name ?? '' }}</div>
</div>
<div class="col-md-12">
    <div class="col-md-3"><label>Улица</label></div>
    <div class="col-md-9">{{ $help->user->mgApplication->street }}</div>
</div>
<div class="col-md-12">
    <div class="col-md-3"><label>Номер дома</label></div>
    <div class="col-md-9">{{ $help->user->mgApplication->building_number }}</div>
</div>
<div class="col-md-12">
    <div class="col-md-3"><label>Квартира</label></div>
    <div class="col-md-9">{{ $help->user->mgApplication->apartment_number }}</div>
</div>
