<template v-if="currentStep === 12">

    <div class="block panel" :class="{ error: false }" v-if="modelRole[ 1 ] != 2">
        <renins-button class="secondary xs" style="float: right" @click="isShowEditModal[ currentStep ] = true"
            v-if="modelRole[ 1 ]">
            Изменить
        </renins-button>
        <div class="panel-row panel-header">Знания, умения, навыки</div>

        <template v-if="formData.knowledgeOfMethods">
            <div class="panel-row">
                <renins-badge-label class="active">Знание методик и практик</renins-badge-label>
            </div>
            <div class="panel-row">
                {{ formData.knowledgeOfMethods }}
            </div>
        </template>

        <template v-if="formData.knowledgeOfComputerPrograms">
            <div class="panel-row">
                <div class="block-desc">
                    <renins-badge-label class="active">Знание средств, технологий и компьютерных программ. Умение работать с определенными документами</renins-badge-label>
                </div>
            </div>
            <div class="panel-row">
                {{ formData.knowledgeOfComputerPrograms }}
            </div>
        </template>

        <template v-if="formData.knowledgeOfSituation">
            <div class="panel-row">
                <div class="block-desc">
                    <renins-badge-label class="active">Знание текущей ситуации в функциональной области</renins-badge-label>
                </div>
            </div>
            <div class="panel-row">
                {{ formData.knowledgeOfSituation }}
            </div>
        </template>

        <template v-if="formData.businessQualities">
            <div class="panel-row">
                <renins-badge-label class="active">Деловые качества сотрудника</renins-badge-label>
            </div>
            <div class="panel-row">
                {{ formData.businessQualities }}
            </div>
        </template>

        <div class="panel-row">
            <div class="block-desc">
                <renins-badge-label class="active">Уровень владения английским языком</renins-badge-label>
            </div>
        </div>
        <div class="panel-row row languages-list">
            <div class="col-6">
                <div class="param-head">Язык</div>
                Английский
            </div>
            <div class="col-6">
                <div class="param-head">Уровень владения</div>
                {{ formData.englishLevel }}
            </div>
        </div>

        <template v-if="formData.languages.length">
            <div class="panel-row">
                <div class="block-desc">
                    <renins-badge-label class="active">Уровень владения другими языками</renins-badge-label>
                </div>
            </div>
            <div class="panel-row row languages-list" v-for="(language, index) in formData.languages">
                <div class="col-6">
                    <div class="param-head" v-if="index === 0">Язык</div>
                    {{ language.name }}
                </div>
                <div class="col-6">
                    <div class="param-head" v-if="index === 0">Уровень владения</div>
                    {{ language.level }}
                </div>
            </div>
        </template>

    </div>
    <div class="error_caption_block">В блоке имеются ошибки. Измените данные в блоке.</div>

    <renins-form-modal v-show="isShowEditModal[ currentStep ]" @close="close(currentStep)" :close-button="false">
        <template #head>Знания, умения, навыки</template>
        <template #body>

            <div class="block">
                <div class="block-row" style="display: block;">
                    Перечислите знания, умения и навыки, деловые и личностные качества необходимые для полноценного
                    выполнения должностных обязанностей. <span style="color: #FF9E17">Необходимо заполнять данный пункт как
                    требование к должности. Не следует ориентироваться на фактические данные лица, ее занимающего.</span>
                </div>
            </div>

            <div class="block">
                <div class="block-row">
                    <renins-badge-label class="active">Знание методик и практик</renins-badge-label>
                </div>
                <div class="block-row row">
                    <renins-textarea placeholder="Описание" v-model="formDataEdit.knowledgeOfMethods"
                                     :error="errors[12].knowledgeOfMethods"
                        class="resize-vertical" rows="3"></renins-textarea>
                </div>
            </div>

            <div class="block">
                <div class="block-row row">
                    <div class="block-desc">
                        <renins-badge-label class="active">
                            Знание средств, технологий и компьютерных программ.
                            Умение работать с определенными документами
                        </renins-badge-label>
                    </div>
                    <div>Перечислите требуемые знания и укажите названия программ, продуктов</div>
                </div>
                <div class="block-row row">
                    <renins-textarea placeholder="Описание" v-model="formDataEdit.knowledgeOfComputerPrograms"
                                     :error="errors[12].knowledgeOfComputerPrograms"
                        class="resize-vertical" rows="3"></renins-textarea>
                </div>
            </div>

            <div class="block">
                <div class="block-row row">
                    <div class="block-desc">
                        <renins-badge-label class="active">Знание текущей ситуации в функциональной области</renins-badge-label>
                    </div>
                    <div>Например, знания конкурентной среды, тенденции развития финансовых рынков, рынков технических систем и пр.</div>
                </div>
                <div class="block-row row">
                    <renins-textarea placeholder="Описание" v-model="formDataEdit.knowledgeOfSituation"
                                     :error="errors[12].knowledgeOfSituation"
                        class="resize-vertical" rows="3"></renins-textarea>
                </div>
            </div>
            <div class="block">
                <div class="block-row">
                    <renins-badge-label class="active">Деловые качества сотрудника</renins-badge-label>
                </div>
                <div class="block-row row">
                    <renins-textarea placeholder="Описание" v-model="formDataEdit.businessQualities"
                                     :error="errors[12].businessQualities"
                        class="resize-vertical" rows="3"></renins-textarea>
                </div>
            </div>

            <div class="block">
                <div class="block-row row">
                    <div class="block-desc">
                        <renins-badge-label class="active">Уровень владения английским языком</renins-badge-label>
                    </div>
                    <div>Заполняется обязательно</div>
                </div>
                <div class="block-row languages-list">
                    <div>
                        <renins-text-input caption="Язык" value="Английский" class="w-100" readonly></renins-text-input>
                    </div>
                    <div>
                        <renins-select v-model="formDataEdit.englishLevel" v-bind:items="englishLevels"
                                       :error="errors[12].englishLevel"
                            placeholder="Уровень владения"></renins-select>
                    </div>
                    <div style="width:56px;">
                    </div>
                </div>
            </div>

            <div class="block">
                <div class="block-row row">
                    <div class="block-desc">
                        <renins-badge-label class="active">Уровень владения другими языками</renins-badge-label>
                    </div>
                    <div>Заполняется опционально</div>
                </div>
                <div class="block-row languages-list" v-for="(language, index) in formDataEdit.languages">
                    <div>
                        <renins-text-input v-model="language.name" caption="Язык" class="w-100"></renins-text-input>
                    </div>
                    <div>
                        <renins-select v-model="language.level" v-bind:items="englishLevels"
                            placeholder="Уровень владения"></renins-select>
                    </div>
                    <div style="width:56px;">
                        <renins-button class="secondary lg" style="width:56px; padding: 0" @click="addLanguage()"
                            v-if="index === formDataEdit.languages.length - 1">
                            <renins-icon class="plus" style="background-color: #1E222E"></renins-icon>
                        </renins-button>
                    </div>
                </div>
            </div>

        </template>
        <template #footer>
            <div class="block" v-if="stepHasErrors(12)">
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