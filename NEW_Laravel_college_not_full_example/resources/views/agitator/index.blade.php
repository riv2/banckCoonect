@extends('layouts.app')

@section('title', __('Profile agitator'))

@section('content')

    <section class="content">
        <div class="container-fluid" id="profile-agitator">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Profile')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">

                            {{-- error block --}}
                            <div v-if="errorMessage" :class="{ 'alert-danger': isError, 'alert-success': !isError }" class="alert margin-t20 margin-b20">
                                <div v-html="errorMessage"> </div>
                            </div>


                            <div class="accordion padding-t10 accordion-bg" id="accordionData">

                                {{--profile --}}
                                <div class="card">
                                    <div class="card-header" id="headingProfile" data-toggle="collapse" data-target="#profile" aria-expanded="true" aria-controls="profile">
                                        <h4 class="mb-0 cursor-pointer">
                                            @lang("Profile")
                                        </h4>
                                    </div>

                                    <div id="profile" class="collapse row" aria-labelledby="headingBalance" data-parent="#accordionData">
                                        <div class="col-12 row padding-15">

                                            <div class="col-sm-12 col-md-3 text-center row">

                                                <template v-if="profileImage">
                                                    <input @change="processImgFile($event)" ref="file" style="display: none" type="file" accept="image/jpeg" />
                                                    <img v-if="profileImage" :src="'/images/uploads/faces/' + profileImage" class="img-thumbnail margin-15" style="display:flex;max-height:300px;" />
                                                    <a class="btn alert-success margin-t5" @click="$refs.file.click()">✓ {{__("Photo uploaded")}}</a>
                                                </template>

                                                <template v-if="!profileImage">
                                                    <input @change="processImgFile($event)" ref="file" style="display: none" type="file" accept="image/jpeg" />
                                                    <button @click="$refs.file.click()" class="btn btn-info margin-t5" type="button">{{__("Upload photo")}}</button>
                                                </template>

                                            </div>
                                            <div class="col-md-8 col-sm-12">
                                                <div class="row">
                                                    <div class="col-4" style="margin-left:30px">
                                                        <label> {{__("Full name")}} </label>
                                                    </div>
                                                    <div class="col">
                                                        @{{ profileStudentProfile.fio }}
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-4" style="margin-left:30px">
                                                        <label> {{__("Phone number")}} </label>
                                                    </div>
                                                    <div class="col">
                                                        @{{ profileStudentProfile.mobile }}
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                {{-- data payment --}}
                                <div class="card">
                                    <div class="card-header" id="headingDataPayment" data-toggle="collapse" data-target="#data_payment" aria-expanded="true" aria-controls="data_payment">
                                        <h4 class="mb-0 cursor-pointer">
                                            @lang("The data for the payment")
                                        </h4>
                                    </div>

                                    <div id="data_payment" class="collapse row" aria-labelledby="headingBalance" data-parent="#accordionData">
                                        <div class="col-12 padding-15">

                                            <ul class="list-group margin-b10">

                                                <li class="list-group-item">
                                                    {{__('Specify the Bank')}}:
                                                    <select v-model="profileBankId" class="form-control" name="profileBankId" >
                                                        @if( !empty($banks) && (count($banks) > 0) )
                                                            @foreach($banks as $itemBank)
                                                                <option value="{{ $itemBank->id }}"> {{ $itemBank->name }} </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </li>
                                                <li class="list-group-item">
                                                    {{__('Specify the IBAN')}}:
                                                    <input v-model="profileIban" class="form-control" type="text" name="profileIban" />
                                                </li>

                                            </ul>

                                            <div class="form-group">
                                                <div class="col-md-8 col-md-offset-4">
                                                    <button @click="profileSaveRequest" type="button" class="btn btn-info">
                                                        {{__("Save")}}
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                {{-- agitator users --}}
                                <div class="card">
                                    <div class="card-header" id="headingAgitatorUsers" data-toggle="collapse" data-target="#agitator_users" aria-expanded="true" aria-controls="agitator_users">
                                        <h4 class="mb-0 cursor-pointer">
                                            @lang("Entrants")
                                        </h4>
                                    </div>

                                    <div id="agitator_users" class="collapse row" aria-labelledby="headingBalance" data-parent="#accordionData">
                                        <div class="col-12 padding-15">

                                            <div class="table-responsive no-padding">
                                                <table class="table table-striped" style="width:100%;">
                                                    <thead>
                                                    <tr>
                                                        <th> {{ __('Fio') }} </th>
                                                        <th> {{ __('Cost') }} </th>
                                                        <th> {{ __('Status') }} </th>
                                                        <th> {{ __('Date') }} </th>
                                                        <th></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <template v-for="(item, index) in profileAgitatorUsers.data">
                                                        <template v-if="item.stud">
                                                            <tr :class="{ 'table-success': item.user_status, 'table-danger': !item.user_status }">
                                                                <td>
                                                                    <template v-if="item.stud.student_profile">
                                                                        @{{ item.stud.student_profile.fio }}
                                                                    </template>
                                                                    <template v-else>
                                                                        @{{ item.stud.name }}
                                                                    </template>
                                                                </td>
                                                                <td> @{{ item.cost }} </td>
                                                                <td> @{{ item.status }} </td>
                                                                <td> @{{ item.created_at }} </td>
                                                                <td>
                                                                    <template v-if="!item.user_status"> @{{ item.user_message }} </template>
                                                                </td>
                                                            </tr>
                                                        </template>

                                                    </template>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <nav aria-label="Page navigation">
                                                <paginate
                                                        v-model="profileAgitatorUsersPageNum"
                                                        :page-count="profileAgitatorUsers.last_page"
                                                        :page-range="3"
                                                        :margin-pages="2"
                                                        :click-handler="profileUsersPaginateClickCallback"
                                                        :prev-text="'{{ __('Previous') }}'"
                                                        :next-text="'{{ __('Next') }}'"
                                                        :container-class="'pagination'"
                                                        :page-class="'page-item'"
                                                        :page-link-class="'page-link'"
                                                        :prev-class="'page-link'"
                                                        :next-class="'page-link'">
                                                </paginate>
                                            </nav>

                                        </div>
                                    </div>

                                </div>

                                {{-- payments --}}
                                <div class="card">
                                    <div class="card-header" id="headingPayments" data-toggle="collapse" data-target="#payments" aria-expanded="true" aria-controls="payments">
                                        <h4 class="mb-0 cursor-pointer">
                                            @lang("Available for payment")
                                        </h4>
                                    </div>

                                    <div id="payments" class="collapse row" aria-labelledby="headingBalance" data-parent="#accordionData">
                                        <div class="col-12 padding-15">

                                            <p> {{ __('Main balance') }}: <strong> @{{ profileUserBalance }} </strong> </p>
                                            <p> {{ __('The balance of payments campaign') }}: <strong> @{{ profileAgitatorBalance }} ( {{ __('Available for output') }} @{{ profileAgitatorAvailableBalance  }} ) </strong> </p>


                                            <button class="btn btn-info" type="button" data-toggle="modal" data-target="#withdrawPayment"> {{ __('Withdraw to the card') }} </button>

                                            <!-- modal -->
                                            <div id="withdrawPayment" class="modal hide fade in" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"> {{ __('Withdraw funds to the card') }} </h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">

                                                            {{-- error block --}}
                                                            <div v-if="withdrawErrorMessage" :class="{ 'alert-danger': isWithdrawError, 'alert-success': !isWithdrawError }" class="alert margin-t20 margin-b20">
                                                                <div v-html="withdrawErrorMessage"> </div>
                                                            </div>

                                                            <div class="alert alert-warning" role="alert">
                                                                {{ __('Confirm the data for the map output or change it') }}
                                                            </div>

                                                            <template if="profileWithdrawInfo.amount" class="padding-b15">

                                                                <p class="text-center"> {{ __('The verbosity of the output') }} </p>

                                                                <table class="table">
                                                                    <template v-if="profileWithdrawInfo.ip">
                                                                        <tr>
                                                                            <th> {{ __('Name') }} </th>
                                                                            <th> {{ __('Explanation') }} </th>
                                                                        </tr>
                                                                        <tr>
                                                                            <td> {{ __('Amount to be charged') }} </td>
                                                                            <td> @{{ profileWithdrawAmount }} </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td> {{ __('Amount to be paid') }} </td>
                                                                            <td> @{{ profileWithdrawInfo.amount }} </td>
                                                                        </tr>
                                                                    </template>
                                                                    <template v-else-if="profileWithdrawInfo.alien">
                                                                        <tr>
                                                                            <th> {{ __('Name') }} </th>
                                                                            <th> {{ __('Explanation') }} </th>
                                                                        </tr>
                                                                        <tr>
                                                                            <td> {{ __('Amount to be charged') }} </td>
                                                                            <td> @{{ profileWithdrawAmount }} </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td> {{ __('IPN') }} </td>
                                                                            <td> {{ __('Individual income tax 20%') }} </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td> {{ __('Amount to be paid') }} </td>
                                                                            <td> @{{ profileWithdrawInfo.amountWithdraw + ' = ' + profileWithdrawInfo.amount + ' - ' + profileWithdrawInfo.amountPercent }} </td>
                                                                        </tr>
                                                                    </template>
                                                                    <template v-else>
                                                                        <tr>
                                                                            <th> {{ __('Name') }} </th>
                                                                            <th> {{ __('Explanation') }} </th>
                                                                        </tr>
                                                                        <tr>
                                                                            <td> {{ __('Amount to be charged') }} </td>
                                                                            <td> @{{ profileWithdrawAmount }} </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td> {{ __('OPV') }} </td>
                                                                            <td> {{ __('Mandatory pension contributions of 10% (not withheld from pensioners)') }} </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td> {{ __('IPN') }} </td>
                                                                            <td> {{ __('Individual income tax 10%') }} </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td> {{ __('OSMS') }} </td>
                                                                            <td> {{ __('Compulsory social health insurance 1%') }} </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td> {{ __('Amount to be paid') }} </td>
                                                                            <td> @{{ profileWithdrawInfo.amountWithdraw + ' = ' + profileWithdrawInfo.amount + ' - ' + profileWithdrawInfo.amountPercent }} </td>
                                                                        </tr>
                                                                    </template>
                                                                </table>

                                                            </template>

                                                            <div class="form-group row">
                                                                <label for="paymethod" class="col-12 control-label"> {{__('Specify the Bank')}} </label>
                                                                <div class="col-12">
                                                                    <select v-model="profileBankId" class="form-control" name="profileBankId1" >
                                                                        @if( !empty($banks) && (count($banks) > 0) )
                                                                            @foreach($banks as $itemBank)
                                                                                <option value="{{ $itemBank->id }}"> {{ $itemBank->name }} </option>
                                                                            @endforeach
                                                                        @endif
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label for="paymethod" class="col-12 control-label"> {{__('Specify the IBAN')}} </label>
                                                                <div class="col-12">
                                                                    <input v-model="profileIban" class="form-control" type="text" name="profileIban1" />
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label for="paymethod" class="col-12 control-label"> {{__('Withdrawal amount')}} </label>
                                                                <div class="col-12">
                                                                    <input v-model="profileWithdrawAmount" @change="profileWithdrawAmountChange" class="form-control" type="text" name="profileWithdrawAmount1" />
                                                                </div>
                                                            </div>



                                                            <div class="form-group row">
                                                                <button @click="profileYrShowblock" class="btn btn-success" type="button"> {{ __('To specify the data for legal entities') }} </button>
                                                            </div>

                                                            <template v-if="profileYrBlockFlag">

                                                                <div class="form-group row">
                                                                    <label class="col-12 control-label"> {{__('Name of the organization')}} </label>
                                                                    <div class="col-12">
                                                                        <input v-model="profileUserBusiness.name" class="form-control" type="text" name="profileYrFirmname" />
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label class="col-12 control-label"> {{__('Adress')}} </label>
                                                                    <div class="col-12">
                                                                        <input v-model="profileUserBusiness.adress" class="form-control" type="text" name="profileYrAdress" />
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label class="col-12 control-label"> {{__('BIN')}} </label>
                                                                    <div class="col-12">
                                                                        <input v-model="profileUserBusiness.bin" class="form-control" type="text" name="profileYrBin" />
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label class="col-12 control-label"> {{__('Bank name')}} </label>
                                                                    <div class="col-12">
                                                                        <input v-model="profileUserBusiness.bank_name" class="form-control" type="text" name="profileYrBankname" />
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label class="col-12 control-label"> {{__('IIK')}} </label>
                                                                    <div class="col-12">
                                                                        <input v-model="profileUserBusiness.iik" class="form-control" type="text" name="profileYrIik" />
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label class="col-12 control-label"> {{__('Bic bank')}} </label>
                                                                    <div class="col-12">
                                                                        <input v-model="profileUserBusiness.bank_bic" class="form-control" type="text" name="profileYrBankbic" />
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label class="col-12 control-label"> {{__('KBE')}} </label>
                                                                    <div class="col-12">
                                                                        <input v-model="profileUserBusiness.kbe" class="form-control" type="text" name="profileYrKbe" />
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label class="col-12 control-label"> {{__('Phone')}} </label>
                                                                    <div class="col-12">
                                                                        <input v-model="profileUserBusiness.phone" class="form-control" type="text" name="profileYrPhone" />
                                                                    </div>
                                                                </div>

                                                            </template>



                                                        </div>
                                                        <div class="modal-footer">
                                                            <button @click="profileChangeBankData"  :disabled="profileDataRequest" type="button" class="btn btn-primary"> {{ __('Save') }} </button>
                                                            <button @click="profileWithdrawRequest" :disabled="profileDataRequest" type="button" class="btn btn-info"> {{ __('Send') }} </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                {{-- transaction history --}}
                                <div class="card">
                                    <div class="card-header" id="headingTransactionHistory" data-toggle="collapse" data-target="#transaction_history" aria-expanded="true" aria-controls="transaction_history">
                                        <h4 class="mb-0 cursor-pointer">
                                            @lang("Transaction history")
                                        </h4>
                                    </div>

                                    <div id="transaction_history" class="collapse row" aria-labelledby="headingBalance" data-parent="#accordionData">
                                        <div class="col-12 padding-15">

                                            <div class="table-responsive no-padding">
                                                <table class="table table-striped" style="width:100%;">
                                                    <thead>
                                                    <tr>
                                                        <th> {{ __('Bank') }} </th>
                                                        <th> {{ __('Cost') }} </th>
                                                        <th> {{ __('Status') }} </th>
                                                        <th> {{ __('Date') }} </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <template v-for="(itemATH, index) in profileAgitatorTransactionHistory.data">
                                                        <tr>
                                                            <td> @{{ itemATH.bank.name }} </td>
                                                            <td> @{{ itemATH.cost }} </td>
                                                            <td> @{{ itemATH.status }} </td>
                                                            <td> @{{ itemATH.created_at }} </td>
                                                        </tr>
                                                    </template>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <nav aria-label="Page navigation">
                                                <paginatehistory
                                                        v-model="profileAgitatorTransactionHistoryPageNum"
                                                        :page-count="profileAgitatorTransactionHistory.last_page"
                                                        :page-range="3"
                                                        :margin-pages="2"
                                                        :click-handler="profileAgitatorPaginateClickCallback"
                                                        :prev-text="'{{ __('Previous') }}'"
                                                        :next-text="'{{ __('Next') }}'"
                                                        :container-class="'pagination'"
                                                        :page-class="'page-item'"
                                                        :page-link-class="'page-link'"
                                                        :prev-class="'page-link'"
                                                        :next-class="'page-link'">
                                                </paginatehistory>
                                            </nav>

                                        </div>
                                    </div>
                                </div>

                                {{-- memo --}}
                                <div class="card">
                                    <div class="card-header" id="headingMemo" data-toggle="collapse" data-target="#memo" aria-expanded="true" aria-controls="memo">
                                        <h4 class="mb-0 cursor-pointer">
                                            @lang("Agitator is memo")
                                        </h4>
                                    </div>

                                    <div id="memo" class="collapse row" aria-labelledby="headingBalance" data-parent="#accordionData">
                                        <div class="col-12 padding-15">


                                            <div id="terms" class="margin-25">
                                                <label>{{__("Terms of use")}}:</label>
                                                <div class="form-group">
                                                    <div class="col-12">
                                                        <textarea rows="5" readonly class="form-control agreement-text">{!! strip_tags(getcong('agitator_terms_conditions_description')) !!}</textarea>
                                                    </div>
                                                </div>
                                                <a href="{{ in_array(app()->getLocale(),[\App\Profiles::EDUCATION_LANG_KZ,\App\Profiles::EDUCATION_LANG_EN]) ? str_replace([\App\Profiles::EDUCATION_LANG_KZ,\App\Profiles::EDUCATION_LANG_EN],'',route('home')) : '' }}download/docs/agitator_public_offer.docx"> @lang('download') (RU)</a> <br>
                                                <a href="{{ in_array(app()->getLocale(),[\App\Profiles::EDUCATION_LANG_KZ,\App\Profiles::EDUCATION_LANG_EN]) ? str_replace([\App\Profiles::EDUCATION_LANG_KZ,\App\Profiles::EDUCATION_LANG_EN],'',route('home')) : '' }}download/docs/agitator_public_offer_kz.docx"> @lang('download') (KZ)</a>
                                            </div>


                                        </div>
                                    </div>
                                </div>

                            </div>


                        </div>
                        <div class="col-md-2"></div>
                    </div>

                </div>
            </div>

        </div>
    </section>

