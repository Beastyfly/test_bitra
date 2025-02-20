<div class="panel" v-if="currentStep === 9">
	<div class="panel-header">Уровень инновационности деятельности</div>
    <div class="block">
        <div class="block-row">
            Выберите из списка 1 наиболее подходящий вариант. Выбор варианта должен подкрепляться вышеописанными должностными обязанностями - функциями.
        </div>
        <div class="r-web-caption r-mb-5 radio_error" v-if="errors.levelOfInnovativeness">
            Не выбран ответ
        </div>
        <div class="block-row">
            <renins-radio v-model="formData.step9.levelOfInnovativeness" :error="errors.levelOfInnovativeness" val="Поддержка существующих стандартов работы">Поддержка существующих стандартов работы</renins-radio>
        </div>
        <div class="block-row">
            <renins-radio v-model="formData.step9.levelOfInnovativeness" :error="errors.levelOfInnovativeness" val="Некоторая оптимизация, улучшение существующих стандартов работы (<10% изменений)">Некоторая оптимизация, улучшение существующих стандартов работы (<10% изменений)</renins-radio>
        </div>
        <div class="block-row">
            <renins-radio v-model="formData.step9.levelOfInnovativeness" :error="errors.levelOfInnovativeness" val="Существенная оптимизация, улучшение существующих стандартов работы (10-25% изменений)">Существенная оптимизация, улучшение существующих стандартов работы (10-25% изменений)</renins-radio>
        </div>
        <div class="block-row">
            <renins-radio v-model="formData.step9.levelOfInnovativeness" :error="errors.levelOfInnovativeness" val="Кардинальное изменение существующих стандартов работы на основе прогрессивных тенденций (>25% изменений)">Кардинальное изменение существующих стандартов работы на основе прогрессивных тенденций (>25% изменений)</renins-radio>
        </div>
        <div class="block-row">
            <renins-radio v-model="formData.step9.levelOfInnovativeness" :error="errors.levelOfInnovativeness" val="Внедрение инновационных изменений - революционных рыночных практик">Внедрение инновационных изменений - революционных рыночных практик</renins-radio>
        </div>
    </div>

    <div class="block" v-if="stepHasErrors(9)">
        <renins-quote class="errored">На форме присутствуют незаполненные поля</renins-quote>
    </div>

    <div v-if="statusId !== 'trash'" class="block">
        <renins-button class="secondary lg" style="width: 210px; margin-right: 16px;" @click="saveAndRedirect()" :loading="isSaving" :disabled="isAutoSaving">
            {{ !isAutoSaving ? 'Сохранить и выйти' : 'Автосохранение...' }}
        </renins-button>
        <renins-button class="primary lg" style="margin-right: 16px;" @click="nextStep()" :disabled="stepHasErrors(9)">
            Продолжить
        </renins-button>
        <renins-button v-if="isOD && stageId && (stageId !== 'create')" class="secondary lg" style="margin-right: 16px;"
            @click="isShowRevokeModal = true; revoke_comment = '';" :loading="isRevoking" :disabled="stepHasErrors(9)">
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
