<div class="panel" v-if="currentStep === 4">
	<div class="panel-header">Должностные обязанности и результаты деятельности</div>
    <div class="block">
        <div class="block-row">
            <div class="block-desc">На какой период устанавливается горизонт планирования для должности?</div>
            Можно указать несколько вариантов, если планы работ сочетают различные периоды
        </div>
        <div class="r-web-caption r-mb-5 radio_error" v-if="errors.isShortTerm || errors.isMediumTerm || errors.isLongTerm">
            Не выбран ответ
        </div>
        <div class="block-row" style="display: flex; gap: 32px; row-gap: 16px; flex-wrap: wrap">
            <div>
                <renins-checkbox v-model="formData.step4.isShortTerm" label="Краткосрочный" :error="errors.isShortTerm"></renins-checkbox>
                <div style="margin-top: 4px; margin-left: 28px;">месяц, квартал, полугодие</div>
            </div>
            <div>
                <renins-checkbox v-model="formData.step4.isMediumTerm" label="Среднесрочный" :error="errors.isMediumTerm"></renins-checkbox>
                <div style="margin-top: 4px; margin-left: 28px;">1–2 года</div>
            </div>
            <div>
                <renins-checkbox v-model="formData.step4.isLongTerm" label="Долгосрочный" :error="errors.isLongTerm"></renins-checkbox>
                <div style="margin-top: 4px; margin-left: 28px;">3–5 лет</div>
            </div>
        </div>
    </div>
    <div class="block">
        <div class="block-row">
            <div class="block-desc">
                <renins-badge-label class="active">Основные обязанности</renins-badge-label>
            </div>
            Перечислите основные должностные обязанности и желаемый результат по каждой из них. Используйте глаголы действия: продает, привлекает, организует, анализирует,  разрабатывает, выносит предложения, принимает решения, обеспечивает, учитывает, подготавливает отчетность, контролирует и т.п. <span style="color: #FF971E">Просьба не указывать здесь управленческие обязанности.</span>
        </div>
        <renins-dnd-board v-bind:value="dragItems" @drop-event="handleDropEvent" :error="errors.mainDuties">

        </renins-dnd-board>
        <div>
            <renins-button class="secondary lg" style="width:56px; padding: 0" @click="addMainDuty()">
                <renins-icon class="plus" style="background-color: #230446"></renins-icon>
            </renins-button>
        </div>
    </div>
    <div class="block">
        <div class="block-row">
            <div class="block-desc">
                <renins-badge-label class="active">Дополнительные обязанности</renins-badge-label>
            </div>
            Укажите дополнительные обязанности — функции в рамках временных проектов, рабочих групп и желаемый результат
        </div>
        <renins-dnd-board v-bind:value="dragItemsAdd" @drop-event="handleDropEventAdd">

        </renins-dnd-board>
        <div>
            <renins-button class="secondary lg" style="width:56px; padding: 0" @click="addAdditionalDuty()">
                <renins-icon class="plus" style="background-color: #230446"></renins-icon>
            </renins-button>
        </div>
    </div>

    <div class="block" v-if="stepHasErrors(4)">
        <renins-quote class="errored">На форме присутствуют незаполненные поля</renins-quote>
    </div>

    <div v-if="statusId !== 'trash'" class="block">
        <renins-button class="secondary lg" style="width: 210px; margin-right: 16px;" @click="saveAndRedirect()" :loading="isSaving" :disabled="isAutoSaving">
            {{ !isAutoSaving ? 'Сохранить и выйти' : 'Автосохранение...' }}
        </renins-button>
        <renins-button class="primary lg" style="margin-right: 16px;" @click="nextStep()" :disabled="stepHasErrors(4)">
            Продолжить
        </renins-button>
        <renins-button v-if="isOD && stageId && (stageId !== 'create')" class="secondary lg" style="margin-right: 16px;"
            @click="isShowRevokeModal = true; revoke_comment = '';" :loading="isRevoking" :disabled="stepHasErrors(4)">
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
