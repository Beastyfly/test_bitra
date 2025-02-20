<div class="panel" v-if="currentStep === 6">
	<div class="panel-header">Полномочия в принятии решений</div>
    <div class="block">
        <div class="block-row">
            <div class="block-desc">Какие решения должность может принимать самостоятельно?</div>
            Например, решения по условиям сделок с клиентами, партнерами, подрядчиками, решения по закупке оборудования, выбору поставщика, решения по срокам/стоимости проведения проектов и т.п.
        </div>
        <div class="block-row">
            <renins-textarea placeholder="Описание" v-model="formData.step6.decisions" class="resize-vertical" rows="3" :error="errors.decisions"></renins-textarea>
        </div>
    </div>

    <div class="block" v-if="stepHasErrors(6)">
        <renins-quote class="errored">На форме присутствуют незаполненные поля</renins-quote>
    </div>

    <div v-if="statusId !== 'trash'" class="block">
        <renins-button class="secondary lg" style="width: 210px; margin-right: 16px;" @click="saveAndRedirect()" :loading="isSaving" :disabled="isAutoSaving">
            {{ !isAutoSaving ? 'Сохранить и выйти' : 'Автосохранение...' }}
        </renins-button>
        <renins-button class="primary lg" style="margin-right: 16px;" @click="nextStep()" :disabled="stepHasErrors(6)">
            Продолжить
        </renins-button>
        <renins-button v-if="isOD && stageId && (stageId !== 'create')" class="secondary lg" style="margin-right: 16px;"
            @click="isShowRevokeModal = true; revoke_comment = '';" :loading="isRevoking" :disabled="stepHasErrors(6)">
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
