<div class="panel" v-if="currentStep === 3">

    <div class="panel-header">Цели подразделения</div>
    <div class="block">
        <div class="block-row">
            Укажите 2-3 основные цели подразделения, к которому относится данная должность.
            Эти цели будут включены в должностную инструкцию по данной позиции.
        </div>
        <div class="block-row goals-list" v-for="(goal, index) in formData.step3.departmentGoals">
            <div>
                <renins-textarea v-model="formData.step3.departmentGoals[index]" :placeholder="'Цель ' + (index + 1)"
                    row="3" class="w-100" :error="index === 0 && errors.departmentGoals"></renins-textarea>
            </div>
            <div style="width:56px;">
                <renins-button class="secondary lg" style="width:56px; padding: 0" v-if="index === formData.step3.departmentGoals.length - 1" @click="addDepartmentGoal()">
                    <renins-icon class="plus" style="background-color: #230446"></renins-icon>
                </renins-button>
            </div>
        </div>
    </div>

    <div class="block panel-header">Цели должности</div>
    <div class="block">
        <div class="block-row">
            Укажите непосредственные цели самой должности, ее предназначение, для чего создана/создается должность
        </div>
        <div class="block-row goals-list" v-for="(goal, index) in formData.step3.positionGoals">
            <div>
                <renins-textarea v-model="formData.step3.positionGoals[index]" :placeholder="'Цель ' + (index + 1)"
                    row="3" class="w-100" :error="index === 0 && errors.positionGoals"></renins-textarea>
            </div>
            <div style="width:56px;">
                <renins-button class="secondary lg" style="width:56px; padding: 0" v-if="index === formData.step3.positionGoals.length - 1" @click="addPositionGoal()">
                    <renins-icon class="plus" style="background-color: #230446"></renins-icon>
                </renins-button>
            </div>
        </div>
    </div>

    <div class="block" v-if="stepHasErrors(3)">
        <renins-quote class="errored">На форме присутствуют незаполненные поля</renins-quote>
    </div>

    <div v-if="statusId !== 'trash'" class="block">
        <renins-button class="secondary lg" style="width: 210px; margin-right: 16px;" @click="saveAndRedirect()" :loading="isSaving" :disabled="isAutoSaving">
            {{ !isAutoSaving ? 'Сохранить и выйти' : 'Автосохранение...' }}
        </renins-button>
        <renins-button class="primary lg" style="margin-right: 16px;" @click="nextStep()" :disabled="stepHasErrors(2)">
            Продолжить
        </renins-button>
        <renins-button v-if="isOD && stageId && (stageId !== 'create')" class="secondary lg" style="margin-right: 16px;"
            @click="isShowRevokeModal = true; revoke_comment = '';" :loading="isRevoking" :disabled="stepHasErrors(3)">
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
