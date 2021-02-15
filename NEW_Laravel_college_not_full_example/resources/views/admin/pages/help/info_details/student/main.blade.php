<div class="col-md-12">
    <div class="col-md-3"><label>ФИО</label></div>
    <div class="col-md-9">{{ $help->user->studentProfile->fio }}</div>
</div>

<div class="col-md-12">
    <div class="col-md-3"><label>Телефон для обратной связи</label></div>
    <div class="col-md-9">{{ $help->phone }}</div>
</div>

<div class="col-md-12">
    <div class="col-md-3"><label>ИИН</label></div>
    <div class="col-md-9">{{ $help->user->studentProfile->iin }}</div>
</div>

<div class="col-md-12">
    <div class="col-md-3"><label>Дата рождения</label></div>
    <div class="col-md-9">{{ \Carbon\Carbon::make($help->user->studentProfile->bdate)->format('d.m.Y') }}</div>
</div>

<div class="col-md-12">
    <div class="col-md-3"><label>Пол</label></div>
    <div class="col-md-9">{{ $help->user->studentProfile->sex ? 'Мужчина' : 'Женщина' }}</div>
</div>

<div class="col-md-12">
    <div class="col-md-3"><label>Номер удостоверения</label></div>
    <div class="col-md-9">{{ $help->user->studentProfile->docnumber }}</div>
</div>

<div class="col-md-12">
    <div class="col-md-3"><label>Номер Документа</label></div>
    <div class="col-md-9">{{ $help->user->studentProfile->docnumber }}</div>
</div>

<div class="col-md-12">
    <div class="col-md-3"><label>Кем выдан</label></div>
    <div class="col-md-9">{{ $help->user->studentProfile->issuing }}</div>
</div>

<div class="col-md-12">
    <div class="col-md-3"><label>Дата выдачи</label></div>
    <div class="col-md-9">{{ \Carbon\Carbon::make($help->user->studentProfile->issuedate)->format('d.m.Y') }}</div>
</div>