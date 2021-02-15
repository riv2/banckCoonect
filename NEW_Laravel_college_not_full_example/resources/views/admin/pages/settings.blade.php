@extends("admin.admin_app")

@section("content")

<div id="main">
	<div class="page-header">
		<h2> Настройки</h2>
		<a href="{{ URL::to('/dashboard') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>
	  
	</div>
	@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
	 @if(Session::has('flash_message'))
				    <div class="alert alert-success">
				    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
<span aria-hidden="true">&times;</span></button>
				        {{ Session::get('flash_message') }}
				    </div>
				@endif
    <div role="tabpanel">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#account" aria-controls="account" role="tab" data-toggle="tab">Настройки</a>
        </li>
        <!--<li role="presentation">
            <a href="#social_links" aria-controls="social_links" role="tab" data-toggle="tab">Соц. сети</a>
        </li>
        <li role="presentation">
            <a href="#share_comments" aria-controls="share_comments" role="tab" data-toggle="tab">Disqus коментарии</a>
        </li>
        --><li role="presentation">
            <a href="#about_us" aria-controls="about_us" role="tab" data-toggle="tab">О нас</a>
        </li>
        <!--
        <li role="presentation">
            <a href="#careers_with_us" aria-controls="careers_with_us" role="tab" data-toggle="tab">Карьера</a>
        </li>
        <li role="presentation">
            <a href="#terms_conditions" aria-controls="terms_conditions" role="tab" data-toggle="tab">Условия использования</a>
        </li>
        <li role="presentation">
            <a href="#privacy_policy" aria-controls="privacy_policy" role="tab" data-toggle="tab">Условия приватности</a>
        </li>
        <li role="presentation">
            <a href="#prices" aria-controls="prices" role="tab" data-toggle="tab">Цены</a>
        </li>
        
        <li role="presentation">
            <a href="#other_Settings" aria-controls="other_Settings" role="tab" data-toggle="tab">CSS/JS</a>
        </li> -->
    </ul>

    <!-- Tab panes -->
    <div class="tab-content tab-content-default">
        <div role="tabpanel" class="tab-pane active" id="account">             
            {!! Form::open(array('url' => '/settings','class'=>'form-horizontal padding-15','name'=>'account_form','id'=>'account_form','role'=>'form','enctype' => 'multipart/form-data')) !!}
                
                <div class="form-group">
                    <label for="avatar" class="col-sm-3 control-label">Лого</label>
                    <div class="col-sm-9">
                        <div class="media">
                            <div class="media-left">
                                @if($settings->site_logo)
                                 
									<img src="{{ URL::asset('upload/'.$settings->site_logo) }}" width="150" alt="person">
								@endif
								                                
                            </div>
                            <div class="media-body media-middle">
                                <input type="file" name="site_logo" class="filestyle">
                                <small class="text-muted bold">Размер 200x75px</small>
                            </div>
                        </div>
	
                    </div>
                </div>
				<div class="form-group">
                    <label for="avatar" class="col-sm-3 control-label">Иконка (favicon)</label>
                    <div class="col-sm-9">
                        <div class="media">
                            <div class="media-left">
                                @if($settings->site_favicon)
                                 
									<img src="{{ URL::asset('upload/'.$settings->site_favicon) }}" alt="person">
								@endif
								                                
                            </div>
                            <div class="media-body media-middle">
                                <input type="file" name="site_favicon" class="filestyle">
                                <small class="text-muted bold">Размер 16x16px</small>
                            </div>
                        </div>
	
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Название сайта</label>
                    <div class="col-sm-9">
                        <input type="text" name="site_name" value="{{ $settings->site_name }}" class="form-control" value="">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Email сайта</label>
                    <div class="col-sm-9">
                        <input type="email" name="site_email" value="{{ $settings->site_email }}" class="form-control" value="">
                    </div>
                </div>
                <!--<div class="form-group">
                    <label for="" class="col-sm-3 control-label">Подпись валюты</label>
                    <div class="col-sm-9">
                        <input type="text" name="currency_sign" value="{{ $settings->currency_sign }}" class="form-control" value="">
                    </div>
                </div>-->
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Описание сайта</label>
                    <div class="col-sm-9">
                        <textarea type="text" name="site_description" class="form-control" rows="5" placeholder="Несколько слов о сайте">{{ $settings->site_description }}</textarea>
                    </div>
                </div>
                 <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Ключевые слова сайта</label>
                    <div class="col-sm-9">
                        <textarea type="text" name="site_keywords" class="form-control" rows="5" placeholder="Несколько слов о сайте">{{ $settings->site_keywords }}</textarea>
                    </div>
                </div>
                <!--<div class="form-group">
                    <label for="" class="col-sm-3 control-label">Виджет в подвале 1</label>
                    <div class="col-sm-9">
                        <textarea type="text" name="footer_widget1" class="form-control" rows="5" placeholder="Несколько слов о сайте">{{ $settings->footer_widget1 }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Виджет в подвале 2</label>
                    <div class="col-sm-9">
                        <textarea type="text" name="footer_widget2" class="form-control" rows="5" placeholder="Несколько слов о сайте">{{ $settings->footer_widget2 }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Виджет в подвале 3</label>
                    <div class="col-sm-9">
                        <textarea type="text" name="footer_widget3" class="form-control" rows="5" placeholder="Несколько слов о сайте">{{ $settings->footer_widget3 }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Текст копирайт</label>
                    <div class="col-sm-9">
                        <textarea type="text" name="site_copyright" class="form-control" rows="5">{{ $settings->site_copyright }}</textarea>
                    </div>
                </div>-->
                
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Количество клиентов</label>
                    <div class="col-sm-9">
                        <input type="text" name="clients_count" value="{{ $settings->clients_count }}" class="form-control" value="">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Количество часов</label>
                    <div class="col-sm-9">
                        <input type="text" name="hours_count" value="{{ $settings->hours_count }}" class="form-control" value="">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Количество проектов</label>
                    <div class="col-sm-9">
                        <input type="text" name="projects_count" value="{{ $settings->projects_count }}" class="form-control" value="">
                    </div>
                </div>
                
                
                <hr>
                <div class="form-group">
                    <div class="col-md-offset-3 col-sm-9 ">
                    	<button type="submit" class="btn btn-primary">Сохранить изменения <i class="md md-lock-open"></i></button>
                         
                    </div>
                </div>

            {!! Form::close() !!} 
        </div>
        
        <div role="tabpanel" class="tab-pane" id="social_links">
            
            {!! Form::open(array('url' => '/social_links','class'=>'form-horizontal padding-15','name'=>'social_links_form','id'=>'social_links_form','role'=>'form')) !!}
                 
                
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Facebook URL</label>
                    
                    <div class="col-sm-9">
                        <input type="text" name="social_facebook" value="{{ $settings->social_facebook }}" class="form-control" value="">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Twitter URL</label>
                    
                    <div class="col-sm-9">
                        <input type="text" name="social_twitter" value="{{ $settings->social_twitter }}" class="form-control" value="">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Linkedin URL</label>
                    
                    <div class="col-sm-9">
                        <input type="text" name="social_linkedin" value="{{ $settings->social_linkedin }}" class="form-control" value="">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">GPlus URL</label>
                    
                    <div class="col-sm-9">
                        <input type="text" name="social_gplus" value="{{ $settings->social_gplus }}" class="form-control" value="">
                    </div>
                </div>
                 
                <hr>
                 <div class="form-group">
                    <div class="col-md-offset-3 col-sm-9 ">
                        <button type="submit" class="btn btn-primary">Сохранить изменения <i class="md md-lock-open"></i></button>
                    </div>
                </div>

            {!! Form::close() !!} 
        </div>
        
        <div role="tabpanel" class="tab-pane" id="share_comments">
            
            {!! Form::open(array('url' => '/addthisdisqus','class'=>'form-horizontal padding-15','name'=>'pass_form','id'=>'pass_form','role'=>'form')) !!}
                
                 
                
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Disqus Code</label>
                    <div class="col-sm-9">
                        <textarea type="text" name="disqus_comment_code" class="form-control" rows="5">{{ $settings->disqus_comment_code }}</textarea>
                    </div>
                </div>
                 
                <hr>
                <div class="form-group">
                    <div class="col-md-offset-3 col-sm-9 ">
                        <button type="submit" class="btn btn-primary">Сохранить изменения <i class="md md-lock-open"></i></button>
                    </div>
                </div>

            {!! Form::close() !!} 
        </div>
        
        <div role="tabpanel" class="tab-pane" id="about_us">
            
            {!! Form::open(array('url' => '/about_us','class'=>'form-horizontal padding-15','name'=>'pass_form','id'=>'pass_form','role'=>'form')) !!}
                
                 
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">О нас заголовок</label>
                    
                    <div class="col-sm-9">
                        <input type="text" name="about_us_title" value="{{ $settings->about_us_title }}" class="form-control" value="">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Описание</label>
                    <div class="col-sm-9">
                        <textarea type="text" name="about_us_description" class="form-control summernote" rows="5">{{ $settings->about_us_description }}</textarea>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">О нас заголовок RU</label>
                    
                    <div class="col-sm-9">
                        <input type="text" name="about_us_title_ru" value="{{ $settings->about_us_title_ru }}" class="form-control" value="">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Описание RU</label>
                    <div class="col-sm-9">
                        <textarea type="text" name="about_us_description_ru" class="form-control summernote" rows="5">{{ $settings->about_us_description_ru }}</textarea>
                    </div>
                </div>
                 
                <hr>
                <div class="form-group">
                    <div class="col-md-offset-3 col-sm-9 ">
                        <button type="submit" class="btn btn-primary">Сохранить изменения <i class="md md-lock-open"></i></button>
                    </div>
                </div>

            {!! Form::close() !!} 
        </div>
        
        <div role="tabpanel" class="tab-pane" id="careers_with_us">
            
            {!! Form::open(array('url' => '/careers_with_us','class'=>'form-horizontal padding-15','name'=>'careers_with_us_form','id'=>'careers_with_us_form','role'=>'form')) !!}
                
                 
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Заголовок карьеры</label>
                    
                    <div class="col-sm-9">
                        <input type="text" name="careers_with_us_title" value="{{ $settings->careers_with_us_title }}" class="form-control" value="">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Описание</label>
                    <div class="col-sm-9">
                        <textarea type="text" name="careers_with_us_description" class="form-control summernote" rows="5">{{ $settings->careers_with_us_description }}</textarea>
                    </div>
                </div>
                 
                <hr>
                <div class="form-group">
                    <div class="col-md-offset-3 col-sm-9 ">
                        <button type="submit" class="btn btn-primary">Сохранить изменения <i class="md md-lock-open"></i></button>
                    </div>
                </div>

            {!! Form::close() !!} 
        </div>
        
        
        <div role="tabpanel" class="tab-pane" id="terms_conditions">
            
            {!! Form::open(array('url' => '/terms_conditions','class'=>'form-horizontal padding-15','name'=>'terms_conditions_form','id'=>'terms_conditions_form','role'=>'form')) !!}
                
                 
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Terms Title</label>
                    
                    <div class="col-sm-9">
                        <input type="text" name="terms_conditions_title" value="{{ $settings->terms_conditions_title }}" class="form-control" value="">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Описание</label>
                    <div class="col-sm-9">
                        <textarea type="text" name="terms_conditions_description" class="form-control summernote" rows="5">{{ $settings->terms_conditions_description }}</textarea>
                    </div>
                </div>
                 
                <hr>
                <div class="form-group">
                    <div class="col-md-offset-3 col-sm-9 ">
                        <button type="submit" class="btn btn-primary">Сохранить изменения <i class="md md-lock-open"></i></button>
                    </div>
                </div>

            {!! Form::close() !!} 
        </div>
        
        
        <div role="tabpanel" class="tab-pane" id="privacy_policy">
            
            {!! Form::open(array('url' => '/privacy_policy','class'=>'form-horizontal padding-15','name'=>'privacy_policy_form','id'=>'privacy_policy_form','role'=>'form')) !!}
                
                 
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Заголовок</label>
                    
                    <div class="col-sm-9">
                        <input type="text" name="privacy_policy_title" value="{{ $settings->privacy_policy_title }}" class="form-control" value="">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Описание</label>
                    <div class="col-sm-9">
                        <textarea type="text" name="privacy_policy_description" class="form-control summernote" rows="5">{{ $settings->privacy_policy_description }}</textarea>
                    </div>
                </div>
                 
                <hr>
                <div class="form-group">
                    <div class="col-md-offset-3 col-sm-9 ">
                        <button type="submit" class="btn btn-primary">Сохранить изменения <i class="md md-lock-open"></i></button>
                    </div>
                </div>

            {!! Form::close() !!} 
        </div>
        
        <div role="tabpanel" class="tab-pane" id="prices">
            
            {!! Form::open(array('url' => '/prices','class'=>'form-horizontal padding-15','name'=>'prices_form','id'=>'prices_form','role'=>'form')) !!}
                
                 
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Заголовок</label>
                    
                    <div class="col-sm-9">
                        <input type="text" name="prices_title" value="{{ $settings->prices_title }}" class="form-control" value="">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Описание</label>
                    <div class="col-sm-9">
                        <textarea type="text" name="prices_description" class="form-control summernote" rows="5">{{ $settings->prices_description }}</textarea>
                    </div>
                </div>
                 
                <hr>
                <div class="form-group">
                    <div class="col-md-offset-3 col-sm-9 ">
                        <button type="submit" class="btn btn-primary">Сохранить изменения <i class="md md-lock-open"></i></button>
                    </div>
                </div>

            {!! Form::close() !!} 
        </div>
        
        <div role="tabpanel" class="tab-pane" id="other_Settings">
            
            {!! Form::open(array('url' => '/headfootupdate','class'=>'form-horizontal padding-15','name'=>'pass_form','id'=>'pass_form','role'=>'form')) !!}
                
                 
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Header Code</label>
                    <div class="col-sm-9">
                        <textarea type="text" name="site_header_code" class="form-control" rows="5" placeholder="You may want to add some html/css/js code to header. ">{{ $settings->site_header_code }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Footer Code</label>
                    <div class="col-sm-9">
                        <textarea type="text" name="site_footer_code" class="form-control" rows="5" placeholder="You may want to add some html/css/js code to footer. ">{{ $settings->site_footer_code }}</textarea>
                    </div>
                </div>
                 
                <hr>
                <div class="form-group">
                    <div class="col-md-offset-3 col-sm-9 ">
                        <button type="submit" class="btn btn-primary">Сохранить изменения <i class="md md-lock-open"></i></button>
                    </div>
                </div>

            {!! Form::close() !!} 
        </div>
         
    </div>
</div>
</div>

@endsection