<template v-if="currentStep === 13">

    <div class="block panel" :class="{ error: false }" v-if="modelRole[ 1 ] != 2">
        <renins-button class="secondary xs" style="float: right" @click="isShowEditModal[ currentStep ] = true"
            v-if="modelRole[ 1 ]">
            Изменить
        </renins-button>
        <div class="panel-row panel-header">Опыт</div>

        <div class="panel-row">
            <renins-badge-label class="active">Профессиональный опыт</renins-badge-label>
        </div>
        <template v-if="formData.professionalExperience">
            <div class="panel-row">
                <div class="param-head">Кол-во</div>
                {{ formData.professionalExperienceYears }}
                <renins-declination :value="formData.professionalExperienceYears">
                    <template #few>лет</template>
                    <template #one>год</template>
                    <template #two>года</template>
                </renins-declination>
            </div>
            <div class="panel-row">
                <div class="param-head">В областях и сферах</div>
                {{ formData.fieldOfActivity }}
            </div>
        </template>

        <template v-if="formData.managementExperience">
            <div class="panel-row">
                <renins-badge-label class="active">Управленческий опыт</renins-badge-label>
            </div>
            <div class="panel-row">
                <div class="param-head">Кол-во</div>
                {{ formData.managementExperienceYears }}
                <renins-declination :value="formData.managementExperienceYears">
                    <template #few>лет</template>
                    <template #one>год</template>
                    <template #two>года</template>
                </renins-declination>
            </div>
            <div class="panel-row">
                <div class="param-head">В областях и сферах</div>
                {{ formData.fieldOfManagementActivity }}
            </div>
        </template>

    </div>
    <div class="error_caption_block">В блоке имеются ошибки. Измените данные в блоке.</div>

    <renins-form-modal v-show="isShowEditModal[ currentStep ]" @close="close(currentStep)" :close-button="false">
        <template #head>Опыт</template>
        <template #body>

            <div class="block">
                <div class="block-row block-desc">
                    Какой опыт необходим для должности?
                </div>
                <div class="block-row row">
                    <div>
                        <renins-checkbox v-model="formDataEdit.professionalExperience" label="Профессиональный опыт"></renins-checkbox>
                    </div>
                </div>
            </div>
            <div v-if="formDataEdit.professionalExperience" class="block" style="margin-left: 30px">
                <div class="block-row row">
                    <div class="col-3">
                        <renins-text-input v-model="formDataEdit.professionalExperienceYears" :error="errors[13].professionalExperienceYears" caption="Кол-во, лет" class="w-100"></renins-text-input>
                    </div>
                </div>
                <div class="block-row">
                    Укажите направления деятельности, по которым необходим профессиональный опыт работы для полноценного выполнения обязанностей этой должности
                </div>
                <div class="block-row block-desc">
                    В областях и сферах
                </div>
                <div class="block-row row">
                    <renins-textarea placeholder="Описание" v-model="formDataEdit.fieldOfActivity" :error="errors[13].fieldOfActivity" class="resize-vertical" rows="3"></renins-textarea>
                </div>
            </div>

            <div class="block">
                <div class="block-row row">
                    <div>
                        <renins-checkbox v-model="formDataEdit.managementExperience" :error="errors[13].managementExperienceYears" label="Управленческий опыт"></renins-checkbox>
                    </div>
                </div>
            </div>
            <div v-if="formDataEdit.managementExperience" class="block" style="margin-left: 30px">
                <div class="block-row row">
                    <div class="col-3">
                        <renins-text-input v-model="formDataEdit.managementExperienceYears" :error="errors[13].fieldOfManagementActivity" caption="Кол-во, лет" class="w-100"></renins-text-input>
                    </div>
                </div>
                <div class="block-row">
                    Укажите какой управленческий опыт необходим для данной должности: управление коллективами (какой численности),
                    управление процессами/проектами (какой сложности и продолжительности), управление ресурсами (какими).
                    Укажите необходимое кол-во лет для каждого из видов опыта.
                </div>
                <div class="block-row block-desc">
                    В областях и сферах
                </div>
                <div class="block-row row">
                    <renins-textarea placeholder="Описание" v-model="formDataEdit.fieldOfManagementActivity" :error="errors[13].fieldOfManagementActivity" class="resize-vertical" rows="3"></renins-textarea>
                </div>
            </div>

        </template>
        <template #footer>
            <div class="block" v-if="stepHasErrors(13)">
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