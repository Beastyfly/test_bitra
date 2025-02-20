<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

use Bitrix\Main\UI\Extension;
use Bitrix\Main\Page\Asset;

Extension::load([
    'ui.vue',
    'renins.ui.base',
    'renins.ui.declination',
    'renins.ui.text-input',
    'renins.ui.textarea',
    'renins.ui.checkbox',
    'renins.ui.button',
    'renins.ui.button-group',
    'renins.ui.range-input',
    'renins.ui.range-steps',
    'renins.ui.select',
    'renins.ui.multi-select',
    'renins.ui.userselector',
    'renins.ui.calendar',
    'renins.ui.breadcrumbs',
    'renins.ui.modal',
    'renins.ui.form-modal',
    'renins.ui.quote',
    'renins.ui.steps',
    'renins.ui.steps-wide',
    'renins.ui.radio',
    'renins.ui.uploader',
    'renins.ui.icon',
    'renins.ui.icon-modal',
    'renins.ui.pad-icon',
    'renins.ui.badge-label',
    'renins.ui.colors',
    'renins.ui.file',
    'renins.ui.user-tooltip',
    'renins.ui.alert',
    'renins.ui.switch',
    'renins.ui.dnd-board',
    'renins.ui.combobox',
    'renins.filters.number-format',
]);
?>
    <div id="job_profile">
        <template v-if="isAccessPage && ((modelRole[0] !== '2') || (modelRole[1] !== '2') || (modelRole[2] !== '2') || (modelRole[3] !== '2'))">
            <div class="block">
                <renins-breadcrumbs class="blue dots" :items="breadcrumbs"></renins-breadcrumbs>
            </div>

            <div class="block">
                <renins-button class="secondary xs" @click="redirectToList('<?=$_COOKIE['backUrlJobProfile'];?>')">
                    <renins-icon class="chevron-left" style="margin-right: 8px; background: var(--text-primary);"></renins-icon>
                    Список профилей
                </renins-button>
            </div>

            <div class="block">
                <h2>Профиль должности</h2>
            </div>

            <div style="margin: 40px 0">
                <renins-button-group v-model="current_step_section" v-bind:items="step_sections"></renins-button-group>
            </div>
            <div style="margin-bottom: 40px">
                <renins-steps-wide class="big" :current_step="currentStep"
                    @left="currentStep--;"
                    @right="currentStep++;">
                    <renins-steps-item :class="getStepClass(1)" step="1" @click="currentStep=1">Должность</renins-steps-item>
                    <renins-steps-item :class="getStepClass(2)" step="2" @click="currentStep=2">Параметры</renins-steps-item>
                    <renins-steps-item :class="getStepClass(3)" step="3" @click="currentStep=3">Цели</renins-steps-item>
                    <renins-steps-item :class="getStepClass(4)" step="4" @click="currentStep=4">Обязанности</renins-steps-item>
                    <renins-steps-item :class="getStepClass(5)" step="5" @click="currentStep=5">Вклад</renins-steps-item>
                    <renins-steps-item :class="getStepClass(6)" step="6" @click="currentStep=6">Полномочия</renins-steps-item>
                    <renins-steps-item :class="getStepClass(7)" step="7" @click="currentStep=7">Финансовый результат</renins-steps-item>
                    <renins-steps-item :class="getStepClass(8)" step="8" @click="currentStep=8">Бюджет</renins-steps-item>
                    <renins-steps-item :class="getStepClass(9)" step="9" @click="currentStep=9">Инновационность</renins-steps-item>
                    <renins-steps-item :class="getStepClass(10)" step="10" @click="currentStep=10">Коммуникации</renins-steps-item>
                    <renins-steps-item :class="getStepClass(11)" step="11" @click="currentStep=11">Требования</renins-steps-item>
                    <renins-steps-item :class="getStepClass(12)" step="12" @click="currentStep=12">Навыки</renins-steps-item>
                    <renins-steps-item :class="getStepClass(13)" step="13" @click="currentStep=13">Опыт</renins-steps-item>
                    <renins-steps-item :class="getStepClass(14)" step="14" @click="currentStep=14">Компетенции</renins-steps-item>
                </renins-steps-wide>
            </div>

            <div class="block panel" style="margin-bottom: -8px;">
                <div class="panel-row panel-header">
                    <div>{{ formData.positionName }}</div>
                    <renins-badge-label :class="statusClass" style="float: right" v-if="status">{{ status }}</renins-badge-label>
                </div>

                <div class="panel-row row">
                    <div class="col-4">
                        <div class="param-head">ID</div>
                        <div>{{ formData.id }}</div>
                    </div>
                    <div class="col-4">
                        <div class="param-head">Этап</div>
                        <div>{{ stage }}</div>
                    </div>
                    <div class="col-4">
                        <div class="param-head">Согласующий</div>
                        <div>{{ processingUser ? processingUser.fio : '-' }}</div>
                    </div>
                </div>

                <div class="panel-row row">
                    <div class="col-2">
                        <div class="param-head">Дата создания</div>
                        <div>{{ createDate }}</div>
                    </div>
                    <div class="col-2">
                        <div class="param-head">Дата изменения</div>
                        <div>{{ updateDate }}</div>
                    </div>
                    <div class="col-2">
                        <div class="param-head">В работе</div>
                        <div>{{ work ? work : '-' }}</div>
                    </div>
                    <div class="col-2">
                        <div class="param-head">SLA роли</div>
                        <div>{{ roleSLA ? roleSLA : '-' }}</div>
                    </div>
                    <div class="col-4">
                        <renins-button class="secondary xs w-100" @click="toggle('stages')">
                            История согласования
                            <renins-icon :class="{'chevron-down': isCollapsed('stages'), 'chevron-up': !isCollapsed('stages') }"
                                style="margin-left: 8px;"></renins-icon>
                        </renins-button>
                    </div>
                </div>

                <?php
                // Этапы согласования
                $APPLICATION->IncludeFile(
                    $APPLICATION->GetTemplatePath(
                        "/local/components/renins/job_profile/templates/detail/stages.php"
                    ), array(), array("MODE" => "php")
                );
                ?>
            </div>
            <div class="block panel">
                <div class="panel-row panel-header">Комментарий</div>
                <div class="panel-row row comment_cont" style="border-radius: 8px; border: 1px solid rgba(204, 207, 222, 1); min-height: 140px; margin: 16px 0px 0px 0px;">
                    <div class="col-1"></div>
                    <div class="col-11">sdfewqdsadasdadsa</div>
                </div>
                <p>
                    <renins-combobox v-model="combobox1" items="comboboxItems"></renins-combobox>
                </p>
                <p>
                    <renins-combobox class="sm" v-model="combobox1" items="comboboxItems"></renins-combobox>
                </p>
                <renins-button class="tertiary w-100">Secondary</renins-button>
            </div>
            <?php
            for ($i = 1; $i <= 16; $i++)
            {
                $APPLICATION->IncludeFile(
                    $APPLICATION->GetTemplatePath(
                        "/local/components/renins/job_profile/templates/detail/steps/step{$i}.php"
                    ), array(), array("MODE" => "php")
                );
            }
            ?>

            <div v-if="allowedSend || inTrash">
                <div class="block">
                    <renins-button v-if="allowedSend" class="primary lg" style="margin-right: 16px;" @click="send" :loading="isSending">Отправить</renins-button>
                    <renins-button class="primary lg" @click="isShowDeleteModal = true;">Удалить</renins-button>
                </div>
            </div>

            <div class="block" style="margin-top: 40px; margin-bottom: 40px;">
                <template v-if="allowedGetToWork && !processingUser">
                    <renins-button class="primary lg" @click="getToWork" :disabled="isProcessing" style="margin-right: 12px;">Взять в работу</renins-button>
                </template>
                <template v-if="((isOD && allowedApproveEarlier) || allowedApprove) && processingUser">
                    <renins-button class="primary lg" @click="isShowApproveModal = true; approve_comment = '';" :disabled="!allowedApprove" style="margin-right: 12px;">
                        Согласовать
                    </renins-button>
                    <renins-button class="secondary lg" @click="isShowRejectModal = true; reject_comment = '';" :disabled="!allowedApprove || isRejecting" style="margin-right: 12px;">
                        Отклонить
                    </renins-button>
                    <renins-button class="secondary lg" @click="isShowReturnModal = true; return_stage = null;" :disabled="!allowedApprove || isReturning" style="margin-right: 12px;">
                        Вернуть на этап
                    </renins-button>
                </template>
                <renins-button v-if="isOD && allowedRevoke" :disabled="isRevoking" class="primary lg" style="margin-right: 12px;"
                    @click="isShowRevokeModal = true; revoke_comment = '';">Отозвать</renins-button>
                <renins-button class="tertiary lg" @click="downloadDI()" style="margin-right: 12px;" v-if="status=='Утвержден'" :disabled="downloadDIError"><renins-icon class="download-file" style="margin-right: 8px;"></renins-icon>
                    Скачать ДИ
                </renins-button>
                <renins-button class="tertiary lg" @click="downloadExcel()"><renins-icon class="download" style="margin-right: 8px;"></renins-icon>
                    Скачать профиль
                </renins-button>
                <renins-button class="secondary lg" @click="if (currentStep < 15) currentStep++;"
                    :disabled="(!(this.isCnB || (this.modelRole[3] === true)) && (currentStep >= 14)) || (currentStep >= 15)" style="padding: 18px; float: right;">
                    <renins-icon class="chevron-right md" style="background: var(--text-primary);"></renins-icon>
                </renins-button>
                <renins-button class="secondary lg" @click="if (currentStep > 1) currentStep--;"
                    :disabled="currentStep <= 1" style="padding: 18px; float: right; margin-right: 16px;">
                    <renins-icon class="chevron-left md" style="background: var(--text-primary);"></renins-icon>
                </renins-button>
            </div>

            <div v-show="downloadDIError" id="download-di-error">
                <renins-quote class="big-quote errored">Должностная инструкция находится на этапе привязки к организационной структуре. По вопросам добавления обращаться на OrgStructure@renins.com</renins-quote>
            </div>

            <renins-modal v-show="isShowDownloadDIModal" @close="isShowDownloadDIModal = false">
                <template #head>Выберите подразделение</template>
                <template #body>
                    Профиль должности «{{ formData.positionName }}» заполнен для нескольких подразделений». Выберите нужное из списка
                    <div>
                        <renins-select placeholder="Подразделение" v-model="selectDiFile" v-bind:items="diFilesSelectList"
                                       class="w-100" style="margin-top: 16px"></renins-select>
                    </div>
                    <div style="padding-top: 24px">
                        <renins-button class="primary md" @click="downloadDi()" :disabled="!selectDiFile">Скачать</renins-button>
                    </div>
                </template>
            </renins-modal>

            <renins-modal v-show="isShowDeleteModal" @close="isShowDeleteModal = false">
                <template #head>Удаление заявки</template>
                <template #body>
                    Вы действительно хотите удалить заявку?
                    <div style="padding-top: 24px">
                        <renins-button class="primary md float-left" style="margin-right: 16px"
                            @click="isShowDeleteModal = false">Закрыть</renins-button>
                        <renins-button class="secondary md float-left" @click="deleteElement"
                            :loading="isDeleting">Удалить</renins-button>
                    </div>
                </template>
            </renins-modal>

            <renins-icon-modal v-if="isShowSuccessfullyArchivedModal" @close="isShowSuccessfullyArchivedModal = false">
                <template #icon>
                    <renins-pad-icon class="lg secondary"><renins-icon
                        class="trash lg color-brand-purple"></renins-icon></renins-pad-icon>
                </template>
                <template #head>Заявка успешно удалена</template>
                <template #body></template>
                <template #footer>
                    <renins-button class="primary" @click="isShowSuccessfullyArchivedModal = false; redirectToList();">
                        Закрыть
                    </renins-button>
                </template>
            </renins-icon-modal>

            <renins-modal v-show="isShowApproveModal" @close="isShowApproveModal = false">
                <template #head>Согласование заявки</template>
                <template #body>
                    При необходимости оставь комментарий к заявке
                    <div style="margin-top: 16px">
                        <renins-textarea rows="5" cols="80" placeholder="Комментарий" class="resize-both"
                            v-model="approve_comment" style="width: 100%"></renins-textarea>
                    </div>
                    <div style="padding-top: 24px">
                        <renins-button class="primary md float-left" @click="approve" :loading="isApproving"
                            style="margin-right: 16px">Отправить</renins-button>
                        <renins-button class="secondary md float-left"
                            @click="isShowApproveModal = false">Закрыть</renins-button>
                    </div>
                </template>
            </renins-modal>

            <renins-icon-modal v-if="isShowSuccessfullyApprovedModal" @close="isShowSuccessfullyApprovedModal = false">
                <template #icon>
                    <renins-pad-icon class="lg secondary"><renins-icon
                        class="trumbs-up lg color-brand-purple"></renins-icon></renins-pad-icon>
                </template>
                <template #head>
                    Заявка
                    <template v-if="stageId !== 'embedding'">согласована</template>
                    <template v-else>утверждена</template>
                </template>
                <template #body>
                    <template v-if="stageId !== 'embedding'">Заявка отправлена на следующий этап согласования</template>
                    <template v-else>Заявка отправлена БОСС-кадровик</template>
                </template>
                <template #footer>
                    <renins-button class="primary"
                        @click="isShowSuccessfullyApprovedModal = false; redirectToList();">Понятно</renins-button>
                </template>
            </renins-icon-modal>

            <renins-modal v-show="isShowRejectModal" @close="isShowRejectModal = false">
                <template #head>Отклонение заявки</template>
                <template #body>
                    При необходимости оставь комментарий к заявке
                    <div style="margin-top: 16px">
                        <renins-textarea rows="5" cols="80" placeholder="Комментарий" class="resize-both"
                            v-model="reject_comment" style="width: 100%"></renins-textarea>
                    </div>
                    <div style="padding-top: 24px">
                        <renins-button class="primary md float-left" @click="reject" :loading="isRejecting" style="margin-right: 16px"
                            :disabled="!reject_comment || (reject_comment.trim() == '')">Отправить</renins-button>
                        <renins-button class="secondary md float-left" @click="isShowRejectModal = false">
                            Закрыть</renins-button>
                    </div>
                </template>
            </renins-modal>

            <renins-modal v-show="isShowReturnModal" @close="isShowReturnModal = false">
                <template #head>Вернуть на этап</template>
                <template #body>
                    Выберите этап на который требуется вернуть профиль
                    <div style="margin-top: 16px">
                        <renins-select placeholder="Выбранный этап" v-model="return_stage" v-bind:items="returningStages"
                            class="w-100" style="margin-bottom: 24px"></renins-select>
                        <renins-textarea rows="5" cols="80" placeholder="Оставьте комментарий" class="resize-both"
                            v-model="return_comment" style="width: 100%"></renins-textarea>
                    </div>
                    <div style="padding-top: 24px">
                        <renins-button class="primary md float-left" style="margin-right: 16px" @click="returning" :loading="isReturning"
                            :disabled="!return_stage || !return_comment || (return_comment.trim() == '')">
                            Отправить</renins-button>
                        <renins-button class="secondary md float-left" @click="isShowReturnModal = false">
                            Закрыть</renins-button>
                    </div>
                </template>
            </renins-modal>

            <renins-modal v-show="isShowRevokeModal" @close="isShowRevokeModal = false">
                <template #head>Отзыв заявки</template>
                <template #body>
                    Укажите причину отзыва
                    <div style="margin-top: 16px">
                        <renins-textarea rows="5" cols="80" placeholder="Комментарий" class="resize-both"
                            v-model="revoke_comment" style="width: 100%"></renins-textarea>
                    </div>
                    <div style="padding-top: 24px">
                        <renins-button class="primary md float-left" @click="revoke" :loading="isRevoking"
                            style="margin-right: 16px">Отозвать</renins-button>
                        <renins-button class="secondary md float-left"
                            @click="isShowRevokeModal = false">Закрыть</renins-button>
                    </div>
                </template>
            </renins-modal>

            <renins-icon-modal v-if="isShowSuccessfullyRejectedModal" @close="isShowSuccessfullyRejectedModal = false">
                <template #icon>
                    <renins-pad-icon class="lg secondary"><renins-icon
                        class="trumbs-up lg color-brand-purple"></renins-icon></renins-pad-icon>
                </template>
                <template #head>Заявка отклонена</template>
                <template #body>Заявка отправлена на доработку инициатору</template>
                <template #footer>
                    <renins-button class="primary"
                        @click="isShowSuccessfullyRejectedModal = false; redirectToList();">Понятно</renins-button>
                </template>
            </renins-icon-modal>

            <renins-icon-modal v-if="isShowSuccessfullyRevokedModal" @close="isShowSuccessfullyRevokedModal = false">
                <template #icon>
                    <renins-pad-icon class="lg secondary"><renins-icon
                        class="trumbs-up lg color-brand-purple"></renins-icon></renins-pad-icon>
                </template>
                <template #head>Заявка отозвана</template>
                <template #body>Заявка отправлена на доработку инициатору</template>
                <template #footer>
                    <renins-button class="primary"
                        @click="isShowSuccessfullyRevokedModal = false; redirectToList();">Понятно</renins-button>
                </template>
            </renins-icon-modal>

            <renins-icon-modal v-if="isShowSuccessfullyReturnedModal" @close="isShowSuccessfullyReturnedModal = false">
                <template #icon>
                    <renins-pad-icon class="lg secondary"><renins-icon
                        class="trumbs-up lg color-brand-purple"></renins-icon></renins-pad-icon>
                </template>
                <template #head>Заявка возвращена</template>
                <template #body>Заявка возвращена на предыдущий этап</template>
                <template #footer>
                    <renins-button class="primary"
                        @click="isShowSuccessfullyReturnedModal = false; redirectToList();">Понятно</renins-button>
                </template>
            </renins-icon-modal>

            <renins-alert ref="alert"></renins-alert>
        </template>
        <template v-else>
            Ошибка! Нет доступа
        </template>
    </div>
    <script>
        window.cfg_job_profile = <?= CUtil::PhpToJSObject($arResult['config']) ?>;
    </script>
<?php
