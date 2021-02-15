@extends(Config::get('chatter.master_file_extend'))

@section(Config::get('chatter.yields.head'))
	<link href="{{ url('/vendor/devdojo/chatter/assets/vendor/spectrum/spectrum.css') }}" rel="stylesheet">
	<link href="{{ url('/vendor/devdojo/chatter/assets/css/chatter.css') }}" rel="stylesheet">
	@if($chatter_editor == 'simplemde')
		<link href="{{ url('/vendor/devdojo/chatter/assets/css/simplemde.min.css') }}" rel="stylesheet">
	@elseif($chatter_editor == 'trumbowyg')
		<link href="{{ url('/vendor/devdojo/chatter/assets/vendor/trumbowyg/ui/trumbowyg.css') }}" rel="stylesheet">
		<style>
			.trumbowyg-box, .trumbowyg-editor {
				margin: 0px auto;
			}
			li{
				display: block;
			}
		</style>
	@endif
@stop

@section('content')

	<div id="chatter" class="chatter_home">

		<div id="chatter_hero">
			<div id="chatter_hero_dimmer"></div>
			<?php $headline_logo = Config::get('chatter.headline_logo'); ?>
			@if( isset( $headline_logo ) && !empty( $headline_logo ) )
				<img src="{{ Config::get('chatter.headline_logo') }}">
			@else
				<h1>@lang('chatter::intro.headline')</h1>
				<p>@lang('chatter::intro.description')</p>
			@endif
		</div>

		@if(config('chatter.errors'))
			@if(Session::has('chatter_alert'))
				<div class="chatter-alert alert alert-{{ Session::get('chatter_alert_type') }}">
					<div class="container">
						<strong><i class="chatter-alert-{{ Session::get('chatter_alert_type') }}"></i> {{ Config::get('chatter.alert_messages.' . Session::get('chatter_alert_type')) }}</strong>
						{{ Session::get('chatter_alert') }}
						<i class="chatter-close"></i>
					</div>
				</div>
				<div class="chatter-alert-spacer"></div>
			@endif
		@endif

		<div class="container chatter_container">

			<div class="row">

				<div class="col-md-3 left-column">
					<!-- SIDEBAR -->
					<div class="chatter_sidebar" id="chatter_sidebar">
						<button class="btn btn-primary" id="new_discussion_btn"><i class="chatter-new"></i> @lang('chatter::messages.discussion.new')</button>
						<a href="{{route('chatter.home')}}"><i class="chatter-bubble"></i> @lang('chatter::messages.discussion.all')</a>
						<categories-block
								:pagination="pagination"
								:categories="categories"
								@paginate="getCategories">
						</categories-block>
					</div>
					<!-- END SIDEBAR -->
				</div>
				<div class="col-md-9 right-column">
					@if(count($discussions) == 0)
						<div class="">
							<div class="padding-25">
								<div class="alert alert-secondary" role="alert">
									@lang('No discussions created.')
								</div>
							</div>
						</div>
					@endif
					<div class="panel">
						<ul class="discussions">
							@foreach($discussions as $discussion)
								<li>
									<a class="discussion_list" href="{{ route('chatter.discussion.showInCategory' , ['category' => $discussion->category->slug, 'slug' => $discussion->slug]) }}">
										<div class="chatter_avatar">
										@if(Config::get('chatter.user.avatar_image_database_field'))

											<?php $db_field = Config::get('chatter.user.avatar_image_database_field'); ?>

											<!-- If the user db field contains http:// or https:// we don't need to use the relative path to the image assets -->
												@if( (substr($discussion->user->{$db_field}, 0, 7) == 'http://') || (substr($discussion->user->{$db_field}, 0, 8) == 'https://') )
													<img src="{{ $discussion->user->{$db_field}  }}">
												@else
													<img src="{{ Config::get('chatter.user.relative_url_to_image_assets') . $discussion->user->{$db_field}  }}">
												@endif

											@else

												<span class="chatter_avatar_circle" style="background-color:#<?= \DevDojo\Chatter\Helpers\ChatterHelper::stringToColorCode($discussion->user->{Config::get('chatter.user.database_field_with_user_name')}) ?>">
					        					{{ strtoupper(substr($discussion->user->{Config::get('chatter.user.database_field_with_user_name')}, 0, 1)) }}
					        				</span>
											<div class="forum_user_role_icon">
												@if($discussion->user->hasClientRole() || $discussion->user->hasAdminRole())
													<span class="role-icon"><i class="fas fa-user-graduate"></i></span>
												@endif
												@if($discussion->user->hasAdminRole() || $discussion->user->hasRight('forum', 'edit'))
													<span class="role-icon"><i class="fas fa-key"></i></span>
												@endif
												@if($discussion->user->hasTeacherRole())
													<span class="role-icon"><i class="fas fa-chalkboard-teacher"></i></span>
												@endif

												@if(!Auth::guest() && (Auth::user()->hasAdminRole() || Auth::user()->hasTeacherRole() || Auth::user()->hasRight('forum', 'read')))
													@if(\DevDojo\Chatter\Models\Ban::checkBaned($discussion->user->id))
														<span class="role-icon pull-right"><i class="fas fa-lock"></i></span>
													@else
														<span class="role-icon pull-right ban-icon-{{ $discussion->user->id }}" style="cursor: pointer;" onclick="forumBan({{ $discussion->user->id }})"><i class="fas fa-lock-open"></i></span>
													@endif
												@endif
											</div>

											@endif
										</div>

										<div class="chatter_middle">
											<h3 class="chatter_middle_title">{{ $discussion->title }} <div class="chatter_cat" style="background-color:{{ $discussion->category->color }}">{{ $discussion->category->name }}</div></h3>
											<span class="chatter_middle_details">@lang('chatter::messages.discussion.posted_by') <span data-href="/user">{{ ucfirst($discussion->user->{Config::get('chatter.user.database_field_with_user_name')}) }}</span> {{ \Carbon\Carbon::createFromTimeStamp(strtotime($discussion->created_at))->diffForHumans() }}</span>
											<p>
											@if(!empty($discussion->post->first()))
												@if($discussion->post->first()->markdown)
													<?php $discussion_body = GrahamCampbell\Markdown\Facades\Markdown::convertToHtml( $discussion->post->first()->body ); ?>
												@else
													<?php $discussion_body = $discussion->post->first()->body; ?>
												@endif
												{{ substr(strip_tags($discussion_body), 0, 200) }}@if(strlen(strip_tags($discussion_body)) > 200){{ '...' }}@endif
											@endif
											</p>
										</div>
										<div class="chatter_right">
											<div class="chatter_count"><i class="chatter-bubble"></i> {{ $discussion->postsCount->first()->total ?? 0}}</div>
										</div>
										<div class="chatter_clear"></div>
									</a>
								</li>
							@endforeach
						</ul>
					</div>
					<div class="d-flex justify-content-center">
						{{ $discussions->links() }}
					</div>

				</div>
			</div>
		</div>

		<div id="new_discussion" class="mobile">


			<div class="chatter_loader dark" id="new_discussion_loader">
				<div></div>
			</div>

			<form id="chatter_form_editor" action="{{ route('chatter.discussion.store') }}" method="POST">
				<div class="row">
					<div class="col-md-7">
						<!-- TITLE -->
						<input type="text" class="form-control" id="title" name="title" placeholder="@lang('chatter::messages.editor.title')" value="{{ old('title') }}" >
					</div>

					<div class="col-md-4">
						<!-- CATEGORY -->
						<disciplines-list></disciplines-list>
					</div>

					<div class="col-md-1">
						<i class="chatter-close"></i>
					</div>
				</div><!-- .row -->

				<!-- BODY -->
				<div id="editor">
					@if( $chatter_editor == 'tinymce' || empty($chatter_editor) )
						<label id="tinymce_placeholder">@lang('chatter::messages.editor.tinymce_placeholder')</label>
						<textarea id="body" class="richText" name="body" placeholder="">{{ old('body') }}</textarea>
					@elseif($chatter_editor == 'simplemde')
						<textarea id="simplemde" name="body" placeholder="">{{ old('body') }}</textarea>
					@elseif($chatter_editor == 'trumbowyg')
						<textarea class="trumbowyg" name="body" placeholder="@lang('chatter::messages.editor.tinymce_placeholder')">{{ old('body') }}</textarea>
					@endif
				</div>

				<input type="hidden" name="_token" id="csrf_token_field" value="{{ csrf_token() }}">

				<div id="new_discussion_footer">
					<input type='text' id="color" name="color" /><span class="select_color_text">@lang('chatter::messages.editor.select_color_text')</span>
					<button id="submit_discussion" class="btn btn-success pull-right"><i class="chatter-new"></i> @lang('chatter::messages.discussion.create')</button>
					<a href="{{ route('chatter.home') }}" class="btn btn-default pull-right" id="cancel_discussion">@lang('chatter::messages.words.cancel')</a>
					<div style="clear:both"></div>
				</div>
			</form>

		</div><!-- #new_discussion -->

	</div>

	@if( $chatter_editor == 'tinymce' || empty($chatter_editor) )
		<input type="hidden" id="chatter_tinymce_toolbar" value="{{ Config::get('chatter.tinymce.toolbar') }}">
		<input type="hidden" id="chatter_tinymce_plugins" value="{{ Config::get('chatter.tinymce.plugins') }}">
	@endif
	<input type="hidden" id="current_path" value="{{ Request::path() }}">

