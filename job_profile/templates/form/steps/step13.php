<div class="panel" v-if="currentStep === 13">
	<div class="panel-header">Опыт</div>

    <div class="block">
        <div class="block-row block-desc">
            Какой опыт необходим для должности?
        </div>
        <div class="block-row row">
            <div>
                <renins-checkbox v-model="formData.step13.professionalExperience" label="Профессиональный опыт"></renins-checkbox>
            </div>
        </div>
    </div>
    <div v-if="formData.step13.professionalExperience" class="block" style="margin-left: 30px">
        <div class="block-row row">
            <div class="col-3">
                <renins-text-input v-model="formData.step13.professionalExperienceYears" :error="errors.professionalExperienceYears" caption="Кол-во, лет" class="w-100"></renins-text-input>
            </div>
        </div>
        <div class="block-row" style="font-size: 15px">
            Укажите направления деятельности, по которым необходим профессиональный опыт работы для полноценного выполнения обязанностей этой должности
        </div>
        <div class="block-row block-desc">
            В областях и сферах
        </div>
        <div class="block-row">
            <renins-textarea placeholder="Описание" v-model="formData.step13.fieldOfActivity" :error="errors.fieldOfActivity" class="resize-vertical" rows="3"></renins-textarea>
        </div>
    </div>

    <div class="block">
        <div class="block-row row">
            <div>
                <renins-checkbox v-model="formData.step13.managementExperience" label="Управленческий опыт"></renins-checkbox>
            </div>
        </div>
    </div>
    <div v-if="formData.step13.managementExperience" class="block" style="margin-left: 30px">
        <div class="block-row row">
            <div class="col-3">
                <renins-text-input v-model="formData.step13.managementExperienceYears" :error="errors.managementExperienceYears" caption="Кол-во, лет" class="w-100"></renins-text-input>
            </div>
        </div>
        <div class="block-row" style="font-size: 15px">
            Укажите какой управленческий опыт необходим для данной должности: управление коллективами (какой численности),
            управление процессами/проектами (какой сложности и продолжительности), управление ресурсами (какими).
            Укажите необходимое кол-во лет для каждого из видов опыта.
        </div>
        <div class="block-row block-desc">
            В областях и сферах
        </div>
        <div class="block-row">
            <renins-textarea placeholder="Описание" v-model="formData.step13.fieldOfManagementActivity" :error="errors.fieldOfManagementActivity" class="resize-vertical" rows="3"></renins-textarea>
        </div>
    </div>

    <div class="block" v-if="stepHasErrors(13)">
        <renins-quote class="errored">На форме присутствуют незаполненные поля</renins-quote>
    </div>

    <div v-if="statusId !== 'trash'" class="block">
        <renins-button class="secondary lg" style="width: 210px; margin-right: 16px;" @click="saveAndRedirect()" :loading="isSaving" :disabled="isAutoSaving">
            {{ !isAutoSaving ? 'Сохранить и выйти' : 'Автосохранение...' }}
        </renins-button>
        <renins-button class="primary lg" style="margin-right: 16px;" @click="nextStep()" :disabled="stepHasErrors(13)">
            Продолжить
        </renins-button>
        <renins-button v-if="isOD && stageId && (stageId !== 'create')" class="secondary lg" style="margin-right: 16px;"
            @click="isShowRevokeModal = true; revoke_comment = '';" :loading="isRevoking" :disabled="stepHasErrors(13)">
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
