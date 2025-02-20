<template v-if="currentStep === 8">

    <div class="block panel" :class="{ error: false }" v-if="modelRole[ 1 ] != 2">
        <renins-button class="secondary xs" style="float: right" @click="isShowEditModal[ currentStep ] = true"
            v-if="modelRole[ 1 ]">
            Изменить
        </renins-button>
        <div class="panel-row panel-header">Ответственность за бюджет по расходам</div>

        <div class="panel-row">
            <template v-if="formData.isNotInvolvedInBudgetManagement
                || formData.isControlTargetBudget
                || formData.isPrepareProposalsToSpendBudget
                || formData.hasAuthorityToMakeDecisions">
                <div v-if="formData.isNotInvolvedInBudgetManagement">
                    <span class="dot2"></span> Не участвует в управлении бюджетом по расходам
                </div>
                <div v-if="formData.isControlTargetBudget">
                    <span class="dot2"></span> Контролирует выполнение целевого расходования бюджета (политики, процедуры, согласования и пр.)
                </div>
                <div v-if="formData.isPrepareProposalsToSpendBudget">
                    <span class="dot2"></span> Готовит предложения о путях целевого расходования бюджета (без полномочий принятия решений)
                </div>
                <div v-if="formData.hasAuthorityToMakeDecisions">
                    <span class="dot2"></span> Имеет полномочия принимать решения о конкретном пути расходования бюджета
                </div>
            </template>
            <div v-else>-</div>
        </div>

        <div class="panel-row row" v-if="formData.CnBSum || formData.nonCnBSum">
            <div class="col-6">
                <div class="param-head">Сумма по С&B, руб/год</div>
                <template v-if="formData.CnBSum">{{ formData.CnBSum | numberFormat}}</template>
                <template v-else>-</template>
            </div>
            <div class="col-6">
                <div class="param-head">Сумма по С&B, руб/год</div>
                <template v-if="formData.nonCnBSum">{{ formData.nonCnBSum | numberFormat}}</template>
                <template v-else>-</template>
            </div>
        </div>

    </div>
    <div class="error_caption_block">В блоке имеются ошибки. Измените данные в блоке.</div>

    <renins-form-modal v-show="isShowEditModal[ currentStep ]" @close="close(currentStep)" :close-button="false">
        <template #head>Ответственность за бюджет по расходам</template>
        <template #body>

            <div class="block">
                <div class="block-row row">
                    <div class="block-desc">Имеет ли должность полномочия управления (или контроля) бюджетом по расходам?</div>
                    <div>Если должность имеет в управлении бюджет по расходам или готовит предложения по путям его целевого использования, необходимо указать сумму бюджета, которая входит в зону ответственности должности. Если должность несет по отношению к бюджету только контрольные функции, <span style="color: #FF971E">контроль соблюдения лимитов — не является управлением бюджетом.</span></div>
                </div>
                <div class="r-web-caption r-mb-5 radio_error" v-if="errors[8].isNotInvolvedInBudgetManagement">
                    Не выбран ответ
                </div>
                <div class="block-row row">
                    <div>
                        <renins-checkbox v-model="formDataEdit.isNotInvolvedInBudgetManagement"
                                         :error="errors[8].isNotInvolvedInBudgetManagement"
                            label="Не участвует в управлении бюджетом по расходам">
                        </renins-checkbox>
                    </div>
                </div>
                <div class="block-row row">
                    <div>
                        <renins-checkbox v-model="formDataEdit.isControlTargetBudget"
                                         :error="errors[8].isControlTargetBudget"
                            label="Контролирует выполнение целевого расходования бюджета (политики, процедуры, согласования и пр.)">
                        </renins-checkbox>
                    </div>
                </div>
                <div class="block-row row">
                    <div>
                        <renins-checkbox v-model="formDataEdit.isPrepareProposalsToSpendBudget"
                                         :error="errors[8].isPrepareProposalsToSpendBudget"
                            label="Готовит предложения о путях целевого расходования бюджета (без полномочий принятия решений)">
                        </renins-checkbox>
                    </div>
                </div>
                <div class="block-row row">
                    <div>
                        <renins-checkbox v-model="formDataEdit.hasAuthorityToMakeDecisions"
                                         :error="errors[8].hasAuthorityToMakeDecisions"
                            label="Имеет полномочия принимать решения о конкретном пути расходования бюджета">
                        </renins-checkbox>
                    </div>
                </div>
            </div>

            <div class="block">
                <div class="block-row row">
                    <div class="col-6">
                        <renins-text-input v-model="formDataEdit.CnBSum" :error="errors[8].CnBSum" caption="Сумма по С&B, руб/год" placeholder="" type="currency" class="w-100"></renins-text-input>
                    </div>
                    <div class="col-6">
                        <renins-text-input v-model="formDataEdit.nonCnBSum" :error="errors[8].nonCnBSum" caption="Сумма по non-С&B, руб/год" placeholder="" type="currency" class="w-100"></renins-text-input>
                    </div>
                </div>
            </div>

        </template>
        <template #footer>
            <div class="block" v-if="stepHasErrors(8)">
                <renins-quote class="errored">На форме присутствуют незаполненные поля</renins-quote>
            </div>
            <div class="row">
                <div class="col-2">
                    <renins-button class="primary w-100" style="margin-right: 16px;"
                        @click="save(currentStep)" :loading="isSaving" :disabled="isSaveButtonDisabled">
                        Сохранить
                    </renins-button>
                </div>
                <div class="col-2">
                    <renins-button class="secondary w-100" @click="close(currentStep)">
                        Закрыть
                    </renins-button>
                </div>
            </div>
        </template>
    </renins-form-modal>

</template>
