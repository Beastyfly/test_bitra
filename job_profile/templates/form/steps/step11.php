<div class="panel" v-if="currentStep === 11">
	<div class="panel-header">Требования, необходимые для выполнения должностных обязанностей</div>
    <div class="block">
        <div class="block-row">
            <div class="block-desc">Образование</div>
            Укажите минимальный уровень образования, необходимый для полноценного выполнения должностных обязанностей. Выберите из списка 1 наиболее подходящий вариант. Просьба заполнять данный пункт как требование к должности. Не следует ориентироваться на фактические данные лица, ее занимающего.
        </div>
        <div class="r-web-caption r-mb-5 radio_error" v-if="errors.minimumLevelOfEducation">
            Не выбран ответ
        </div>
        <div class="block-row">
            <renins-radio v-model="formData.step11.minimumLevelOfEducation" :error="errors.minimumLevelOfEducation" val="Среднее общее">Среднее общее</renins-radio>
        </div>
        <div class="block-row">
            <renins-radio v-model="formData.step11.minimumLevelOfEducation" :error="errors.minimumLevelOfEducation" val="Среднее специальное">Среднее специальное</renins-radio>
        </div>
        <div class="block-row">
            <renins-radio v-model="formData.step11.minimumLevelOfEducation" :error="errors.minimumLevelOfEducation" val="Неполное высшее">Неполное высшее</renins-radio>
        </div>
        <div class="block-row">
            <renins-radio v-model="formData.step11.minimumLevelOfEducation" :error="errors.minimumLevelOfEducation" val="Высшее">Высшее</renins-radio>
        </div>
        <div class="block-row">
            <renins-radio v-model="formData.step11.minimumLevelOfEducation" :error="errors.minimumLevelOfEducation" val="MBA">MBA</renins-radio>
        </div>
        <div class="block-row">
            <renins-radio v-model="formData.step11.minimumLevelOfEducation" :error="errors.minimumLevelOfEducation" val="Ученая степень (кандидат/доктор наук)">Ученая степень (кандидат/доктор наук)</renins-radio>
        </div>
    </div>
    <div class="block">
        <div class="block-row">
            <renins-text-input v-model="formData.step11.Qualification" caption="Специализация / Квалификация" class="w-100"></renins-text-input>
        </div>
    </div>
    <div class="block">
        <renins-text-input v-model="formData.step11.Certification" caption="Сертификация (если необходима для должности) " class="w-100"></renins-text-input>
    </div>
    <div class="block">
        <renins-text-input v-model="formData.step11.professionalStandard" caption="Соответствие квалификационным требованиям (профстандарт)" class="w-100"></renins-text-input>
    </div>

    <div class="block" v-if="stepHasErrors(11)">
        <renins-quote class="errored">На форме присутствуют незаполненные поля</renins-quote>
    </div>

    <div v-if="statusId !== 'trash'" class="block">
        <renins-button class="secondary lg" style="width: 210px; margin-right: 16px;" @click="saveAndRedirect()" :loading="isSaving" :disabled="isAutoSaving">
            {{ !isAutoSaving ? 'Сохранить и выйти' : 'Автосохранение...' }}
        </renins-button>
        <renins-button class="primary lg" style="margin-right: 16px;" @click="nextStep()" :disabled="stepHasErrors(11)">
            Продолжить
        </renins-button>
        <renins-button v-if="isOD && stageId && (stageId !== 'create')" class="secondary lg" style="margin-right: 16px;"
            @click="isShowRevokeModal = true; revoke_comment = '';" :loading="isRevoking" :disabled="stepHasErrors(11)">
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
