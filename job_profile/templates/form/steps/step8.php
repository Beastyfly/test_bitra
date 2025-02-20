<div class="panel" v-if="currentStep === 8">
	<div class="panel-header">Ответственность за бюджет по расходам</div>
    <div class="block">
        <div class="block-row">
            <div class="block-desc">Имеет ли должность полномочия управления (или контроля) бюджетом по расходам?</div>
            Если должность имеет в управлении бюджет по расходам или готовит предложения по путям его целевого использования, необходимо указать сумму бюджета, которая входит в зону ответственности должности. Если должность несет по отношению к бюджету только контрольные функции, <span style="color: #FF971E">контроль соблюдения лимитов — не является управлением бюджетом.</span>
        </div>
        <div class="r-web-caption r-mb-5 radio_error" v-if="errors.isNotInvolvedInBudgetManagement">
            Не выбран ответ
        </div>
        <div class="block-row row">
            <div>
                <renins-checkbox v-model="formData.step8.isNotInvolvedInBudgetManagement" :error="errors.isNotInvolvedInBudgetManagement" label="Не участвует в управлении бюджетом по расходам"></renins-checkbox>
            </div>
        </div>
        <div class="block-row row">
            <div>
                <renins-checkbox v-model="formData.step8.isControlTargetBudget" :error="errors.isControlTargetBudget" label="Контролирует выполнение целевого расходования бюджета (политики, процедуры, согласования и пр.)"></renins-checkbox>
            </div>
        </div>
        <div class="block-row row">
            <div>
                <renins-checkbox v-model="formData.step8.isPrepareProposalsToSpendBudget" :error="errors.isPrepareProposalsToSpendBudget" label="Готовит предложения о путях целевого расходования бюджета (без полномочий принятия решений)"></renins-checkbox>
            </div>
        </div>
        <div class="block-row row">
            <div>
                <renins-checkbox v-model="formData.step8.hasAuthorityToMakeDecisions" :error="errors.hasAuthorityToMakeDecisions" label="Имеет полномочия принимать решения о конкретном пути расходования бюджета"></renins-checkbox>
            </div>
        </div>
    </div>

    <div class="block">
        <div class="block-row row">
            <div class="col-6">
                <renins-text-input v-model="formData.step8.CnBSum" :error="errors.CnBSum" caption="Сумма по С&B, руб/год" placeholder="" type="currency" class="w-100"></renins-text-input>
            </div>
            <div class="col-6">
                <renins-text-input v-model="formData.step8.nonCnBSum" :error="errors.nonCnBSum" caption="Сумма по non-С&B, руб/год" placeholder="" type="currency" class="w-100"></renins-text-input>
            </div>
        </div>
    </div>

    <div class="block" v-if="stepHasErrors(8)">
        <renins-quote class="errored">На форме присутствуют незаполненные поля</renins-quote>
    </div>

    <div v-if="statusId !== 'trash'" class="block">
        <renins-button class="secondary lg" style="width: 210px; margin-right: 16px;" @click="saveAndRedirect()" :loading="isSaving" :disabled="isAutoSaving">
            {{ !isAutoSaving ? 'Сохранить и выйти' : 'Автосохранение...' }}
        </renins-button>
        <renins-button class="primary lg" style="margin-right: 16px;" @click="nextStep()" :disabled="stepHasErrors(8)">
            Продолжить
        </renins-button>
        <renins-button v-if="isOD && stageId && (stageId !== 'create')" class="secondary lg" style="margin-right: 16px;"
            @click="isShowRevokeModal = true; revoke_comment = '';" :loading="isRevoking" :disabled="stepHasErrors(8)">
            Отозвать
        </renins-button>
    </div>
    <div v-else>
        <renins-button class="secondary lg" style="margin-right: 16px;"
            @click="isShowDeleteModal = true;" :loading="isDeleting">
            Удалить
        </renins-button>
    </div>
</div>
