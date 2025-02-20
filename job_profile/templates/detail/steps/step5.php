<template v-if="currentStep === 5">

    <div class="block panel" :class="{ error: false }" v-if="modelRole[ 1 ] != 2">
        <renins-button class="secondary xs" style="float: right" @click="isShowEditModal[ currentStep ] = true"
            v-if="modelRole[ 1 ]">
            Изменить
        </renins-button>
        <div class="panel-row panel-header">Вклад должности в общие результаты компании</div>

        <div class="panel-row">
            {{ formData.positionContribution }}
        </div>

        <div class="panel-row">
            <div>
                <div class="block-desc">
                    <renins-badge-label class="active">Описание вклада</renins-badge-label>
                </div>
            </div>
            <div>
                {{ formData.positionContributionDescription }}
            </div>
        </div>

    </div>
    <div class="error_caption_block">В блоке имеются ошибки. Измените данные в блоке.</div>

    <renins-form-modal v-show="isShowEditModal[ currentStep ]" @close="close(currentStep)" :close-button="false">
        <template #head>Вклад должности в общие результаты компании</template>
        <template #body>

            <div class="block">
                <div class="r-web-caption r-mb-5 radio_error" v-if="errors[5].positionContribution">
                    Не выбран ответ
                </div>
                <div class="block-row row">
                    <renins-radio v-model="formDataEdit.positionContribution" val="Оперативный" :error="errors[5].positionContribution">
                        Оперативный
                    </renins-radio>
                    <div class="param-head" style="margin-top: 4px; margin-left: 28px;">
                        Выполнение задач по заранее разработанным правилам; стандартизированный и
                        регламентированный функционал; краткосрочные планы (до полугода)
                    </div>
                </div>
                <div class="block-row row">
                    <renins-radio v-model="formDataEdit.positionContribution" val="Тактический" :error="errors[5].positionContribution">
                        Тактический
                    </renins-radio>
                    <div class="param-head" style="margin-top: 4px; margin-left: 28px;">
                        Выполнение задач носит нестандартный характер, требует инновационного подхода,
                        реализация среднесрочных планов (до года)
                    </div>
                </div>
                <div class="block-row row">
                   <renins-radio v-model="formDataEdit.positionContribution" val="Стратегический" :error="errors[5].positionContribution">
                        Стратегический
                    </renins-radio>
                    <div class="param-head" style="margin-top: 4px; margin-left: 28px;">
                        Значительное влияние на формирование стратегии развития компании
                    </div>
                </div>
            </div>

            <div v-if="formDataEdit.positionContribution === 'Стратегический'" class="block">
                <div class="block-row block-desc">
                    Опишите в чем заключается стратегический вклад должности
                </div>
                <div class="block-row row">
                    <renins-textarea placeholder="Описание" v-model="formDataEdit.positionContributionDescription"
                                     :error="errors[5].positionContributionDescription"
                                     class="resize-vertical" rows="3"></renins-textarea>
                </div>
            </div>

        </template>
        <template #footer>
            <div class="block" v-if="stepHasErrors(5)">
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