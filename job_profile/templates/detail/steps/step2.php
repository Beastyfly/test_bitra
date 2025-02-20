<template v-if="currentStep === 2">

    <div class="block panel" :class="{ error: false }" v-if="modelRole[ 1 ] != 2">
        <renins-button class="secondary xs" style="float: right" @click="isShowEditModal[ currentStep ] = true"
            v-if="modelRole[ 1 ]">
            Изменить
        </renins-button>

        <template v-if="formData.isManager || formData.isItinerantWork || formData.isShiftSchedule">
            <div class="panel-row panel-header">Параметры должности</div>

            <div class="panel-row pads-row">
                <div v-if="formData.isManager">
                    <renins-pad-icon class="xs secondary"><renins-icon class="star color-brand-purple sm"></renins-icon></renins-pad-icon>
                    Руководитель
                </div>
                <div v-if="formData.isItinerantWork">
                    <renins-pad-icon class="xs secondary"><renins-icon class="route color-brand-purple sm"></renins-icon></renins-pad-icon>
                    Разъездной характер работы ({{ formData.fieldPercent }}%)
                </div>
                <div v-if="formData.isShiftSchedule">
                    <renins-pad-icon class="xs secondary"><renins-icon class="calendar color-brand-purple sm"></renins-icon></renins-pad-icon>
                    Сменный график
                </div>
            </div>
        </template>


        <div class="panel-row panel-header">Подчиненные</div>

        <div class="panel-row">
            <renins-badge-label class="active">Прямые подчиненные по орг. структуре (административные)</renins-badge-label>
        </div>
        <div class="panel-row row">
            <div class="col-3">
                <div class="param-head">Прямые подчиненные</div>
                {{ formData.subordinatesCount }} чел.
            </div>
            <div class="col-3">
                <div class="param-head">Подчиненные на всех уровнях</div>
                {{ formData.allSubordinatesCount }} чел.
            </div>
        </div>
        <div class="panel-row" v-if="formData.subordinatesComment">
            <div class="param-head">Прямые подчиненные, должности и подразделения</div>
            {{ formData.subordinatesComment }}
        </div>

        <div class="panel-row">
            <renins-badge-label class="active">Другие подчиненные</renins-badge-label>
        </div>
        <div class="panel-row row">
            <div class="col-3">
                <div class="param-head">Функциональные</div>
                {{ formData.funcSubordinatesCount }} чел.
            </div>
            <div class="col-3">
                <div class="param-head">Проектные</div>
                {{ formData.projectSubordinatesCount }} чел.
            </div>
            <div class="col-3">
                <div class="param-head">Внешние (аутсорсинг)</div>
                {{ formData.outsourceSubordinatesCount }} чел.
            </div>
        </div>
        <div class="panel-row" v-if="formData.outsourceComment">
            <div class="param-head">Не прямые подчиненные, роли и подрядчики</div>
            {{ formData.outsourceComment }}
        </div>


        <div class="panel-row panel-header">Формат работы</div>

        <div class="panel-row pads-row">
            <div>
                <renins-pad-icon class="xs secondary"><renins-icon class="monitor color-brand-purple sm"></renins-icon></renins-pad-icon>
                {{ formData.schedule }} <template v-if="formData.schedule === 'Гибридный'">({{ formData.distantPercent }}% удаленной работы)</template>
            </div>
        </div>
        <div class="panel-row" v-if="formData.diffModeComment">
            <div class="param-head">Причина указания формата работы отличного от рекомендованного</div>
            {{ formData.diffModeComment }}
        </div>

    </div>
    <div class="error_caption_block">В блоке имеются ошибки. Измените данные в блоке.</div>

    <renins-form-modal v-show="isShowEditModal[ currentStep ]" @close="close(currentStep)" :close-button="false">
        <template #head>Параметры</template>
        <template #body>
        <div class="high-margin">

            <div class="block">
                <div class="block-row">
                    Поставьте галочку напротив пунктов, которые относятся к должности. Если ни один из пунктов не подходит,
                    оставьте все варианты без отметки.
                </div>
                <div class="block-row">
                    <renins-checkbox v-model="formDataEdit.isManager" label="Руководитель"></renins-checkbox>
                </div>
                <div class="block-row">
                    <renins-checkbox v-model="formDataEdit.isItinerantWork" label="Разъездной характер работы"></renins-checkbox>
                </div>
                <div class="block-row row" v-if="formDataEdit.isItinerantWork">
                    <div class="col-6">
                        <renins-text-input v-model="formDataEdit.fieldPercent" caption="Процент полевой работы, %" type="number"
                            min="0" max="100" class="w-100"></renins-text-input>
                    </div>
                </div>
                <div class="block-row">
                    <renins-checkbox v-model="formDataEdit.isShiftSchedule" label="Сменный график"></renins-checkbox>
                </div>
            </div>


            <div class="panel-header">Подчиненные</div>

            <div class="block">
                <div class="block-row" style="margin-top: -8px">
                    Отметьте подчиненных, относящихся к данной должности. Если ни один из вариантов не подходит,
                    оставьте все пункты без отметки.
                </div>
                <div class="block-row">
                    <renins-checkbox v-model="formDataEdit.hasSubs" label="Подчиненные по орг. структуре (административные подчиненные)" :error="errors[2].hasSubs"></renins-checkbox>
                </div>

                <template v-if="formDataEdit.hasSubs">
                    <div class="block-row row">
                        <div class="col-6">
                            <renins-text-input v-model="formDataEdit.subordinatesCount" caption="Кол-во прямых подчиненных" type="number" class="w-100" :error="errors[2].subordinatesCount"></renins-text-input>
                        </div>
                        <div class="col-6">
                            <renins-text-input v-model="formDataEdit.allSubordinatesCount" caption="Кол-во подчиненных на всех уровнях" type="number" class="w-100" :error="errors[2].allSubordinatesCount"></renins-text-input>
                        </div>
                    </div>
                    <div class="block-row row">
                        <div class="block-desc">
                            Укажите должности и подразделения, подчиняющиеся данной должности напрямую по организационной структуре
                        </div>
                        <renins-textarea v-model="formDataEdit.subordinatesComment" placeholder="Поле для ввода информации" rows="3" class="resize-vertical" :error="errors[2].subordinatesComment"></renins-textarea>
                    </div>
                </template>

                <div class="block-row">
                    <renins-checkbox v-model="formDataEdit.hasFuncSubs" label="Функциональные подчиненные"></renins-checkbox>
                </div>
                <div class="block-row row" v-if="formDataEdit.hasFuncSubs">
                    <div class="col-6">
                        <renins-text-input v-model="formDataEdit.funcSubordinatesCount" caption="Кол-во, чел." placeholder="" type="number" class="w-100"></renins-text-input>
                    </div>
                </div>

                <div class="block-row">
                    <renins-checkbox v-model="formDataEdit.hasProjectSubs" label="Проектные подчиненные"></renins-checkbox>
                </div>
                <div class="block-row row" v-if="formDataEdit.hasProjectSubs">
                    <div class="col-6">
                        <renins-text-input v-model="formDataEdit.projectSubordinatesCount" caption="Кол-во, чел." placeholder="" type="number" class="w-100"></renins-text-input>
                    </div>
                </div>

                <div class="block-row">
                    <renins-checkbox v-model="formDataEdit.hasOutsourceSubs" label="Внешние подчиненные (аутсорс)"></renins-checkbox>
                </div>
                <div class="block-row row" v-if="formDataEdit.hasOutsourceSubs">
                    <div class="col-6">
                        <renins-text-input v-model="formDataEdit.outsourceSubordinatesCount" caption="Кол-во, чел." placeholder="" type="number" class="w-100"></renins-text-input>
                    </div>
                </div>
                <div class="block-row row" v-if="formDataEdit.hasFuncSubs || formDataEdit.hasProjectSubs || formDataEdit.hasOutsourceSubs">
                    <div class="block-desc">
                        Опишите роли и подрядчиков, подчиняющихся данной должности не напрямую
                    </div>
                    <renins-textarea v-model="formDataEdit.outsourceComment" placeholder="Поле для ввода информации" rows="3" class="resize-vertical"></renins-textarea>
                </div>
            </div>


            <div class="panel-header">Формат работы</div>

            <div class="block">
                <div class="block-row" style="margin-top: -8px">
                    Выберите формат вручную или воспользуйтесь калькулятором для определения рекомендуемого формата работы.
                </div>

                <div class="block-row">
                    <renins-switch v-model="formDataEdit.calculator" label="Калькулятор"></renins-switch>
                </div>
                <div class="block-border" v-if="formDataEdit.calculator">
                    <div class="block-row row">
                        <div class="block-desc">
                            Требуется взаимодействие с внешними клиентами (контрагенты, гос органы, партнеры)?
                        </div>
                        <div class="block-desc">
                            <renins-radio v-model="formDataEdit.relationOutClients" val="Да, нельзя делать удаленно">Да, нельзя делать удаленно</renins-radio>
                        </div>
                        <div class="block-desc">
                            <renins-radio v-model="formDataEdit.relationOutClients" val="Да, частично можно делать удаленно">Да, частично можно делать удаленно</renins-radio>
                        </div>
                        <div class="block-desc">
                            <renins-radio v-model="formDataEdit.relationOutClients" val="Да, можно делать полностью удаленно">Да, можно делать полностью удаленно</renins-radio>
                        </div>
                        <div class="block-desc">
                            <renins-radio v-model="formDataEdit.relationOutClients" val="Нет">Нет</renins-radio>
                        </div>
                    </div>

                    <div class="block-row row">
                        <div class="block-desc">
                            Необходимо взаимодействие с внутренними клиентами/коллегами?
                        </div>
                        <div class="block-desc">
                            <renins-radio v-model="formDataEdit.relationInClients" val="Да, нельзя делать удаленно">Да, нельзя делать удаленно</renins-radio>
                        </div>
                        <div class="block-desc">
                            <renins-radio v-model="formDataEdit.relationInClients" val="Да, частично можно делать удаленно">Да, частично можно делать удаленно</renins-radio>
                        </div>
                        <div class="block-desc">
                            <renins-radio v-model="formDataEdit.relationInClients" val="Да, можно делать полностью удаленно">Да, можно делать полностью удаленно</renins-radio>
                        </div>
                        <div class="block-desc" style="margin-bottom:0">
                            <renins-radio v-model="formDataEdit.relationInClients" val="Нет">Нет</renins-radio>
                        </div>
                    </div>

                    <div class="block-row row">
                        <div class="block-desc">
                            Требуется физическое обслуживания оборудования и/или материалов?
                        </div>
                        <div class="block-desc">
                            <renins-radio v-model="formDataEdit.physicService" val="Да, нельзя делать удаленно">Да, нельзя делать удаленно</renins-radio>
                        </div>
                        <div class="block-desc">
                            <renins-radio v-model="formDataEdit.physicService" val="Да, частично можно делать удаленно">Да, частично можно делать удаленно</renins-radio>
                        </div>
                        <div class="block-desc">
                            <renins-radio v-model="formDataEdit.physicService" val="Да, можно делать полностью удаленно">Да, можно делать полностью удаленно</renins-radio>
                        </div>
                        <div class="block-desc" style="margin-bottom:0">
                            <renins-radio v-model="formDataEdit.physicService" val="Нет">Нет</renins-radio>
                        </div>
                    </div>

                    <div class="block-row row">
                        <div class="block-desc">
                            Сложно привлечь компетенции (сложный подбор, мало кандидатов с подходящей компетенцией)
                        </div>
                        <div class="block-desc">
                            <renins-radio v-model="formDataEdit.difficultAttractComps" val="Да">Да</renins-radio>
                        </div>
                        <div class="block-desc" style="margin-bottom:0">
                            <renins-radio v-model="formDataEdit.difficultAttractComps" val="Нет">Нет</renins-radio>
                        </div>
                    </div>

                    <div class="block-row row">
                        <div class="block-desc">
                            Руководитель (да/нет) и режим работы подчиненных
                        </div>
                        <div class="block-desc">
                            <renins-radio v-model="formDataEdit.workModeSubs" val="Да, все подчиненные с комбинированным и дистанционным режимом">Да, все подчиненные с комбинированным и дистанционным режимом</renins-radio>
                        </div>
                        <div class="block-desc">
                            <renins-radio v-model="formDataEdit.workModeSubs" val="Да, есть подчиненные со стандартным режимом">Да, есть подчиненные со стандартным режимом</renins-radio>
                        </div>
                        <div class="block-desc" style="margin-bottom:0">
                            <renins-radio v-model="formDataEdit.workModeSubs" val="Нет">Нет</renins-radio>
                        </div>
                    </div>

                    <div class="block-row row">
                        <div class="col-6">
                            <renins-text-input v-model="recommendFormat" caption="Рекомендуемый формат"
                                readonly type="text" class="w-100"></renins-text-input>
                        </div>
                        <div class="col-3">
                            <renins-button class="primary lg" @click="formDataEdit.schedule = recommendFormat"
                                :disabled="recommendFormat == ''">
                                Использовать
                            </renins-button>
                        </div>
                    </div>
                </div>

                <div class="r-web-caption r-mb-5 radio_error" v-if="errors[2].schedule">
                    Не выбран ответ
                </div>
                <div class="block-row row">
                    <div class="col-4">
                        <renins-radio v-model="formDataEdit.schedule" val="Офисный" :error="errors[2].schedule">Офисный</renins-radio>
                        <div style="margin-top: 4px; padding-left: 28px;">
                            0% удаленной работы
                        </div>
                    </div>
                    <div class="col-4">
                        <renins-radio v-model="formDataEdit.schedule" val="Гибридный" :error="errors[2].schedule">Гибридный</renins-radio>
                        <div style="margin-top: 4px; padding-left: 28px;">
                            20 – 80% удаленной работы
                        </div>
                    </div>
                    <div class="col-4">
                        <renins-radio v-model="formDataEdit.schedule" val="Удаленный" :error="errors[2].schedule">Удаленный</renins-radio>
                        <div style="margin-top: 4px; padding-left: 28px;">
                            80 – 100% удаленной работы
                        </div>
                    </div>
                </div>

                <div class="block-row row" v-if="formDataEdit.schedule === 'Гибридный'">
                    <div class="col-6">
                        <renins-text-input v-model="formDataEdit.distantPercent" caption="Процент удаленной работы, %" type="number"
                            min="0" max="100" class="w-100" :error="errors[2].distantPercent"></renins-text-input>
                    </div>
                </div>

                <template v-if="formDataEdit.calculator && recommendFormat && (formDataEdit.schedule != recommendFormat)">
                    <div class="block-row">
                        <renins-quote class="warning radius">Если выбранный формат не совпадает с рекомендованным, укажите причину выбора</renins-quote>
                    </div>
                    <div class="block-row row">
                        <renins-textarea v-model="formDataEdit.diffModeComment" placeholder="Комментарий" rows="3" class="resize-vertical"></renins-textarea>
                    </div>
                </template>
            </div>

        </div>
        </template>
        <template #footer>
            <div class="block" v-if="stepHasErrors(2)">
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
