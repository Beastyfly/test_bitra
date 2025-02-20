<template v-if="currentStep === 15">

    <div class="block panel" :class="{ error: false }" v-if="modelRole[ 3 ] != 2">
        <renins-button class="secondary xs" style="float: right" @click="isShowEditModal[ currentStep ] = true"
            v-if="modelRole[ 3 ]">
            Изменить
        </renins-button>
        <div class="panel-row panel-header">Информация по компенсациям и льготам</div>

        <div class="panel-row">
            <renins-badge-label class="active">Код обзора</renins-badge-label>
        </div>
        <div class="panel-row row" v-for="(review, index) in formData.review">
            <div class="col-8">
                <div class="param-head">Наименование обзора</div>
                {{ review.name }}
            </div>
            <div class="col-4">
                <div class="param-head">Код обзора</div>
                {{ review.code }}
            </div>
        </div>

        <div class="panel-row">
            <renins-badge-label class="active">Программа премирования</renins-badge-label>
        </div>
        <div class="panel-row">
            <div>{{ premium() }}</div>
            <div v-if="formData.premiumPercent">{{ formData.premiumPercent }}% от оклада</div>
        </div>

        <div class="panel-row">
            <renins-badge-label class="active">Грейд</renins-badge-label>
        </div>
        <div class="panel-row">
            {{ formData.gradeNotDefined ? 'Грейд не определен' : formData.grade }}
        </div>

        <div class="panel-row row">
            <div class="col-6">
                <renins-badge-label class="active">Оклад, руб.</renins-badge-label>
            </div>
            <div class="col-6">
                <renins-badge-label class="active">Зарплатная вилка, руб.</renins-badge-label>
            </div>
        </div>
        <div class="panel-row row">
            <div class="col-6">
                {{ formData.forkMid }}
            </div>
            <div class="col-6">
                {{ formData.forkLow }} - {{ formData.forkHigh }}
            </div>
        </div>

    </div>
    <div class="error_caption_block">В блоке имеются ошибки. Измените данные в блоке.</div>

    <renins-form-modal v-show="isShowEditModal[ currentStep ]" @close="close(currentStep)" :close-button="false">
        <template #head>Информация по компенсациям и льготам</template>
        <template #body>

            <div class="block">
                <div class="block-row">
                    <renins-badge-label class="active">Код обзора</renins-badge-label>
                </div>
                <div class="block-row row" v-for="(review, index) in formDataEdit.review">
                    <div class="col-8">
                        <renins-text-input v-model="formDataEdit.review[index].name" caption="Наименование обзора" class="w-100"></renins-text-input>
                    </div>
                    <div class="col-4">
                        <renins-text-input v-model="formDataEdit.review[index].code" caption="Код обзора" class="w-100"></renins-text-input>
                    </div>
                </div>
                <div class="block-row">
                    <renins-button class="xs" style="background-color: #fff!important" @click="addReview()">
                        Добавить
                        <renins-icon class="plus" style="margin-left: 8px; background-color: #1E222E"></renins-icon>
                    </renins-button>
                </div>
            </div>

            <div class="block" style="margin-top: 40px">
                <div class="block-row">
                    <renins-badge-label class="active">Программа премирования</renins-badge-label>
                </div>
            </div>
            <div class="block">
                <div class="block-row row">
                    <div>
                        <renins-checkbox v-model="formDataEdit.premiumMonth" label="Ежемесячная"></renins-checkbox>
                    </div>
                </div>
            </div>
            <div class="block">
                <div class="block-row row">
                    <div>
                        <renins-checkbox v-model="formDataEdit.premiumQuarter" label="Квартальная"></renins-checkbox>
                    </div>
                </div>
            </div>
            <div class="block">
                <div class="block-row row">
                    <div>
                        <renins-checkbox v-model="formDataEdit.premiumHalfyear" label="Полугодовая"></renins-checkbox>
                    </div>
                </div>
            </div>
            <div class="block">
                <div class="block-row row">
                    <div>
                        <renins-checkbox v-model="formDataEdit.premiumYear" label="Годовая"></renins-checkbox>
                    </div>
                </div>
            </div>
            <div class="block">
                <div class="block-row row">
                    <div class="col-4">
                        <renins-text-input v-model="formDataEdit.premiumPercent" type="number" caption="Процент от оклада, %"></renins-text-input>
                    </div>
                </div>
            </div>

            <div class="block" style="margin-top: 40px">
                <div class="block-row">
                    <renins-badge-label class="active">Грейд</renins-badge-label>
                </div>
                <div class="block-row row">
                    <div class="col-4">
                        <renins-text-input v-model="formDataEdit.grade" type="number" caption="Укажите число от 1 до 20"
                            :disabled="formDataEdit.gradeNotDefined"></renins-text-input>
                    </div>
                    <div class="col-4">
                        <renins-checkbox v-model="formDataEdit.gradeNotDefined" label="Грейд не определен"></renins-checkbox>
                    </div>
                </div>
            </div>

            <div class="block" style="margin-top: 40px">
                <div class="block-row row">
                    <div class="col-4">
                        <renins-badge-label class="active">Оклад, руб.</renins-badge-label>
                    </div>
                    <div class="col-4">
                        <renins-badge-label class="active">Зарплатная вилка, руб.</renins-badge-label>
                    </div>
                </div>
                <div class="block-row row">
                    <div class="col-4">
                        <div class="block-row block-desc">
                            Укажите желаемую сумму
                        </div>
                        <renins-text-input v-model="formDataEdit.forkMid" type="currency" caption="Сумма, руб"></renins-text-input>
                    </div>
                    <div class="col-4">
                        <div class="block-row block-desc">
                            Рассчитывает автоматически
                        </div>
                        <renins-text-input v-model="formDataEdit.forkLow" type="currency" caption="70%" :disabled="true"></renins-text-input>
                    </div>
                    <div class="col-4">
                        <div class="block-row block-desc">
                            &nbsp;
                        </div>
                        <renins-text-input v-model="formDataEdit.forkHigh" type="currency" caption="130%" :disabled="true"></renins-text-input>
                    </div>
                </div>
            </div>

        </template>
        <template #footer>
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