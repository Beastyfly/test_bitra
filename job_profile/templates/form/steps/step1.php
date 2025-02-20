<div class="panel" v-if="currentStep === 1">
    <div class="panel-header">Должность</div>

    <div class="block">
        <div class="block-row">
            Укажите полное наименование должности, согласно штатному расписанию, для вновь создаваемой должности — ее
            планируемое наименование
        </div>
        <div class="block-row">
            <renins-text-input v-model="formData.step1.positionName" caption="Название должности" tooltip="true"
                class="w-100" :readonly="isFillingStage" :disabled="isFillingStage" :error="errors.positionName"></renins-text-input>
        </div>
        <div class="block-row row">
            <div class="col-4">
                <renins-select placeholder="Кост-центр" v-model="formData.step1.costCenter" v-bind:items="costCenters"
                    class="w-100" :readonly="isFillingStage" :disabled="isFillingStage" :error="errors.costCenter"></renins-select>
            </div>
            <div class="col-4">
                <renins-text-input caption="Функция 1" v-model="formData.step1.func1Name"
                    class="w-100" :readonly="true" :disabled="true" :tooltip="true"></renins-text-input>
            </div>
            <div class="col-4">
                <renins-text-input caption="Функция 2" v-model="formData.step1.func2Name"
                    class="w-100" :readonly="true" :disabled="true" :tooltip="true"></renins-text-input>
            </div>
        </div>
    </div>

    <div class="block">
        <div class="block-row">
            Укажите всю цепочку подразделений, в которые входит должность по организационной структуре, без
            использования аббревиатур — все уровни
        </div>
        <div class="block-row row">
            <div class="col-12">
                <renins-select placeholder="Подразделение" v-model="formData.step1.department" v-bind:items="departments"
                    :readonly="isFillingStage" :disabled="isFillingStage" :tooltip="true" :input="true"></renins-select>
            </div>
        </div>
        <div class="block-row row">
            <div class="col-6">
                <renins-select placeholder="Branch" v-model="formData.step1.branch" v-bind:items="branches"
                    :readonly="isFillingStage" :disabled="isFillingStage" :error="errors.branch"></renins-select>
            </div>
            <div class="col-6">
                <renins-select placeholder="Location" v-model="formData.step1.location" v-bind:items="locations"
                    :readonly="isFillingStage" :disabled="isFillingStage"></renins-select>
            </div>
        </div>
    </div>

    <template v-if="!isFillingStage || isOD">
        <div class="block">
            <div class="panel-header">
                Требуется согласование
            </div>
        </div>
        <div class="block">
            <div class="block-row">
                Здесь вы задаете цепочку согласования. Заполняйте поля, только если требуется согласование.
                Если руководитель является и административным и функциональным руководителем сотрудника,
                укажите его 1 раз. Если вы хотите добавить согласующего не являющегося руководителем сотрудника
                по орг. структуре, нажмите на плюс.
            </div>
        </div>
        <div class="block">
            <div class="block-row row">
                <div class="col-6">
                    <div class="block-row block-desc">
                        Административный руководитель
                    </div>
                    <renins-userselector ref="adm" v-model="formData.step1.admManager" caption="Введите ФИО" class="w-100"
                        single="true" :options="{all:'Y', showFullName: 'Y'}" :readonly="isFillingStage" :disabled="isFillingStage"></renins-userselector>
                </div>
                <div class="col-6">
                    <div class="block-row block-desc">
                        Функциональный руководитель
                    </div>
                    <renins-userselector ref="func" v-model="formData.step1.funcManager" caption="Введите ФИО" class="w-100"
                        single="true" :options="{all:'Y', showFullName: 'Y'}" :readonly="isFillingStage" :disabled="isFillingStage"></renins-userselector>
                </div>
            </div>
            <div class="block-row row">
                <div class="col-6">
                    <div class="block-row block-desc">
                        Вышестоящий административный руководитель
                    </div>
                    <renins-userselector ref="adm" v-model="formData.step1.headAdmManager" caption="Введите ФИО" class="w-100"
                        single="true" :options="{all:'Y', showFullName: 'Y'}" :readonly="isFillingStage" :disabled="isFillingStage"></renins-userselector>
                </div>
                <div class="col-6" v-for="(approver, index) in formData.step1.addApprovers">
                    <div class="block-row block-desc">
                        Дополнительное согласование
                    </div>
                    <div class="add-approvers">
                        <renins-userselector v-model="formData.step1.addApprovers[index]" caption="Введите ФИО"
                            :single="true" :options="{all:'Y', showFullName: 'Y'}" :readonly="isFillingStage" :disabled="isFillingStage"></renins-userselector>
                        <renins-button v-if="!isFillingStage" class="secondary lg" style="width:56px; padding: 0" @click="removeApprover(index)">
                            <renins-icon class="trash" style="background-color: #1E222E"></renins-icon>
                        </renins-button>
                    </div>
                </div>
                <div class="col-6" v-if="!isFillingStage && (formData.step1.addApprovers.length < 3)">
                    <div class="block-row block-desc" v-if="formData.step1.addApprovers.length !== 1">&nbsp;</div>
                    <renins-button class="secondary lg" style="width:56px; padding: 0" @click="addApprover()">
                        <renins-icon class="plus" style="background-color: #1E222E"></renins-icon>
                    </renins-button>
                </div>
            </div>
        </div>

        <div class="block">
            <div class="panel-header">
                Выбор наблюдателей
            </div>
        </div>
        <div class="block">
            <div class="block-row">
                Добавьте наблюдателей, если требуется. Они не будут участвовать в процессе согласования,
                но смогут видеть все этапы согласования профиля и все блоки, кроме раздела C&B
            </div>
        </div>
        <div class="block">
            <div class="block-row row">
                <div class="col-6">
                    <renins-text-input v-model="formData.step1.managerExcoFio" caption="Менеджер Exco"
                        :readonly="true" :disabled="true"></renins-text-input>
                </div>
                <div class="col-6">
                    <renins-text-input v-model="formData.step1.managerLineFio" caption="Менеджер Line"
                        :readonly="true" :disabled="true"></renins-text-input>
                </div>
            </div>
        </div>
        <div class="block">
            <div class="block-row block-desc">
                Дополнительный наблюдатель
            </div>
            <div class="block-row row">
                <div class="col-6" v-for="(observer, index) in formData.step1.addObservers">
                    <div class="add-approvers">
                        <renins-userselector v-model="formData.step1.addObservers[index]" caption="Введите ФИО"
                            :single="true" :options="{all:'Y', showFullName: 'Y'}" :readonly="isFillingStage" :disabled="isFillingStage"></renins-userselector>
                        <renins-button v-if="!isFillingStage" class="secondary lg" style="width:56px; padding: 0" @click="removeObserver(index)">
                            <renins-icon class="trash" style="background-color: #1E222E"></renins-icon>
                        </renins-button>
                    </div>
                </div>
                <div class="col-6" v-if="!isFillingStage">
                    <renins-button class="secondary lg" style="width:56px; padding: 0" @click="addObserver()">
                        <renins-icon class="plus" style="background-color: #1E222E"></renins-icon>
                    </renins-button>
                </div>
            </div>
        </div>

        <div class="block">
            <div class="panel-header">
                Заполнение профиля
            </div>
        </div>
        <div class="block">
            <div class="block-row block-desc">
                Ответственный за заполнение профиля
            </div>
            <div class="block-row row">
                <div class="col-6">
                    <renins-userselector v-model="formData.step1.delegate" caption="Введите ФИО" class="w-100"
                        single="true" :options="{showFullName: 'Y'}" :readonly="isFillingStage" :disabled="isFillingStage">
                    </renins-userselector>
                </div>
                <div class="col-6">
                    <renins-button class="primary lg" style="margin-right: 16px;" @click="saveAndDelegate();" v-if="allowedDelegateFilling"
                        :disabled="!formData.step1.delegate || isSaving || isProcessing">
                        Отправить
                    </renins-button>
                </div>
            </div>
        </div>
    </template>

    <div class="block" v-if="stepHasErrors(1)">
        <renins-quote class="errored">На форме присутствуют незаполненные поля</renins-quote>
    </div>

    <div v-if="statusId !== 'trash'" class="block">
        <renins-button class="secondary lg" style="width: 210px; margin-right: 16px;" @click="saveAndRedirect()" :loading="isSaving" :disabled="isAutoSaving">
            {{ !isAutoSaving ? 'Сохранить и выйти' : 'Автосохранение...' }}
        </renins-button>
        <renins-button class="primary lg" style="margin-right: 16px;" @click="nextStep()" :disabled="stepHasErrors(1)">
            Продолжить
        </renins-button>
        <renins-button v-if="isOD && stageId && (stageId !== 'create')" class="secondary lg" style="margin-right: 16px;"
            @click="isShowRevokeModal = true; revoke_comment = '';" :loading="isRevoking" :disabled="stepHasErrors(1)">
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
