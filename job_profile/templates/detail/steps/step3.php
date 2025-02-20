<template v-if="currentStep === 3">

    <div class="block panel" :class="{ error: false }" v-if="modelRole[ 1 ] != 2">
        <renins-button class="secondary xs" style="float: right" @click="isShowEditModal[ currentStep ] = true"
            v-if="modelRole[ 1 ]">
            Изменить
        </renins-button>
        <div class="panel-row panel-header">Цели</div>

        <div class="panel-row">
            <div class="block-desc">
                <renins-badge-label class="active">Цели подразделения</renins-badge-label>
            </div>
        </div>
        <div class="panel-row goals-list">
            <div class="col-12" v-for="(goal, index) in formData.departmentGoals">
                <span class="dot"></span>{{ goal }}
            </div>
        </div>

        <div class="panel-row">
            <div class="block-desc">
                <renins-badge-label class="active">Цели должности</renins-badge-label>
            </div>
        </div>
        <div class="panel-row goals-list">
            <div class="col-12" v-for="(goal, index) in formData.positionGoals">
                <span class="dot"></span>{{ goal }}
            </div>
        </div>

    </div>
    <div class="error_caption_block">В блоке имеются ошибки. Измените данные в блоке.</div>

    <renins-form-modal v-show="isShowEditModal[ currentStep ]" @close="close(currentStep)" :close-button="false">
        <template #head>Цели подразделения </template>
        <template #body>

            <div class="block">
                <div class="block-row">
                    Укажите 2-3 основные цели подразделения, к которому относится данная должность.
                    Эти цели будут включены в должностную инструкцию по данной позиции.
                </div>
                <div class="block-row goals-list" v-for="(goal, index) in formDataEdit.departmentGoals">
                    <div>
                        <renins-textarea v-model="formDataEdit.departmentGoals[index]" :placeholder="'Цель ' + (index + 1)"
                            row="3" class="w-100" :error="index === 0 && errors[3].departmentGoals"></renins-textarea>
                    </div>
                    <div style="width:56px;">
                        <renins-button class="secondary lg" style="width:56px; padding: 0" v-if="index === formDataEdit.departmentGoals.length - 1" @click="addDepartmentGoal()">
                            <renins-icon class="plus" style="background-color: #230446"></renins-icon>
                        </renins-button>
                    </div>
                </div>
            </div>

            <div class="panel-header" style="margin-top: 24px;">Цели должности</div>
            <div class="block">
                <div class="block-row">
                    Укажите непосредственные цели самой должности, ее предназначение, для чего создана/создается должность
                </div>
                <div class="block-row goals-list" v-for="(goal, index) in formDataEdit.positionGoals">
                    <div>
                        <renins-textarea v-model="formDataEdit.positionGoals[index]" :placeholder="'Цель ' + (index + 1)"
                            row="3" class="w-100" :error="index === 0 && errors[3].positionGoals"></renins-textarea>
                    </div>
                    <div style="width:56px;">
                        <renins-button class="secondary lg" style="width:56px; padding: 0" v-if="index === formDataEdit.positionGoals.length - 1" @click="addPositionGoal()">
                            <renins-icon class="plus" style="background-color: #230446"></renins-icon>
                        </renins-button>
                    </div>
                </div>
            </div>

        </template>
        <template #footer>
            <div class="block" v-if="stepHasErrors(3)">
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