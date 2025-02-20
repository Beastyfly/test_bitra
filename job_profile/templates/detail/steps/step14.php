<template v-if="currentStep === 14">

    <div class="block panel" :class="{ error: false }" v-if="modelRole[ 2 ] != 2">
        <renins-button class="secondary xs" style="float: right" @click="isShowEditModal[ currentStep ] = true"
            v-if="modelRole[ 2 ]">
            Изменить
        </renins-button>
        <div class="panel-row panel-header">Компетенции</div>

        <div class="tasc-list__table details">
            <div class="tasc-list__th">
                <div class="first-col">#</div>
                <div>Компетенция</div>
            </div>

            <div class="tasc-list__tr" v-for="(question, index) in competencesQuestions"
                v-if="formData.checksCompetencies[ question.id ]">
                <div class="first-col">
                    {{ formData.compTableIndex[ question.id ] }}
                </div>
                <div class="tr-flex">
                    <div class="nowrap">{{ question.text }}</div>
                    <div class="nowrap">
                        <renins-badge-label class="warning" v-if="formData.competencies[ question.id ] === 'Начальный'">Начальный</renins-badge-label>
                        <renins-badge-label class="active" v-if="formData.competencies[ question.id ] === 'Средний'">Средний</renins-badge-label>
                        <renins-badge-label class="success" v-if="formData.competencies[ question.id ] === 'Продвинутый'">Продвинутый</renins-badge-label>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="error_caption_block">В блоке имеются ошибки. Измените данные в блоке.</div>

    <renins-form-modal v-show="isShowEditModal[ currentStep ]" @close="close(currentStep)" :close-button="false">
        <template #head>Компетенции</template>
        <template #body>

            <div class="block block-desc">
                Отметьте галочкой необходимые для роли компетенции (минимум 11). Среди выбранных компетенций укажите желаемый уровень
            </div>
            <div class="r-web-caption r-mb-5 radio_error" v-if="errors[14].competencies">
                Не выбран ответ
            </div>
            <div class="block">
                <template v-for="(question, index) in competencesQuestions">
                    <div class="block-row row" style="margin-top: 24px">
                        <div>
                            <renins-checkbox v-model="formDataEdit.checksCompetencies[ question.id ]" :error="errors[14].competencies" :label="question.text"></renins-checkbox>

                            <renins-icon tooltip="Проводит экспериментальную проверку правильности решений, принятых на предыдущих этапах, и подготовку к их внедрению. Видит пользу в получении метрик и уроков на пилотном запуске. При этом готов к тому, что сроки и ресурсы проекта могут быть увеличены"
                                         v-if="question.text === 'Пилотирует решения'" class="help-circle color-gray" style="margin-left: 4px"></renins-icon>

                            <div v-if="question.text === 'Привлекает в команду сильных людей'" style="font-size: 15px; margin: 4px 0 0 30px">
                                Индикатор актуален в большей степени для руководителей
                            </div>
                        </div>
                    </div>
                    <div v-if="formDataEdit.checksCompetencies[ question.id ]" class="block-row" style="margin-left: 28px">
                        <renins-radio v-model="formDataEdit.competencies[ question.id ]" :error="errors.competencies && !formDataEdit.competencies[ question.id ]" val="Начальный">Начальный</renins-radio>
                        <renins-radio v-model="formDataEdit.competencies[ question.id ]" :error="errors.competencies && !formDataEdit.competencies[ question.id ]" val="Средний" style="margin-left: 32px">Средний</renins-radio>
                        <renins-radio v-model="formDataEdit.competencies[ question.id ]" :error="errors.competencies && !formDataEdit.competencies[ question.id ]" val="Продвинутый" style="margin-left: 32px">Продвинутый</renins-radio>
                    </div>
                </template>
            </div>

        </template>
        <template #footer>
            <div class="block" v-if="stepHasErrors(14)">
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