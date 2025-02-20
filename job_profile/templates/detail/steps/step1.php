<template v-if="currentStep === 1">

    <div class="block panel" :class="{ error: false }" v-if="modelRole[ 0 ] != 2">
        <renins-button class="secondary xs" style="float: right"
            @click="isShowEditModal[ currentStep ] = true" v-if="modelRole[ 0 ]">
            Изменить
        </renins-button>
        <div class="panel-row panel-header">Должность</div>

        <div class="panel-row">
            <div class="param-head">Название должности</div>
            {{ formData.positionName }}
        </div>
        <div class="panel-row row">
            <div class="col-4">
                <div class="param-head">Кост-центр</div>
                {{ formData.costCenter }}
            </div>
            <div class="col-4">
                <div class="param-head">Функция 1</div>
                {{ formData.func1Name }}
            </div>
            <div class="col-4">
                <div class="param-head">Функция 2</div>
                {{ formData.func2Name }}
            </div>
        </div>

        <div class="panel-row">
            <div class="param-head">Подразделение</div>
            {{ formData.department }}
        </div>

        <div class="panel-row row">
            <div class="col-6">
                <div class="param-head">Branch</div>
                {{ formData.branch }}
            </div>
            <div class="col-6">
                <div class="param-head">Location</div>
                {{ formData.location }}
            </div>
        </div>

        <div class="panel-row panel-header">Наблюдатели</div>

        <div class="panel-row row">
            <div class="col-6">
                <div class="param-head">Менеджер Exco</div>
                {{ formData.managerExcoFio }}
            </div>
            <div class="col-6">
                <div class="param-head">Менеджер Line</div>
                {{ formData.managerLineFio }}
            </div>
        </div>

        <div class="panel-row row">
            <div class="col-6" v-for="(observer, index) in formData.addObserversFio">
                <div class="param-head">
                    <template v-if="index === 0">Дополнительный наблюдатель</template>
                    <template v-else> </template>
                </div>
                {{ formData.addObserversFio[index] }}
            </div>
        </div>

    </div>
    <div class="error_caption_block">В блоке имеются ошибки. Измените данные в блоке.</div>

    <renins-form-modal v-show="isShowEditModal[ currentStep ]" @close="close(currentStep)" :close-button="false">
        <template #head>Должность</template>
        <template #body>

            <div class="block">
                <div class="block-row">
                    Укажите полное наименование должности, согласно штатному расписанию,
                    для вновь создаваемой должности — ее планируемое наименование
                </div>
                <div class="block-row">
                    <renins-text-input v-model="formDataEdit.positionName" caption="Название должности"
                        class="w-100" tooltip="true" :error="errors[1].positionName"></renins-text-input>
                </div>
                <div class="block-row row">
                    <div class="col-4">
                        <renins-select placeholder="Кост-центр" v-model="formDataEdit.costCenter" :error="errors[1].costCenter" v-bind:items="costCenters"
                            class="w-100"></renins-select>
                    </div>
                    <div class="col-4">
                        <renins-text-input caption="Функция 1" v-model="formDataEdit.func1Name"
                            class="w-100" :readonly="true" :disabled="true" :tooltip="true"></renins-text-input>
                    </div>
                    <div class="col-4">
                        <renins-text-input caption="Функция 2" v-model="formDataEdit.func2Name"
                            class="w-100" :readonly="true" :disabled="true" :tooltip="true"></renins-text-input>
                    </div>
                </div>
            </div>

            <div class="block">
                <div class="block-row">
                    Укажите всю цепочку подразделений, в которые входит должность по организационной структуре,
                    без использования аббревиатур — все уровни
                </div>
                <div class="block-row row">
                    <div class="col-12">
                        <renins-select placeholder="Подразделение" v-model="formDataEdit.department"
                            v-bind:items="departments" :tooltip="true" :input="true"></renins-select>
                    </div>
                </div>
                <div class="block-row row">
                    <div class="col-6">
                        <renins-select placeholder="Branch" v-model="formDataEdit.branch" :error="errors[1].branch"
                            v-bind:items="branches" tooltip="true"></renins-select>
                    </div>
                    <div class="col-6">
                        <renins-select placeholder="Location" v-model="formDataEdit.location"
                            v-bind:items="locations" tooltip="true"></renins-select>
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
                        <renins-text-input v-model="formDataEdit.managerExcoFio" caption="Менеджер Exco"
                            :readonly="true" :disabled="true"></renins-text-input>
                    </div>
                    <div class="col-6">
                        <renins-text-input v-model="formDataEdit.managerLineFio" caption="Менеджер Line"
                            :readonly="true" :disabled="true"></renins-text-input>
                    </div>
                </div>
            </div>
            <div class="block">
                <div class="block-row block-desc">
                    Дополнительный наблюдатель
                </div>
                <div class="block-row row" v-for="(observer, index) in formDataEdit.addObservers">
                    <div class="add-approvers">
                        <renins-userselector v-model="formDataEdit.addObservers[index]" caption="Введите ФИО"
                            :single="true" :options="{all:'Y', showFullName: 'Y'}"></renins-userselector>
                        <renins-button class="secondary lg" style="min-width:56px; padding: 0; margin-left: 16px;" @click="removeObserver(index)">
                            <renins-icon class="trash" style="background-color: #1E222E"></renins-icon>
                        </renins-button>
                        <renins-button v-if="formDataEdit.addObservers.length == index + 1" class="secondary lg" style="min-width:56px; padding: 0; margin-left: 16px;" @click="addObserver()">
                            <renins-icon class="plus" style="background-color: #1E222E"></renins-icon>
                        </renins-button>
                    </div>
                </div>
                <renins-button v-if="formDataEdit.addObservers.length == 0 || !formDataEdit.addObservers" class="secondary lg" style="width:56px; padding: 0;" @click="addObserver()">
                    <renins-icon class="plus" style="background-color: #1E222E"></renins-icon>
                </renins-button>
            </div>

        </template>
        <template #footer>
            <div class="block" v-if="stepHasErrors(1)">
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
