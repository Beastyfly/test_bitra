<template v-if="currentStep === 7">

    <div class="block panel high-margin" :class="{ error: false }" v-if="modelRole[ 1 ] != 2">
        <renins-button class="secondary xs" style="float: right" @click="isShowEditModal[ currentStep ] = true"
            v-if="modelRole[ 1 ]">
            Изменить
        </renins-button>
        <div class="panel-row panel-header">Ответственность за финансовый результат</div>

        <div class="block-row" style="margin-top: 24px;">
            <div>
                <div class="param-head">Вносит личный вклад в генерацию финансового результата</div>
                {{ formData.financialResultGeneration === 'Да' ? 'Да' : 'Нет'}}
            </div>
        </div>

        <template v-if="formData.financialResultGeneration === 'Да'">
            <div class="block-row">
                <div>
                    <div class="param-head">Сумма по EBIT, руб/год</div>
                    {{ formData.EBIT | numberFormat }}
                </div>
            </div>
            <div class="panel-row">
                <div>
                    <div class="param-head">Сумма по WP, руб/год</div>
                    {{ formData.WP | numberFormat }}
                </div>
            </div>
        </template>

    </div>
    <div class="error_caption_block">В блоке имеются ошибки. Измените данные в блоке.</div>

    <renins-form-modal v-show="isShowEditModal[ currentStep ]" @close="close(currentStep)" :close-button="false">
        <template #head>Ответственность за финансовый результат</template>
        <template #body>

            <div class="block">
                <div class="block-row row">
                    <div class="block-desc">Вносит ли должность личный вклад в генерацию финансового результата?</div>
                    <div>Если для должности установлен план по продаже продуктов/услуг на определенную сумму в год, по
                    привлечению определенного числа клиентов, заключению партнерских соглашений, развитию бизнеса,
                        открытию точек продаж, то необходимо оценить значение личного вклада в генерацию прибыли компании.</div>
                </div>
                <div class="r-web-caption r-mb-5 radio_error" v-if="errors[7].financialResultGeneration">
                    Не выбран ответ
                </div>
                <div class="block-row">
                    <renins-radio v-model="formDataEdit.financialResultGeneration" val="Да" :error="errors[7].financialResultGeneration">Да</renins-radio>
                    <renins-radio v-model="formDataEdit.financialResultGeneration" val="Нет" :error="errors[7].financialResultGeneration" style="margin-left: 32px">Нет</renins-radio>
                </div>
            </div>

            <template v-if="formDataEdit.financialResultGeneration === 'Да'">
                <div class="block">
                    <div class="block-row">
                        <div class="block-desc">Сколько он составляет по EBIT, руб/год? </div>
                    </div>
                    <div class="block-row row">
                        <div class="col-4">
                            <renins-text-input v-model="formDataEdit.EBIT" :error="errors[7].EBIT" caption="Сумма, руб" placeholder="" type="currency" class="w-100"></renins-text-input>
                        </div>
                    </div>
                </div>
                <div class="block">
                    <div class="block-row">
                        <div class="block-desc">Сколько он составляет по WP (подписанная премия), руб/год? </div>
                    </div>
                    <div class="block-row row">
                        <div class="col-4">
                            <renins-text-input v-model="formDataEdit.WP" :error="errors[7].WP" caption="Сумма, руб" placeholder="" type="currency" class="w-100"></renins-text-input>
                        </div>
                    </div>
                </div>
            </template>

        </template>
        <template #footer>
            <div class="block" v-if="stepHasErrors(7)">
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