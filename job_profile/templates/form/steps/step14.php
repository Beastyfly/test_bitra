<div class="panel" v-if="currentStep === 14">
	<div class="panel-header">Компетенции</div>
	<div class="block block-desc">
        Отметьте галочкой необходимые для роли компетенции (минимум 11). Среди выбранных компетенций укажите желаемый уровень
	</div>
    <div class="r-web-caption r-mb-5 radio_error" v-if="errors.competencies">
        Не выбран ответ
    </div>
    <div class="block">
        <template v-for="(question, index) in competencesQuestions">
            <div class="block-row row" style="margin-top: 24px">
                <div>
                    <renins-checkbox v-model="formData.step14.checksCompetencies[ question.id ]" :label="question.text"  :error="errors.competencies"></renins-checkbox>

                    <renins-icon tooltip="Проводит экспериментальную проверку правильности решений, принятых на предыдущих этапах, и подготовку к их внедрению. Видит пользу в получении метрик и уроков на пилотном запуске. При этом готов к тому, что сроки и ресурсы проекта могут быть увеличены"
                        v-if="question.text === 'Пилотирует решения'" class="help-circle color-gray" style="margin-left: 4px"></renins-icon>

                    <div v-if="question.text === 'Привлекает в команду сильных людей'" style="font-size: 15px; margin: 4px 0 0 30px">
                        Индикатор актуален в большей степени для руководителей
                    </div>
                </div>
            </div>
            <div v-if="formData.step14.checksCompetencies[ question.id ]" class="block-row" style="margin-left: 28px">
                <renins-radio v-model="formData.step14.competencies[ question.id ]" val="Начальный" :error="errors.competencies && !formData.step14.competencies[question.id]">Начальный</renins-radio>
                <renins-radio v-model="formData.step14.competencies[ question.id ]" val="Средний" :error="errors.competencies && !formData.step14.competencies[question.id]" style="margin-left: 32px">Средний</renins-radio>
                <renins-radio v-model="formData.step14.competencies[ question.id ]" val="Продвинутый" :error="errors.competencies && !formData.step14.competencies[question.id]" style="margin-left: 32px">Продвинутый</renins-radio>
            </div>
        </template>
    </div>

    <div class="block" v-if="stepHasErrors(14)">
        <renins-quote class="errored">На форме присутствуют незаполненные поля</renins-quote>
    </div>

    <div v-if="statusId !== 'trash'" class="block">
        <renins-button class="secondary lg" style="width: 210px; margin-right: 16px;" @click="saveAndRedirect()" :loading="isSaving" :disabled="isAutoSaving">
            {{ !isAutoSaving ? 'Сохранить и выйти' : 'Автосохранение...' }}
        </renins-button>
        <renins-button class="primary lg" style="margin-right: 16px;" @click="isShowSendModal = true" :loading="isSending" :disabled="stepHasErrors(14)">
            Отправить
        </renins-button>
        <renins-button v-if="isOD && stageId && (stageId !== 'create')" class="secondary lg" style="margin-right: 16px;"
            @click="isShowRevokeModal = true; revoke_comment = '';" :loading="isRevoking" :disabled="stepHasErrors(14)">
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
