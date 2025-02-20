<div class="panel-row row" v-if="!isCollapsed('stages')">
    <div class="tasc-list__table" v-if="!isCollapsed('stages')">
        <div class="tasc-list__th">
            <div class="col-4">Этап</div>
            <div class="col-4">Согласующий</div>
            <div class="col-4">
                <div class="tr-flex2">
                    <div>Статус</div>
                    <div>Изменение статуса</div>
                </div>
            </div>
        </div>
        <div class="tasc-list__tr" v-for="stage in stages" :data-id="stage.ID" v-if="allowedChangeStages || (stage.CHECKED === 'Y')">
            <div class="ellipsis_text col-4">{{ stage.NAME }}</div>
            <div class="col-4">
                <div class="tr-flex">
                    <renins-user-tooltip v-if="stage.RESPONSIBLE_USER" :user-id="stage.RESPONSIBLE_USER" class="ellipsis_text" style="display: block; padding: 0">
                        {{ stage.RESPONSIBLE_USER_NAME }}
                    </renins-user-tooltip>
                    <template v-else-if="allowedChangeStages && (stage.CAN_CHANGE_RESPONSIBLE === 'Y')">
                        <renins-button class="secondary xs nowrap" @click="editStageFormData.TITLE = 'Назначить согласующего'; showStageModal(stage)" :disabled="isStageUpdating(stage.ID)">
                            Назначить согласующего
                        </renins-button>
                    </template>
                    <template v-else><span style="color: #6E748C;">N/D</span></template>

                    <renins-button class="xs" style="float: right;" @click="editStageFormData.TITLE = 'Изменить согласующего'; showStageModal(stage)" :disabled="isStageUpdating(stage.ID)"
                        v-if="allowedChangeStages && stage.RESPONSIBLE_USER && (stage.CAN_CHANGE_RESPONSIBLE === 'Y')
                        && !(stage.STATUS === 'sent' || stage.STATUS === 'approved' || stage.STATUS === 'signed' || stage.STATUS === 'completed')">
                        <renins-icon class="edit3" style="background: var(--text-primary);"></renins-icon>
                    </renins-button>
                    <renins-button v-if="allowedChangeStages && (stage.CAN_DELETE === 'Y')" class="secondary xs" style="margin-left: 8px; float: right" @click="deleteAdditionStage(stage.ID)" :loading="isStageDeleting(stage.ID)">
                        <renins-icon class="trash" style="margin-right: 8px; background: var(--text-primary);"></renins-icon>
                        Удалить
                    </renins-button>
                </div>
            </div>
            <div class="col-4">
                <div class="tr-flex2">
                    <div class="nowrap">
                        <renins-badge-label class="success" v-if="stage.STATUS === 'sent' || stage.STATUS === 'approved' || stage.STATUS === 'signed' || stage.STATUS === 'completed'">{{ stage.STATUS_NAME }}</renins-badge-label>
                        <renins-badge-label class="active" v-if="stage.STATUS === 'on_approval'">{{ stage.STATUS_NAME }}</renins-badge-label>
                        <renins-badge-label class="errored" v-if="stage.STATUS === 'paused'">{{ stage.STATUS_NAME }}</renins-badge-label>
                    </div>
                    <div class="nowrap">
                        {{ stage.END_TIME_2 }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<renins-form-modal v-show="isShowStageModal" :close-button="false" scroll="false" width="600">
    <template #head>{{ editStageFormData.TITLE }}</template>
    <template #body>
        <renins-userselector v-model="editStageFormData.RESPONSIBLE_USER" :single="true" caption="Введите ФИО" style="margin-bottom: 24px;"></renins-userselector>
    </template>
    <template #footer>
        <div class="row">
            <div class="col-3">
                <renins-button class="primary w-100" @click="updateResponsibleUser(editStageFormData.ID, editStageFormData.RESPONSIBLE_USER)" :loading="isStageUpdating(editStageFormData.ID)">Сохранить</renins-button>
            </div>
            <div class="col-3">
                <renins-button class="secondary w-100" @click="isShowStageModal = false" :disabled="isStageUpdating(editStageFormData.ID)">Закрыть</renins-button>
            </div>
        </div>
    </template>
</renins-form-modal>
