<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var $arResult
 */

use Bitrix\Main\UI\Extension;

Extension::load([
    'ui.bootstrap4',
    'ui.vue',
    'renins.ui.breadcrumbs',
    'renins.ui.button',
    'renins.ui.tabs',
    'renins.ui.pagination',
    'renins.ui.modal',
    'renins.ui.form-modal',
    'renins.ui.badge-label',
    'renins.ui.pad-icon',
    'renins.ui.icon',
    'renins.ui.select',
    'renins.ui.tooltip',
    'renins.ui.calendar',
    'renins.ui.alert',
    'renins.ui.base',
    'renins.ui.checkbox',
    'renins.ui.text-input',
    'renins.ui.avatar',
    'renins.ui.link',
]);
?>

<div id="job_profile" class="tasc-list" :class="{'mounted': mounted}">
    <template v-if="(allCount < 1) && !isOD">

        <div class="panel1">
            <div class="block1">
                <div class="icon">
                    <img src="/local/images/lock.png"/>
                </div>
            </div>
            <div class="block1">
                <div class="r-web-headline_5">Доступ ограничен</div>
                <div class="descr">
                    В настоящий момент создание заявок доступно только административным руководителям,
                    а доступ к активным заявкам — участникам бизнес-процесса. Если вам необходим доступ,
                    пожалуйста, напишите на <renins-link href="mailto:OK@renins.ru">OK@renins.ru</renins-link>
                </div>
            </div>
            <div class="block1">
                <renins-button class="secondary xs" @click="location.href = '/requests/'">
                    <renins-icon class="chevron-left"
                                 style="margin-right: 8px; background: var(--text-primary);"></renins-icon>
                    Заявки
                </renins-button>
            </div>
        </div>

    </template>
    <template v-else>

        <div style="margin-top: 28px">
            <renins-breadcrumbs class="blue dots" :items="breadcrumbs"></renins-breadcrumbs>
        </div>

        <div class="r-web-headline-4 r-mv-7" style="display: flex">
            <span class="r-mr-7">Профиль должности</span>
            <a v-if="isOD" href="/renins/job_profile/">
                <renins-button class="primary sm">Создать новый
                    <renins-icon class="plus color-white" style="margin-left: 8px;"></renins-icon>
                </renins-button>
            </a>
        </div>

        <renins-tabs v-model="selected_tab" v-bind:items="tabs_items" style="margin-bottom: 16px;"
                     @input="clearFilter"></renins-tabs>

        <!--<div v-if="isOD" style="display: inline-block;">
            <renins-button v-if="(selected_tab.value !== 'active') && (selected_tab.value !== 'approved')" class="tertiary transparent md float-left" style="margin: 0 16px 16px 0"
                    @click="showDelElementModal = true" :loading="deleting" :disabled="!canDeleting">
                <renins-icon class="trash" style="background-color: #230446; margin-right: 8px"></renins-icon>
                Удалить
            </renins-button>
            <renins-button class="tertiary transparent md float-left" style="margin-bottom: 16px;"
                    @click="showCopyElementModal = true" :loading="copying" :disabled="!canCopying">
                <renins-icon class="copy" style="background-color: #230446; margin-right: 8px"></renins-icon>
                Дублировать
            </renins-button>
        </div>-->

        <div style="display: flex;margin-bottom: 16px; flex-wrap: wrap;"
             >
            <div class="filter filter-wd-24">
                <renins-text-input v-model="searchIDs" caption="Поиск по ID заявки" placeholder="ID заявки"
                                   class="search" :items="createdIDs" style="width: 100%;"></renins-text-input>
            </div>
            <div class="filter filter-wd-38">
                <renins-select placeholder="Функция 1" v-model="searchFunc1" :items="createdFunc1"></renins-select>
            </div>
            <div class="filter filter-wd-38 func2-mr">
                <renins-select placeholder="Функция 2" v-model="searchFunc2" :items="createdFunc2" ></renins-select>
            </div>
            <div class="filter filter-wd-24 not-mr-bt">
                <renins-select placeholder="Статус" v-model="searchStatus" :items="createdStatuses"></renins-select>
            </div>
            <div class="filter filter-wd-24 not-mr-bt">
                <renins-select placeholder="Кост-центр" v-model="searchCostCenter"
                               :items="createdCostCenters"></renins-select>
            </div>
            <div class="filter filter-wd-52 not-mr-bt" style="margin-right: 0;" >
                <renins-select placeholder="Подразделение" v-model="searchPodraz"
                               :items="createdPodraz"></renins-select>
            </div>
            <!--
            <renins-pad-icon style="cursor: pointer" class="mdd secondary" @click="clearFilter">
                <renins-icon class="x md"></renins-icon>
            </renins-pad-icon>-->
        </div>
        <div name="fade-slide" style="margin-bottom: 16px; display: flex; flex-direction: row;" v-if="isOD">
            <renins-button class="renins-button xs"  @click.stop="toggleSettings" style="margin-right: 8px;">
                <renins-icon class="settings" style="margin-right: 8px;"></renins-icon>
                Настроить вид списка
            </renins-button>
            <transition name="fade">
                <div class="settings-block " v-if="isSettingsVisible" :class="{ 'visible': isSettingsVisible }">
                    <ul class="settings-list">
                        <li class="settings-item" v-for="(item, index) in listSettings" :key="index">
                            <renins-checkbox v-model="item.visible" ></renins-checkbox>
                            <span>{{item.name}}</span>
                        </li>
                    </ul>
                </div>
            </transition>
            <div v-show="isAnyCheckboxSelected" class="download-button-container">
                <renins-button class="renins-button transparent xs" @click="downloadExcel">
                    <renins-icon class="download" style="margin-right: 8px;"></renins-icon>
                    Скачать
                </renins-button>
                <renins-button class="renins-button transparent xs"
                               @click="showCopyElementModal = true" :loading="copying" :disabled="!canCopying">
                    <renins-icon class="copy" style="margin-right: 8px;"></renins-icon>
                    Дублировать
                </renins-button>
                <renins-button class="renins-button transparent xs" @click="showToArchiveElementModal = true"
                               :loading="archive" :disabled="!canArchive">
                    <renins-icon class="to-dir" style="margin-right: 8px;"></renins-icon>
                    В архив
                </renins-button>
                <renins-button v-if="(selected_tab.value !== 'active') && (selected_tab.value !== 'approved')"
                               class="renins-button transparent xs" @click="showDelElementModal = true"
                               :loading="deleting" :disabled="!canDeleting">
                    <renins-icon class="trash" style="margin-right: 8px;"></renins-icon>
                    Удалить
                </renins-button>
            </div>
        </div>

        <div v-if="!loading">

            <div class="tasc-list__table" v-if="list.length > 0" :class="{'loading': loading}">
                <div class="tasc-list__th">
                    <div class="check" v-if="isOD" @click.stop>
                        <renins-checkbox v-model="isAllChecked" :indeterminate="isIndeterminate" @click.stop></renins-checkbox>
                    </div>
                    <div class="id" @click="setSort('ID')" v-if="listSettings.ID.visible">
                        <div class="sortable nowrap"
                             :class="{sort_asc: isSort('ID', 'ASC'), sort_desc: isSort('ID', 'DESC')}">ID
                        </div>
                    </div>
                    <div class="ellipsis_text" @click="setSort('PROPERTY_NAZVANIE_DOLZHNOSTI')" v-if="listSettings.job.visible">
                        <div class="sortable"
                             :class="{sort_asc: isSort('PROPERTY_NAZVANIE_DOLZHNOSTI', 'ASC'), sort_desc: isSort('PROPERTY_NAZVANIE_DOLZHNOSTI', 'DESC')}">
                            Должность
                        </div>
                    </div>
                    <div class="cost" @click="setSort('PROPERTY_COST_CENTER')" v-if="listSettings.costCenter.visible">
                        <div class="sortable"
                             :class="{sort_asc: isSort('PROPERTY_COST_CENTER', 'ASC'), sort_desc: isSort('PROPERTY_COST_CENTER', 'DESC')}">
                            Кост центр
                        </div>
                    </div>
                    <div class="ellipsis_text" @click="setSort('PROPERTY_FUNC1_NAME')" v-if="listSettings.func1.visible">
                        <div class="sortable"
                             :class="{sort_asc: isSort('PROPERTY_FUNC1_NAME', 'ASC'), sort_desc: isSort('PROPERTY_FUNC1_NAME', 'DESC')}">
                            Функция 1
                        </div>
                    </div>
                    <div class="ellipsis_text" @click="setSort('PROPERTY_FUNC2_NAME')" v-if="listSettings.func2.visible">
                        <div class="sortable"
                             :class="{sort_asc: isSort('PROPERTY_FUNC2_NAME', 'ASC'), sort_desc: isSort('PROPERTY_FUNC2_NAME', 'DESC')}">
                            Функция 2
                        </div>
                    </div>
                    <div @click="setSort('PROPERTY_STAGE')" class="statusSelect"
                         v-if="(selected_tab.value == 'my-action' || selected_tab.value == 'active' || selected_tab.value == 'all') && listSettings.stage.visible">
                        <div class="sortable"
                             :class="{sort_asc: isSort('PROPERTY_STAGE', 'ASC'), sort_desc: isSort('PROPERTY_STAGE', 'DESC')}">
                            Этап
                        </div>
                    </div>
                    <div @click="setSort('PROPERTY_APPROVAL_EMPLOYEE')" v-if="listSettings.nameApproval.visible">
                        <div class="sortable"
                             :class="{sort_asc: isSort('PROPERTY_STATUS', 'ASC'), sort_desc: isSort('PROPERTY_STATUS', 'DESC')}">
                            Ответственный
                        </div>
                    </div>
                    <div @click="setSort('PROPERTY_STATUS')" v-if="listSettings.status.visible">
                        <div class="sortable"
                             :class="{sort_asc: isSort('PROPERTY_STATUS', 'ASC'), sort_desc: isSort('PROPERTY_STATUS', 'DESC')}">
                            Статус
                        </div>
                    </div>
                    <div @click="setSort('PROPERTY_FINISHED_AGREEMENT')" v-if="(selected_tab.value === 'approved' || selected_tab.value === 'all') && listSettings.approve.visible">
                        <div class="sortable"
                             :class="{sort_asc: isSort('PROPERTY_FINISHED_AGREEMENT', 'ASC'), sort_desc: isSort('PROPERTY_FINISHED_AGREEMENT', 'DESC')}">
                            Утвержден
                        </div>
                    </div>
                    <div v-if="(selected_tab.value === 'my-action') && canGetToWork" class="remove-action">
                    </div>
                </div>
                <div class="tasc-list__tr" v-for="item in list" v-if="!item.deleted"
                     @click="redirectToElement(item, $event)">
                    <div class="check" v-if="isOD" @click.stop>
                        <renins-checkbox v-model="checkItems[ item.ID ]" @click.stop></renins-checkbox>
                    </div>

                    <div class="id" v-if="listSettings.ID.visible == true">{{ item.ID }}</div>
                    <div class="ellipsis_text" :title="item.PROPERTY_NAZVANIE_DOLZHNOSTI_VALUE" v-if="listSettings.job.visible == true">{{
                        item.PROPERTY_NAZVANIE_DOLZHNOSTI_VALUE }}
                    </div>
                    <div class="ellipsis_text cost" v-if="listSettings.costCenter.visible == true">{{ item.PROPERTY_COST_CENTER_VALUE }}</div>
                    <div class="ellipsis_text func" v-if="listSettings.func1.visible == true">{{ item.PROPERTY_FUNC1_NAME_VALUE }}</div>
                    <div class="ellipsis_text func" v-if="listSettings.func2.visible == true">{{ item.PROPERTY_FUNC2_NAME_VALUE }}</div>
                    <div class="ellipsis_text"
                         v-if="(selected_tab.value == 'my-action' || selected_tab.value == 'active' || selected_tab.value == 'all') && (listSettings.stage.visible == true)">{{ item.STAGE }}
                    </div>
                    <div class="ellipsis_text approve_cell" v-if="listSettings.nameApproval.visible == true">
                        <renins-tooltip v-if="item.processingUser">
                            <template v-slot:main class="approve_cell" >
                                {{item.processingUser.fio}}
                            </template>
                            <template v-slot:tooltip>
                                <div class='row' >
                                    <div class='col-2' v-if="item.processingUser.photo">
                                        <div class='field'><renins-avatar class="lg" :user-id="item.processingUser.id"></renins-avatar></div>
                                    </div>
                                    <div class='col-10' v-if="item.processingUser.fio">
                                        <div class='field'>ФИО</div>{{ item.processingUser.fio }}
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class='col-12' v-if="item.processingUser.position">
                                        <div class='field'>Должность</div>{{ item.processingUser.position }}
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class='col-12' v-if="item.processingUser.department">
                                        <div class='field'>Подразделение</div>{{ item.processingUser.department }}
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class='col-12' v-if="item.processingUser.email">
                                        <div class='field'>Почта</div><renins-link :href="'mailto:' + item.processingUser.email">{{ item.processingUser.email }}</renins-link>
                                    </div
                                </div>
                                <div class='row'>
                                    <div class='col-12' v-if="item.processingUser.phone">
                                        <div class='field'>Телефон</div><renins-link href="'phoneto' + item.processingUser.phone">{{ item.processingUser.phone }}</renins-link>
                                    </div
                                </div>
                            </template>
                        </renins-tooltip>
                        <div v-else>N/D</div>
                    </div>
                    <div style="width: 50px;" v-if="listSettings.status.visible == true">
                        <renins-badge-label class="nowrap" :class="item.STATUS_CLASS">{{ item.STATUS }}
                        </renins-badge-label>
                    </div>
                    <div style="width: 50px;" v-if="(selected_tab.value === 'approved' || selected_tab.value === 'all') && listSettings.approve.visible">{{
                        item.PROPERTY_FINISHED_AGREEMENT_FORMATTED }}
                    </div>
                    <div v-if="(selected_tab.value === 'my-action') && canGetToWork" class="remove-action">
                        <renins-pad-icon v-if="item.canGetToWork && !item.processingUser"
                                         class="xs transparent action-btn" tooltip="Взять в работу"
                                         @click.stop="getToWork(item.ID)" :data-id="item.ID">
                            <renins-icon class="plus-square"></renins-icon>
                        </renins-pad-icon>
                        <renins-pad-icon v-if="item.canGetToWork && item.processingUser"
                                         class="xs transparent action-btn" tooltip="Делегировать"
                                         @click.stop="isShowDelegateModal = true; processingUsers = item.processingUsers; processingItemId = item.ID; processingUserId = item.processingUser.id">
                            <renins-icon class="arrow-right-circle"></renins-icon>
                        </renins-pad-icon>
                    </div>
                </div>
            </div>
            <div v-else class="empty_list">
                <template v-if="selected_tab.value == 'closed'">В настоящий момент в папке нет заявок</template>
                <template v-else>В настоящий момент в папке нет активных заявок</template>
            </div>
            <div class="r-mt-9" v-if="rowsCount > perPage">
                <!--<renins-pagination :records="rowsCount" v-model="currentPage" :per-page="perPage" :options="{}" @input="loadList()"></renins-pagination>-->
            </div>
        </div>
        <div v-else style="margin-top: 79px;" class="loader-overlay">
            <div class="spinner"></div>
        </div>

        <renins-modal v-if="showDelElementModal" @close="showDelElementModal = false">
            <template #head>Удаление заявок</template>
            <template #body>
                <div>Вы действительно хотите удалить выбранные заявки?</div>
                <div style="padding-top: 24px">
                    <renins-button class="primary md float-left" style="margin-right: 16px" @click="removeItems()"
                                   :loading="deleting">Удалить
                    </renins-button>
                    <renins-button class="secondary md float-left" @click="showDelElementModal = false">Отмена
                    </renins-button>
                </div>
            </template>
        </renins-modal>

        <renins-modal v-if="showCopyElementModal" @close="showCopyElementModal = false">
            <template #head>Дублирование заявок</template>
            <template #body>
                <div>Выбранные заявки буду дублированы. Запустить дублирование?</div>
                <div style="padding-top: 24px">
                    <renins-button class="primary md float-left" style="margin-right: 16px" @click="copyItems()"
                                   :loading="copying">Дублировать
                    </renins-button>
                    <renins-button class="secondary md float-left" @click="showCopyElementModal = false">Отмена
                    </renins-button>
                </div>
            </template>
        </renins-modal>

        <renins-modal v-show="isShowDelegateModal" @close="isShowDelegateModal = false">
            <template #head>Изменение согласующего</template>
            <template #body>
                Укажи сотрудника, которому будет назначена заявка
                <div style="margin-top: 16px">
                    <renins-select
                            placeholder="Введи ФИО сотрудника"
                            v-model="delegateData.processingUser"
                            v-bind:items="filteredProcessingUsers"
                            :error="delegateData.error"
                            @input="delegateData.error = null"
                    />
                </div>
                <div style="padding-top: 24px">
                    <renins-button
                            class="primary md float-left"
                            @click="delegate"
                            :loading="isProcessing"
                            style="margin-right: 16px"
                    >Выбрать
                    </renins-button>
                    <renins-button
                            class="secondary md float-left"
                            @click="isShowDelegateModal = false"
                    >Закрыть
                    </renins-button>
                </div>
            </template>
        </renins-modal>

        <renins-modal v-if="showToArchiveElementModal" @close="showToArchiveElementModal = false">
            <template #head>Перенос в архив заявок</template>
            <template #body>
                <div>Выбранные заявки буду отправлены в архив. Запустить перенос в архив?</div>
                <div style="padding-top: 24px">
                    <renins-button class="primary md float-left" style="margin-right: 16px" @click="toArchive()"
                                   :loading="archive">Перенести
                    </renins-button>
                    <renins-button class="secondary md float-left" @click="showToArchiveElementModal = false">Отмена
                    </renins-button>
                </div>
            </template>
        </renins-modal>

    </template>

    <renins-alert ref="alert"></renins-alert>
</div>

<script>
    window.cfg_job_profile = <?=CUtil::PhpToJSObject($arResult['config'])?>;
</script>
