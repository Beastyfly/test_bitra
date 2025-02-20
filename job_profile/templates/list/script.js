BX.ready(function () {
    BX.BitrixVue.createApp({
        data() {
            return {
                list: [],
                requestController: null,
                currentUserId: null,
                currentUserFio: null,
                loading: false,
                isLoading: false,
                tabs_items: [],
                allCount: 0,
                selected_tab: null,
                sort: 'ID',
                order: 'DESC',
                currentPage: 1,
                perPage: 100,
                rowsCount: 0,
                isOD: false,
                canDownloadSLA: false,
                filledId: null,
                createdCostCenters: [],
                createdFunc1: [],
                createdFunc2: [],
                createdStatuses: [],
                createdIDs: [],
                createdPodraz: [],
                searchCostCenter: '',
                searchFunc1: '',
                searchFunc2: '',
                searchStatus: '',
                searchIDs: '',
                searchPodraz: '',
                mounted: false,
                breadcrumbs: [{label: 'Навигатор', url: '/renins-kb/'}, {label: 'Профиль должности '}],
                deleting: false,
                delItem: null,
                checkItems: {},
                showDelElementModal: false,
                isShowDelegateModal: false,
                copying: false,
                archive: false,
                showCopyElementModal: false,
                processingItemId: null,
                processingUserId: null,
                processingUsers: [],
                isProcessing: false,
                isGettingToWork: {},
                delegateData: {
                    processingUser: null,
                    error: null
                },
                showToArchiveElementModal: false,
                listSettings: {
                    ID: {name: 'ID', visible: true},
                    job: {name: 'Должность', visible: true},
                    costCenter: {name: 'Кост-центр', visible: true},
                    func1: {name: 'Функция 1', visible: true},
                    func2: {name: 'Функция 2', visible: true},
                    stage: {name: 'Этап', visible: true},
                    nameApproval: {name: 'Ответственный', visible: false},
                    status: {name: 'Статус', visible: true},
                    approve: {name: 'Утвержден', visible: false}
                },
                isSettingsVisible: false,
            }
        },
        created() {
            let cfg = window.cfg_job_profile;
            this.init(cfg);
        },
        mounted() {

            this.mounted = true;

            if (this.filledId)
                this.$refs.alert.add({
                    autoClose: true,
                    class: 'success',
                    text: 'Профиль должности #' + this.filledId + ' отправлен на проверку OD'
                });

        },
        watch: {
            searchCostCenter(newVal, oldVal) {
                if (newVal !== oldVal) {
                    this.applyFilter();
                }
            },
            searchFunc1(newVal, oldVal) {
                if (newVal !== oldVal) {
                    this.applyFilter();
                }
            },
            searchFunc2(newVal, oldVal) {
                if (newVal !== oldVal) {
                    this.applyFilter();
                }
            },
            searchStatus(newVal, oldVal) {
                if (newVal !== oldVal) {
                    this.applyFilter();
                }
            },
            searchIDs(newVal, oldVal) {
                if (newVal !== oldVal) {
                    this.applyFilter();
                }
            },
            searchPodraz(newVal, oldVal) {
                if (newVal !== oldVal) {
                    this.applyFilter();
                }
            },
            listSettings: {
                handler(newVal) {
                    this.saveColumnSettings();
                },
                deep: true
            }
            /*
            selected_tab(newVal, oldVal) {
                if (newVal !== oldVal) {
                    this.applyFilter();
                }
            },*/
        },
        methods: {
            init: function (data) {
                console.log(data);
                console.log( this.allCount);
                this.tabs_items = data.tabs;
                this.tabs_items.forEach(item => this.allCount += Number(item.counter));
                console.log( this.allCount);
                const params = this.getUrlParams();
                this.loadColumnSettings();
                this.searchCostCenter = params['q1'];
                this.searchFunc1 = params['q2'];
                this.searchFunc2 = params['q3'];
                this.searchStatus = params['q4'];
                this.searchIDs = params['q5'];
                this.searchPodraz = params['q6'];

                this.list = data.list

                this.currentUserId = data.currentUserId;
                this.currentUserFio = data.currentUserFio;
                this.rowsCount = data.rows_count;
                this.perPage = Number(data.perPage);
                this.sort = data.sort;
                this.order = data.order;
                this.isOD = data.isOD;
                this.canDownloadSLA = data.canDownloadSLA;
                this.filledId = data.filledId;
                this.createdCostCenters = data.createdCostCenters;
                this.createdFunc1 = data.createdFunc1;
                this.createdFunc2 = data.createdFunc2;
                this.createdStatuses = data.createdStatuses;
                this.createdIDs = data.createdIDs;
                this.createdPodraz = data.createdPodraz;

                //this.allCount = 0;


                let item = this.tabs_items.find((item) => item.value === data.tab);
                if (!item) {
                    item = this.tabs_items[0];
                }
                this.selected_tab = item;

                this.currentPage = 1;
                this.loading = false;

                window.addEventListener('scroll', this.handleScroll);
            },
            reloadData() {
                let cfg = window.cfg_job_profile;
                this.init(cfg);
            },
            handleScroll: function () {
                // Проверяем, что загрузка не идет и есть еще элементы для загрузки
                if (this.isLoading || this.list.length >= this.rowsCount) return;

                // Вычисляем текущую позицию скролла
                const scrollPosition = window.innerHeight + window.scrollY;
                const pageHeight = document.documentElement.scrollHeight;
/*
                // Если пользователь доскролил до самого конца страницы, вызываем loadMoreItems
                if (scrollPosition >= pageHeight) {
                    this.loadMoreItems();
                }*/
            },
            loadMoreItems: function () {
                if (this.loading) return; // Если уже идет загрузка, выходим

                this.isLoading = true;

                // Увеличиваем текущую страницу
                this.currentPage += 1;

                // Загружаем следующую порцию данных
                let request = BX.ajax.runComponentAction('renins:job_profile', 'listLoadRecords', {
                    mode: 'class',
                    data: {
                        sort: this.sort,
                        order: this.order,
                        pageSize: this.perPage,
                        page: this.currentPage,
                        tab: this.selected_tab.value,
                        q: [
                            this.searchCostCenter,
                            this.searchFunc1,
                            this.searchFunc2,
                            this.searchStatus,
                            this.searchIDs,
                            this.searchPodraz,
                        ],
                    }
                });

                request.then((response) => {
                    // Добавляем новые элементы в существующий список
                    this.list = this.list.concat(response.data.list);
                    this.rowsCount = response.data.rows_count;
                    this.createdCostCenters = response.data.createdCostCenters;
                    this.createdFunc1 = response.data.createdFunc1;
                    this.createdFunc2 = response.data.createdFunc2;
                    this.createdStatuses = response.data.createdStatuses;
                    this.createdPodraz = response.data.createdPodraz;

                    // Если загружено меньше элементов, чем perPage, значит, это последняя страница
                    if (response.data.list.length < this.perPage) {
                        // Отключаем обработчик скролла, если больше нечего загружать
                        window.removeEventListener('scroll', this.handleScroll);
                    }

                    this.isLoading = false;
                }, (response) => {
                    console.log('Ошибка при загрузке данных:', response);
                    this.isLoading = false;
                });
            },
            getUrlParams() {
                const urlParams = new URLSearchParams(window.location.search);
                const paramsArray = {};

                for (const [key, value] of urlParams.entries()) {
                    paramsArray[key] = value;
                }

                return paramsArray;
            },
            clearFilter() {
                this.searchCostCenter = '';
                this.searchFunc1 = '';
                this.searchFunc2 = '';
                this.searchStatus = '';
                this.searchIDs = '';
                this.searchPodraz = '';

                this.applyFilter();
            },
            applyFilter() {
                this.searchCostCenter = this.searchCostCenter ? this.searchCostCenter : '';
                this.searchFunc1 = this.searchFunc1 ? this.searchFunc1 : '';
                this.searchFunc2 = this.searchFunc2 ? this.searchFunc2 : '';
                this.searchStatus = this.searchStatus ? this.searchStatus : '';
                this.searchIDs = this.searchIDs ? this.searchIDs : '';
                this.searchPodraz = this.searchPodraz ? this.searchPodraz : '';
                this.loadList();
                this.setHistory();
            },
            setHistory() {
                const url = `/renins/job_profile/list/?page=${this.currentPage}&tab=${this.selected_tab.value}&perPage=${this.perPage}` +
                    `&q1=${this.searchCostCenter}&q2=${this.searchFunc1}&q3=${this.searchFunc2}` +
                    `&q4=${this.searchStatus}&q5=${this.searchIDs}&q6=${this.searchPodraz}`;
                if (window.history.pushState) {
                    window.history.pushState(
                        {
                            page: this.currentPage,
                            tab: this.selected_tab.value,
                            perPage: this.perPage,
                            q1: this.searchCostCenter,
                            q2: this.searchFunc1,
                            q3: this.searchFunc2,
                            q4: this.searchStatus,
                            q5: this.searchIDs,
                            q6: this.searchPodraz
                        },
                        '',
                        url
                    );
                } else {
                    window.location.assign(url);
                }
            },
            saveColumnSettings() {
                const visibleColumns = {};
                Object.keys(this.listSettings).forEach(key => {
                    visibleColumns[key] = this.listSettings[key].visible;
                });
                localStorage.setItem('columnSettings', JSON.stringify(visibleColumns));
            },
            loadColumnSettings() {
                const savedSettings = localStorage.getItem('columnSettings');
                if (savedSettings) {
                    const visibleColumns = JSON.parse(savedSettings);
                    Object.keys(this.listSettings).forEach(key => {
                        if (visibleColumns.hasOwnProperty(key)) {
                            this.listSettings[key].visible = visibleColumns[key];
                        }
                    });
                }
            },
            redirectToElement(item, event) {
                const status = item.PROPERTY_STATUS_VALUE;
                const stage = item.PROPERTY_STAGE_VALUE;
                const createdBy = item.CREATED_BY;
                const processingUserId = item.processingUser?.id;
                this.setHistory();

                if(item.STATUS === 'Черновик' || item.STATUS === 'Отозван' || item.STATUS === 'На доработке' || item.STATUS === 'Отклонен')
                {
                    if(this.currentUserId === createdBy || this.currentUserId === processingUserId){
                        window.location.href = '/renins/job_profile/?DRAFT=' + item.ID ;
                    }
                    if(this.isOD === true){
                        window.location.href = '/renins/job_profile/?DRAFT=' + item.ID;
                    }
                } else {
                    window.location.href = '/renins/job_profile/' + item.ID + '/';
                }
            },
            isSort(sort, order) {
                return this.sort === sort && this.order === order;
            },
            setSort(sort) {
                if (this.sort === sort) {
                    this.order = this.order == 'ASC' ? 'DESC' : 'ASC';
                } else {
                    this.sort = sort;
                    this.order = 'ASC';
                }
                this.loadList();
            },
            loadList: function () {
                if (this.requestController) {
                    this.requestController.abort(); // Отмена предыдущего запроса
                }
                this.loading = true;
                let currentTab = this.selected_tab

                this.requestController = new AbortController();
                const signal = this.requestController.signal;

                this.loading = true;
                this.currentPage = 1; // Сбрасываем текущую страницу
                this.list = []; // Очищаем список

                let request = BX.ajax.runComponentAction('renins:job_profile', 'listLoadRecords', {
                    mode: 'class',
                    data: {
                        sort: this.sort,
                        order: this.order,
                        pageSize: this.perPage,
                        page: this.currentPage,
                        tab: this.selected_tab.value,
                        q: [
                            this.searchCostCenter,
                            this.searchFunc1,
                            this.searchFunc2,
                            this.searchStatus,
                            this.searchIDs,
                            this.searchPodraz,
                        ],
                    },
                    signal: signal
                });

                request.then((response) => {
                    if (signal.aborted) return;
                    console.log(response)

                    this.list = response.data.list;
                    this.createdCostCenters = response.data.createdCostCenters;
                    this.createdFunc1 = response.data.createdFunc1;
                    this.createdFunc2 = response.data.createdFunc2;
                    this.createdStatuses = response.data.createdStatuses;
                    this.createdIDs = response.data.createdIDs;
                    this.createdPodraz = response.data.createdPodraz;
                    this.checkItems = {};

                    this.rowsCount = response.data.rows_count;

                    let item = this.tabs_items.find((item) => item.value === currentTab.value);
                    this.tabs_items = response.data.tabs
                    this.selected_tab = item;

                    if (item)
                        item.counter = this.rowsCount;
                    else
                        item = this.tabs_items[0];


                    // Если элементов меньше, чем perPage, отключаем обработчик скролла
                    if (this.list.length < this.perPage) {
                        window.removeEventListener('scroll', this.handleScroll);
                    } else {
                        // Иначе добавляем обработчик скролла
                        window.addEventListener('scroll', this.handleScroll);
                    }

                    this.loading = false;
                }, (response) => {
                    console.log('Ошибка при загрузке данных:', response);
                    this.loading = false;
                });
            },
            showDelModal(id) {
                this.delItem = id;
                this.showDelElementModal = true;
            },
            downloadExcel() {
                let itemsToExport = Object.keys(this.checkItems).filter(id => this.checkItems[ id ]);;
                var link = document.createElement('a');
                link.setAttribute('href', '/renins/job_profile/exportAny.php?ID=' + itemsToExport);
                //link.setAttribute('download', 'report.xlsx');
                link.click();
            },
            removeItems() {
                this.deleting = true;
                let itemsToDeleting = Object.keys(this.checkItems).filter(id => this.checkItems[id]);

                let request = BX.ajax.runComponentAction('renins:job_profile', 'removeItems', {
                    mode: 'class',
                    data: {
                        ids: itemsToDeleting,
                        deleting: (this.selected_tab.value === 'trash' ? 'Y' : 'N')
                    }
                });
                request.then((response) => {
                    console.log('response', response);
                    if (!response.data.del)
                        alert('Не все заявки удалены!');

                    let item = this.tabs_items.find((item) => item.value === 'trash');
                    if (item)
                        item.counter = response.data.count;

                    this.checkItems = {};
                    this.showDelElementModal = false;
                    this.deleting = false;
                    this.loadList();
                }, function (response) {
                    alert('Ошибка!');
                    this.deleting = false;
                });
            },
            copyItems() {
                this.copying = true;
                let itemsToCopy = Object.keys(this.checkItems).filter(id => this.checkItems[id]);

                let request = BX.ajax.runComponentAction('renins:job_profile', 'copyItems', {
                    mode: 'class',
                    data: {
                        ids: itemsToCopy
                    }
                });
                request.then((response) => {
                    console.log('response', response);

                    this.checkItems = {};
                    this.showCopyElementModal = false;
                    this.copying = false;
                    this.loadList();
                }, function (response) {
                    alert('Ошибка!');
                    this.copying = false;
                });
            },
            getToWork(id) {
                // Взять заявку в работу
                let icon = $('[data-id=' + id + '] .renins-icon').addClass('color-gray');

                let request = BX.ajax.runComponentAction('renins:job_profile', 'getToWork', {
                    mode: 'class',
                    data: {
                        id: id
                    }
                });
                request.then(() => {
                    icon.removeClass('color-gray');
                    this.list = this.list.map(item => {
                        if (item.ID === id) {
                            item.processingUser = {
                                id: this.currentUserId,
                                fio: this.currentUserFio
                            };
                            item.STATUS_CLASS = 'success';
                            item.STATUS = 'В работе';
                        }
                        return item;
                    });
                    console.log(this.list);
                }, function (response) {
                    console.log(response);
                    icon.removeClass('color-gray');
                    alert('Ошибка!');
                });
            },
            delegate() {
                // Делегировать заявку сотруднику

                if (!this.delegateData.processingUser) {
                    this.delegateData.error = 'Выберите сотрудника'
                    return false;
                } else {
                    this.delegateData.error = null;
                }

                this.isProcessing = true;
                let request = BX.ajax.runComponentAction('renins:job_profile', 'delegate', {
                    mode: 'class',
                    data: {
                        id: this.processingItemId,
                        userId: this.delegateData.processingUser
                    }
                });
                request.then((response) => {
                    this.isProcessing = false;
                    this.isShowDelegateModal = false;

                    this.delegateData.processingUser = null;
                    this.list = this.list.map(item => {
                        if (item.ID === this.processingItemId) {
                            item.deleted = true;
                            item.processingUser = {
                                id: response.data.id,
                                fio: response.data.fio
                            };
                        }
                        return item;
                    });

                    let item = this.tabs_items.find((item) => item.value === this.selected_tab.value);
                    if (item)
                        item.counter -= 1;
                }, function (response) {
                    console.log(response);
                    this.isProcessing = false;
                    alert('Ошибка!');
                });
            },
            toArchive(){
                this.archive = true;
                let itemsArchive = Object.keys(this.checkItems).filter(id => this.checkItems[id]);

                let request = BX.ajax.runComponentAction('renins:job_profile', 'addProfileToArchive', {
                    mode: 'class',
                    data: {
                        elementIdS: itemsArchive
                    }
                })
                request.then((response) => {
                    console.log('response', response);

                    this.checkItems = {};
                    this.showToArchiveElementModal = false;

                    this.archive = false;

                    this.loadList();
                }, function (response) {
                    alert('Ошибка!');
                    this.archive = false;
                });
            },
            toggleAllItems(value) {
                const list = this.list || [];
                list.forEach(item => {
                    this.$set(this.checkItems, item.ID, value);
                });
            },
            toggleColumnVisibility(columnName) {
                const column = this.listSettings[columnName];
                if (column) {
                    column.visible = !column.visible;
                    this.saveColumnSettings(); // Сохраняем настройки в localStorage
                }
            },
            toggleSettings() {
                this.isSettingsVisible = !this.isSettingsVisible;
                if (this.isSettingsVisible) {
                    // Добавляем обработчик клика вне элемента
                    document.addEventListener('click', this.handleClickOutside);
                } else {
                    // Убираем обработчик, если блок скрыт
                    document.removeEventListener('click', this.handleClickOutside);
                }
            },
            handleClickOutside(event) {
                const settingsBlock = this.$el.querySelector('.settings-block');
                const button = this.$el.querySelector('.renins-button');
                // Проверяем, был ли клик вне блока и кнопки
                if (settingsBlock && !settingsBlock.contains(event.target) && !button.contains(event.target)) {
                    this.isSettingsVisible = false;
                    document.removeEventListener('click', this.handleClickOutside);
                }
            },
        },
        computed: {
            filteredProcessingUsers() {
                return this.processingUsers.filter(item => parseInt(item.value) !== parseInt(this.processingUserId));
            },
            canDeleting() {
                // Если ничего не выбрано, то нельзя удалить
                if ((Object.keys(this.checkItems).length === 0)
                    || (Object.values(this.checkItems).indexOf(true) < 0))
                    return false;

                // Если выбраны те, которые можно удалить
                return this.list.every(item =>
                    (!this.checkItems.hasOwnProperty(item.ID)
                        || !this.checkItems[item.ID]
                        || item.canDelete));
            },
            canCopying() {
                // Если ничего не выбрано, то нельзя дублировать
                if ((Object.keys(this.checkItems).length === 0)
                    || (Object.values(this.checkItems).indexOf(true) < 0))
                    return false;
                // Проверяем, что выбран только один элемент
                const selectedItemsCount = Object.values(this.checkItems).filter(checked => checked).length;
                return true;
            },
            canArchive() {
                // Если ничего не выбрано, то нельзя архивировать
                if ((Object.keys(this.checkItems).length === 0)
                    || (Object.values(this.checkItems).indexOf(true) < 0))
                    return false;

                return true;
            },
            canGetToWork() {
                return this.list.some(item => item.canGetToWork);
            },
            isAnyCheckboxSelected() {
                return Object.values(this.checkItems).some(checked => checked);
            },
            isAllChecked: {
                get() {
                    const list = this.list || [];
                    return list.length > 0 && list.every(item => this.checkItems[item.ID]);
                },
                set(value) {
                    this.toggleAllItems(value);
                }
            },
            isIndeterminate() {
                const list = this.list || [];
                return !this.isAllChecked && list.some(item => this.checkItems[item.ID]);
            },
            visibleColumns() {
                return this.listSettings.filter(setting => setting.visible);
            },
        }
    }).mount('#job_profile');
});
