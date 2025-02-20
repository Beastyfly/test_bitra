<template v-if="currentStep === 4">

    <div class="block panel" :class="{ error: false }" v-if="modelRole[ 1 ] != 2">
        <renins-button class="secondary xs" style="float: right" @click="isShowEditModal[ currentStep ] = true"
            v-if="modelRole[ 1 ]">
            Изменить
        </renins-button>
        <div class="panel-row panel-header">Должностные обязанности и результаты деятельности</div>

            <template v-if="planning">
                <div class="panel-row">
                    <div class="param-head">Горизонт планирования</div>
                    {{ planning() }}
                </div>
            </template>

            <div class="panel-row">
                <div class="block-desc">
                    <renins-badge-label class="active">Основные обязанности</renins-badge-label>
                </div>
            </div>
            <div class="panel-row row" v-for="(goal, index) in formData.mainDuties">
                <div class="col-6">
                    <div class="param-head">Обязанность {{ index + 1 }}</div>
                    {{ goal.duty }}
                </div>
                <div class="col-6">
                    <div class="param-head">Результат {{ index + 1 }}</div>
                    {{ goal.result }}
                </div>
            </div>

            <div class="panel-row">
                <div class="block-desc">
                    <renins-badge-label class="active">Дополнительные обязанности</renins-badge-label>
                </div>
            </div>
            <div class="panel-row row" v-for="(goal, index) in formData.addDuties">
                <div class="col-6">
                    <div class="param-head">Обязанность {{ index + 1 }}</div>
                    {{ goal.duty }}
                </div>
                <div class="col-6">
                    <div class="param-head">Результат {{ index + 1 }}</div>
                    {{ goal.result }}
                </div>
            </div>

        </div>
        <div class="error_caption_block">В блоке имеются ошибки. Измените данные в блоке.</div>

        <renins-form-modal v-show="isShowEditModal[ currentStep ]" @close="close(currentStep)" :close-button="false">
            <template #head>Должностные обязанности и результаты деятельности</template>
            <template #body>

                <div class="block">
                    <div class="block-row row">
                        <div class="block-desc">На какой период устанавливается горизонт планирования для должности?
                        </div>
                        <span>Можно указать несколько вариантов, если планы работ сочетают различные периоды</span>
                    </div>
                    <div class="r-web-caption r-mb-5 radio_error" v-if="errors[4].isShortTerm">
                        Не выбран ответ
                    </div>
                    <div class="block-row" style="display: flex; gap: 32px; row-gap: 16px; flex-wrap: wrap">
                        <div>
                            <renins-checkbox v-model="formDataEdit.isShortTerm" label="Краткосрочный"
                                             :error="errors[4].isShortTerm"></renins-checkbox>
                            <div class="param-head" style="margin-top: 4px; margin-left: 28px;">месяц, квартал,
                                полугодие
                            </div>
                        </div>
                        <div>
                            <renins-checkbox v-model="formDataEdit.isMediumTerm" label="Среднесрочный"
                                             :error="errors[4].isMediumTerm"></renins-checkbox>
                            <div class="param-head" style="margin-top: 4px; margin-left: 28px;">1–2 года</div>
                        </div>
                        <div>
                            <renins-checkbox v-model="formDataEdit.isLongTerm" label="Долгосрочный"
                                             :error="errors[4].isLongTerm"></renins-checkbox>
                            <div class="param-head" style="margin-top: 4px; margin-left: 28px;">3–5 лет</div>
                        </div>
                    </div>
                </div>

                <div class="block">
                    <div class="block-row row">
                        <div class="block-desc">
                            <renins-badge-label class="active">Основные обязанности</renins-badge-label>
                        </div>
                        <span>
                        Перечислите основные должностные обязанности и желаемый результат по каждой из них.
                        Используйте глаголы действия: продает, привлекает, организует, анализирует,  разрабатывает,
                        выносит предложения, принимает решения, обеспечивает, учитывает, подготавливает отчетность,
                        контролирует и т.п. <span style="color: #FF971E">Просьба не указывать здесь управленческие обязанности.</span>
                    </span>
                    </div>
                    <renins-dnd-board v-bind:value="dragItems" @drop-event="handleDropEvent">

                    </renins-dnd-board>
                    <div>
                        <renins-button class="secondary lg" style="width:56px; padding: 0" @click="addMainDuty()">
                            <renins-icon class="plus" style="background-color: #230446"></renins-icon>
                        </renins-button>
                    </div>
                </div>

                <div class="block">
                    <div class="block-row row">
                        <div class="block-desc">
                            <renins-badge-label class="active">Дополнительные обязанности</renins-badge-label>
                        </div>
                        <span>
                        Укажите дополнительные обязанности — функции в рамках временных проектов, рабочих групп и желаемый результат
                    </span>
                    </div>
                    <renins-dnd-board v-bind:value="dragItemsAdd" @drop-event="handleDropEventAdd">

                    </renins-dnd-board>
                    <div>
                        <renins-button class="secondary lg" style="width:56px; padding: 0" @click="addAdditionalDuty()">
                            <renins-icon class="plus" style="background-color: #230446"></renins-icon>
                        </renins-button>
                    </div>
                </div>

            </template>
            <template #footer>
                <div class="block" v-if="stepHasErrors(4)">
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
