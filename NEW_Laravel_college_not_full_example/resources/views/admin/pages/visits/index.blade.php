@extends("admin.admin_app")

@section("content")
    <div id="visits-admin-page">

        <div v-show="showParts" v-bind:class="[ 'modal', {active: showParts} ]" v-cloak id="documents-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg " style="min-width:950px;" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" @click="hideModal"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Всего документов с посещениями за этот период - @{{ printPartsCount }}</h4>
                        <span>Для @{{ totalCount }} студентов</span>
                    </div>
                    <div class="modal-body col-sm-12" style="overflow-y: auto;max-height: 65vh;">
                        <ul>
                            <li v-for="(partNum, index) in printPartsCount">
                                <a v-if="partNum == 1" v-bind:href=" '{{route('print.visitorsPDF')}}/?name=' + name + '&group=' + group + '&year=' + year + '&month=' + month + '&partNum=' + partNum">Список с 1 по @{{((partNum - 1) * 100  + 100)}} </a>
                                <a v-if="partNum != 1 && partNum != printPartsCount" v-bind:href=" '{{route('print.visitorsPDF')}}/?name=' + name + '&group=' + group + '&year=' + year + '&month=' + month + '&partNum=' + partNum">Список с @{{ ((partNum - 1) * 100) +1 }} по @{{((partNum - 1) * 100  + 100)}} </a>
                                <a v-if="partNum == printPartsCount" v-bind:href=" '{{route('print.visitorsPDF')}}/?name=' + name + '&group=' + group + '&year=' + year + '&month=' + month + '&partNum=' + partNum">Список с @{{ ((partNum - 1) * 100) +1 }} по @{{totalCount}} </a>
                            </li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" @click="hideModal">Ок</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header">
            <h2>Посещаемость</h2>
        </div>

        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        <div class="panel panel-default panel-shadow">
            <div class="panel-body" v-cloak>

                <div class="table-responsive">
                        <table style="width: 100%; margin-bottom: 2rem">
                            <thead>
                                <tr>
                                    <th class="text-center width-150">
                                        Ученик
                                        <input class="form-control" type="text" id="profile" name="name" placeholder="ФИО" v-model="name" @keyup="buildTable">
                                    </th>
                                    <th class="text-center width-150">
                                        Группа
                                        <select class="form-control" id="group_select" name="group" v-model="group" @change="buildTable">
                                            <option value=""></option>
                                            @foreach($groups as $group)
                                                <option value="{{ $group->id }}">{{ strtoupper($group->name) }}</option>
                                            @endforeach
                                        </select>
                                    </th>
                                    <th class="text-center width-150">
                                        Месяц
                                        <select class="form-control" id="month_select" name="month" v-model="month" @change="buildTable">
                                            <option value="1">Январь</option>
                                            <option value="2">Февраль</option>
                                            <option value="3">Март</option>
                                            <option value="4">Апрель</option>
                                            <option value="5">Май</option>
                                            <option value="6">Июнь</option>
                                            <option value="7">Июль</option>
                                            <option value="8">Август</option>
                                            <option value="9">Сентябрь</option>
                                            <option value="10">Октябрь</option>
                                            <option value="11">Ноябрь</option>
                                            <option value="12">Декабрь</option>
                                        </select>
                                    </th>

                                    <th class="text-center width-150">
                                        Год
                                        <select class="form-control" id="year_select" name="year" v-model="year" @change="buildTable"></select>
                                    </th>
                                    <th class="text-center width-50">
                                        <br>
                                        <button class="btn btn-outline-dark"
                                                @click="getDocumentsList">
                                            <i class="fa fa-print" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                </div>

                <div class="table-responsive" style="overflow: unset;">
                    <table id="main-table-ajax" class="table table-striped dt-responsive table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ФИО</th>
                                <th v-for='day in days'>@{{ day }}</th>
                            </tr>
                        </thead>
                        <tbody v-if="profiles">
                            <tr v-for="profile in profiles">
                                <td>@{{ profile.user_id }}</td>
                                <td> @{{ profile.user_full_name }} </td>
                                <td v-for="day in days">
                                    <div v-if="
                                            checkinCount(profile.lecture_list, day) ||
                                            checkinCount(profile.other_discipline_list, day) ||
                                            checkinCount(profile.online_list, day)
                                    ">
                                        <span>
                                            @{{ (checkinCount(profile.lecture_list, day) + checkinCount(profile.other_discipline_list, day) + checkinCount(profile.online_list, day))  }}
                                        </span>
                                        <div class="visits-tooltip changed-offset">
                                            <p v-for="checkin in profile.lecture_list" v-if="checkin.day_in_month == day">
                                                <span class="text-primary"> @{{ checkin.discipline_name }} : </span>
                                                @{{ checkin.visits_time }} -
                                                <span class="text-success"> @{{ checkin.teacher_fio }} </span>
                                            </p>
                                            <p v-for="discipline in profile.other_discipline_list" v-if="discipline.day_in_month == day">
                                                <span class="text-primary"> @{{ discipline.discipline_name }} : </span>
                                                @{{ discipline.visits_time }}
                                            </p>
                                            <p v-for="discipline in profile.online_list" v-if="discipline.day_in_month == day">
                                                @{{ discipline.visits_time }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <b-pagination v-bind:total-rows="pageCount" v-model="currentPage" :per-page="1">
                    </b-pagination>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
@endsection

@section('scripts')

    <script type="text/javascript">

        var app = new Vue({
            el: '#visits-admin-page',
            data: {
                profiles: null,
                isLoaded: false,
                days: null,
                month: new Date().getMonth(),
                year: new Date().getFullYear(),
                group: '',
                name: '',
                showParts: false,
                currentPage: 1,
                totalCount: null,
                pageLength: 10,
                modalShow: false,
            },

            mounted: function() {
                this.buildTable();
            },

            created: function(){
                this.generateYears();
            },

            methods: {

                getProfilesList: function () {
                    var self = this;

                    var year = this.year;
                    var month = this.month;
                    var group = this.group;
                    var name = this.name;
                    var currentPage = this.currentPage;
                    var pageLength = this.pageLength;
                    this.showParts = false;

                    axios.post('{{ route('profiles.visitors.list') }}', {
                        year,
                        month,
                        group,
                        name,
                        currentPage,
                        pageLength
                    })
                    .then(function (response) {
                        if (response) {
                            self.isLoaded = true;
                            self.profiles = response.data.profiles;
                            self.totalCount = response.data.totalCount
                        } else {
                            console.log('bad request');
                        }
                    });
                },

                buildTable: function () {
                    this.days = new Date(this.year, this.month, 0).getDate();
                    this.getProfilesList();
                },

                checkinCount: function(profile, day) {
                    var resultCount = 0;
                    var listLength = profile.length;

                    for(var i=0; i<listLength; i++) {
                        if(profile[i].day_in_month == day) {
                            resultCount++;
                        }
                    }

                    return resultCount;
                },

                generateYears: function() {
                    var max = new Date().getFullYear(),
                        min = max - 9,
                        select = document.getElementById('year_select');

                    for (var i = min; i<=max; i++){
                        var opt = document.createElement('option');
                        opt.value = i;
                        opt.innerHTML = i;
                        select.appendChild(opt);
                    }
                },

                getDocumentsList: function () {
                    var msg = 'Это может занять некоторое время, т.к. студентов очень много, Вы уверены?';

                    if  (confirm(msg)) {
                        if(this.printPartsCount > 1) {
                            this.showParts = true;
                        } else {
                            window.location.href = '{{route('print.visitorsPDF')}}/?name=' + this.name + '&group=' + this.group + '&year=' + this.year + '&month=' + this.month + '&partNum=1'
                        }
                    }
                },

                hideModal: function () {
                    this.showParts = false;
                }

            },
            watch: {
                currentPage : function(val) {
                    this.getProfilesList();
                }
            },
            computed: {
                pageCount: function() {
                    return Math.ceil(this.totalCount / this.pageLength);
                },
                printPartsCount: function() {
                    return Math.ceil(this.totalCount / 100);
                }
            }
        });

    </script>

@endsection

<style>
    td {
        position: relative;
    }

    .visits-tooltip {
        display: none;
        position: absolute;
        width: 350px;
        left: calc(90% + 10px);
        top: 0;
        min-width: 180px;
        min-height: 140px;
        z-index: 200;
        padding: 10px;
        background: #f5f5f5;
        color: #000;
        border-radius: 5px;
        box-shadow: 0 3px 15px -1px rgba(0,0,0,0.75);
    }

    .visits-tooltip > li:not(:last-of-type) {
        margin-bottom: 5px;
    }

    .visits-tooltip:before {
        content: " ";
        position: absolute;
        top: 15px;
        left: -6px;
        margin-left: 0;
        border-top: 14px solid #f5f5f5;
        border-left: 14px solid #f5f5f5;
        border-radius: 5px;
        font-size: 0;
        line-height: 0;
        z-index: 999;
        background: #f5f5f5;
        transform: rotate(45deg);
    }

    td:hover .visits-tooltip {
        display: block;
    }

    .changed-offset {
        left: -10px;
        transform: translateX(-100%);
    }

    .changed-offset:before {
        left: unset;
        right: -5px;
    }

    #documents-modal.active {
        display: block;
    }

</style>
