@extends("admin.admin_app")

@section("content")
    <div id="nobd">
        <div class="page-header">
            <h2> Данные НОБД </h2>
        </div>

        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row padding-20">


                    <div class="col-12 padding-15">


                        <div v-if="errorMessage" :class="{ 'alert-danger': isError, 'alert-success': !isError }" class="alert margin-t20 margin-b20">
                            <div v-html="errorMessage"> </div>
                        </div>


                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

                            <!-- академическом отпуске -->
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingnobdAcademicLeave">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#nobdAcademicLeave" aria-expanded="true" aria-controls="nobdAcademicLeave">
                                            Академический отпуск
                                        </a>
                                    </h4>
                                </div>
                                <div id="nobdAcademicLeave" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingnobdAcademicLeave">
                                    <div class="panel-body">

                                        <div class="pull-right padding-10">
                                            <button @click="dataEdit(0,'nobd_academic_leave')" class="btn btn-primary"> Добавить </button>
                                        </div>
                                        <div class="clearfix"></div>

                                        <table class="table">
                                            <tr>
                                                <td> ID </td>
                                                <td> Код </td>
                                                <td> Название </td>
                                                <td>  </td>
                                            </tr>
                                            <template v-if="nobdAcademicLeaveList">
                                                <tr v-for="(itemAL, indexAL) in nobdAcademicLeaveList.data">
                                                    <td> @{{ itemAL.id }} </td>
                                                    <td> @{{ itemAL.code }} </td>
                                                    <td> @{{ itemAL.name }} </td>
                                                    <td>
                                                        <button @click="dataEdit(itemAL.id,'nobd_academic_leave')" :disabled="dataRequest" class="btn btn-success" type="button"><i class="fa fa-edit"></i></button>
                                                        <button @click="dataRemove(itemAL.id,'nobd_academic_leave')" :disabled="dataRequest" class="btn btn-danger" type="button"><i class="fa fa-minus-circle"></i></button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </table>
                                        <nav aria-label="Page navigation">
                                            <academicleave
                                                    v-model="nobdAcademicLeavePageNum"
                                                    :page-count="nobdAcademicLeaveList.last_page"
                                                    :page-range="3"
                                                    :margin-pages="2"
                                                    :click-handler="academicLeavePaginate"
                                                    :prev-text="'{{ __('Previous') }}'"
                                                    :next-text="'{{ __('Next') }}'"
                                                    :container-class="'pagination'"
                                                    :page-class="'page-item'"
                                                    :page-link-class="'page-link'"
                                                    :prev-class="'page-link'"
                                                    :next-class="'page-link'">
                                            </academicleave>
                                        </nav>

                                    </div>
                                </div>
                            </div>

                            <!-- академическая мобильность -->
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingnobdAcademicMobility">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#AcademicMobility" aria-expanded="true" aria-controls="AcademicMobility">
                                            Академическая мобильность
                                        </a>
                                    </h4>
                                </div>
                                <div id="AcademicMobility" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingnobdAcademicMobility">
                                    <div class="panel-body">

                                        <div class="pull-right padding-10">
                                            <button @click="dataEdit(0,'nobd_academic_mobility')" class="btn btn-primary"> Добавить </button>
                                        </div>
                                        <div class="clearfix"></div>

                                        <table class="table">
                                            <tr>
                                                <td> ID </td>
                                                <td> Код </td>
                                                <td> Название </td>
                                                <td>  </td>
                                            </tr>
                                            <template v-if="nobdAcademicMobilityList">
                                                <tr v-for="(itemML, indexML) in nobdAcademicMobilityList.data">
                                                    <td> @{{ itemML.id }} </td>
                                                    <td> @{{ itemML.code }} </td>
                                                    <td> @{{ itemML.name }} </td>
                                                    <td>
                                                        <button @click="dataEdit(itemML.id,'nobd_academic_mobility')" :disabled="dataRequest" class="btn btn-success" type="button"><i class="fa fa-edit"></i></button>
                                                        <button @click="dataRemove(itemML.id,'nobd_academic_mobility')" :disabled="dataRequest" class="btn btn-danger" type="button"><i class="fa fa-minus-circle"></i></button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </table>
                                        <nav aria-label="Page navigation">
                                            <academicmobility
                                                    v-model="nobdAcademicMobilityPageNum"
                                                    :page-count="nobdAcademicMobilityList.last_page"
                                                    :page-range="3"
                                                    :margin-pages="2"
                                                    :click-handler="academicMobilityPaginate"
                                                    :prev-text="'{{ __('Previous') }}'"
                                                    :next-text="'{{ __('Next') }}'"
                                                    :container-class="'pagination'"
                                                    :page-class="'page-item'"
                                                    :page-link-class="'page-link'"
                                                    :prev-class="'page-link'"
                                                    :next-class="'page-link'">
                                            </academicmobility>
                                        </nav>
                                    </div>
                                </div>
                            </div>

                            <!-- причины отчисления -->
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingnobdCauseStayYear">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#CauseStayYear" aria-expanded="true" aria-controls="CauseStayYear">
                                            Причины отчисления
                                        </a>
                                    </h4>
                                </div>
                                <div id="CauseStayYear" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingnobdCauseStayYear">
                                    <div class="panel-body">

                                        <div class="pull-right padding-10">
                                            <button @click="dataEdit(0,'nobd_cause_stay_year')" class="btn btn-primary"> Добавить </button>
                                        </div>
                                        <div class="clearfix"></div>

                                        <table class="table">
                                            <tr>
                                                <td> ID </td>
                                                <td> Код </td>
                                                <td> Название </td>
                                                <td>  </td>
                                            </tr>
                                            <template v-if="nobdCauseStayYearList">
                                                <tr v-for="(itemCSY, indexCSY) in nobdCauseStayYearList.data">
                                                    <td> @{{ itemCSY.id }} </td>
                                                    <td> @{{ itemCSY.code }} </td>
                                                    <td> @{{ itemCSY.name }} </td>
                                                    <td>
                                                        <button @click="dataEdit(itemCSY.id,'nobd_cause_stay_year')" :disabled="dataRequest" class="btn btn-success" type="button"><i class="fa fa-edit"></i></button>
                                                        <button @click="dataRemove(itemCSY.id,'nobd_cause_stay_year')" :disabled="dataRequest" class="btn btn-danger" type="button"><i class="fa fa-minus-circle"></i></button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </table>
                                        <nav aria-label="Page navigation">
                                            <causestayyear
                                                    v-model="nobdCauseStayYearPageNum"
                                                    :page-count="nobdCauseStayYearList.last_page"
                                                    :page-range="3"
                                                    :margin-pages="2"
                                                    :click-handler="causeStayYearPaginate"
                                                    :prev-text="'{{ __('Previous') }}'"
                                                    :next-text="'{{ __('Next') }}'"
                                                    :container-class="'pagination'"
                                                    :page-class="'page-item'"
                                                    :page-link-class="'page-link'"
                                                    :prev-class="'page-link'"
                                                    :next-class="'page-link'">
                                            </causestayyear>
                                        </nav>
                                    </div>
                                </div>
                            </div>

                            <!-- страны -->
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingNobdCountry">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#Country" aria-expanded="true" aria-controls="Country">
                                            Страны
                                        </a>
                                    </h4>
                                </div>
                                <div id="Country" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingNobdCountry">
                                    <div class="panel-body">

                                        <div class="pull-right padding-10">
                                            <button @click="dataEdit(0,'nobd_country')" class="btn btn-primary"> Добавить </button>
                                        </div>
                                        <div class="clearfix"></div>

                                        <table class="table">
                                            <tr>
                                                <td> ID </td>
                                                <td> Код </td>
                                                <td> Название </td>
                                                <td>  </td>
                                            </tr>
                                            <template v-if="nobdCountryList">
                                                <tr v-for="(itemC, indexC) in nobdCountryList.data">
                                                    <td> @{{ itemC.id }} </td>
                                                    <td> @{{ itemC.code }} </td>
                                                    <td> @{{ itemC.name }} </td>
                                                    <td>
                                                        <button @click="dataEdit(itemC.id,'nobd_country')" :disabled="dataRequest" class="btn btn-success" type="button"><i class="fa fa-edit"></i></button>
                                                        <button @click="dataRemove(itemC.id,'nobd_country')" :disabled="dataRequest" class="btn btn-danger" type="button"><i class="fa fa-minus-circle"></i></button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </table>
                                        <nav aria-label="Page navigation">
                                            <country
                                                    v-model="nobdCountryPageNum"
                                                    :page-count="nobdCountryList.last_page"
                                                    :page-range="3"
                                                    :margin-pages="2"
                                                    :click-handler="countryPaginate"
                                                    :prev-text="'{{ __('Previous') }}'"
                                                    :next-text="'{{ __('Next') }}'"
                                                    :container-class="'pagination'"
                                                    :page-class="'page-item'"
                                                    :page-link-class="'page-link'"
                                                    :prev-class="'page-link'"
                                                    :next-class="'page-link'">
                                            </country>
                                        </nav>
                                    </div>
                                </div>
                            </div>

                            <!-- группа инвалидности -->
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingNobdDisabilityGroup">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#DisabilityGroup" aria-expanded="true" aria-controls="DisabilityGroup">
                                            Группы инвалидности
                                        </a>
                                    </h4>
                                </div>
                                <div id="DisabilityGroup" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingNobdDisabilityGroup">
                                    <div class="panel-body">

                                        <div class="pull-right padding-10">
                                            <button @click="dataEdit(0,'nobd_disability_group')" class="btn btn-primary"> Добавить </button>
                                        </div>
                                        <div class="clearfix"></div>

                                        <table class="table">
                                            <tr>
                                                <td> ID </td>
                                                <td> Код </td>
                                                <td> Название </td>
                                                <td>  </td>
                                            </tr>
                                            <template v-if="nobdDisabilityGroupList">
                                                <tr v-for="(itemDG, indexDG) in nobdDisabilityGroupList.data">
                                                    <td> @{{ itemDG.id }} </td>
                                                    <td> @{{ itemDG.code }} </td>
                                                    <td> @{{ itemDG.name }} </td>
                                                    <td>
                                                        <button @click="dataEdit(itemDG.id,'nobd_disability_group')" :disabled="dataRequest" class="btn btn-success" type="button"><i class="fa fa-edit"></i></button>
                                                        <button @click="dataRemove(itemDG.id,'nobd_disability_group')" :disabled="dataRequest" class="btn btn-danger" type="button"><i class="fa fa-minus-circle"></i></button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </table>
                                        <nav aria-label="Page navigation">
                                            <disabilitygroup
                                                    v-model="nobdDisabilityGroupPageNum"
                                                    :page-count="nobdDisabilityGroupList.last_page"
                                                    :page-range="3"
                                                    :margin-pages="2"
                                                    :click-handler="disabilityGroupPaginate"
                                                    :prev-text="'{{ __('Previous') }}'"
                                                    :next-text="'{{ __('Next') }}'"
                                                    :container-class="'pagination'"
                                                    :page-class="'page-item'"
                                                    :page-link-class="'page-link'"
                                                    :prev-class="'page-link'"
                                                    :next-class="'page-link'">
                                            </disabilitygroup>
                                        </nav>
                                    </div>
                                </div>
                            </div>

                            <!-- трудоустройство -->
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingNobdEmploymentOpportunity">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#EmploymentOpportunity" aria-expanded="true" aria-controls="EmploymentOpportunity">
                                            Трудоустройство
                                        </a>
                                    </h4>
                                </div>
                                <div id="EmploymentOpportunity" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingNobdEmploymentOpportunity">
                                    <div class="panel-body">

                                        <div class="pull-right padding-10">
                                            <button @click="dataEdit(0,'nobd_employment_opportunity')" class="btn btn-primary"> Добавить </button>
                                        </div>
                                        <div class="clearfix"></div>

                                        <table class="table">
                                            <tr>
                                                <td> ID </td>
                                                <td> Код </td>
                                                <td> Название </td>
                                                <td>  </td>
                                            </tr>
                                            <template v-if="nobdEmploymentOpportunityList">
                                                <tr v-for="(itemEO, indexEO) in nobdEmploymentOpportunityList.data">
                                                    <td> @{{ itemEO.id }} </td>
                                                    <td> @{{ itemEO.code }} </td>
                                                    <td> @{{ itemEO.name }} </td>
                                                    <td>
                                                        <button @click="dataEdit(itemEO.id,'nobd_employment_opportunity')" :disabled="dataRequest" class="btn btn-success" type="button"><i class="fa fa-edit"></i></button>
                                                        <button @click="dataRemove(itemEO.id,'nobd_employment_opportunity')" :disabled="dataRequest" class="btn btn-danger" type="button"><i class="fa fa-minus-circle"></i></button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </table>
                                        <nav aria-label="Page navigation">
                                            <employmentopportunity
                                                    v-model="nobdEmploymentOpportunityPageNum"
                                                    :page-count="nobdEmploymentOpportunityList.last_page"
                                                    :page-range="3"
                                                    :margin-pages="2"
                                                    :click-handler="employmentOpportunityPaginate"
                                                    :prev-text="'{{ __('Previous') }}'"
                                                    :next-text="'{{ __('Next') }}'"
                                                    :container-class="'pagination'"
                                                    :page-class="'page-item'"
                                                    :page-link-class="'page-link'"
                                                    :prev-class="'page-link'"
                                                    :next-class="'page-link'">
                                            </employmentopportunity>
                                        </nav>
                                    </div>
                                </div>
                            </div>

                            <!-- мероприятия -->
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingNobdEvents">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#Events" aria-expanded="true" aria-controls="Events">
                                            Мероприятия
                                        </a>
                                    </h4>
                                </div>
                                <div id="Events" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingNobdEvents">
                                    <div class="panel-body">

                                        <div class="pull-right padding-10">
                                            <button @click="dataEdit(0,'nobd_events')" class="btn btn-primary"> Добавить </button>
                                        </div>
                                        <div class="clearfix"></div>

                                        <table class="table">
                                            <tr>
                                                <td> ID </td>
                                                <td> Код </td>
                                                <td> Название </td>
                                                <td>  </td>
                                            </tr>
                                            <template v-if="nobdEventsList">
                                                <tr v-for="(itemE, indexE) in nobdEventsList.data">
                                                    <td> @{{ itemE.id }} </td>
                                                    <td> @{{ itemE.code }} </td>
                                                    <td> @{{ itemE.name }} </td>
                                                    <td>
                                                        <button @click="dataEdit(itemE.id,'nobd_events')" :disabled="dataRequest" class="btn btn-success" type="button"><i class="fa fa-edit"></i></button>
                                                        <button @click="dataRemove(itemE.id,'nobd_events')" :disabled="dataRequest" class="btn btn-danger" type="button"><i class="fa fa-minus-circle"></i></button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </table>
                                        <nav aria-label="Page navigation">
                                            <events
                                                    v-model="nobdEventsPageNum"
                                                    :page-count="nobdEventsList.last_page"
                                                    :page-range="3"
                                                    :margin-pages="2"
                                                    :click-handler="eventsPaginate"
                                                    :prev-text="'{{ __('Previous') }}'"
                                                    :next-text="'{{ __('Next') }}'"
                                                    :container-class="'pagination'"
                                                    :page-class="'page-item'"
                                                    :page-link-class="'page-link'"
                                                    :prev-class="'page-link'"
                                                    :next-class="'page-link'">
                                            </events>
                                        </nav>
                                    </div>
                                </div>
                            </div>

                            <!-- специальность по обмену -->
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="NobdExchangeSpecialty">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#ExchangeSpecialty" aria-expanded="true" aria-controls="ExchangeSpecialty">
                                            Специальность по обмену
                                        </a>
                                    </h4>
                                </div>
                                <div id="ExchangeSpecialty" class="panel-collapse collapse" role="tabpanel" aria-labelledby="NobdExchangeSpecialty">
                                    <div class="panel-body">

                                        <div class="pull-right padding-10">
                                            <button @click="dataEdit(0,'nobd_exchange_specialty')" class="btn btn-primary"> Добавить </button>
                                        </div>
                                        <div class="clearfix"></div>

                                        <table class="table">
                                            <tr>
                                                <td> ID </td>
                                                <td> Код </td>
                                                <td> Название </td>
                                                <td>  </td>
                                            </tr>
                                            <template v-if="nobdExchangeSpecialtyList">
                                                <tr v-for="(itemES, indexES) in nobdExchangeSpecialtyList.data">
                                                    <td> @{{ itemES.id }} </td>
                                                    <td> @{{ itemES.code }} </td>
                                                    <td> @{{ itemES.name }} </td>
                                                    <td>
                                                        <button @click="dataEdit(itemES.id,'nobd_exchange_specialty')" :disabled="dataRequest" class="btn btn-success" type="button"><i class="fa fa-edit"></i></button>
                                                        <button @click="dataRemove(itemES.id,'nobd_exchange_specialty')" :disabled="dataRequest" class="btn btn-danger" type="button"><i class="fa fa-minus-circle"></i></button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </table>
                                        <nav aria-label="Page navigation">
                                            <exchangespecialty
                                                    v-model="nobdExchangeSpecialtyPageNum"
                                                    :page-count="nobdExchangeSpecialtyList.last_page"
                                                    :page-range="3"
                                                    :margin-pages="2"
                                                    :click-handler="exchangeSpecialtyPaginate"
                                                    :prev-text="'{{ __('Previous') }}'"
                                                    :next-text="'{{ __('Next') }}'"
                                                    :container-class="'pagination'"
                                                    :page-class="'page-item'"
                                                    :page-link-class="'page-link'"
                                                    :prev-class="'page-link'"
                                                    :next-class="'page-link'">
                                            </exchangespecialty>
                                        </nav>
                                    </div>
                                </div>
                            </div>

                            <!-- вид диплома -->
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="NobdFormDiplom">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#formDiplom" aria-expanded="true" aria-controls="formDiplom">
                                            Вид диплома
                                        </a>
                                    </h4>
                                </div>
                                <div id="formDiplom" class="panel-collapse collapse" role="tabpanel" aria-labelledby="NobdFormDiplom">
                                    <div class="panel-body">

                                        <div class="pull-right padding-10">
                                            <button @click="dataEdit(0,'nobd_form_diplom')" class="btn btn-primary"> Добавить </button>
                                        </div>
                                        <div class="clearfix"></div>

                                        <table class="table">
                                            <tr>
                                                <td> ID </td>
                                                <td> Код </td>
                                                <td> Название </td>
                                                <td>  </td>
                                            </tr>
                                            <template v-if="nobdFormDiplomList">
                                                <tr v-for="(itemFD, indexFD) in nobdFormDiplomList.data">
                                                    <td> @{{ itemFD.id }} </td>
                                                    <td> @{{ itemFD.code }} </td>
                                                    <td> @{{ itemFD.name }} </td>
                                                    <td>
                                                        <button @click="dataEdit(itemFD.id,'nobd_form_diplom')" :disabled="dataRequest" class="btn btn-success" type="button"><i class="fa fa-edit"></i></button>
                                                        <button @click="dataRemove(itemFD.id,'nobd_form_diplom')" :disabled="dataRequest" class="btn btn-danger" type="button"><i class="fa fa-minus-circle"></i></button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </table>
                                        <nav aria-label="Page navigation">
                                            <formdiplom
                                                    v-model="nobdFormDiplomPageNum"
                                                    :page-count="nobdFormDiplomList.last_page"
                                                    :page-range="3"
                                                    :margin-pages="2"
                                                    :click-handler="formDiplomPaginate"
                                                    :prev-text="'{{ __('Previous') }}'"
                                                    :next-text="'{{ __('Next') }}'"
                                                    :container-class="'pagination'"
                                                    :page-class="'page-item'"
                                                    :page-link-class="'page-link'"
                                                    :prev-class="'page-link'"
                                                    :next-class="'page-link'">
                                            </formdiplom>
                                        </nav>
                                    </div>
                                </div>
                            </div>

                            <!-- языки -->
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="NobdLanguage">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#Language" aria-expanded="true" aria-controls="Language">
                                            Языки
                                        </a>
                                    </h4>
                                </div>
                                <div id="Language" class="panel-collapse collapse" role="tabpanel" aria-labelledby="NobdLanguage">
                                    <div class="panel-body">

                                        <div class="pull-right padding-10">
                                            <button @click="dataEdit(0,'nobd_language')" class="btn btn-primary"> Добавить </button>
                                        </div>
                                        <div class="clearfix"></div>

                                        <table class="table">
                                            <tr>
                                                <td> ID </td>
                                                <td> Код </td>
                                                <td> Название </td>
                                                <td>  </td>
                                            </tr>
                                            <template v-if="nobdLanguageList">
                                                <tr v-for="(itemL, indexL) in nobdLanguageList.data">
                                                    <td> @{{ itemL.id }} </td>
                                                    <td> @{{ itemL.code }} </td>
                                                    <td> @{{ itemL.name }} </td>
                                                    <td>
                                                        <button @click="dataEdit(itemL.id,'nobd_language')" :disabled="dataRequest" class="btn btn-success" type="button"><i class="fa fa-edit"></i></button>
                                                        <button @click="dataRemove(itemL.id,'nobd_language')" :disabled="dataRequest" class="btn btn-danger" type="button"><i class="fa fa-minus-circle"></i></button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </table>
                                        <nav aria-label="Page navigation">
                                            <language
                                                    v-model="nobdLanguagePageNum"
                                                    :page-count="nobdLanguageList.last_page"
                                                    :page-range="3"
                                                    :margin-pages="2"
                                                    :click-handler="languagePaginate"
                                                    :prev-text="'{{ __('Previous') }}'"
                                                    :next-text="'{{ __('Next') }}'"
                                                    :container-class="'pagination'"
                                                    :page-class="'page-item'"
                                                    :page-link-class="'page-link'"
                                                    :prev-class="'page-link'"
                                                    :next-class="'page-link'">
                                            </language>
                                        </nav>
                                    </div>
                                </div>
                            </div>

                            <!-- тип оплаты -->
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="NobdPaymentType">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#PaymentType" aria-expanded="true" aria-controls="PaymentType">
                                            Тип оплаты
                                        </a>
                                    </h4>
                                </div>
                                <div id="PaymentType" class="panel-collapse collapse" role="tabpanel" aria-labelledby="NobdPaymentType">
                                    <div class="panel-body">

                                        <div class="pull-right padding-10">
                                            <button @click="dataEdit(0,'nobd_payment_type')" class="btn btn-primary"> Добавить </button>
                                        </div>
                                        <div class="clearfix"></div>

                                        <table class="table">
                                            <tr>
                                                <td> ID </td>
                                                <td> Код </td>
                                                <td> Название </td>
                                                <td>  </td>
                                            </tr>
                                            <template v-if="nobdPaymentTypeList">
                                                <tr v-for="(itemPT, indexPT) in nobdPaymentTypeList.data">
                                                    <td> @{{ itemPT.id }} </td>
                                                    <td> @{{ itemPT.code }} </td>
                                                    <td> @{{ itemPT.name }} </td>
                                                    <td>
                                                        <button @click="dataEdit(itemPT.id,'nobd_payment_type')" :disabled="dataRequest" class="btn btn-success" type="button"><i class="fa fa-edit"></i></button>
                                                        <button @click="dataRemove(itemPT.id,'nobd_payment_type')" :disabled="dataRequest" class="btn btn-danger" type="button"><i class="fa fa-minus-circle"></i></button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </table>
                                        <nav aria-label="Page navigation">
                                            <paymenttype
                                                    v-model="nobdPaymentTypePageNum"
                                                    :page-count="nobdPaymentTypeList.last_page"
                                                    :page-range="3"
                                                    :margin-pages="2"
                                                    :click-handler="paymentTypePaginate"
                                                    :prev-text="'{{ __('Previous') }}'"
                                                    :next-text="'{{ __('Next') }}'"
                                                    :container-class="'pagination'"
                                                    :page-class="'page-item'"
                                                    :page-link-class="'page-link'"
                                                    :prev-class="'page-link'"
                                                    :next-class="'page-link'">
                                            </paymenttype>
                                        </nav>
                                    </div>
                                </div>
                            </div>

                            <!-- причина выбытия -->
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="NobdReasonDisposal">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#ReasonDisposal" aria-expanded="true" aria-controls="ReasonDisposal">
                                            Причина выбытия
                                        </a>
                                    </h4>
                                </div>
                                <div id="ReasonDisposal" class="panel-collapse collapse" role="tabpanel" aria-labelledby="NobdReasonDisposal">
                                    <div class="panel-body">

                                        <div class="pull-right padding-10">
                                            <button @click="dataEdit(0,'nobd_reason_disposal')" class="btn btn-primary"> Добавить </button>
                                        </div>
                                        <div class="clearfix"></div>

                                        <table class="table">
                                            <tr>
                                                <td> ID </td>
                                                <td> Код </td>
                                                <td> Название </td>
                                                <td>  </td>
                                            </tr>
                                            <template v-if="nobdReasonDisposalList">
                                                <tr v-for="(itemRD, indexRD) in nobdReasonDisposalList.data">
                                                    <td> @{{ itemRD.id }} </td>
                                                    <td> @{{ itemRD.code }} </td>
                                                    <td> @{{ itemRD.name }} </td>
                                                    <td>
                                                        <button @click="dataEdit(itemRD.id,'nobd_reason_disposal')" :disabled="dataRequest" class="btn btn-success" type="button"><i class="fa fa-edit"></i></button>
                                                        <button @click="dataRemove(itemRD.id,'nobd_reason_disposal')" :disabled="dataRequest" class="btn btn-danger" type="button"><i class="fa fa-minus-circle"></i></button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </table>
                                        <nav aria-label="Page navigation">
                                            <reasondisposal
                                                    v-model="nobdReasonDisposalPageNum"
                                                    :page-count="nobdReasonDisposalList.last_page"
                                                    :page-range="3"
                                                    :margin-pages="2"
                                                    :click-handler="reasonDisposalPaginate"
                                                    :prev-text="'{{ __('Previous') }}'"
                                                    :next-text="'{{ __('Next') }}'"
                                                    :container-class="'pagination'"
                                                    :page-class="'page-item'"
                                                    :page-link-class="'page-link'"
                                                    :prev-class="'page-link'"
                                                    :next-class="'page-link'">
                                            </reasondisposal>
                                        </nav>
                                    </div>
                                </div>
                            </div>

                            <!-- награда -->
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="NobdReward">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#Reward" aria-expanded="true" aria-controls="Reward">
                                            Награды
                                        </a>
                                    </h4>
                                </div>
                                <div id="Reward" class="panel-collapse collapse" role="tabpanel" aria-labelledby="NobdReward">
                                    <div class="panel-body">

                                        <div class="pull-right padding-10">
                                            <button @click="dataEdit(0,'nobd_reward')" class="btn btn-primary"> Добавить </button>
                                        </div>
                                        <div class="clearfix"></div>

                                        <table class="table">
                                            <tr>
                                                <td> ID </td>
                                                <td> Код </td>
                                                <td> Название </td>
                                                <td>  </td>
                                            </tr>
                                            <template v-if="nobdRewardList">
                                                <tr v-for="(itemR, indexR) in nobdRewardList.data">
                                                    <td> @{{ itemR.id }} </td>
                                                    <td> @{{ itemR.code }} </td>
                                                    <td> @{{ itemR.name }} </td>
                                                    <td>
                                                        <button @click="dataEdit(itemR.id,'nobd_reward')" :disabled="dataRequest" class="btn btn-success" type="button"><i class="fa fa-edit"></i></button>
                                                        <button @click="dataRemove(itemR.id,'nobd_reward')" :disabled="dataRequest" class="btn btn-danger" type="button"><i class="fa fa-minus-circle"></i></button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </table>
                                        <nav aria-label="Page navigation">
                                            <reward
                                                    v-model="nobdRewardPageNum"
                                                    :page-count="nobdRewardList.last_page"
                                                    :page-range="3"
                                                    :margin-pages="2"
                                                    :click-handler="rewardPaginate"
                                                    :prev-text="'{{ __('Previous') }}'"
                                                    :next-text="'{{ __('Next') }}'"
                                                    :container-class="'pagination'"
                                                    :page-class="'page-item'"
                                                    :page-link-class="'page-link'"
                                                    :prev-class="'page-link'"
                                                    :next-class="'page-link'">
                                            </reward>
                                        </nav>
                                    </div>
                                </div>
                            </div>

                            <!-- обучается по квоте -->
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="NobdTrainedQuota">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#TrainedQuota" aria-expanded="true" aria-controls="TrainedQuota">
                                            Обучается по квоте
                                        </a>
                                    </h4>
                                </div>
                                <div id="TrainedQuota" class="panel-collapse collapse" role="tabpanel" aria-labelledby="NobdTrainedQuota">
                                    <div class="panel-body">

                                        <div class="pull-right padding-10">
                                            <button @click="dataEdit(0,'nobd_trained_quota')" class="btn btn-primary"> Добавить </button>
                                        </div>
                                        <div class="clearfix"></div>

                                        <table class="table">
                                            <tr>
                                                <td> ID </td>
                                                <td> Код </td>
                                                <td> Название </td>
                                                <td>  </td>
                                            </tr>
                                            <template v-if="nobdTrainedQuotaList">
                                                <tr v-for="(itemTQ, indexTQ) in nobdTrainedQuotaList.data">
                                                    <td> @{{ itemTQ.id }} </td>
                                                    <td> @{{ itemTQ.code }} </td>
                                                    <td> @{{ itemTQ.name }} </td>
                                                    <td>
                                                        <button @click="dataEdit(itemTQ.id,'nobd_trained_quota')" :disabled="dataRequest" class="btn btn-success" type="button"><i class="fa fa-edit"></i></button>
                                                        <button @click="dataRemove(itemTQ.id,'nobd_trained_quota')" :disabled="dataRequest" class="btn btn-danger" type="button"><i class="fa fa-minus-circle"></i></button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </table>
                                        <nav aria-label="Page navigation">
                                            <trainedquota
                                                    v-model="nobdTrainedQuotaPageNum"
                                                    :page-count="nobdTrainedQuotaList.last_page"
                                                    :page-range="3"
                                                    :margin-pages="2"
                                                    :click-handler="trainedQuotaPaginate"
                                                    :prev-text="'{{ __('Previous') }}'"
                                                    :next-text="'{{ __('Next') }}'"
                                                    :container-class="'pagination'"
                                                    :page-class="'page-item'"
                                                    :page-link-class="'page-link'"
                                                    :prev-class="'page-link'"
                                                    :next-class="'page-link'">
                                            </trainedquota>
                                        </nav>
                                    </div>
                                </div>
                            </div>

                            <!-- вид направления -->
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="NobdTypeDirection">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#TypeDirection" aria-expanded="true" aria-controls="TypeDirection">
                                            Вид направления
                                        </a>
                                    </h4>
                                </div>
                                <div id="TypeDirection" class="panel-collapse collapse" role="tabpanel" aria-labelledby="NobdTypeDirection">
                                    <div class="panel-body">

                                        <div class="pull-right padding-10">
                                            <button @click="dataEdit(0,'nobd_type_direction')" class="btn btn-primary"> Добавить </button>
                                        </div>
                                        <div class="clearfix"></div>

                                        <table class="table">
                                            <tr>
                                                <td> ID </td>
                                                <td> Код </td>
                                                <td> Название </td>
                                                <td>  </td>
                                            </tr>
                                            <template v-if="nobdTypeDirectionList">
                                                <tr v-for="(itemTD, indexTD) in nobdTypeDirectionList.data">
                                                    <td> @{{ itemTD.id }} </td>
                                                    <td> @{{ itemTD.code }} </td>
                                                    <td> @{{ itemTD.name }} </td>
                                                    <td>
                                                        <button @click="dataEdit(itemTD.id,'nobd_type_direction')" :disabled="dataRequest" class="btn btn-success" type="button"><i class="fa fa-edit"></i></button>
                                                        <button @click="dataRemove(itemTD.id,'nobd_type_direction')" :disabled="dataRequest" class="btn btn-danger" type="button"><i class="fa fa-minus-circle"></i></button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </table>
                                        <nav aria-label="Page navigation">
                                            <typedirection
                                                    v-model="nobdTypeDirectionPageNum"
                                                    :page-count="nobdTypeDirectionList.last_page"
                                                    :page-range="3"
                                                    :margin-pages="2"
                                                    :click-handler="typeDirectionPaginate"
                                                    :prev-text="'{{ __('Previous') }}'"
                                                    :next-text="'{{ __('Next') }}'"
                                                    :container-class="'pagination'"
                                                    :page-class="'page-item'"
                                                    :page-link-class="'page-link'"
                                                    :prev-class="'page-link'"
                                                    :next-class="'page-link'">
                                            </typedirection>
                                        </nav>
                                    </div>
                                </div>
                            </div>

                            <!-- вид мероприятия -->
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="NobdTypeEvent">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#TypeEvent" aria-expanded="true" aria-controls="TypeEvent">
                                            Вид мероприятия
                                        </a>
                                    </h4>
                                </div>
                                <div id="TypeEvent" class="panel-collapse collapse" role="tabpanel" aria-labelledby="NobdTypeEvent">
                                    <div class="panel-body">

                                        <div class="pull-right padding-10">
                                            <button @click="dataEdit(0,'nobd_type_event')" class="btn btn-primary"> Добавить </button>
                                        </div>
                                        <div class="clearfix"></div>

                                        <table class="table">
                                            <tr>
                                                <td> ID </td>
                                                <td> Код </td>
                                                <td> Название </td>
                                                <td>  </td>
                                            </tr>
                                            <template v-if="nobdTypeEventList">
                                                <tr v-for="(itemTE, indexTE) in nobdTypeEventList.data">
                                                    <td> @{{ itemTE.id }} </td>
                                                    <td> @{{ itemTE.code }} </td>
                                                    <td> @{{ itemTE.name }} </td>
                                                    <td>
                                                        <button @click="dataEdit(itemTE.id,'nobd_type_event')" :disabled="dataRequest" class="btn btn-success" type="button"><i class="fa fa-edit"></i></button>
                                                        <button @click="dataRemove(itemTE.id,'nobd_type_event')" :disabled="dataRequest" class="btn btn-danger" type="button"><i class="fa fa-minus-circle"></i></button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </table>
                                        <nav aria-label="Page navigation">
                                            <typeevent
                                                    v-model="nobdTypeEventPageNum"
                                                    :page-count="nobdTypeEventList.last_page"
                                                    :page-range="3"
                                                    :margin-pages="2"
                                                    :click-handler="typeEventPaginate"
                                                    :prev-text="'{{ __('Previous') }}'"
                                                    :next-text="'{{ __('Next') }}'"
                                                    :container-class="'pagination'"
                                                    :page-class="'page-item'"
                                                    :page-link-class="'page-link'"
                                                    :prev-class="'page-link'"
                                                    :next-class="'page-link'">
                                            </typeevent>
                                        </nav>
                                    </div>
                                </div>
                            </div>

                            <!-- виды нарушений -->
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="NobdTypeViolation">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#TypeViolation" aria-expanded="true" aria-controls="TypeViolation">
                                            Виды нарушений
                                        </a>
                                    </h4>
                                </div>
                                <div id="TypeViolation" class="panel-collapse collapse" role="tabpanel" aria-labelledby="NobdTypeViolation">
                                    <div class="panel-body">

                                        <div class="pull-right padding-10">
                                            <button @click="dataEdit(0,'nobd_type_violation')" class="btn btn-primary"> Добавить </button>
                                        </div>
                                        <div class="clearfix"></div>

                                        <table class="table">
                                            <tr>
                                                <td> ID </td>
                                                <td> Код </td>
                                                <td> Название </td>
                                                <td>  </td>
                                            </tr>
                                            <template v-if="nobdTypeViolationList">
                                                <tr v-for="(itemTV, indexTV) in nobdTypeViolationList.data">
                                                    <td> @{{ itemTV.id }} </td>
                                                    <td> @{{ itemTV.code }} </td>
                                                    <td> @{{ itemTV.name }} </td>
                                                    <td>
                                                        <button @click="dataEdit(itemTV.id,'nobd_type_violation')" :disabled="dataRequest" class="btn btn-success" type="button"><i class="fa fa-edit"></i></button>
                                                        <button @click="dataRemove(itemTV.id,'nobd_type_violation')" :disabled="dataRequest" class="btn btn-danger" type="button"><i class="fa fa-minus-circle"></i></button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </table>
                                        <nav aria-label="Page navigation">
                                            <typeviolation
                                                    v-model="nobdTypeViolationPageNum"
                                                    :page-count="nobdTypeViolationList.last_page"
                                                    :page-range="3"
                                                    :margin-pages="2"
                                                    :click-handler="typeViolationPaginate"
                                                    :prev-text="'{{ __('Previous') }}'"
                                                    :next-text="'{{ __('Next') }}'"
                                                    :container-class="'pagination'"
                                                    :page-class="'page-item'"
                                                    :page-link-class="'page-link'"
                                                    :prev-class="'page-link'"
                                                    :next-class="'page-link'">
                                            </typeviolation>
                                        </nav>
                                    </div>
                                </div>
                            </div>

                            <!-- студент обучается по обмену -->
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="NobdStudyExchange">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#StudyExchange" aria-expanded="true" aria-controls="StudyExchange">
                                            Студент обучается по обмену
                                        </a>
                                    </h4>
                                </div>
                                <div id="StudyExchange" class="panel-collapse collapse" role="tabpanel" aria-labelledby="NobdStudyExchange">
                                    <div class="panel-body">

                                        <div class="pull-right padding-10">
                                            <button @click="dataEdit(0,'nobd_study_exchange')" class="btn btn-primary"> Добавить </button>
                                        </div>
                                        <div class="clearfix"></div>

                                        <table class="table">
                                            <tr>
                                                <td> ID </td>
                                                <td> Код </td>
                                                <td> Название </td>
                                                <td>  </td>
                                            </tr>
                                            <template v-if="nobdStudyExchangeList">
                                                <tr v-for="(itemSE, indexSE) in nobdStudyExchangeList.data">
                                                    <td> @{{ itemSE.id }} </td>
                                                    <td> @{{ itemSE.code }} </td>
                                                    <td> @{{ itemSE.name }} </td>
                                                    <td>
                                                        <button @click="dataEdit(itemSE.id,'nobd_study_exchange')" :disabled="dataRequest" class="btn btn-success" type="button"><i class="fa fa-edit"></i></button>
                                                        <button @click="dataRemove(itemSE.id,'nobd_study_exchange')" :disabled="dataRequest" class="btn btn-danger" type="button"><i class="fa fa-minus-circle"></i></button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </table>
                                        <nav aria-label="Page navigation">
                                            <studyexchange
                                                    v-model="nobdStudyExchangePageNum"
                                                    :page-count="nobdStudyExchangeList.last_page"
                                                    :page-range="3"
                                                    :margin-pages="2"
                                                    :click-handler="studyExchangePaginate"
                                                    :prev-text="'{{ __('Previous') }}'"
                                                    :next-text="'{{ __('Next') }}'"
                                                    :container-class="'pagination'"
                                                    :page-class="'page-item'"
                                                    :page-link-class="'page-link'"
                                                    :prev-class="'page-link'"
                                                    :next-class="'page-link'">
                                            </studyexchange>
                                        </nav>
                                    </div>
                                </div>
                            </div>


                        </div>


                        <!-- modal -->
                        <div id="dataModal" class="modal" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title pull-left"> Редактировать </h5>
                                        <button @click="modalClose" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body padding-20">

                                        <div class="row padding-10">
                                            <div class="form-group">
                                                <label class="col-12 control-label"> Код </label>
                                                <div class="col-12">
                                                    <input v-model="dataItem.code" class="form-control" type="text" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row padding-10">
                                            <div class="form-group">
                                                <label class="col-12 control-label"> Название </label>
                                                <div class="col-12">
                                                    <input v-model="dataItem.name" class="form-control" type="text" />
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button @click="dataSave(dataType)" class="btn btn-primary"> Сохранить </button>
                                        <button @click="modalClose" type="button" class="btn btn-info" data-dismiss="modal"> {{ __('Close') }} </button>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>

                </div>
            </div>
        </div>


    </div>

