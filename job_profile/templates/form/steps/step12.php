<div class="panel" v-if="currentStep === 12">
	<div class="panel-header">Знания, умения, навыки </div>
    <div class="block">
        <div class="block-row">
            Перечислите знания, умения и навыки, деловые и личностные качества необходимые для полноценного выполнения должностных обязанностей. <span style="color: #FF9E17">Необходимо заполнять данный пункт как требование к должности. Не следует ориентироваться на фактические данные лица, ее занимающего.</span>
        </div>
	</div>
    <div class="block">
        <div class="block-row">
            <renins-badge-label class="active">Знание методик и практик</renins-badge-label>
        </div>
        <div class="block-row">
            <renins-textarea placeholder="Описание" v-model="formData.step12.knowledgeOfMethods" :error="errors.knowledgeOfMethods" class="resize-vertical" rows="3"></renins-textarea>
        </div>
    </div>
    <div class="block">
        <div class="block-row">
            <div class="block-desc">
                <renins-badge-label class="active">Знание средств, технологий и компьютерных программ. Умение работать с определенными документами</renins-badge-label>
            </div>
            Перечислите требуемые знания и укажите названия программ, продуктов
        </div>
        <div class="block-row">
            <renins-textarea placeholder="Описание" v-model="formData.step12.knowledgeOfComputerPrograms" :error="errors.knowledgeOfComputerPrograms" class="resize-vertical" rows="3"></renins-textarea>
        </div>
    </div>
    <div class="block">
        <div class="block-row">
            <div class="block-desc">
                <renins-badge-label class="active">Знание текущей ситуации в функциональной области</renins-badge-label>
            </div>
            Например, знания конкурентной среды, тенденции развития финансовых рынков, рынков технических систем и пр.
        </div>
        <div class="block-row">
            <renins-textarea placeholder="Описание" v-model="formData.step12.knowledgeOfSituation" :error="errors.knowledgeOfSituation" class="resize-vertical" rows="3"></renins-textarea>
        </div>
    </div>
    <div class="block">
        <div class="block-row">
            <renins-badge-label class="active">Деловые качества сотрудника</renins-badge-label>
        </div>
        <div class="block-row">
            <renins-textarea placeholder="Описание" v-model="formData.step12.businessQualities" :error="errors.businessQualities" class="resize-vertical" rows="3"></renins-textarea>
        </div>
    </div>

    <div class="block">
        <div class="block-row">
            <div class="block-desc">
                <renins-badge-label class="active">Уровень владения английским языком</renins-badge-label>
            </div>
            Заполняется обязательно
        </div>
        <div class="block-row languages-list">
            <div>
                <renins-text-input caption="Язык" value="Английский" class="w-100" readonly></renins-text-input>
            </div>
            <div>
                <renins-select
                        placeholder="Уровень владения"
                        v-model="formData.step12.englishLevel"
                        v-bind:items="englishLevels"
                        :error="errors.englishLevel"
                ></renins-select>
            </div>
            <div style="width:56px;">
            </div>
        </div>
    </div>
    <div class="block">
        <div class="block-row">
            <div class="block-desc">
                <renins-badge-label class="active">Уровень владения другими языками</renins-badge-label>
            </div>
            Заполняется опционально
        </div>
        <div class="block-row languages-list" v-for="(language, index) in formData.step12.languages">
            <div>
                <renins-text-input v-model="formData.step12.languages[index].name" caption="Язык" class="w-100"></renins-text-input>
            </div>
            <div>
                <renins-select
                        placeholder="Уровень владения"
                        v-model="formData.step12.languages[index].level"
                        v-bind:items="englishLevels"
                ></renins-select>
            </div>
            <div style="width:56px;">
                <renins-button class="secondary lg" style="width:56px; padding: 0" v-if="index === formData.step12.languages.length - 1" @click="addLanguage()">
                    <renins-icon class="plus" style="background-color: #1E222E"></renins-icon>
                </renins-button>
            </div>
        </div>
    </div>

    <div class="block" v-if="stepHasErrors(12)">
        <renins-quote class="errored">На форме присутствуют незаполненные поля</renins-quote>
    </div>

    <div v-if="statusId !== 'trash'" class="block">
        <renins-button class="secondary lg" style="width: 210px; margin-right: 16px;" @click="saveAndRedirect()" :loading="isSaving" :disabled="isAutoSaving">
            {{ !isAutoSaving ? 'Сохранить и выйти' : 'Автосохранение...' }}
        </renins-button>
        <renins-button class="primary lg" style="margin-right: 16px;" @click="nextStep()" :disabled="stepHasErrors(12)">
            Продолжить
        </renins-button>
        <renins-button v-if="isOD && stageId && (stageId !== 'create')" class="secondary lg" style="margin-right: 16px;"
            @click="isShowRevokeModal = true; revoke_comment = '';" :loading="isRevoking" :disabled="stepHasErrors(12)">
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
