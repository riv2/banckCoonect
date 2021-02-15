@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>Настройки приемки</h2>
        </div>
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        <ul class="nav nav-tabs nav-tabs-theme-lang nav-justified">
            <li v-bind:class="{active: activeTab == 'bachelor'}" v-on:click="activeTab = 'bachelor'"><a href="#">Для бакалавра</a></li>
            <li v-bind:class="{active: activeTab == 'master'}" v-on:click="activeTab = 'master'"><a href="#">Для магистра</a></li>
        </ul>

        <div class="panel panel-default panel-shadow">
            <div class="panel-body" >

                <div v-show="activeTab == 'bachelor'">
                    <form name="bc" action="{{ route('adminInspectionBcPost') }}" method="post">
                        {{ csrf_field() }}
                        <config-student-document v-for="config in bcConfigList" v-bind:document="config"></config-student-document>

                        <div class="col-md-12">
                            <button class="btn btn-primary" type="submit">Сохранить</button>
                        </div>
                    </form>
                </div>

                <div v-show="activeTab == 'master'">
                    <form name="mg" action="{{ route('adminInspectionMgPost') }}" method="post">
                        {{ csrf_field() }}
                        <config-student-document v-for="config in mgConfigList" v-bind:document="config"></config-student-document>

                        <div class="col-md-12">
                            <button class="btn btn-primary" type="submit">Сохранить</button>
                        </div>
                    </form>
                </div>

            </div>
            <div class="clearfix"></div>
        </div>

    </div>



@endsection

@section('scripts')

    <script type="text/javascript" >
        Vue.component('config-student-document', {
            props: ['document'],
            data: function () {
                return {
                    showDate: false,
                    month: null,
                    day: null
                };
            },
            created: function () {
                if(this.document.value != null) {
                    date = new Date(this.document.value);
                    this.month = date.getMonth() + 1;
                    this.day = date.getDate();
                    this.showDate = true;
                }
            },
            methods: {
                dateUpdate: function (event) {
                    var date = new Date();

                    if(this.day && this.month) {
                        date.setDate(this.day);
                        date.setMonth(this.month - 1);

                        var month = date.getMonth()+1;
                        var day = date.getDate();

                        this.document.value = date.getFullYear() + '-' + (month < 10 ? '0' + month : month) + '-' + (day < 10 ? '0' + day : day);
                    }
                },
                monthByNum: function (num) {
                    var monthList = ['Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сетнбря', 'Октября', 'Ноября', 'Декабря'];
                    return monthList[num-1];
                },
                clearDocumentValue: function(event) {
                    if(this.showDate == false) {
                        this.document.value = null;
                        this.month = null;
                        this.day = null;
                    }
                }
            },
            template: `
                    <div class="col-md-12" v-bind:style="{height:'40px'}">
                        <div class="col-md-2">
                            <input type="checkbox" v-model="showDate" v-on:change="clearDocumentValue()" />
                            <span>@{{ document.title }}</span>
                        </div>
                        <div class="col-md-10" v-if="showDate">
                            <div class="col-md-3">
                                Предоставить справку до
                            </div>
                            <div class="col-md-1">
                                <select v-model="day" v-on:change="dateUpdate()" class="form-control">
                                    <option v-for="day in 31" v-bind:value="day">@{{ day }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select v-model="month" v-on:change="dateUpdate()" class="form-control">
                                    <option v-for="month in 12" v-bind:value="month">@{{ monthByNum(month) }}</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" v-bind:name="document.name" v-model="document.value">
                    </div>
            `
        });

        var app = new Vue({
            el: '#main',
            data: {
                activeTab: '{{$tab}}',
                bcConfigList:[{
                        name: 'deadline_residence_registration',
                        title: 'Адресная справка',
                        value: @if($bcConfig->deadline_residence_registration == null) null @else '{{$bcConfig->deadline_residence_registration}}' @endif
                    },{
                        name: 'deadline_r086',
                        title: 'Справка r086',
                        value: @if($bcConfig->deadline_r086 == null) null @else '{{$bcConfig->deadline_r086}}' @endif
                    },{
                        name: 'deadline_r063',
                        title: 'Справка r063',
                        value: @if($bcConfig->deadline_r063 == null) null @else '{{$bcConfig->deadline_r063}}' @endif
                    },{
                        name: 'deadline_ent',
                        title: 'ЕНТ',
                        value: @if($bcConfig->deadline_ent == null) null @else '{{$bcConfig->deadline_ent}}' @endif
                    },{
                        name: 'deadline_diploma_supplement',
                        title: 'Приложение к диплому',
                        value: @if($bcConfig->deadline_diploma_supplement == null) null @else '{{$bcConfig->deadline_diploma_supplement}}' @endif
                    },{
                        name: 'deadline_nostrification',
                        title: 'Нострификация',
                        value: @if($bcConfig->deadline_nostrification == null) null @else '{{$bcConfig->deadline_nostrification}}' @endif
                    }
                ],
                mgConfigList:[{
                        name: 'deadline_residence_registration',
                        title: 'Адресная справка',
                        value: @if($mgConfig->deadline_residence_registration == null) null @else '{{$mgConfig->deadline_residence_registration}}' @endif
                    },{
                        name: 'deadline_r086',
                        title: 'Справка r086',
                        value: @if($mgConfig->deadline_r086 == null) null @else '{{$mgConfig->deadline_r086}}' @endif
                    },{
                        name: 'deadline_r063',
                        title: 'Справка r063',
                        value: @if($mgConfig->deadline_r063 == null) null @else '{{$mgConfig->deadline_r063}}' @endif
                    },{
                        name: 'deadline_ent',
                        title: 'ЕНТ',
                        value: @if($mgConfig->deadline_ent == null) null @else '{{$mgConfig->deadline_ent}}' @endif
                    },{
                        name: 'deadline_diploma_supplement',
                        title: 'Приложение к диплому',
                        value: @if($mgConfig->deadline_diploma_supplement == null) null @else '{{$mgConfig->deadline_diploma_supplement}}' @endif
                    },{
                        name: 'deadline_nostrification',
                        title: 'Нострификация',
                        value: @if($mgConfig->deadline_nostrification == null) null @else '{{$mgConfig->deadline_nostrification}}' @endif
                    },{
                        name: 'deadline_military_commision',
                        title: 'Военный коммисариат',
                        value: @if($mgConfig->deadline_military_commision == null) null @else '{{$mgConfig->deadline_military_commision}}' @endif
                    },{
                        name: 'deadline_english_certificate',
                        title: 'Сертификатпо английскому',
                        value: @if($mgConfig->deadline_english_certificate == null) null @else '{{$mgConfig->deadline_english_certificate}}' @endif
                    },{
                        name: 'deadline_isbn',
                        title: 'ISBN',
                        value: @if($mgConfig->deadline_isbn == null) null @else '{{$mgConfig->deadline_isbn}}' @endif
                    },{
                        name: 'deadline_work_book',
                        title: 'Трудовая книжка',
                        value: @if($mgConfig->deadline_work_book == null) null @else '{{$mgConfig->deadline_work_book}}' @endif
                    }
                ]
            }
        });
    </script>

@endsection