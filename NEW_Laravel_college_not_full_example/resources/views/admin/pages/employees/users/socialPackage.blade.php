@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <div class="row">
                <div class="col-md-10">
                    <h2>Социальный пакет сотрудника - {{ $user->studentProfile->fio ?? '' }}</h2>
                </div> 
            </div>
        </div>

        {!! Form::open([
            'url' => route('employees.user.edit.social.package')
        ]) !!}
            <input type="hidden" name="employees_user_id" value="{{ $id }}">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Бензин</label>
                        <input type="text" class="form-control" name="gas" placeholder="Бензин:" value="{{ isset($package)? $package->gas : '' }}">
                        @if(!empty($errors->first('social_package.gas')))
                            <span class="invalid-feedback">
                                {{ $errors->first('social_package.gas') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Серт. Корзина</label>
                        <input type="text" class="form-control" name="basket" placeholder="Серт. Корзина:" value="{{ isset($package)? $package->basket : '' }}">
                        @if(!empty($errors->first('social_package.basket')))
                            <span class="invalid-feedback">
                                {{ $errors->first('social_package.basket') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Серт. Медикаменты</label>
                        <input type="text" class="form-control" name="medicines" placeholder="Серт. Медикаменты:" value="{{ isset($package)? $package->medicines : '' }}">
                        @if(!empty($errors->first('social_package.medicines')))
                            <span class="invalid-feedback">
                                {{ $errors->first('social_package.medicines') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Сотовая связь</label>
                        <input type="text" class="form-control" name="cellular" placeholder="Сотовая связь:" value="{{ isset($package)? $package->cellular : '' }}">
                        @if(!empty($errors->first('social_package.cellular')))
                            <span class="invalid-feedback">
                                {{ $errors->first('social_package.cellular') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Такси</label>
                        <input type="text" class="form-control" name="taxi" placeholder="Такси:" value="{{ isset($package)? $package->taxi : '' }}">
                        @if(!empty($errors->first('social_package.taxi')))
                            <span class="invalid-feedback">
                                {{ $errors->first('social_package.taxi') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-1">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="food" {{ isset($package) && $package->food == true? 'checked' : '' }}>
                        <label class="form-check-label" for="file">
                            Питание
                        </label>
                    </div>
                </div>
                <div class="col-md-2 text-right">
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </div>
        {!! Form::close() !!}
        
    </div>
@endsection