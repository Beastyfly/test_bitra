<template v-if="currentStep === 6">

    <div class="block panel" :class="{ error: false }" v-if="modelRole[ 1 ] != 2">
        <renins-button class="secondary xs" style="float: right" @click="isShowEditModal[ currentStep ] = true"
            v-if="modelRole[ 1 ]">
            Изменить
        </renins-button>
        <div class="panel-row panel-header">Полномочия в принятии решений</div>

        <div class="panel-row">
            {{ formData.decisions }}
        </div>

    </div>
    <div class="error_caption_block">В блоке имеются ошибки. Измените данные в блоке.</div>

    <renins-form-modal v-show="isShowEditModal[ currentStep ]" @close="close(currentStep)" :close-button="false">
        <template #head>Полномочия в принятии решений</template>
        <template #body>

            <div class="block">
                <div class="block-row row">
                    <div class="block-desc">Какие решения должность может принимать самостоятельно?</div>
                    <div>Например, решения по условиям сделок с клиентами, партнерами, подрядчиками, решения по закупке
                    оборудования, выбору поставщика, решения по срокам/стоимости проведения проектов и т.п.</div>
                </div>
                <div class="block-row row">
                    <renins-textarea placeholder="Описание" v-model="formDataEdit.decisions" :error="errors[6].decisions"
                        class="resize-vertical" rows="3"></renins-textarea>
                </div>
            </div>

        </template>
        <template #footer>
            <div class="block" v-if="stepHasErrors(6)">
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
