<template v-if="currentStep === 10">

    <div class="block panel high-margin" :class="{ error: false }" v-if="modelRole[ 1 ] != 2">
        <renins-button class="secondary xs" style="float: right" @click="isShowEditModal[ currentStep ] = true"
            v-if="modelRole[ 1 ]">
            Изменить
        </renins-button>

        <div class="panel-header">Коммуникации</div>

        <div v-if="formData.interactionCircleWithinTheCompany" style="margin-top:24px">
            <renins-badge-label class="active">Коммуникации внутри компании</renins-badge-label>
            <div class="panel-row">
                {{ formData.interactionCircleWithinTheCompany }}
            </div>
        </div>

        <div class="panel-row" style="margin-top:24px">
            <renins-badge-label class="active">Внешние коммуникации</renins-badge-label>
        </div>
        <div class="panel-row" v-if="formData.b2bClients && (formData.b2bClients[0] !== 'false')">
            <div class="param-head">Клиенты B2B</div>
            <div>{{ formData.b2bClients.join(', ') }}</div>
        </div>
        <div class="panel-row" v-if="formData.b2cClients && (formData.b2cClients[0] !== 'false')">
            <div class="param-head">Клиенты B2С</div>
            <div>{{ formData.b2cClients.join(', ') }}</div>
        </div>
        <div class="panel-row" v-if="formData.otherClients && (formData.b2cClients[0] !== 'false')">
            <div class="param-head">Другие</div>
            <div>{{ formData.otherClients.join(', ') }}</div>
        </div>

        <div class="panel-row">
            <div class="param-head">Названия внешних организаций, уровень должностей взаимодействия </div>
            <div>{{ formData.namesOfExternalOrganizations ? formData.namesOfExternalOrganizations : '-' }}</div>
        </div>

        <div class="panel-row" style="margin-top:24px">
            <renins-badge-label class="active">Преобладающий характер коммуникаций</renins-badge-label>
        </div>
        <div class="panel-row">
            <template v-if="formData.isTransmittingInformation
                || formData.isConsulting
                || formData.isInteraction
                || formData.isParticipationNegotiations
                || formData.isAuthoritativeInfluence
                || formData.isStrategicNegotiations">
                <div v-if="formData.isTransmittingInformation">
                    <span class="dot2"></span> Прием/передача информации
                </div>
                <div v-if="formData.isConsulting">
                    <span class="dot2"></span> Консультирование, объяснение существующих правил, стремление к соглашению
                </div>
                <div v-if="formData.isInteraction">
                    <span class="dot2"></span> Взаимодействие и влияние с применением профессиональной аргументации
                </div>
                <div v-if="formData.isParticipationNegotiations">
                    <span class="dot2"></span> Участие в переговорах рабочего / тактического уровня
                </div>
                <div v-if="formData.isAuthoritativeInfluence">
                    <span class="dot2"></span> Ключевое участие в переговорах тактического уровня, авторитетное влияние на результаты переговоров
                </div>
                <div v-if="formData.isStrategicNegotiations">
                    <span class="dot2"></span> Ведение стратегических переговоров
                </div>
            </template>
            <div v-else>-</div>
        </div>

        <div class="panel-row">
            <div class="param-head">Коммуникации в ситуациях конфликта интересов сторон</div>
            <div>{{ formData.amountOfCommunications }}</div>
        </div>

    </div>
    <div class="error_caption_block">В блоке имеются ошибки. Измените данные в блоке.</div>

    <renins-form-modal v-show="isShowEditModal[ currentStep ]" @close="close(currentStep)" :close-button="false">
        <template #head>Коммуникации</template>
        <template #body>

            <div class="panel-header">Коммуникации внутри компании</div>

            <div class="block">
                <div class="block-row">
                    Укажите названия подразделений/комитетов компании, которые входят в круг рабочего взаимодействия должности.
                    <br>
                    Укажите уровень должностей, с которыми ведется взаимодействие (рядовые сотрудники, руководители, топ-менеджеры и т.д.).        </div>
                <div class="block-row row">
                    <renins-textarea placeholder="Описание" v-model="formDataEdit.interactionCircleWithinTheCompany"
                        class="resize-vertical" rows="3"></renins-textarea>
                </div>
            </div>

            <div class="panel-header">Внешние коммуникации</div>

            <div class="block">
                <div class="block-row">
                    Укажите с какими организациями и бизнесами предполагается взаимодействие.
                    Возможен выбор нескольких вариантов из выпадающего списка        </div>
                <div class="block-row">
                    <renins-multi-select placeholder="Клиенты B2B" style="margin-right: 16px; width: 100%" v-model="formDataEdit.b2bClients"
                        v-bind:items="b2bClients"></renins-multi-select>
                </div>
            </div>
            <div class="block">
                <div class="block-row">
                    <renins-multi-select placeholder="Клиенты B2C" style="margin-right: 16px; width: 100%" v-model="formDataEdit.b2cClients"
                        v-bind:items="b2cClients"></renins-multi-select>
                </div>
            </div>

            <div class="block">
                <div class="block-row">
                    <renins-multi-select placeholder="Другие" style="margin-right: 16px; width: 100%" v-model="formDataEdit.otherClients"
                        v-bind:items="otherClients"></renins-multi-select>
                </div>
            </div>
            <div class="block">
                <div class="block-row">
                    <div class="block-desc">Укажите названия внешних организаций, уровень должностей взаимодействия </div>
                </div>
                <div class="block-row row">
                    <renins-textarea placeholder="Описание" v-model="formDataEdit.namesOfExternalOrganizations"
                        class="resize-vertical" rows="3"></renins-textarea>
                </div>
            </div>
            <div class="block">
                <div class="block-row row">
                    <div class="block-desc">Преобладающий характер коммуникаций </div>
                    <div>Выберите из списка, возможен выбор нескольких подходящих вариантов</div>
                </div>
                <div class="r-web-caption r-mb-5 radio_error" v-if="errors[10].isTransmittingInformation">
                    Не выбран ответ
                </div>
                <div class="block-row row">
                    <div>
                        <renins-checkbox v-model="formDataEdit.isTransmittingInformation" :error="errors[10].isTransmittingInformation" label="Прием/передача информации"></renins-checkbox>
                    </div>
                </div>
                <div class="block-row row">
                    <div>
                        <renins-checkbox v-model="formDataEdit.isConsulting" :error="errors[10].isConsulting"
                            label="Консультирование, объяснение существующих правил, стремление к соглашению"></renins-checkbox>
                    </div>
                </div>
                <div class="block-row row">
                    <div>
                        <renins-checkbox v-model="formDataEdit.isInteraction" :error="errors[10].isInteraction"
                            label="Взаимодействие и влияние с применением профессиональной аргументации"></renins-checkbox>
                    </div>
                </div>
                <div class="block-row">
                    <div>
                        <renins-checkbox v-model="formDataEdit.isParticipationNegotiations" :error="errors[10].isParticipationNegotiations"
                            label="Участие в переговорах рабочего / тактического уровня"></renins-checkbox>
                    </div>
                </div>
                <div class="block-row">
                    <div>
                        <renins-checkbox v-model="formDataEdit.isAuthoritativeInfluence" :error="errors[10].isAuthoritativeInfluence"
                            label="Ключевое участие в переговорах тактического уровня, авторитетное влияние на результаты переговоров"></renins-checkbox>
                    </div>
                </div>
                <div class="block-row">
                    <div>
                        <renins-checkbox v-model="formDataEdit.isStrategicNegotiations" :error="errors[10].isStrategicNegotiations"
                            label="Ведение стратегических переговоров"></renins-checkbox>
                    </div>
                </div>
            </div>

            <div class="block">
                <div class="block-row row">
                    <div class="block-desc">Коммуникации в ситуациях конфликта интересов сторон</div>
                    <div>Выберите из списка 1 наиболее подходящий вариант</div>
                </div>
                <div class="r-web-caption r-mb-5 radio_error" v-if="errors[10].amountOfCommunications">
                    Не выбран ответ
                </div>
                <div class="block-row">
                    <renins-radio v-model="formDataEdit.amountOfCommunications" :error="errors[10].amountOfCommunications" val="Редко">Редко</renins-radio>
                </div>
                <div class="block-row">
                    <renins-radio v-model="formDataEdit.amountOfCommunications" :error="errors[10].amountOfCommunications" val="Иногда">Иногда</renins-radio>
                </div>
                <div class="block-row">
                    <renins-radio v-model="formDataEdit.amountOfCommunications" :error="errors[10].amountOfCommunications" val="Часто">Часто</renins-radio>
                </div>
            </div>

        </template>
        <template #footer>
            <div class="block" v-if="stepHasErrors(10)">
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