<table class="table table-striped table-hover" id="modal_info">
    <thead>
    <tr>
        <th>
            Форма обучения
            <select class="form-control" id="edu_form_select">
                <option value=""></option>
                @foreach($educationForms as $educationForm)
                    <option value="{{$educationForm}}">{{$educationForm}}</option>
                @endforeach
            </select>
        </th>
        <th>
            Образование
            <select class="form-control" id="base_edu_select">
                <option value=""></option>
                @foreach($baseEducations as $baseEducation)
                    <option value="{{$baseEducation}}">{{$baseEducation}}</option>
                @endforeach
            </select>
        </th>
        <th>
            Тип цены
            <select class="form-control" id="price_type_select">
                <option value=""></option>
                @foreach($priceTypes as $priceType)
                    <option value="{{$priceType}}">{{$priceType}}</option>
                @endforeach
            </select>
        </th>
        <th>Цена</th>
    </tr>
    </thead>

    @foreach($prices as $price)
        <tr>
            <td>{{__($price->study_form)}}</td>
            <td>{{__($price->base_education)}}</td>
            <td>{{__($price->price_type)}}</td>
            <td>{{$price->price}}</td>
        </tr>
    @endforeach
</table>