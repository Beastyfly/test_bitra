<template v-if="currentStep === 11">

    <div class="block panel" :class="{ error: false }" v-if="modelRole[ 1 ] != 2">
        <renins-button class="secondary xs" style="float: right" @click="isShowEditModal[ currentStep ] = true"
            v-if="modelRole[ 1 ]">
            Изменить
        </renins-button>
        <div class="panel-row panel-header">Требования, необходимые для выполнения должностных обязанностей</div>

        <div class="panel-row">
            <div class="block-desc">Образование</div>
            {{ formData.minimumLevelOfEducation }}
        </div>

        <div class="panel-row">
            <div class="param-head">Специализация / Квалификация</div>
            {{ formData.Qualification }}
        </div>
        <div class="panel-row">
            <div class="param-head">Сертификация (если необходима для должности)</div>
            {{ formData.Certification }}
        </div>
        <div class="panel-row">
            <div class="param-head">Соответствие квалификационным требованиям (профстандарт)</div>
            {{ formData.professionalStandard }}
        </div>

    </div>
    <div class="error_caption_block">В блоке имеются ошибки. Измените данные в блоке.</div>

    <renins-form-modal v-show="isShowEditModal[ currentStep ]" @close="close(currentStep)" :close-button="false">
        <template #head>Требования, необходимые для выполнения должностных обязанностей</template>
        <template #body>

            <div class="block">
                <div class="block-row row">
                    <div class="block-desc">Образование</div>
                    <div>Укажите минимальный уровень образования, необходимый для полноценного выполнения должностных
                    обязанностей. Выберите из списка 1 наиболее подходящий вариант. Просьба заполнять данный пункт
                        как требование к должности. Не следует ориентироваться на фактические данные лица, ее занимающего.</div>
                </div>
                <div class="r-web-caption r-mb-5 radio_error" v-if="errors[11].minimumLevelOfEducation">
                    Не выбран ответ
                </div>
                <div class="block-row">
                    <renins-radio v-model="formDataEdit.minimumLevelOfEducation" :error="errors[11].minimumLevelOfEducation" val="Среднее общее">
                        Среднее общее
                    </renins-radio>
                </div>
                <div class="block-row">
                    <renins-radio v-model="formDataEdit.minimumLevelOfEducation" :error="errors[11].minimumLevelOfEducation" val="Среднее специальное">
                        Среднее специальное
                    </renins-radio>
                </div>
                <div class="block-row">
                    <renins-radio v-model="formDataEdit.minimumLevelOfEducation" :error="errors[11].minimumLevelOfEducation" val="Неполное высшее">
                        Неполное высшее
                    </renins-radio>
                </div>
                <div class="block-row">
                    <renins-radio v-model="formDataEdit.minimumLevelOfEducation" :error="errors[11].minimumLevelOfEducation" val="Высшее">
                        Высшее
                    </renins-radio>
                </div>
                <div class="block-row">
                    <renins-radio v-model="formDataEdit.minimumLevelOfEducation" :error="errors[11].minimumLevelOfEducation" val="MBA">
                        MBA
                    </renins-radio>
                </div>
                <div class="block-row">
                    <renins-radio v-model="formDataEdit.minimumLevelOfEducation" :error="errors[11].minimumLevelOfEducation"
                        val="Ученая степень (кандидат/доктор наук)">
                        Ученая степень (кандидат/доктор наук)
                    </renins-radio>
                </div>
            </div>

            <div class="block">
                <renins-text-input v-model="formDataEdit.Qualification" class="w-100"
                    caption="Специализация / Квалификация"></renins-text-input>
            </div>
            <div class="block">
                <renins-text-input v-model="formDataEdit.Certification" class="w-100"
                    caption="Сертификация (если необходима для должности)"></renins-text-input>
            </div>
            <div class="block">
                <renins-text-input v-model="formDataEdit.professionalStandard" class="w-100"
                    caption="Соответствие квалификационным требованиям (профстандарт)"></renins-text-input>
            </div>

        </template>
        <template #footer>
            <div class="block" v-if="stepHasErrors(11)">
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