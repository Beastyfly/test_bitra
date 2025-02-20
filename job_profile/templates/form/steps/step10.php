<div class="panel" v-if="currentStep === 10">
	<div class="panel-header">Коммуникации внутри компании</div>

    <div class="block">
        <div class="block-row">
            Укажите названия подразделений/комитетов компании, которые входят в круг рабочего взаимодействия должности.
            <br>
            Укажите уровень должностей, с которыми ведется взаимодействие (рядовые сотрудники, руководители, топ-менеджеры и т.д.).        </div>
        <div class="block-row">
            <renins-textarea placeholder="Описание" v-model="formData.step10.interactionCircleWithinTheCompany" class="resize-vertical" rows="3"></renins-textarea>
        </div>
    </div>

    <div class="block panel-header">Внешние коммуникации</div>

    <div class="block">
        <div class="block-row">
            Укажите с какими организациями и бизнесами предполагается взаимодействие. Возможен выбор нескольких вариантов из выпадающего списка        </div>
        <div class="block-row">
            <renins-multi-select placeholder="Клиенты B2B" style="margin-right: 16px; width: 100%" v-model="formData.step10.b2bClients" v-bind:items="b2bClients"></renins-multi-select>
        </div>
    </div>
    <div class="block">
        <div class="block-row">
            <renins-multi-select placeholder="Клиенты B2C" style="margin-right: 16px; width: 100%" v-model="formData.step10.b2cClients" v-bind:items="b2cClients"></renins-multi-select>
        </div>
    </div>

    <div class="block">
        <div class="block-row">
            <renins-multi-select placeholder="Другие" style="margin-right: 16px; width: 100%" v-model="formData.step10.otherClients" v-bind:items="otherClients"></renins-multi-select>
        </div>
    </div>
    <div class="block">
        <div class="block-row">
            <div class="block-desc">Укажите названия внешних организаций, уровень должностей взаимодействия </div>
        </div>
        <div class="block-row">
            <renins-textarea placeholder="Описание" v-model="formData.step10.namesOfExternalOrganizations" class="resize-vertical" rows="3"></renins-textarea>
        </div>
    </div>

    <div class="block panel-header">Преобладающий характер коммуникаций</div>

    <div class="block">
        <div class="block-row">
            Выберите из списка, возможен выбор нескольких подходящих вариантов
        </div>
        <div class="r-web-caption r-mb-5 radio_error" v-if="errors.isTransmittingInformation">
            Не выбран ответ
        </div>
        <div class="block-row row">
            <div>
                <renins-checkbox v-model="formData.step10.isTransmittingInformation" :error="errors.isTransmittingInformation" label="Прием/передача информации"></renins-checkbox>
            </div>
        </div>
        <div class="block-row row">
            <div>
                <renins-checkbox v-model="formData.step10.isConsulting" :error="errors.isConsulting" label="Консультирование, объяснение существующих правил, стремление к соглашению"></renins-checkbox>
            </div>
        </div>
        <div class="block-row row">
            <div>
                <renins-checkbox v-model="formData.step10.isInteraction" :error="errors.isInteraction" label="Взаимодействие и влияние с применением профессиональной аргументации"></renins-checkbox>
            </div>
        </div>
        <div class="block-row">
            <div>
                <renins-checkbox v-model="formData.step10.isParticipationNegotiations" :error="errors.isParticipationNegotiations" label="Участие в переговорах рабочего / тактического уровня"></renins-checkbox>
            </div>
        </div>
        <div class="block-row">
            <div>
                <renins-checkbox v-model="formData.step10.isAuthoritativeInfluence" :error="errors.isAuthoritativeInfluence" label="Ключевое участие в переговорах тактического уровня, авторитетное влияние на результаты переговоров"></renins-checkbox>
            </div>
        </div>
        <div class="block-row">
            <div>
                <renins-checkbox v-model="formData.step10.isStrategicNegotiations" :error="errors.isStrategicNegotiations" label="Ведение стратегических переговоров"></renins-checkbox>
            </div>
        </div>
    </div>

    <div class="block">
        <div class="block-row">
            <div class="block-desc">Коммуникации в ситуациях конфликта интересов сторон</div>
            Выберите из списка 1 наиболее подходящий вариант
        </div>
        <div class="r-web-caption r-mb-5 radio_error" v-if="errors.amountOfCommunications">
            Не выбран ответ
        </div>
        <div class="block-row">
            <renins-radio v-model="formData.step10.amountOfCommunications" :error="errors.amountOfCommunications" val="Редко">Редко</renins-radio>
        </div>
        <div class="block-row">
            <renins-radio v-model="formData.step10.amountOfCommunications" :error="errors.amountOfCommunications" val="Иногда">Иногда</renins-radio>
        </div>
        <div class="block-row">
            <renins-radio v-model="formData.step10.amountOfCommunications" :error="errors.amountOfCommunications" val="Часто">Часто</renins-radio>
        </div>
    </div>

    <div class="block" v-if="stepHasErrors(10)">
        <renins-quote class="errored">На форме присутствуют незаполненные поля</renins-quote>
    </div>

    <div v-if="statusId !== 'trash'" class="block">
        <renins-button class="secondary lg" style="width: 210px; margin-right: 16px;" @click="saveAndRedirect()" :loading="isSaving" :disabled="isAutoSaving">
            {{ !isAutoSaving ? 'Сохранить и выйти' : 'Автосохранение...' }}
        </renins-button>
        <renins-button class="primary lg" style="margin-right: 16px;" @click="nextStep()" :disabled="stepHasErrors(10)">
            Продолжить
        </renins-button>
        <renins-button v-if="isOD && stageId && (stageId !== 'create')" class="secondary lg" style="margin-right: 16px;"
            @click="isShowRevokeModal = true; revoke_comment = '';" :loading="isRevoking" :disabled="stepHasErrors(10)">
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