@endsection

@section('scripts')

    <script src="https://unpkg.com/vuejs-paginate@0.9.0"></script>

    <script type="text/javascript">

        Vue.component('paginate', VuejsPaginate);
        Vue.component('paginatehistory', VuejsPaginate);

        var app = new Vue({
            el: '#profile-agitator',
            data: {

                profileImage: '',
                profileImgSource: '',
                profileIban: '',
                profileBankId: '',
                profileAgitatorUsers: {
                    current_page: 0,
                    last_page: 0,
                    data: [],
                    total: 0
                },
                profileStudentProfile: {},
                profileAgitatorUsersPageNum: 1,
                profileUserBalance: '',
                profileAgitatorBalance: '',
                profileAgitatorAvailableBalance: '',
                isError: false,
                errorMessage: '',
                isWithdrawError: false,
                withdrawErrorMessage: '',
                profileWithdrawAmount: '',
                profileAgitatorTransactionHistory: {
                    current_page: 0,
                    last_page: 0,
                    data: [],
                    total: 0
                },
                profileAgitatorTransactionHistoryPageNum: 1,
                profileDataRequest: false,
                profileUserBusiness: {
                    name: '',
                    adress: '',
                    bin: '',
                    bank_name: '',
                    bank_bic: '',
                    iik: '',
                    kbe: '',
                    phone: ''
                },
                profileYrBlockFlag: false,
                profileWithdrawInfo: {}

            },
            methods: {

                profileWithdrawAmountChange: function(value){
                    this.profileGetWithdrawInfo(value);
                },
                profileYrShowblock: function()
                {
                    this.profileYrBlockFlag = !this.profileYrBlockFlag;
                },
                profileUsersPaginateClickCallback: function(pageNum) {

                    this.profileAgitatorUsersPageNum = pageNum;
                    this.profileLoadAgitatorUsers();
                },
                profileAgitatorPaginateClickCallback: function(pageNum)
                {
                    this.profileAgitatorTransactionHistoryPageNum = pageNum;
                    this.profileAgitatorLoadTransactionHistory();
                },
                profileLoadData: function(){

                    var self = this;
                    axios.post('{{ route('agitatorRegisterProfileLoadData') }}',{
                        "_token": "{{ csrf_token() }}"
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.profileImage                    = response.data.image;
                            self.profileIban                     = 'KZ' + response.data.userBank.iban;
                            self.profileBankId                   = response.data.userBank.bank_id;
                            self.profileUserBalance              = response.data.userBalance;
                            self.profileAgitatorBalance          = response.data.agitatorBalance;
                            self.profileAgitatorAvailableBalance = response.data.agitatorAvailableBalance;
                            self.profileWithdrawAmount           = response.data.agitatorAvailableBalance;
                            self.profileStudentProfile           = response.data.studentProfile;
                            if( response.data.userBusiness != null ){
                                self.profileUserBusiness = response.data.userBusiness;
                            }
                            self.profileGetWithdrawInfo( self.profileWithdrawAmount );

                        }
                    })
                    .catch( error => {

                        console.log(error)

                    });
                },
                profileLoadAgitatorUsers: function(){

                    var self = this;
                    axios.post('{{ route('agitatorProfileLoadAgitatorUsers') }}',{
                        "_token": "{{ csrf_token() }}",
                        "page": this.profileAgitatorUsersPageNum
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.profileAgitatorUsers = response.data.agitatorUsers;

                        }
                    })
                    .catch( error => {

                        console.log(error)
                    });
                },
                profileAgitatorLoadTransactionHistory: function(){

                    var self = this;
                    axios.post('{{ route('AgitatorTransactionHistory') }}',{
                        "_token": "{{ csrf_token() }}",
                        "page": this.profileAgitatorTransactionHistoryPageNum
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.profileAgitatorTransactionHistory = response.data.agitatorTransactionHistory;

                        }
                     })
                     .catch( error => {

                        console.log(error)
                    });
                },
                processImgFile: function(event){
                    var file = event.target.files[0];
                    if(file) {
                        var self = this;
                        self.profileImage = file.name;
                        self.profileImgSource = '';

                        var reader = new FileReader();
                        reader.readAsBinaryString(file);
                        reader.onload = function (evt) {

                            self.profileImgSource = btoa(evt.target.result);

                            axios.post('{{ route('agitatorRegisterProfileSaveImage') }}',{
                                "_token": "{{ csrf_token() }}",
                                "profileImage": self.profileImage,
                                "profileImgSource": self.profileImgSource
                            })
                            .then(function(response){

                                if( response.data.status ){
                                    self.profileLoadData();
                                }
                            })
                            .catch( error => {
                                console.log(error)
                            });

                        };

                    }
                },
                profileSaveRequest: function(){

                    this.isError = false;
                    this.errorMessage = '';

                    var profileBankId = $("select[name=profileBankId]");
                    var profileIban   = $("input[name=profileIban]");

                    if( profileBankId.val() == '' ){

                        profileBankId.addClass('error');
                        this.isError = true;
                        this.errorMessage = '{{ __('Bank cannot be empty') }}';
                        return
                    }

                    if( profileIban.val().length == 0 ){

                        profileIban.addClass('error');
                        this.isError = true;
                        this.errorMessage = '{{ __('IBAN cannot be empty') }}';
                        return
                    }

                    if( profileIban.val().length != 20 ){

                        profileIban.addClass('error');
                        this.isError = true;
                        this.errorMessage = '{{ __('IBAN cannot be less or more than 20 characters') }}';
                        return
                    }

                    if( !this.isError ){

                        var self = this;
                        axios.post('{{ route('agitatorRegisterProfileIbanPost') }}',{
                            "_token": "{{ csrf_token() }}",
                            "bank_id": this.profileBankId,
                            "iban": this.profileIban
                        })
                        .then(function(response){

                            if( response.data.status ) {

                                self.errorMessage = '{{ __('Data changed successfully') }}';
                            } else {

                                self.isError = true;
                                self.errorMessage = response.data.status;

                            }
                        })
                        .catch( error => {

                            console.log(error)
                        });

                    }

                },
                profileChangeBankData: function(){

                    this.isWithdrawError = false;
                    this.withdrawErrorMessage = '';

                    var profileBankId = $("select[name=profileBankId1]");
                    var profileIban   = $("input[name=profileIban1]");

                    var profileYrFirmname = $("input[name=profileYrFirmname]");
                    var profileYrAdress = $("input[name=profileYrAdress]");
                    var profileYrBin = $("input[name=profileYrBin]");
                    var profileYrBankname = $("input[name=profileYrBankname]");
                    var profileYrIik = $("input[name=profileYrIik]");
                    var profileYrBankbic = $("input[name=profileYrBankbic]");
                    var profileYrKbe = $("input[name=profileYrKbe]");
                    var profileYrPhone = $("input[name=profileYrPhone]");

                    if( profileBankId.val() == '' ){

                        profileBankId.addClass('error');
                        this.isWithdrawError = true;
                        this.withdrawErrorMessage = '{{ __('Bank cannot be empty') }}';
                    }

                    if( profileIban.val().length == 0 ){

                        profileIban.addClass('error');
                        this.isWithdrawError = true;
                        this.withdrawErrorMessage = '{{ __('IBAN cannot be empty') }}';
                    }

                    if( profileIban.val().length != 20 ){

                        profileIban.addClass('error');
                        this.isWithdrawError = true;
                        this.withdrawErrorMessage = '{{ __('IBAN cannot be less or more than 20 characters') }}';
                    }

                    // test yr data
                    if( this.profileYrBlockFlag ){

                        if( profileYrFirmname.val() == '' ){

                            profileYrFirmname.addClass('error');
                            this.isWithdrawError = true;
                            this.withdrawErrorMessage = '{{ __('The organization name cannot be empty') }}';
                        }
                        if( profileYrAdress.val() == '' ){

                            profileYrAdress.addClass('error');
                            this.isWithdrawError = true;
                            this.withdrawErrorMessage = '{{ __('The address cannot be empty') }}';
                        }
                        if( profileYrAdress.val().length < 10 ){

                            profileYrAdress.addClass('error');
                            this.isWithdrawError = true;
                            this.withdrawErrorMessage = '{{ __('The address cannot be less than 10 characters') }}';
                        }
                        if( profileYrBin.val() == '' ){

                            profileYrBin.addClass('error');
                            this.isWithdrawError = true;
                            this.withdrawErrorMessage = '{{ __('The BIN cannot be empty') }}';
                        }
                        if( profileYrBankname.val() == '' ){

                            profileYrBankname.addClass('error');
                            this.isWithdrawError = true;
                            this.withdrawErrorMessage = '{{ __('The Bank name cannot be empty') }}';
                        }
                        if( profileYrIik.val() == '' ){

                            profileYrIik.addClass('error');
                            this.isWithdrawError = true;
                            this.withdrawErrorMessage = '{{ __('The IIK cannot be empty') }}';
                        }
                        if( profileYrBankbic.val() == '' ){

                            profileYrBankbic.addClass('error');
                            this.isWithdrawError = true;
                            this.withdrawErrorMessage = '{{ __('The Bank is BIC cannot be empty') }}';
                        }
                        if( profileYrKbe.val() == '' ){

                            profileYrKbe.addClass('error');
                            this.isWithdrawError = true;
                            this.withdrawErrorMessage = '{{ __('The KBE cannot be empty') }}';
                        }
                        if( profileYrPhone.val() == '' ){

                            profileYrPhone.addClass('error');
                            this.isWithdrawError = true;
                            this.withdrawErrorMessage = '{{ __('The phone number cannot be empty') }}';
                        }
                    }

                    if( this.isWithdrawError ){
                        $('#withdrawPayment').scrollTop(0);
                        return;
                    }

                    this.profileDataRequest = true;
                    var self = this;
                    axios.post('{{ route('agitatorRegisterProfileIbanPost') }}',{
                        "_token": "{{ csrf_token() }}",
                        "bank_id": this.profileBankId,
                        "iban": this.profileIban,
                        "yr_data": this.profileUserBusiness,
                        "is_yr": this.profileYrBlockFlag
                    })
                    .then(function(response){

                        if( response.data.status ) {

                            self.withdrawErrorMessage = '{{ __('Data changed successfully') }}';
                        } else {

                            self.isWithdrawError = true;
                            self.withdrawErrorMessage = response.data.status;

                        }
                        self.profileDataRequest = false;
                    })
                    .catch( error => {

                        console.log(error)
                    });
                },
                profileWithdrawRequest: function(){

                    this.isWithdrawError = false;
                    this.withdrawErrorMessage = '';

                    var profileBankId           = $("select[name=profileBankId1]");
                    var profileIban             = $("input[name=profileIban1]");
                    var profileWithdrawAmount   = $("input[name=profileWithdrawAmount1]");

                    if( profileBankId.val() == '' ){

                        profileBankId.addClass('error');
                        this.isWithdrawError = true;
                        this.withdrawErrorMessage = '{{ __('Bank cannot be empty') }}';
                    }

                    if( profileIban.val().length == 0 ){

                        profileIban.addClass('error');
                        this.isWithdrawError = true;
                        this.withdrawErrorMessage = '{{ __('IBAN cannot be empty') }}';
                    }

                    if( profileIban.val().length < 10 ){

                        profileIban.addClass('error');
                        this.isWithdrawError = true;
                        this.withdrawErrorMessage = '{{ __('IBAN cannot be less than 10 characters') }}';
                    }

                    if( profileWithdrawAmount.val() <= 0 ){

                        profileWithdrawAmount.addClass('error');
                        this.isWithdrawError = true;
                        this.withdrawErrorMessage = '{{ __('The withdrawal amount cannot be empty') }}';
                    }

                    if( this.profileWithdrawAmount > this.profileAgitatorAvailableBalance ){

                        profileWithdrawAmount.addClass('error');
                        this.isWithdrawError = true;
                        this.withdrawErrorMessage = '{{ __('The withdrawal amount is greater than the allowed amount') }}';
                    }

                    if( (this.profileWithdrawAmount % 5000) != 0 ){

                        profileWithdrawAmount.addClass('error');
                        this.isWithdrawError = true;
                        this.withdrawErrorMessage = '{{ __('The withdrawal amount must be a multiple of 5000') }}';
                    }

                    if( this.isWithdrawError ){
                        $('#withdrawPayment').scrollTop(0);
                        return;
                    }

                    this.profileDataRequest = true;
                    var self = this;
                    axios.post('{{ route('agitatorProfileSendWithdrawRequest') }}',{
                        "_token": "{{ csrf_token() }}",
                        "bank_id": this.profileBankId,
                        "iban": this.profileIban,
                        "cost": this.profileWithdrawAmount
                    })
                    .then(function(response){

                        if( response.data.status ) {

                            self.withdrawErrorMessage = response.data.message;
                            self.profileLoadData();
                            self.profileAgitatorLoadTransactionHistory();
                            self.profileLoadAgitatorUsers();

                            $('#withdrawPayment').scrollTop(0);

                        } else {

                            self.isWithdrawError = true;
                            self.withdrawErrorMessage = response.data.status;

                        }
                        self.profileDataRequest = false;
                    })
                    .catch( error => {

                        console.log(error)
                    });

                },
                profileGetWithdrawInfo: function(){

                    var self = this;
                    axios.post('{{ route('AgitatorGetWithdrawInfo') }}',{
                        "_token": "{{ csrf_token() }}",
                        "cost": this.profileWithdrawAmount
                    })
                    .then(function(response){

                        if( response.data.status ) {

                            self.profileWithdrawInfo = response.data.withdrawInfo;

                        } else {}

                    })
                    .catch( error => {

                        console.log(error)
                    });
                }
            },
            created: function(){

                this.profileLoadData();
                this.profileLoadAgitatorUsers();
                this.profileAgitatorLoadTransactionHistory();

            }

        });

    </script>

    <script type="text/javascript">

        window.onload = function(){

            $('[data-toggle="tooltip"]').tooltip();

        };

    </script>



@endsection