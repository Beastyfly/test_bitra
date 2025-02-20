<div class="panel" v-if="currentStep === 5">
    <div class="panel-header">Вклад должности в общие результаты компании</div>
    <div class="block">
        <div class="r-web-caption r-mb-5 radio_error" v-if="errors.positionContribution">
            Не выбран ответ
        </div>
        <div class="block-row">
            <renins-radio v-model="formData.step5.positionContribution" val="Оперативный" :error="errors.positionContribution">Оперативный</renins-radio>
            <div style="margin-top: 4px; padding-left: 28px;">
                Выполнение задач по заранее разработанным правилам; стандартизированный и регламентированный функционал; краткосрочные планы (до полугода)
            </div>
        </div>
        <div class="block-row">
            <renins-radio v-model="formData.step5.positionContribution" val="Тактический" :error="errors.positionContribution">Тактический</renins-radio>
            <div style="margin-top: 4px; padding-left: 28px;">
                Выполнение задач носит нестандартный характер, требует инновационного подхода, реализация среднесрочных планов (до года)
            </div>
        </div>
        <div class="block-row">
            <renins-radio v-model="formData.step5.positionContribution" val="Стратегический" :error="errors.positionContribution">Стратегический</renins-radio>
            <div style="margin-top: 4px; padding-left: 28px;">
                Значительное влияние на формирование стратегии развития компании
            </div>
        </div>
    </div>

    <div v-if="formData.step5.positionContribution === 'Стратегический'" class="block">
        <div class="block-row block-desc">
            Опишите в чем заключается стратегический вклад должности
        </div>
        <div class="block-row">
            <renins-textarea placeholder="Описание" v-model="formData.step5.positionContributionDescription" :error="errors.positionContributionDescription" class="resize-vertical" rows="3"></renins-textarea>
        </div>
    </div>

    <div class="block" v-if="stepHasErrors(5)">
        <renins-quote class="errored">На форме присутствуют незаполненные поля</renins-quote>
    </div>

    <div v-if="statusId !== 'trash'" class="block">
        <renins-button class="secondary lg" style="width: 210px; margin-right: 16px;" @click="saveAndRedirect()" :loading="isSaving" :disabled="isAutoSaving">
            {{ !isAutoSaving ? 'Сохранить и выйти' : 'Автосохранение...' }}
        </renins-button>
        <renins-button class="primary lg" style="margin-right: 16px;" @click="nextStep()"  :disabled="stepHasErrors(5)">
            Продолжить
        </renins-button>
        <renins-button v-if="isOD && stageId && (stageId !== 'create')" class="secondary lg" style="margin-right: 16px;"
            @click="isShowRevokeModal = true; revoke_comment = '';" :loading="isRevoking" :disabled="stepHasErrors(5)">
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
