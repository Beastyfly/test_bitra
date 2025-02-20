<template v-if="currentStep === 9">

    <div class="block panel" :class="{ error: false }" v-if="modelRole[ 1 ] != 2">
        <renins-button class="secondary xs" style="float: right" @click="isShowEditModal[ currentStep ] = true"
            v-if="modelRole[ 1 ]">
            Изменить
        </renins-button>
        <div class="panel-row panel-header">Уровень инновационности деятельности</div>

        <div class="panel-row">
            {{ formData.levelOfInnovativeness }}
        </div>

    </div>
    <div class="error_caption_block">В блоке имеются ошибки. Измените данные в блоке.</div>

    <renins-form-modal v-show="isShowEditModal[ currentStep ]" @close="close(currentStep)" :close-button="false">
        <template #head>Уровень инновационности деятельности</template>
        <template #body>

            <div class="block">
                <div class="block-row">
                    Выберите из списка 1 наиболее подходящий вариант. Выбор варианта должен подкрепляться вышеописанными
                    должностными обязанностями - функциями.
                </div>
                <div class="r-web-caption r-mb-5 radio_error" v-if="errors[9].levelOfInnovativeness">
                    Не выбран ответ
                </div>
                <div class="block-row">
                    <renins-radio v-model="formDataEdit.levelOfInnovativeness"
                                  :error="errors[9].levelOfInnovativeness"
                        val="Поддержка существующих стандартов работы">
                        Поддержка существующих стандартов работы
                    </renins-radio>
                </div>
                <div class="block-row">
                    <renins-radio v-model="formDataEdit.levelOfInnovativeness"
                                  :error="errors[9].levelOfInnovativeness"
                        val="Некоторая оптимизация, улучшение существующих стандартов работы (<10% изменений)">
                        Некоторая оптимизация, улучшение существующих стандартов работы (<10% изменений)
                    </renins-radio>
                </div>
                <div class="block-row">
                    <renins-radio v-model="formDataEdit.levelOfInnovativeness"
                                  :error="errors[9].levelOfInnovativeness"
                        val="Существенная оптимизация, улучшение существующих стандартов работы (10-25% изменений)">
                        Существенная оптимизация, улучшение существующих стандартов работы (10-25% изменений)
                    </renins-radio>
                </div>
                <div class="block-row">
                    <renins-radio v-model="formDataEdit.levelOfInnovativeness"
                                  :error="errors[9].levelOfInnovativeness"
                        val="Кардинальное изменение существующих стандартов работы на основе прогрессивных тенденций (>25% изменений)">
                        Кардинальное изменение существующих стандартов работы на основе прогрессивных тенденций (>25% изменений)
                    </renins-radio>
                </div>
                <div class="block-row">
                    <renins-radio v-model="formDataEdit.levelOfInnovativeness"
                                  :error="errors[9].levelOfInnovativeness"
                        val="Внедрение инновационных изменений - революционных рыночных практик">
                        Внедрение инновационных изменений - революционных рыночных практик
                    </renins-radio>
                </div>
            </div>

        </template>
        <template #footer>
            <div class="block" v-if="stepHasErrors(9)">
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