@endsection

@section('scripts')

    <script src="https://unpkg.com/vuejs-paginate@0.9.0"></script>

    <script type="text/javascript">

        Vue.component('academicleave', VuejsPaginate);
        Vue.component('academicmobility', VuejsPaginate);
        Vue.component('causestayyear', VuejsPaginate);
        Vue.component('country', VuejsPaginate);
        Vue.component('disabilitygroup', VuejsPaginate);
        Vue.component('employmentopportunity', VuejsPaginate);
        Vue.component('events', VuejsPaginate);
        Vue.component('exchangespecialty', VuejsPaginate);
        Vue.component('formdiplom', VuejsPaginate);
        Vue.component('language', VuejsPaginate);
        Vue.component('paymenttype', VuejsPaginate);
        Vue.component('reasondisposal', VuejsPaginate);
        Vue.component('reward', VuejsPaginate);
        Vue.component('trainedquota', VuejsPaginate);
        Vue.component('typedirection', VuejsPaginate);
        Vue.component('typeevent', VuejsPaginate);
        Vue.component('typeviolation', VuejsPaginate);
        Vue.component('studyexchange', VuejsPaginate);


        var app = new Vue({
            el: '#nobd',
            data: {

                isError: false,
                errorMessage: '',
                dataRequest: false,
                dataItem: {
                    code: '',
                    name: ''
                },
                dataType: '',

                nobdAcademicLeaveList: {
                    current_page: 0,
                    last_page: 0,
                    data: [],
                    total: 0
                },
                nobdAcademicLeavePageNum: 1,

                nobdAcademicMobilityList: {
                    current_page: 0,
                    last_page: 0,
                    data: [],
                    total: 0
                },
                nobdAcademicMobilityPageNum: 1,

                nobdCauseStayYearList: {
                    current_page: 0,
                    last_page: 0,
                    data: [],
                    total: 0
                },
                nobdCauseStayYearPageNum: 1,

                nobdCountryList: {
                    current_page: 0,
                    last_page: 0,
                    data: [],
                    total: 0
                },
                nobdCountryPageNum: 1,

                nobdDisabilityGroupList: {
                    current_page: 0,
                    last_page: 0,
                    data: [],
                    total: 0
                },
                nobdDisabilityGroupPageNum: 1,

                nobdEmploymentOpportunityList: {
                    current_page: 0,
                    last_page: 0,
                    data: [],
                    total: 0
                },
                nobdEmploymentOpportunityPageNum: 1,

                nobdEventsList: {
                    current_page: 0,
                    last_page: 0,
                    data: [],
                    total: 0
                },
                nobdEventsPageNum: 1,

                nobdExchangeSpecialtyList: {
                    current_page: 0,
                    last_page: 0,
                    data: [],
                    total: 0
                },
                nobdExchangeSpecialtyPageNum: 1,

                nobdFormDiplomList: {
                    current_page: 0,
                    last_page: 0,
                    data: [],
                    total: 0
                },
                nobdFormDiplomPageNum: 1,

                nobdLanguageList: {
                    current_page: 0,
                    last_page: 0,
                    data: [],
                    total: 0
                },
                nobdLanguagePageNum: 1,

                nobdPaymentTypeList: {
                    current_page: 0,
                    last_page: 0,
                    data: [],
                    total: 0
                },
                nobdPaymentTypePageNum: 1,

                nobdReasonDisposalList: {
                    current_page: 0,
                    last_page: 0,
                    data: [],
                    total: 0
                },
                nobdReasonDisposalPageNum: 1,

                nobdRewardList: {
                    current_page: 0,
                    last_page: 0,
                    data: [],
                    total: 0
                },
                nobdRewardPageNum: 1,

                nobdTrainedQuotaList: {
                    current_page: 0,
                    last_page: 0,
                    data: [],
                    total: 0
                },
                nobdTrainedQuotaPageNum: 1,

                nobdTypeDirectionList: {
                    current_page: 0,
                    last_page: 0,
                    data: [],
                    total: 0
                },
                nobdTypeDirectionPageNum: 1,

                nobdTypeEventList: {
                    current_page: 0,
                    last_page: 0,
                    data: [],
                    total: 0
                },
                nobdTypeEventPageNum: 1,

                nobdTypeViolationList: {
                    current_page: 0,
                    last_page: 0,
                    data: [],
                    total: 0
                },
                nobdTypeViolationPageNum: 1,

                nobdStudyExchangeList: {
                    current_page: 0,
                    last_page: 0,
                    data: [],
                    total: 0
                },
                nobdStudyExchangePageNum: 1

            },
            methods: {

                academicLeavePaginate: function(pageNum) {

                    this.nobdAcademicLeavePageNum = pageNum;
                    this.academicLeaveGetList();
                },
                academicMobilityPaginate: function(pageNum) {

                    this.nobdAcademicMobilityPageNum = pageNum;
                    this.academicMobilityGetList();
                },
                causeStayYearPaginate: function(pageNum) {

                    this.nobdCauseStayYearPageNum = pageNum;
                    this.causeStayYearGetList();
                },
                countryPaginate: function(pageNum) {

                    this.nobdCountryPageNum = pageNum;
                    this.countryGetList();
                },
                disabilityGroupPaginate: function(pageNum) {

                    this.nobdDisabilityGroupPageNum = pageNum;
                    this.disabilityGroupGetList();
                },
                employmentOpportunityPaginate: function(pageNum) {

                    this.nobdEmploymentOpportunityPageNum = pageNum;
                    this.employmentOpportunityGetList();
                },
                eventsPaginate: function(pageNum) {

                    this.nobdEventsPageNum = pageNum;
                    this.eventsGetList();
                },
                exchangeSpecialtyPaginate: function(pageNum) {

                    this.nobdExchangeSpecialtyPageNum = pageNum;
                    this.exchangeSpecialtyGetList();
                },
                formDiplomPaginate: function(pageNum) {

                    this.nobdFormDiplomPageNum = pageNum;
                    this.formDiplomGetList();
                },
                languagePaginate: function(pageNum) {

                    this.nobdLanguagePageNum = pageNum;
                    this.languageGetList();
                },
                paymentTypePaginate: function(pageNum) {

                    this.nobdPaymentTypePageNum = pageNum;
                    this.paymentTypeGetList();
                },
                reasonDisposalPaginate: function(pageNum) {

                    this.nobdReasonDisposalPageNum = pageNum;
                    this.reasonDisposalGetList();
                },
                rewardPaginate: function(pageNum) {

                    this.nobdRewardPageNum = pageNum;
                    this.rewardGetList();
                },
                trainedQuotaPaginate: function(pageNum) {

                    this.nobdTrainedQuotaPageNum = pageNum;
                    this.trainedQuotaGetList();
                },
                typeDirectionPaginate: function(pageNum) {

                    this.nobdTypeDirectionPageNum = pageNum;
                    this.typeDirectionGetList();
                },
                typeEventPaginate: function(pageNum) {

                    this.nobdTypeEventPageNum = pageNum;
                    this.typeEventGetList();
                },
                typeViolationPaginate: function(pageNum) {

                    this.nobdTypeViolationPageNum = pageNum;
                    this.typeViolationGetList();
                },
                studyExchangePaginate: function(pageNum) {

                    this.nobdStudyExchangePageNum = pageNum;
                    this.studyExchangeGetList();
                },

                modalShow: function(type){
                    this.dataType = type;
                    $('#dataModal').addClass('show');
                },
                modalClose: function(){
                    $('#dataModal').removeClass('show');
                    this.dataItem = {};
                },
                dataEdit: function(id,type){
                    this.dataLoadItem(id,type);
                    this.modalShow(type);
                },
                dataSave: function(type){

                    this.isError = false;
                    this.errorMessage = '';

                    this.dataRequest = true;
                    var self = this;
                    axios.post('{{ route('adminNobddataEditItem') }}',{
                        "_token": "{{ csrf_token() }}",
                        "id": this.dataItem.id,
                        "type": type,
                        "model": this.dataItem
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.getListByType(type);
                            self.modalClose();

                        } else {

                            self.isError = true;
                        }

                        self.errorMessage = response.data.message;
                        self.dataRequest = false;

                    })
                    .catch( error => {

                        console.log(error)
                    });

                },
                dataRemove: function(id,type){

                    this.isError = false;
                    this.errorMessage = '';

                    if (!confirm('Вы хотите удалить ПЛ?')) {
                        return;
                    }

                    this.dataRequest = true;
                    var self = this;
                    axios.post('{{ route('adminNobddataRemoveItem') }}',{
                        "_token": "{{ csrf_token() }}",
                        "id": id,
                        "type": type,
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.getListByType(type);

                        } else {

                            self.isError = true;
                        }

                        self.errorMessage = response.data.message;
                        self.dataRequest = false;

                    })
                    .catch( error => {

                        console.log(error)
                    });

                },
                dataLoadItem: function(id,type){

                    var self = this;
                    axios.post('{{ route('adminNobddataGetItem') }}',{
                        "_token": "{{ csrf_token() }}",
                        "id": id,
                        "type": type,
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.dataItem = response.data.model;
                        }
                    })
                    .catch( error => {

                        console.log(error)
                    });
                },

                academicLeaveGetList: function(){

                    var self = this;
                    axios.post('{{ route('adminNobddataGetNobdAcademicLeave') }}',{
                        "_token": "{{ csrf_token() }}",
                        "nobd_academic_leave": this.nobdAcademicLeavePageNum
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.nobdAcademicLeaveList = response.data.model;
                        }
                    })
                    .catch( error => {

                        console.log(error)
                    });
                },
                academicMobilityGetList: function(){

                    var self = this;
                    axios.post('{{ route('adminNobddataGetNobdAcademicMobility') }}',{
                        "_token": "{{ csrf_token() }}",
                        "nobd_academic_mobility": this.nobdAcademicMobilityPageNum
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.nobdAcademicMobilityList = response.data.model;
                        }
                    })
                    .catch( error => {

                        console.log(error)
                    });
                },
                causeStayYearGetList: function(){

                    var self = this;
                    axios.post('{{ route('adminNobddataGetNobdCauseStayYear') }}',{
                        "_token": "{{ csrf_token() }}",
                        "nobd_cause_stay_year": this.nobdCauseStayYearPageNum
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.nobdCauseStayYearList = response.data.model;
                        }
                    })
                    .catch( error => {

                        console.log(error)
                    });
                },
                countryGetList: function(){

                    var self = this;
                    axios.post('{{ route('adminNobddataGetNobdCountry') }}',{
                        "_token": "{{ csrf_token() }}",
                        "nobd_country": this.nobdCountryPageNum
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.nobdCountryList = response.data.model;
                        }
                    })
                    .catch( error => {

                        console.log(error)
                    });
                },
                disabilityGroupGetList: function(){

                    var self = this;
                    axios.post('{{ route('adminNobddataGetNobdDisabilityGroup') }}',{
                        "_token": "{{ csrf_token() }}",
                        "nobd_disability_group": this.nobdDisabilityGroupPageNum
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.nobdDisabilityGroupList = response.data.model;
                        }
                    })
                    .catch( error => {

                        console.log(error)
                    });
                },
                employmentOpportunityGetList: function(){

                    var self = this;
                    axios.post('{{ route('adminNobddataGetNobdEmploymentOpportunity') }}',{
                        "_token": "{{ csrf_token() }}",
                        "nobd_employment_opportunity": this.nobdEmploymentOpportunityPageNum
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.nobdEmploymentOpportunityList = response.data.model;
                        }
                    })
                    .catch( error => {

                        console.log(error)
                    });
                },
                eventsGetList: function(){

                    var self = this;
                    axios.post('{{ route('adminNobddataGetNobdEvents') }}',{
                        "_token": "{{ csrf_token() }}",
                        "nobd_events": this.nobdEventsPageNum
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.nobdEventsList = response.data.model;
                        }
                    })
                    .catch( error => {

                        console.log(error)
                    });
                },
                exchangeSpecialtyGetList: function(){

                    var self = this;
                    axios.post('{{ route('adminNobddataGetNobdExchangeSpecialty') }}',{
                        "_token": "{{ csrf_token() }}",
                        "nobd_exchange_specialty": this.nobdExchangeSpecialtyPageNum
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.nobdExchangeSpecialtyList = response.data.model;
                        }
                    })
                    .catch( error => {

                        console.log(error)
                    });
                },
                formDiplomGetList: function(){

                    var self = this;
                    axios.post('{{ route('adminNobddataGetNobdFormDiplom') }}',{
                        "_token": "{{ csrf_token() }}",
                        "nobd_form_diplom": this.nobdFormDiplomPageNum
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.nobdFormDiplomList = response.data.model;
                        }
                    })
                    .catch( error => {

                        console.log(error)
                    });
                },
                languageGetList: function(){

                    var self = this;
                    axios.post('{{ route('adminNobddataGetNobdLanguage') }}',{
                        "_token": "{{ csrf_token() }}",
                        "nobd_language": this.nobdLanguagePageNum
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.nobdLanguageList = response.data.model;
                        }
                    })
                    .catch( error => {

                        console.log(error)
                    });
                },
                paymentTypeGetList: function(){

                    var self = this;
                    axios.post('{{ route('adminNobddataGetNobdPaymentType') }}',{
                        "_token": "{{ csrf_token() }}",
                        "nobd_payment_type": this.nobdPaymentTypePageNum
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.nobdPaymentTypeList = response.data.model;
                        }
                    })
                    .catch( error => {

                        console.log(error)
                    });
                },
                reasonDisposalGetList: function(){

                    var self = this;
                    axios.post('{{ route('adminNobddataGetNobdReasonDisposal') }}',{
                        "_token": "{{ csrf_token() }}",
                        "nobd_reason_disposal": this.nobdReasonDisposalPageNum
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.nobdReasonDisposalList = response.data.model;
                        }
                    })
                    .catch( error => {

                        console.log(error)
                    });
                },
                rewardGetList: function(){

                    var self = this;
                    axios.post('{{ route('adminNobddataGetNobdReward') }}',{
                        "_token": "{{ csrf_token() }}",
                        "nobd_reward": this.nobdRewardPageNum
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.nobdRewardList = response.data.model;
                        }
                    })
                    .catch( error => {

                        console.log(error)
                    });
                },
                trainedQuotaGetList: function(){

                    var self = this;
                    axios.post('{{ route('adminNobddataGetNobdTrainedQuota') }}',{
                        "_token": "{{ csrf_token() }}",
                        "nobd_trained_quota": this.nobdTrainedQuotaPageNum
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.nobdTrainedQuotaList = response.data.model;
                        }
                    })
                    .catch( error => {

                        console.log(error)
                    });
                },
                typeDirectionGetList: function(){

                    var self = this;
                    axios.post('{{ route('adminNobddataGetNobdTypeDirection') }}',{
                        "_token": "{{ csrf_token() }}",
                        "nobd_type_direction": this.nobdTypeDirectionPageNum
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.nobdTypeDirectionList = response.data.model;
                        }
                    })
                    .catch( error => {

                        console.log(error)
                    });
                },
                typeEventGetList: function(){

                    var self = this;
                    axios.post('{{ route('adminNobddataGetNobdTypeEvent') }}',{
                        "_token": "{{ csrf_token() }}",
                        "nobd_type_event": this.nobdTypeEventPageNum
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.nobdTypeEventList = response.data.model;
                        }
                    })
                    .catch( error => {

                        console.log(error)
                    });
                },
                typeViolationGetList: function(){

                    var self = this;
                    axios.post('{{ route('adminNobddataGetNobdTypeViolation') }}',{
                        "_token": "{{ csrf_token() }}",
                        "nobd_type_violation": this.nobdTypeViolationPageNum
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.nobdTypeViolationList = response.data.model;
                        }
                    })
                    .catch( error => {

                        console.log(error)
                    });
                },
                studyExchangeGetList: function(){

                    var self = this;
                    axios.post('{{ route('adminNobddataGetNobdStudyExchange') }}',{
                        "_token": "{{ csrf_token() }}",
                        "nobd_study_exchange": this.nobdStudyExchangePageNum
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.nobdStudyExchangeList = response.data.model;
                        }
                    })
                    .catch( error => {

                        console.log(error)
                    });
                },
                getListByType: function(type){
                    switch(type){
                        case 'nobd_academic_leave':
                            this.academicLeaveGetList();
                        break;
                        case 'nobd_academic_mobility':
                            this.academicMobilityGetList();
                        break;
                        case 'nobd_cause_stay_year':
                            this.causeStayYearGetList();
                        break;
                        case 'nobd_country':
                            this.countryGetList();
                        break;
                        case 'nobd_disability_group':
                            this.disabilityGroupGetList();
                        break;
                        case 'nobd_employment_opportunity':
                            this.employmentOpportunityGetList();
                        break;
                        case 'nobd_events':
                            this.eventsGetList();
                        break;
                        case 'nobd_exchange_specialty':
                            this.exchangeSpecialtyGetList();
                        break;
                        case 'nobd_form_diplom':
                            this.formDiplomGetList();
                        break;
                        case 'nobd_language':
                            this.languageGetList();
                        break;
                        case 'nobd_payment_type':
                            this.paymentTypeGetList();
                        break;
                        case 'nobd_reason_disposal':
                            this.reasonDisposalGetList();
                        break;
                        case 'nobd_reward':
                            this.rewardGetList();
                        break;
                        case 'nobd_trained_quota':
                            this.trainedQuotaGetList();
                        break;
                        case 'nobd_type_direction':
                            this.typeDirectionGetList();
                        break;
                        case 'nobd_type_event':
                            this.typeEventGetList();
                        break;
                        case 'nobd_type_violation':
                            this.typeViolationGetList();
                        break;
                        case 'nobd_study_exchange':
                            this.studyExchangeGetList();
                        break;
                    }
                }
            },
            created: function(){

                this.academicLeaveGetList();
                this.academicMobilityGetList();
                this.causeStayYearGetList();
                this.countryGetList();
                this.disabilityGroupGetList();
                this.employmentOpportunityGetList();
                this.eventsGetList();
                this.exchangeSpecialtyGetList();
                this.formDiplomGetList();
                this.languageGetList();
                this.paymentTypeGetList();
                this.reasonDisposalGetList();
                this.rewardGetList();
                this.trainedQuotaGetList();
                this.typeDirectionGetList();
                this.typeEventGetList();
                this.typeViolationGetList();
                this.studyExchangeGetList();
            }
        })


    </script>

@endsection