<div class="panel" v-if="currentStep === 7">
	<div class="panel-header">Ответственность за финансовый результат</div>
    <div class="block">
        <div class="block-row">
            <div class="block-desc">Вносит ли должность личный вклад в генерацию финансового результата? </div>
            Если для должности установлен план по продаже продуктов/услуг на определенную сумму в год,
            по привлечению определенного числа клиентов, заключению партнерских соглашений, развитию бизнеса,
            открытию точек продаж, то необходимо оценить значение личного вклада в генерацию прибыли компании.
        </div>
        <div class="r-web-caption r-mb-5 radio_error" v-if="errors.financialResultGeneration">
            Не выбран ответ
        </div>
        <div class="block-row">
            <renins-radio v-model="formData.step7.financialResultGeneration" val="Да" :error="errors.financialResultGeneration">Да</renins-radio>
            <renins-radio v-model="formData.step7.financialResultGeneration" val="Нет" :error="errors.financialResultGeneration" style="margin-left: 32px">Нет</renins-radio>
        </div>
    </div>

    <template v-if="formData.step7.financialResultGeneration === 'Да'">
        <div class="block">
            <div class="block-row">
                <div class="block-desc">Сколько он составляет по EBIT, руб/год? </div>
            </div>
            <div class="block-row row">
                <div class="col-4">
                    <renins-text-input v-model="formData.step7.EBIT" caption="Сумма, руб" placeholder="" type="currency" class="w-100" :error="errors.EBIT"></renins-text-input>
                </div>
            </div>
        </div>
        <div class="block">
            <div class="block-row">
                <div class="block-desc">Сколько он составляет по WP (подписанная премия), руб/год? </div>
            </div>
            <div class="block-row row">
                <div class="col-4">
                    <renins-text-input v-model="formData.step7.WP" caption="Сумма, руб" placeholder="" type="currency" class="w-100" :error="errors.WP"></renins-text-input>
                </div>
            </div>
        </div>
    </template>

    <div class="block" v-if="stepHasErrors(7)">
        <renins-quote class="errored">На форме присутствуют незаполненные поля</renins-quote>
    </div>

    <div v-if="statusId !== 'trash'" class="block">
        <renins-button class="secondary lg" style="width: 210px; margin-right: 16px;" @click="saveAndRedirect()" :loading="isSaving" :disabled="isAutoSaving">
            {{ !isAutoSaving ? 'Сохранить и выйти' : 'Автосохранение...' }}
        </renins-button>
        <renins-button class="primary lg" style="margin-right: 16px;" @click="nextStep()" :disabled="stepHasErrors(7)">
            Продолжить
        </renins-button>
        <renins-button v-if="isOD && stageId && (stageId !== 'create')" class="secondary lg" style="margin-right: 16px;"
            @click="isShowRevokeModal = true; revoke_comment = '';" :loading="isRevoking" :disabled="stepHasErrors(7)">
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
