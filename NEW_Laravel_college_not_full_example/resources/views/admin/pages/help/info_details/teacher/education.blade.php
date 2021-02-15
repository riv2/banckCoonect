@if(isset($help->user->educationDocumentList[0]))
    <div class="col-md-12">
        <div class="col-md-3"><label>Образование:</label></div>
        <div class="col-md-9">

            @if($help->user->educationDocumentList[0]->level == 'secondary')
                Среднее образование
            @endif
            @if($help->user->educationDocumentList[0]->level == 'secondary_special')
                Средне-специальное образование
            @endif
            @if($help->user->educationDocumentList[0]->level == 'higher')
                Высшее образование
            @endif
        </div>
    </div>
    <div class="col-md-12">
        <div class="col-md-3"><label>Серия</label></div>
        <div class="col-md-9">{{ $help->user->educationDocumentList[0]->doc_series ?? '' }}</div>
    </div>
    <div class="col-md-12">
        <div class="col-md-3"><label>Номер</label></div>
        <div class="col-md-9">{{ $help->user->educationDocumentList[0]->doc_number ?? '' }}</div>
    </div>
    <div class="col-md-12">
        <div class="col-md-3"><label>Наименование учреждения</label></div>
        <div class="col-md-9">{{ $help->user->educationDocumentList[0]->institution_name ?? '' }}</div>
    </div>
    <div class="col-md-12">
        <div class="col-md-3"><label>Дата выдачи</label></div>
        <div class="col-md-9">{{ \Carbon\Carbon::make($help->user->educationDocumentList[0]->date)->format('d.m.Y') }}</div>
    </div>
    <div class="col-md-12">
        <div class="col-md-3"><label>Город выдачи</label></div>
        <div class="col-md-9">{{ $help->user->educationDocumentList[0]->city ?? '' }}</div>
    </div>
    <div class="col-md-12">
        <div class="col-md-3"><label>Специализация</label></div>
        <div class="col-md-9">{{ $help->user->educationDocumentList[0]->specialization }}</div>
    </div>
    <div class="col-md-12">
        <div class="col-md-3"><label>Выдан в Казахстане</label></div>
        <div class="col-md-9">{{ $help->user->educationDocumentList[0]->kz_holder ? 'Да' : 'Нет' }}</div>
    </div>
    @if($help->user->educationDocumentList[0]->kz_holder == false)
        <div class="col-md-12">
            <div class="col-md-3"><label>Данные о нострификации</label></div>
            <div class="col-md-9">{{ $help->user->educationDocumentList[0]->nostrification }}</div>
        </div>
    @endif
@endif