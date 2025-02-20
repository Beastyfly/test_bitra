<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

Bitrix\Main\UI\Extension::load([
    'ui.vue',
    'ui.bootstrap4',
    'renins.ui.base',
    'renins.ui.breadcrumbs',
    'renins.ui.button-group',
    'renins.ui.range-input',
    'renins.ui.range-steps',
    'renins.ui.text-input',
    'renins.ui.textarea',
    'renins.ui.checkbox',
    'renins.ui.button',
    'renins.ui.select',
    'renins.ui.userselector',
    'renins.ui.modal',
    'renins.ui.form-modal',
    'renins.ui.steps',
    'renins.ui.steps-wide',
    'renins.ui.radio',
    'renins.ui.quote',
    'renins.ui.icon',
    'renins.ui.multi-select',
    'renins.ui.badge-label',
    'renins.ui.icon-modal',
    'renins.ui.switch',
    'renins.ui.declination',
    'renins.ui.dnd-board',
]);
?>
    <div id="job_profile">
        <template v-if="isAccessPage">
            <div class="block">
                <renins-breadcrumbs class="blue dots" :items="breadcrumbs"></renins-breadcrumbs>
            </div>

            <div class="block">
                <renins-button class="secondary xs" @click="redirectBack()">
                    <renins-icon class="chevron-left" style="margin-right: 8px; background: var(--text-primary);"></renins-icon>
                    Список профилей
                </renins-button>
            </div>

            <div class="block">
                <h2>Профиль должности</h2>
            </div>

            <div id="group-button" style="margin: 40px 0px;">
                <renins-button-group v-model="current_step_section" v-bind:items="step_sections"></renins-button-group>
            </div>

            <div v-if="current_step_section !== 'C&B'" style="margin-bottom: 40px">
                <renins-steps-wide class="big" :current_step="currentStep"
                    @left="currentStep--; if (currentStep === 11) currentStep--;"
                    @right="currentStep++; if (currentStep === 11) currentStep++;">
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

            <div class="block panel stages" v-if="currentStep !== 1" style="margin-bottom: -24px;">
                <div class="panel-row panel-header">
                    <div>{{ formData.step1.positionName }}</div>
                    <renins-badge-label :class="statusClass" style="float: right" v-if="status">{{ status }}</renins-badge-label>
                </div>

                <div class="panel-row row">
                    <div class="col-4">
                        <div class="param-head">ID</div>
                        <div>{{ formData.step1.id ? formData.step1.id : '-' }}</div>
                    </div>
                    <div class="col-4">
                        <div class="param-head">Этап</div>
                        <div>{{ stage ? stage : '-' }}</div>
                    </div>
                    <div class="col-4">
                        <div class="param-head">Согласующий</div>
                        <div>{{ processingUser ? processingUser.fio : '-' }}</div>
                    </div>
                </div>

                <div class="panel-row row">
                    <div class="col-2">
                        <div class="param-head">Дата создания</div>
                        <div>{{ createDate ? createDate : '-' }}</div>
                    </div>
                    <div class="col-2">
                        <div class="param-head">Дата изменения</div>
                        <div>{{ updateDate ? updateDate : '-' }}</div>
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
                        "/local/components/renins/job_profile/templates/form/stages.php"
                    ), array(), array("MODE" => "php")
                );
                ?>
            </div>

            <?php
	        for ($i = 1; $i <= 15; $i++)
	        {
		        $APPLICATION->IncludeFile(
			        $APPLICATION->GetTemplatePath(
				        "/local/components/renins/job_profile/templates/form/steps/step{$i}.php"
			        ), array(), array("MODE" => "php")
		        );
	        }
	        ?>
            
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

            <renins-modal v-show="isShowSendModal" @close="isShowSendModal = false; stepsWithErrors = [];" scroll="false" width="600">
                <template #head>Отправка профиля</template>
                <template #body>
                    Поздравляем, вы успешно завершили заполнение профиля. Если требуется, оставьте комментарий
                    <div style="margin-top: 24px; margin-bottom: 24px">
                        <renins-textarea rows="5" cols="80" placeholder="Комментарий" class="resize-both"
                            v-model="send_comment" style="width: 100%"></renins-textarea>
                    </div>
                    <renins-quote class="errored r-mb-4" v-if="stepsWithErrors.length">На форме присутствуют незаполненные поля на
                        <renins-declination :value="stepsWithErrors.length">
                            <template #few>шагах</template>
                            <template #one>шаге</template>
                            <template #two>шагах</template>
                        </renins-declination>: {{ stepsWithErrors.join(', ') }}</renins-quote>
                    <div class="row">
                        <div class="col-3">
                            <renins-button class="primary w-100" @click="send()" :loading="isSending">Отправить</renins-button>
                        </div>
                        <div class="col-3">
                            <renins-button class="secondary w-100" @click="isShowSendModal = false; stepsWithErrors = [];" :disabled="isSending">Закрыть</renins-button>
                        </div>
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

        </template>
        <template v-else>
            Ошибка! Нет доступа
        </template>
    </div>
    <script>
        window.cfg_job_profile = <?= CUtil::PhpToJSObject($arResult['config']) ?>;
    </script>
<?php
