<div v-if="currentStep === 15">
    <div class="panel">
        <div class="panel-header">Информация по компенсациям и льготам</div>

        <div class="block">
            <div class="block-row">
                <renins-badge-label class="active">Код обзора</renins-badge-label>
            </div>
            <div class="block-row row" v-for="(review, index) in formData.step15.review">
                <div class="col-8">
                    <renins-text-input v-model="formData.step15.review[index].name" caption="Наименование обзора" class="w-100"></renins-text-input>
                </div>
                <div class="col-4">
                    <renins-text-input v-model="formData.step15.review[index].code" caption="Код обзора" class="w-100"></renins-text-input>
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
                    <renins-checkbox v-model="formData.step15.premiumMonth" label="Ежемесячная"></renins-checkbox>
                </div>
            </div>
        </div>
        <div class="block">
            <div class="block-row row">
                <div>
                    <renins-checkbox v-model="formData.step15.premiumQuarter" label="Квартальная"></renins-checkbox>
                </div>
            </div>
        </div>
        <div class="block">
            <div class="block-row row">
                <div>
                    <renins-checkbox v-model="formData.step15.premiumHalfyear" label="Полугодовая"></renins-checkbox>
                </div>
            </div>
        </div>
        <div class="block">
            <div class="block-row row">
                <div>
                    <renins-checkbox v-model="formData.step15.premiumYear" label="Годовая"></renins-checkbox>
                </div>
            </div>
        </div>
        <div class="block">
            <div class="block-row row">
                <div class="col-4">
                    <renins-text-input v-model="formData.step15.premiumPercent" type="number" caption="Процент от оклада, %"></renins-text-input>
                </div>
            </div>
        </div>

        <div class="block" style="margin-top: 40px">
            <div class="block-row">
                <renins-badge-label class="active">Грейд</renins-badge-label>
            </div>
            <div class="block-row row">
                <div class="col-4">
                    <renins-text-input v-model="formData.step15.grade" type="number" caption="Укажите число от 1 до 20"
                                       :disabled="formData.step15.gradeNotDefined"></renins-text-input>
                </div>
                <div class="col-4">
                    <renins-checkbox v-model="formData.step15.gradeNotDefined" label="Грейд не определен"></renins-checkbox>
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
                    <renins-text-input v-model="formData.step15.forkMid" type="currency" caption="Сумма, руб"></renins-text-input>                </div>
                <div class="col-4">
                    <div class="block-row block-desc">
                        Рассчитывает автоматически
                    </div>
                    <renins-text-input v-model="formData.step15.forkLow" type="currency" caption="70%" :disabled="true"></renins-text-input>                </div>
                <div class="col-4">
                    <div class="block-row block-desc">
                        &nbsp;
                    </div>
                    <renins-text-input v-model="formData.step15.forkHigh" type="currency" caption="130%" :disabled="true"></renins-text-input>                </div>
            </div>
        </div>
    </div>

    <div v-if="statusId !== 'trash'" class="block" style="margin-top: 40px">
        <renins-button class="secondary lg" style="width: 210px; margin-right: 16px;" @click="saveAndRedirect()" :loading="isSaving" :disabled="isAutoSaving">
            {{ !isAutoSaving ? 'Сохранить и выйти' : 'Автосохранение...' }}
        </renins-button>
        <renins-button class="primary lg" style="margin-right: 16px;" @click="isShowSendModal = true" :loading="isSending">
            Отправить
        </renins-button>
        <renins-button v-if="isOD && stageId && (stageId !== 'create')" class="secondary lg" style="margin-right: 16px;"
            @click="isShowRevokeModal = true; revoke_comment = '';" :loading="isRevoking" :disabled="stepHasErrors(15)">
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