@endsection

@section(Config::get('chatter.yields.footer'))


	@if( $chatter_editor == 'tinymce' || empty($chatter_editor) )
		<script src="{{ url('/vendor/devdojo/chatter/assets/vendor/tinymce/tinymce.min.js') }}"></script>
		<script src="{{ url('/vendor/devdojo/chatter/assets/js/tinymce.js') }}"></script>
		<script>
			var my_tinymce = tinyMCE;
			$('document').ready(function(){
				$('#tinymce_placeholder').click(function(){
					my_tinymce.activeEditor.focus();
				});
			});
		</script>
	@elseif($chatter_editor == 'simplemde')
		<script src="{{ url('/vendor/devdojo/chatter/assets/js/simplemde.min.js') }}"></script>
		<script src="{{ url('/vendor/devdojo/chatter/assets/js/chatter_simplemde.js') }}"></script>
	@elseif($chatter_editor == 'trumbowyg')
		<script src="{{ url('/vendor/devdojo/chatter/assets/vendor/trumbowyg/trumbowyg.min.js') }}"></script>
		<script src="{{ url('/vendor/devdojo/chatter/assets/vendor/trumbowyg/plugins/preformatted/trumbowyg.preformatted.min.js') }}"></script>
		<script src="{{ url('/vendor/devdojo/chatter/assets/js/trumbowyg.js') }}"></script>
	@endif

	<script src="{{ url('/vendor/devdojo/chatter/assets/vendor/spectrum/spectrum.js') }}"></script>
	<script src="{{ url('/vendor/devdojo/chatter/assets/js/chatter.js') }}"></script>
	<script>
		$('document').ready(function(){
			$('.chatter-close, #cancel_discussion').click(function(){
				$('#new_discussion').slideUp();
			});
			$('#new_discussion_btn').click(function(){
				@if(Auth::guest())
						window.location.href = "{{ route('login') }}";
				@else
				$('#new_discussion').slideDown();
				$('#title').focus();
				@endif
			});
			$("#color").spectrum({
				color: "#333639",
				preferredFormat: "hex",
				containerClassName: 'chatter-color-picker',
				cancelText: '',
				chooseText: 'close',
				move: function(color) {
					$("#color").val(color.toHexString());
				}
			});
			@if (count($errors) > 0)
			$('#new_discussion').slideDown();
			$('#title').focus();
			@endif
		});

		const categoriesBlock = Vue.component('categories_block', {
			props: [
				'pagination',
				'categories',
				'paginate',
			],
			data: function(){
				return {
					category_url: '{{route('chatter.category.show',['slug' => ''])}}',
					select_value: 1,
					search: ''
				}
			},
			methods: {
				isCurrentPage(page) {
					if(this.pagination.current_page === page){
						return true
					}else{
						return false
					};
				},
				changePage(page) {
					if (page > this.pagination.last_page) {
						page = this.pagination.last_page;
					}
					this.pagination.current_page = page;
					this.$emit('paginate', this.search);
				},
				changePageSelect(e) {
					this.pagination.current_page = e.target.value
					this.changePage(e.target.value)
				}
			},
			computed: {
				pages() {
					let pages = [];
					let from = this.pagination.current_page - Math.floor(this.offset / 2);
					if (from < 1) {
						from = 1;
					}
					let to = from + 5 - 1;
					if (to > this.pagination.last_page) {
						to = this.pagination.last_page;
					}
					while (from <= to) {
						pages.push(from);
						from++;
					}
					return pages;
				}
			},
			template: `<div class="panel panel-default panel-shadow">
								<div class="form-group d-lg-flex">
									<input type="text" name="search" v-model="search" class="form-control col-12 col-lg-8" placeholder="@lang('Search')" id="">
									<button v-on:click="changePage(1)" class="btn btn-success col-12 col-lg-4">@lang('Search')</button>
								</div>
								<ul class="nav nav-pills nav-stacked" id="forum-menu">
									<li v-for="category in categories">
										<a v-bind:href="category_url + '/' + category['slug']" class="d-flex">
											<div class="chatter-box" v-bind:style="'background-color:'+ category['color']"></div>
											<div v-html="category['name']"></div>
										</a>
									</li>
								</ul>
                                <nav class="d-block text-center padding-10" v-if="pagination.last_page > 1">
                                    <select class="form-control" :value="pagination.current_page" v-on:change="changePageSelect">
                                        <option v-for="page in pagination.last_page" :value="page" v-html="page" ></option>
                                    </select>
                                   <div class="btn-group">
										<button class="btn btn-default"
                                                @click="changePage(1)">
                                                <i class="fas fa-angle-double-left"></i>
                                        </button>
                                        <button class="btn btn-default"
                                                @click="changePage(pagination.current_page - 1)"
                                                :disabled="pagination.current_page <= 1">
                                                <i class="fas fa-angle-left"></i>
                                        </button>
                                        <button
                                              	class="btn btn-default"
                                                v-html="pagination.current_page">
                                        </button>
                                        <button class="btn btn-default"
                                                @click="changePage(pagination.current_page + 1)"
                                                :disabled="pagination.current_page >= pagination.last_page">
                                                <i class="fas fa-angle-right"></i>
                                        </button>
										 <button class="btn btn-default"
                                                @click="changePage(pagination.last_page)">
                                                <i class="fas fa-angle-double-right"></i>
                                        </button>
                                    </div>
                                </nav>
                       </div>`
		})
		const disciplinesList = Vue.component('disciplines_list', {
			data: function () {
				return {
					categories: []
				}
			},
			methods: {
				getAllCategories: function () {
					axios.post(`{{route('chatter.getAllCategories')}}`)
							.then(res => {
								this.categories = res.data;
							}).catch(error => {
						console.error(error.response.data);
					});
				}
			},
			mounted: function() {
				this.getAllCategories();
			},
			template: `<select id="chatter_category_id" class="form-control" name="chatter_category_id">
							<option value="">@lang('chatter::messages.editor.select')</option>
							<option :value="category.id" v-for="category in categories" v-html="category.name"></option>
						</select>`
		});

		const app = new Vue({
			el: '#chatter',
			data: function(){
				return{
					categories: '',
					pagination: {
						current_page : 1
					}
				}
			},
			components: {
				'categories-block': categoriesBlock,
				'disciplines-list': disciplinesList
			},
			methods: {
				getCategories(search = ''){
					console.log(search)
					axios.get(`{{route('chatter.categories')}}?page=${this.pagination.current_page}&search=${search}`)
						.then(res => {
							this.categories = res.data.data.data;
							this.pagination = res.data.pagination;
						}).catch(error => {
							console.error(error.response.data);
						});
				}
			},
			mounted() {
				this.getCategories();
			}
		})
	</script>
@stop