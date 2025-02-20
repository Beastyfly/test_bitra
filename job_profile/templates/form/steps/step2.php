<div class="panel high-margin" v-if="currentStep === 2">
    <div class="panel-header">Параметры должности</div>

    <div class="block">
        <div class="block-row" style="margin-top: -8px">
            Поставьте галочку напротив пунктов, которые относятся к должности. Если ни один из пунктов не подходит,
            оставьте все варианты без отметки.
        </div>
        <div class="block-row">
            <renins-checkbox v-model="formData.step2.isManager" label="Руководитель"></renins-checkbox>
        </div>
        <div class="block-row">
            <renins-checkbox v-model="formData.step2.isItinerantWork" label="Разъездной характер работы"></renins-checkbox>
        </div>
        <div class="block-row row" v-if="formData.step2.isItinerantWork">
            <div class="col-4">
                <renins-text-input v-model="formData.step2.fieldPercent" caption="Процент полевой работы, %" type="number"
                    min="0" max="100" class="w-100"></renins-text-input>
            </div>
        </div>
        <div class="block-row">
            <renins-checkbox v-model="formData.step2.isShiftSchedule" label="Сменный график"></renins-checkbox>
        </div>
    </div>


    <div class="panel-header" style="margin-top: 24px">Подчиненные</div>

    <div class="block">
        <div class="block-row" style="margin-top: -8px">
            Отметьте подчиненных, относящихся к данной должности. Если ни один из вариантов не подходит,
            оставьте все пункты без отметки.
        </div>
        <div class="block-row">
            <renins-checkbox v-model="formData.step2.hasSubs" label="Прямые подчиненные по орг. структуре (административные)" :error="errors.hasSubs"></renins-checkbox>
        </div>

        <template v-if="formData.step2.hasSubs">
            <div class="block-row row">
                <div class="col-6">
                    <renins-text-input v-model="formData.step2.subordinatesCount" caption="Кол-во прямых подчиненных" type="number" class="w-100" :error="errors.subordinatesCount"></renins-text-input>
                </div>
                <div class="col-6">
                    <renins-text-input v-model="formData.step2.allSubordinatesCount" caption="Кол-во подчиненных на всех уровнях" type="number" class="w-100" :error="errors.allSubordinatesCount"></renins-text-input>
                </div>
            </div>
            <div class="block-row">
                <div class="block-desc">
                    Укажите должности и подразделения, подчиняющиеся данной должности напрямую по организационной структуре
                </div>
                <renins-textarea v-model="formData.step2.subordinatesComment" placeholder="Поле для ввода информации" rows="3" class="resize-vertical" :error="errors.subordinatesComment"></renins-textarea>
            </div>
        </template>

        <div class="block-row">
            <renins-checkbox v-model="formData.step2.hasFuncSubs" label="Функциональные подчиненные"></renins-checkbox>
        </div>
        <div class="block-row row" v-if="formData.step2.hasFuncSubs">
            <div class="col-4">
                <renins-text-input v-model="formData.step2.funcSubordinatesCount" caption="Кол-во, чел." placeholder="" type="number" class="w-100"></renins-text-input>
            </div>
        </div>

        <div class="block-row">
            <renins-checkbox v-model="formData.step2.hasProjectSubs" label="Проектные подчиненные"></renins-checkbox>
        </div>
        <div class="block-row row" v-if="formData.step2.hasProjectSubs">
            <div class="col-4">
                <renins-text-input v-model="formData.step2.projectSubordinatesCount" caption="Кол-во, чел." placeholder="" type="number" class="w-100"></renins-text-input>
            </div>
        </div>

        <div class="block-row">
            <renins-checkbox v-model="formData.step2.hasOutsourceSubs" label="Внешние подчиненные (аутсорс)"></renins-checkbox>
        </div>
        <div class="block-row row" v-if="formData.step2.hasOutsourceSubs">
            <div class="col-4">
                <renins-text-input v-model="formData.step2.outsourceSubordinatesCount" caption="Кол-во, чел." placeholder="" type="number" class="w-100"></renins-text-input>
            </div>
        </div>
        <div class="block-row" v-if="formData.step2.hasFuncSubs || formData.step2.hasProjectSubs || formData.step2.hasOutsourceSubs">
            <div class="block-desc">
                Опишите роли и подрядчиков, подчиняющихся данной должности не напрямую
            </div>
            <renins-textarea v-model="formData.step2.outsourceComment" placeholder="Поле для ввода информации" rows="3" class="resize-vertical"></renins-textarea>
        </div>
    </div>


    <div class="panel-header" style="margin-top: 24px">Формат работы</div>

    <div class="block">
        <div class="block-row" style="margin-top: -8px">
            Выберите формат вручную или воспользуйтесь калькулятором для определения рекомендуемого формата работы.
        </div>

        <div class="block-row">
            <renins-switch v-model="formData.step2.calculator" label="Калькулятор"></renins-switch>
        </div>
        <div class="block-row block-border" v-if="formData.step2.calculator">
            <div class="block-row">
                <div class="block-desc">
                    Требуется взаимодействие с внешними клиентами (контрагенты, гос органы, партнеры)?
                </div>
                <div class="block-desc">
                    <renins-radio v-model="formData.step2.relationOutClients" val="Да, нельзя делать удаленно">Да, нельзя делать удаленно</renins-radio>
                </div>
                <div class="block-desc">
                    <renins-radio v-model="formData.step2.relationOutClients" val="Да, частично можно делать удаленно">Да, частично можно делать удаленно</renins-radio>
                </div>
                <div class="block-desc">
                    <renins-radio v-model="formData.step2.relationOutClients" val="Да, можно делать полностью удаленно">Да, можно делать полностью удаленно</renins-radio>
                </div>
                <div class="block-desc">
                    <renins-radio v-model="formData.step2.relationOutClients" val="Нет">Нет</renins-radio>
                </div>
            </div>

            <div class="block-row">
                <div class="block-desc">
                    Необходимо взаимодействие с внутренними клиентами/коллегами?
                </div>
                <div class="block-desc">
                    <renins-radio v-model="formData.step2.relationInClients" val="Да, нельзя делать удаленно">Да, нельзя делать удаленно</renins-radio>
                </div>
                <div class="block-desc">
                    <renins-radio v-model="formData.step2.relationInClients" val="Да, частично можно делать удаленно">Да, частично можно делать удаленно</renins-radio>
                </div>
                <div class="block-desc">
                    <renins-radio v-model="formData.step2.relationInClients" val="Да, можно делать полностью удаленно">Да, можно делать полностью удаленно</renins-radio>
                </div>
                <div class="block-desc">
                    <renins-radio v-model="formData.step2.relationInClients" val="Нет">Нет</renins-radio>
                </div>
            </div>

            <div class="block-row">
                <div class="block-desc">
                    Требуется физическое обслуживания оборудования и/или материалов?
                </div>
                <div class="block-desc">
                    <renins-radio v-model="formData.step2.physicService" val="Да, нельзя делать удаленно">Да, нельзя делать удаленно</renins-radio>
                </div>
                <div class="block-desc">
                    <renins-radio v-model="formData.step2.physicService" val="Да, частично можно делать удаленно">Да, частично можно делать удаленно</renins-radio>
                </div>
                <div class="block-desc">
                    <renins-radio v-model="formData.step2.physicService" val="Да, можно делать полностью удаленно">Да, можно делать полностью удаленно</renins-radio>
                </div>
                <div class="block-desc">
                    <renins-radio v-model="formData.step2.physicService" val="Нет">Нет</renins-radio>
                </div>
            </div>

            <div class="block-row">
                <div class="block-desc">
                    Сложно привлечь компетенции (сложный подбор, мало кандидатов с подходящей компетенцией)
                </div>
                <div class="block-desc">
                    <renins-radio v-model="formData.step2.difficultAttractComps" val="Да">Да</renins-radio>
                </div>
                <div class="block-desc">
                    <renins-radio v-model="formData.step2.difficultAttractComps" val="Нет">Нет</renins-radio>
                </div>
            </div>

            <div class="block-row">
                <div class="block-desc">
                    Руководитель (да/нет) и режим работы подчиненных
                </div>
                <div class="block-desc">
                    <renins-radio v-model="formData.step2.workModeSubs" val="Да, все подчиненные с комбинированным и дистанционным режимом">Да, все подчиненные с комбинированным и дистанционным режимом</renins-radio>
                </div>
                <div class="block-desc">
                    <renins-radio v-model="formData.step2.workModeSubs" val="Да, есть подчиненные со стандартным режимом">Да, есть подчиненные со стандартным режимом</renins-radio>
                </div>
                <div class="block-desc">
                    <renins-radio v-model="formData.step2.workModeSubs" val="Нет">Нет</renins-radio>
                </div>
            </div>

            <div class="block-row row">
                <div class="col-6">
                    <renins-text-input v-model="recommendFormat" caption="Рекомендуемый формат:" :showonly="true"
                        class="w-100"></renins-text-input>
                </div>
                <div class="col-3">
                    <renins-button class="primary lg" @click="formData.step2.schedule = recommendFormat"
                        :disabled="recommendFormat == ''">
                        Использовать
                    </renins-button>
                </div>
            </div>
        </div>

        <div class="r-web-caption r-mb-5 radio_error" v-if="errors.schedule">
            Не выбран ответ
        </div>
        <div class="block-row row" style="align-items: start;">
            <div class="col-4">
                <renins-radio v-model="formData.step2.schedule" val="Офисный" :error="errors.schedule">Офисный</renins-radio>
                <div style="margin-top: 4px; padding-left: 28px;">
                    0% удаленной работы
                </div>
            </div>
            <div class="col-4">
                <renins-radio v-model="formData.step2.schedule" val="Гибридный" :error="errors.schedule">Гибридный</renins-radio>
                <div style="margin-top: 4px; padding-left: 28px;">
                    20 – 80% удаленной работы
                </div>
            </div>
            <div class="col-4">
                <renins-radio v-model="formData.step2.schedule" val="Удаленный" :error="errors.schedule">Удаленный</renins-radio>
                <div style="margin-top: 4px; padding-left: 28px;">
                    80 – 100% удаленной работы
                </div>
            </div>
        </div>

        <div class="block-row row" v-if="formData.step2.schedule === 'Гибридный'">
            <div class="col-4">
                <renins-text-input v-model="formData.step2.distantPercent" caption="Процент удаленной работы, %" type="number"
                    min="0" max="100" class="w-100" :error="errors.distantPercent"></renins-text-input>
            </div>
        </div>

        <template v-if="formData.step2.calculator && recommendFormat && (formData.step2.schedule != recommendFormat)">
            <div class="block-row">
                <renins-quote class="warning radius">Если выбранный формат не совпадает с рекомендованным, укажите причину выбора </renins-quote>
            </div>
            <div class="block-row">
                <renins-textarea v-model="formData.step2.diffModeComment" placeholder="Комментарий" rows="3" class="resize-vertical"></renins-textarea>
            </div>
        </template>
    </div>

    <div class="block" v-if="stepHasErrors(2)">
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
            @click="isShowRevokeModal = true; revoke_comment = '';" :loading="isRevoking" :disabled="stepHasErrors(2)">
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
