BX.ready(function () {
    BX.BitrixVue.createApp({
        data() {
            return {
                comboboxItems: [
                    {
                        label: 'Группа 1',
                        type: 'group'
                    },
                    {
                        value: 'В',
                        label: 'В'
                    },
                    {
                        value: 'Я',
                        label: 'Я'
                    },
                    {
                        label: 'Группа 2',
                        type: 'group'
                    },
                    {
                        value: 'D',
                        label: 'D'
                    },
                    {
                        value: 'D2',
                        label: 'D2'
                    },
                    {
                        value: 'D3',
                        label: 'D3'
                    },
                    {
                        value: 'D4',
                        label: 'D4'
                    },
                    {
                        value: 'D5',
                        label: 'D5'
                    },
                    {
                        value: 'D6',
                        label: 'D6'
                    },
                    {
                        value: 'D7',
                        label: 'D7'
                    },
                    {
                        value: 'D8',
                        label: 'D8'
                    },
                ],
                combobox1: 'D',
                dragItems: [],// Drag-and-Drop step4
                dragItemsAdd: [],// Drag-and-Drop step4
                isAccessPage: false,
                currentStep: 1,
                currentStepLast: null,
                currentUserId: null,
                isOD: false,
                isFiller: false,
                isApprover: false,
                isTnD: false,
                isCnB: false,
                initiator: null,
                inTrash: false,
                status: null,
                statusId: null,
                statusClass: null,
                stage: null,
                stageId: null,
                createDate: null,
                updateDate: null,
                processingUser: {},
                processingUsers: {},
                work: null,
                roleSLA: null,
                editStageFormData: {
                    ID: '',
                    TITLE: '',
                    RESPONSIBLE_USER: ''
                },
                modelRole: [],
                stages: [],
                isFullyApproved: false,
                costCenters: [],
                departments: [],
                diFiles: [],
                selectDiFile: null,
                branches: [],
                locations: [],
                returningStages: [],

                breadcrumbs: [],
                defaultBreadcrumbs: [
                    {label: 'HR-сервисы', url: '/renins-kb/'},
                    {label: 'Профиль должности', url: '/renins/job_profile/list/'},
                ],
                step_sections: ['HR OD', 'Руководитель', 'T&D', 'C&B'],
                current_step_section: 'HR OD',
                collapsed: {
                    stages: true,
                },
                showAddForm: false,
                addFormData: {
                    users: []
                },
                approve_comment: null,
                reject_comment: null,
                revoke_comment: null,
                return_comment: null,
                return_stage: null,
                isAddFormSaving: false,
                isSaving: false,
                isSending: false,
                isDeleting: false,
                isApproving: false,
                isRejecting: false,
                isRevoking: false,
                isProcessing: false,
                isReturning: false,
                isSaveButtonDisabled: false,
                allowedSend: false,
                allowedChangeStages: false,
                allowedApprove: false,
                allowedApproveEarlier: false,
                allowedGetToWork: false,
                allowedRevoke: false,
                isShowStageModal: false,
                isShowDeleteModal: false,
                isShowApproveModal: false,
                isShowRejectModal: false,
                isShowRevokeModal: false,
                isShowReturnModal: false,
                isShowSuccessfullyApprovedModal: false,
                isShowSuccessfullyRejectedModal: false,
                isShowSuccessfullyRevokedModal: false,
                isShowSuccessfullyArchivedModal: false,
                isShowSuccessfullyReturnedModal: false,
                isShowDownloadDIModal: false,
                delegateData: {
                    processingUser: [],
                    error: null
                },

                isShowEditModal: {
                    1: false,
                    2: false,
                    3: false,
                    4: false,
                    5: false,
                    6: false,
                    7: false,
                    8: false,
                    9: false,
                    10: false,
                    11: false,
                    12: false,
                    13: false,
                    14: false,
                    15: false,
                    16: false
                },
                errors: {
                    1: {},
                    2: {},
                    3: {},
                    4: {},
                    5: {},
                    6: {},
                    7: {},
                    8: {},
                    9: {},
                    10: {},
                    11: {},
                    12: {},
                    13: {},
                    14: {},
                    15: {},
                    16: {}
                },

                formData: {},
                formDataEdit: {},
                b2bClients: [
                    {
                        value: 'Крупные',
                        label: 'Крупные'
                    },
                    {
                        value: 'Средние',
                        label: 'Средние'
                    },
                    {
                        value: 'Мелкие',
                        label: 'Мелкие'
                    }
                ],
                b2cClients: [
                    {
                        value: 'VIP',
                        label: 'VIP'
                    },
                    {
                        value: 'Средние',
                        label: 'Средние'
                    },
                    {
                        value: 'Мелкие',
                        label: 'Мелкие'
                    }
                ],
                otherClients: [
                    {
                        value: 'Гос. органы',
                        label: 'Гос. органы'
                    },
                    {
                        value: 'Общественные организации',
                        label: 'Общественные организации'
                    },
                    {
                        value: 'Партнеры',
                        label: 'Партнеры'
                    },
                    {
                        value: 'Дилеры',
                        label: 'Дилеры'
                    },
                    {
                        value: 'Агенты',
                        label: 'Агенты'
                    }
                ],
                englishLevels: [
                    {
                        value: 'Не обязателен',
                        label: 'Не обязателен'
                    },
                    {
                        value: 'Elementary (A1)',
                        label: 'Elementary (A1)'
                    },
                    {
                        value: 'Pre-intermediate (A2)',
                        label: 'Pre-intermediate (A2)'
                    },
                    {
                        value: 'Intermediate (B1)',
                        label: 'Intermediate (B1)'
                    },
                    {
                        value: 'Upper-intermediate (B2)',
                        label: 'Upper-intermediate (B2)'
                    },
                    {
                        value: 'Advanced (C1)',
                        label: 'Advanced (C1)'
                    },
                    {
                        value: 'Proficiency (C2)',
                        label: 'Proficiency (C2)'
                    },
                ],
                competencesQuestions: [
                    {id: 'q1', text: 'Учитывает мотивы, чувства и потребности окружающих'},
                    {id: 'q2', text: 'Предвосхищает потребности'},
                    {id: 'q3', text: 'Неравнодушен к проблемам других, оказывает помощь'},
                    {id: 'q4', text: 'Выходит за рамки инструкций'},
                    {id: 'q5', text: 'Оперативно реагирует на запросы, выполняет взятые обязательства'},
                    {id: 'q6', text: 'Озвучивает мысли ясно и понятно'},
                    {id: 'q7', text: 'Объясняет причины отказа, предлагает решения'},
                    {id: 'q8', text: 'Качественно анализирует и синтезирует информацию'},
                    {id: 'q9', text: 'Опирается на данные и аналитику'},
                    {id: 'q10', text: 'Предотвращает возможные риски'},
                    {id: 'q11', text: 'Пилотирует решения'},
                    {id: 'q12', text: 'Честен и открыт с окружающими'},
                    {id: 'q13', text: 'Настойчив в достижении цели'},
                    {id: 'q14', text: 'Берет ответственность за решения'},
                    {id: 'q15', text: 'Действует для изменения ситуации'},
                    {id: 'q16', text: 'Развивается и самосовершенствуется'},
                    {id: 'q17', text: 'Ставит перед собой новые амбициозные цели'},
                    {id: 'q18', text: 'Изучает новые технологии'},
                    {id: 'q19', text: 'Внедряет новые подходы'},
                    {id: 'q20', text: 'Привлекает в команду сильных людей'},
                    {id: 'q21', text: 'Вносит предложения по улучшению процессов и регламентов смежных подразделений'},
                    {id: 'q22', text: 'Ориентируется на цели и интересы компании'},
                    {id: 'q23', text: 'Сотрудничает с коллегами, нацелен на общий результат'},
                    {id: 'q24', text: 'Учится на ошибках'},
                    {id: 'q25', text: 'Поддерживает и помогает другим в развитии'},
                    {id: 'q26', text: 'Дает обратную связь'},
                    {id: 'q27', text: 'Принимает обратную связь'},
                    {id: 'q28', text: 'Уважает время и ресурсы коллег'},
                    {id: 'q29', text: 'Своевременно отвечает на вопросы окружающих'},
                ],
                updatingStages: {},
                deletingStages: {},
                requiredFields: [],
                validatedSteps: [],
                downloadDIError: false,
            }
        },
        created() {
            let cfg = window.cfg_job_profile;
            this.init(cfg);
            this.getItemsList()
        },
        methods: {
            init: function (data) {
                console.log('data', data);
                this.currentUserId = data.currentUserId;
                this.isOD = data.isOD;
                this.isFiller = data.isFiller;
                this.isApprover = data.isApprover;
                this.isTnD = data.isTnD;
                this.isCnB = data.isCnB;

                this.initiator = data.initiator;
                this.inTrash = data.inTrash;
                this.status = data.status;
                this.statusId = data.statusId;
                this.statusClass = data.statusClass;
                this.stage = data.stage;
                this.stageId = data.stageId;
                this.stages = data.stages;
                this.isFullyApproved = data.isFullyApproved;
                this.processingUser = data.processingUser;
                this.processingUsers = data.processingUsers;
                this.createDate = data.createDate;
                this.updateDate = data.updateDate;
                this.modelRole = data.modelRole;
                this.work = data.work;
                this.roleSLA = data.roleSLA;
                this.allowedChangeStages = data.allowedChangeStages;
                this.allowedApprove = data.allowedApprove;
                this.allowedApproveEarlier = data.allowedApproveEarlier;
                this.allowedGetToWork = data.allowedGetToWork;
                this.allowedRevoke = data.allowedRevoke;
                this.costCenters = data.costCenters;
                this.departments = data.departments;
                this.diFiles = data.diFiles;
                this.branches = data.branches;
                this.locations = data.locations;
                this.returningStages = data.returningStages;
                this.isAccessPage = data.isAccessPage;

                if (this.isFiller && (this.stageId == 'filling') && (this.statusId != 'trash'))
                    window.location.href = '/renins/job_profile/?DRAFT=' + data.formData.id;

                // Загрузка данных в форму
                for (const property in data.formData) {
                    if (data.formData.hasOwnProperty(property))
                        this.formData[property] = data.formData[property];
                }

                if (!this.formData.competencies)
                    this.formData.competencies = {};
                this.formData.checksCompetencies = {};
                this.formData.compTableIndex = {};
                let i = 1;
                for (let index in this.competencesQuestions) {
                    let id = this.competencesQuestions[index].id;
                    if (this.formData.competencies.hasOwnProperty(id) && this.formData.competencies[id]) {
                        this.formData.compTableIndex[id] = i++;
                        this.formData.checksCompetencies[id] = true;
                    }
                }

                this.breadcrumbs = [...this.defaultBreadcrumbs, {label: 'ID ' + this.formData.id}];

                this.copyFormForEdit();

                // Удаление кнопки блока C&B если нет доступа к блоку
                if ((parseInt(this.modelRole[3]) === 2) && (this.step_sections.length === 4))
                    this.step_sections.pop();

                // Переходы сразу на конкретные шаги, если шаг не записан
                if (!this.currentStepLast) {
                    if (this.isOD)
                        this.current_step_section = 'HR OD';
                    else if (this.isFiller || this.isApprover)
                        this.current_step_section = 'Руководитель';
                    else if (this.isTnD)
                        this.current_step_section = 'T&D';
                    // Переход сразу на этап C&B при возможности редактировать
                    else if (this.isCnB || (this.modelRole[3] === true))
                        this.current_step_section = 'C&B';
                }

                if (!this.formDataEdit.languages)
                    this.formDataEdit.languages = [];
                if (this.formDataEdit.languages.length < 1)
                    this.formDataEdit.languages = [
                        {name: '', level: ''},
                    ];

                if (!this.formDataEdit.review)
                    this.formDataEdit.review = [];
                if (this.formDataEdit.review.length < 1)
                    this.formDataEdit.review = [
                        {name: '', code: ''},
                    ];

                this.requiredFields = data.requiredFields;
            },
            //--Функционал Drag-and-Drop--
            getItemsList() {
                if (this.formData.mainDuties && !Array.isArray(this.formData.mainDuties)) {
                    this.formData.mainDuties = Object.values(this.formData.mainDuties);
                }

                let arrRes = []
                this.formData.mainDuties.forEach(function (elem, ind) {
                    let promRes = {
                        'duty': elem.duty,
                        'result': elem.result,
                        'id': ind
                    }
                    arrRes.push(promRes)
                })
                this.dragItems = arrRes

                if (this.formData.addDuties && !Array.isArray(this.formData.addDuties)) {
                    this.formData.addDuties = Object.values(this.formData.addDuties);
                }

                arrRes = []
                this.formDataEdit.addDuties.forEach(function (elem, ind) {
                    let promRes = {
                        'duty': elem.duty,
                        'result': elem.result,
                        'id': ind
                    }
                    arrRes.push(promRes)
                })
                this.dragItemsAdd = arrRes
            },
            handleDropEvent(newItems, targetIndex) {
                this.dragItems = newItems;
            },
            handleDropEventAdd(newItems, targetIndex) {
                this.dragItemsAdd = newItems;
            },
            //--Функционал Drag-and-Drop--
            copyFormForEdit() {
                // Копирование данных формы для редактирования
                this.formDataEdit = JSON.parse(JSON.stringify(this.formData));
            },
            toggle(value) {
                this.$set(this.collapsed, value, !Boolean(this.collapsed[value]));
                console.log(this.collapsed[value]);
            },
            isCollapsed(value) {
                if (!this.collapsed.hasOwnProperty(value)) return false;
                return Boolean(this.collapsed[value]);
            },
            close(block) {
                this.isShowEditModal[block] = false;
                this.copyFormForEdit();
                this.errors[block] = {};
            },
            redirectToList() {
                window.location.href = document.referrer;
               // window.location.href = '/renins/job_profile/list/';
            },
            getStepClass(i) {
                return {active: this.currentStep === i, success: this.currentStep > i};
            },
            addDepartmentGoal() {
                this.formDataEdit.departmentGoals.push('');
            },
            addPositionGoal() {
                this.formDataEdit.positionGoals.push('');
            },
            addMainDuty() {
                this.formDataEdit.mainDuties.push({duty: '', result: '', id: ''});
                this.dragItems.push({duty: '', result: '', id: ''});
            },
            addAdditionalDuty() {
                this.formDataEdit.addDuties.push({duty: '', result: '', id: ''});
                this.dragItemsAdd.push({duty: '', result: '', id: ''});
            },
            addLanguage() {
                this.formDataEdit.languages.push({name: '', level: ''});
            },
            addReview() {
                this.formDataEdit.review.push({name: '', code: ''});
            },
            addApprover() {
                if (!this.formDataEdit.addApprovers)
                    this.formDataEdit.addApprovers = [];
                this.formDataEdit.addApprovers.push('');
            },
            removeApprover(index) {
                this.formDataEdit.addApprovers.splice(index, 1);
            },
            addObserver() {
                if (!this.formDataEdit.addObservers)
                    this.formDataEdit.addObservers = [];
                this.formDataEdit.addObservers.push('');
            },
            removeObserver(index) {
                this.formDataEdit.addObservers.splice(index, 1);
            },
            showStageModal(stage) {
                this.editStageFormData.ID = stage.ID;
                this.editStageFormData.RESPONSIBLE_USER = stage.RESPONSIBLE_USER;
                this.isShowStageModal = true;
            },
            updateResponsibleUser(stageId, value) {
                this.updatingStages = {...this.updatingStages, [stageId]: true}
                let request = BX.ajax.runComponentAction('renins:job_profile', 'updateStage', {
                    mode: 'class',
                    data: {
                        entityId: this.formData.id,
                        stageId: stageId,
                        data: {
                            RESPONSIBLE_USER: value
                        },
                    }
                });
                request.then((response) => {
                    console.log(response);
                    this.stages = response.data;

                    // Обновим поле с ответственным
                    let stage = this.stages.find(item => item.ID === stageId);
                    if (stage.NAME === this.stage)
                        this.processingUser = {
                            id: value,
                            fio: stage.RESPONSIBLE_USER_NAME
                        };

                    this.updatingStages = {...this.updatingStages, [stageId]: false}
                    this.isShowStageModal = false;
                    this.sendNotifyChangeApprove()
                }, function (response) {
                    console.log(response);
                    this.updatingStages = {...this.updatingStages, [stageId]: false}
                    alert('Ошибка!');
                });
            },
            stepHasErrors(step) {
                let errorsCount = 0;
                const requiredFields = Object.keys(this.requiredFields).filter(key => this.requiredFields[key].step == step);
                requiredFields.forEach(key => {
                    if (this.errors[step][key]) {
                        errorsCount++;
                    }
                });
                return errorsCount > 0;
            },
            validateStep(step, revalidate) {
                // Валидация формы
                let errorsCount = 0;
                const requiredFields = Object.keys(this.requiredFields).filter(key => {
                    if (revalidate && !this.errors[step].hasOwnProperty(key)) {
                        return false;
                    }

                    if (this.requiredFields[key].step == step) {
                        return true;
                    }
                    return false;
                });
                console.log('requiredFields', requiredFields);
                requiredFields.forEach(key => {
                    if (this.errors[step].hasOwnProperty(key)) {
                        delete this.errors[step][key];
                    }
                    let relHasValue = false;
                    let fieldValue = this.formDataEdit[key];
                    const hasRel = Boolean(this.requiredFields[key].rel) || this.requiredFields[key].relFields.length > 0; // Зависиме поле
                    if (hasRel && this.requiredFields[key].rel) {
                        const relValue = this.formDataEdit[this.requiredFields[key].rel];
                        if (this.requiredFields[key].relValue.length) {
                            relHasValue = this.requiredFields[key].relValue.includes(relValue);
                        } else {
                            relHasValue = Boolean(relValue);
                        }
                    }

                    this.requiredFields[key].relFields.forEach(item => {
                        const relValue = this.formDataEdit[item];
                        if (relValue) {
                            relHasValue = true;
                        }
                    })

                    // Если массив (цели или обязанности)
                    if (Array.isArray(fieldValue)) {
                        fieldValue = fieldValue.filter(v => {
                            if (typeof v === 'object' && v.hasOwnProperty('duty')) {
                                return v.duty && v.result;
                            }
                            return v;
                        }).join('');
                    }

                    // Если объект (компетенции на 14 шаге)
                    if (!Array.isArray(fieldValue) && fieldValue instanceof Object) {
                        fieldValue = Object.keys(fieldValue).filter(item => fieldValue[item]).length >= this.requiredFields[key].minObjValues;
                    }

                    // Проверка заполнение одного из полей
                    this.requiredFields[key].radioFields.forEach(field => {
                        if (!fieldValue) {
                            fieldValue = this.formDataEdit[field];
                        }
                    });

                    // Провезка зависимых полей
                    if (hasRel) {
                        if (!fieldValue && relHasValue) {
                            this.errors[step][key] = true
                            //console.log(`${key}: обязательное для заполнения (зависимое)`);
                            errorsCount++;
                        }
                    } else if (!fieldValue) {
                        this.errors[step][key] = true
                        // console.log(`${key}: обязательное для заполнения`);
                        errorsCount++;
                    }
                });
                this.errors = {...this.errors};

                // Помечаем что шаг проходил проверку
                if (!revalidate && !this.validatedSteps.includes(step)) {
                    this.validatedSteps.push(step);
                }
                console.log('this.errors', this.errors);
                return errorsCount === 0;
            },
            revalidateStep(step) {
                this.validateStep(step, true);
            },
            save(block) {
                console.log('djn')

                console.log(this.dragItems)
                this.formDataEdit.mainDuties = this.dragItems
                this.formDataEdit.addDuties = this.dragItemsAdd
                if (!this.validateStep(block)) {
                    this.isSaveButtonDisabled = true;
                    return;
                }

                for (let index in this.competencesQuestions) {
                    let id = this.competencesQuestions[index].id;

                    console.log(this.competencesQuestions[index]);
                    console.log(this.formDataEdit.competencies[id]);

                    if (!this.formDataEdit.checksCompetencies.hasOwnProperty(id)
                        || !this.formDataEdit.checksCompetencies[id])
                        this.formDataEdit.competencies[id] = null;
                }

                if (!this.formDataEdit.hasSubs) {
                    this.formDataEdit.subordinatesCount = 0;
                    this.formDataEdit.allSubordinatesCount = 0;
                }

                if (!this.formDataEdit.hasFuncSubs) this.formDataEdit.funcSubordinatesCount = 0;
                if (!this.formDataEdit.hasProjectSubs) this.formDataEdit.projectSubordinatesCount = 0;
                if (!this.formDataEdit.hasOutsourceSubs) this.formDataEdit.outsourceSubordinatesCount = 0;

                if (this.formDataEdit.b2bClients.length === 0)
                    this.formDataEdit.b2bClients = [''];

                if (this.formDataEdit.b2cClients.length === 0)
                    this.formDataEdit.b2cClients = [''];

                if (this.formDataEdit.otherClients.length === 0)
                    this.formDataEdit.otherClients = [''];

                if (this.formDataEdit.hasOwnProperty('addObservers') && (this.formDataEdit.addObservers.length === 0))
                    this.formDataEdit.addObservers = [''];

                const formData = this.getDifData();
                if (!formData || Object.keys(formData).length === 0) {
                    this.close(block);
                    return;
                }

                this.currentStepLast = this.currentStep;
                this.isSaving = true;
                formData.id = this.formData.id;
                formData.editMode = true;
                let request = BX.ajax.runComponentAction('renins:job_profile', 'save', {
                    mode: 'class',
                    data: {
                        formData: formData
                    }
                });
                request.then((response) => {
                    this.load(() => {
                        this.isSaving = false;
                        this.close(block);
                        this.currentStep = this.currentStepLast;
                    });
                }, function (response) {
                    console.log(response);
                    this.isSaving = false;
                    alert('Ошибка!');
                });
            },
            load(callback) {
                let request = BX.ajax.runComponentAction('renins:job_profile', 'loadDetail', {
                    mode: 'class',
                    data: {
                        id: this.formData.id
                    }
                });
                request.then((response) => {
                    console.log('response', response);
                    this.init(response.data);
                    if (typeof callback === 'function') {
                        callback(response.data);
                    }
                }, function (response) {
                    console.log(response);
                    alert('Ошибка!');
                });
            },
            getDifData() {
                // Фильтруем измененные поля
                return Object.keys(this.formDataEdit).reduce((acc, key) => {
                    if (key === 'employeeObject') {
                        return acc;
                    }
                    if ((['addApprovers', 'addObservers', 'b2bClients', 'b2cClients', 'departmentGoals',
                        'otherClients', 'positionGoals', 'checksCompetencies']).indexOf(key) >= 0) {
                        if (this.formDataEdit[key].length !== this.formData[key].length)
                            return {...acc, [key]: this.formDataEdit[key]}

                        for (let i = 0; i < this.formDataEdit[key].length; i++) {
                            if (!this.formData[key].hasOwnProperty(i)
                                || (this.formDataEdit[key][i] !== this.formData[key][i]))
                                return {...acc, [key]: this.formDataEdit[key]}
                        }
                    } else if ((['addDuties', 'review', 'languages', 'mainDuties']).indexOf(key) >= 0) {
                        if (!this.isArrayEmpty(this.formDataEdit[key])
                            && !this.isArrayEmpty(this.formData[key])
                            && (this.formDataEdit[key].length !== this.formData[key].length))
                            return {...acc, [key]: this.formDataEdit[key]}

                        for (let i = 0; i < this.formDataEdit[key].length; i++) {
                            if (!this.formData[key].hasOwnProperty(i))
                                return {...acc, [key]: this.formDataEdit[key]}

                            for (let item in this.formDataEdit[key][i]) {
                                if (this.formDataEdit[key][i][item] !== this.formData[key][i][item])
                                    return {...acc, [key]: this.formDataEdit[key]}
                            }
                        }
                    } else if (key === 'competencies') {
                        for (const comp in this.formDataEdit[key]) {
                            if (this.formDataEdit[key][comp] !== this.formData[key][comp])
                                return {...acc, [key]: this.formDataEdit[key]}
                        }
                    } else if (this.formDataEdit[key] !== this.formData[key]) {
                        return {...acc, [key]: this.formDataEdit[key]}
                    }
                    return acc;
                }, {});
            },
            isArrayEmpty(ar) {
                for (let i = 0; i < ar.length; i++) {
                    for (let item in ar[i]) {
                        if (ar[i][item].length > 0)
                            return false;
                    }
                }
                return true;
            },
            send() {
                this.isSending = true;
                // TODO валидация формы

                this.saveForm().then(() => {
                    let request = BX.ajax.runComponentAction('renins:job_profile', 'send', {
                        mode: 'class',
                        data: {
                            id: this.formData.id,
                        }
                    });
                    request.then((response) => {
                        this.isSending = false;
                    }, function (response) {
                        this.isSending = false;
                        alert('Ошибка!')
                    });
                });
            },
            deleteElement() {
                this.isDeleting = true;
                let request = BX.ajax.runComponentAction('renins:job_profile', 'removeItems', {
                    mode: 'class',
                    data: {
                        ids: [this.formData.id],
                        deleting: (this.inTrash === true ? 'Y' : 'N')
                    }
                });
                request.then(() => {
                    this.isShowSuccessfullyArchivedModal = true;
                    this.isShowDeleteModal = false;
                }, function (response) {
                    console.log(response);
                    this.isDeleting = false;
                    alert('Ошибка!');
                });
            },
            approve() {
                this.isApproving = true;

                let request = BX.ajax.runComponentAction('renins:job_profile', 'approve', {
                    mode: 'class',
                    data: {
                        id: this.formData.id,
                        comment: this.approve_comment,
                    }
                });
                request.then((response) => {
                    this.isShowApproveModal = false;
                    this.isShowSuccessfullyApprovedModal = true;
                }, function (response) {
                    console.log(response);
                    this.isApproving = false;
                    alert('Ошибка!');
                });

            },
            sendNotifyChangeApprove(){
                console.log('notify')
                let request = BX.ajax.runComponentAction('renins:job_profile', 'sendNotifyChangeApprove', {
                    mode: 'class',
                    data: {
                        messageFields: {
                            userID: this.editStageFormData.RESPONSIBLE_USER,
                            job: this.formDataEdit.positionName,
                            podraz: this.formDataEdit.department,
                            func1Name: this.formDataEdit.func1Name,
                            func2Name: this.formDataEdit.func1Name,
                            costCenter: this.formDataEdit.costCenter,
                            linkToProfile: window.location.hostname + '/renins/job_profile/?DRAFT=' + this.formDataEdit.id,
                            getToProfileExcel: window.location.hostname + '/renins/job_profile/export.php?ID=' + this.formDataEdit.id,
                        },
                    }
                });
                request.then((response) => {
                    console.log('SUCCESS')
                }, (response) => {
                    alert('Ошибка! Уведомление не было отправлено')
                });
            },
            reject() {
                this.isRejecting = true;
                let request = BX.ajax.runComponentAction('renins:job_profile', 'reject', {
                    mode: 'class',
                    data: {
                        id: this.formData.id,
                        comment: this.reject_comment,
                    }
                });
                request.then((response) => {
                    this.isShowRejectModal = false;
                    this.isShowSuccessfullyRejectedModal = true;
                    this.isRejecting = false;
                }, function (response) {
                    console.log(response);
                    this.isRejecting = false;
                    alert('Ошибка!');
                });
            },
            revoke() {
                this.isRevoking = true;
                let request = BX.ajax.runComponentAction('renins:job_profile', 'revoke', {
                    mode: 'class',
                    data: {
                        id: this.formData.id,
                        comment: this.revoke_comment
                    }
                });
                request.then(() => {
                    this.isShowRevokeModal = false;
                    this.isShowSuccessfullyRevokedModal = true;
                    this.redirectToList();
                }, function (response) {
                    console.log(response);
                    this.isRevoking = false;
                    alert('Ошибка!');
                });
            },
            returning() {
                this.isReturning = true;
                let request = BX.ajax.runComponentAction('renins:job_profile', 'return', {
                    mode: 'class',
                    data: {
                        id: this.formData.id,
                        stage: this.return_stage,
                        comment: this.return_comment
                    }
                });
                request.then(() => {
                    this.isShowReturnModal = false;
                    this.isShowSuccessfullyReturnedModal = true;
                }, function (response) {
                    console.log(response);
                    this.isReturning = false;
                    alert('Ошибка!');
                });
            },
            delegate() {

            },
            isStageDeleting(id) {
                return Boolean(this.deletingStages[id]);
            },
            isStageUpdating(id) {
                return Boolean(this.updatingStages[id]);
            },
            toggleChecked(stageId, value) {
                this.updatingStages = {...this.updatingStages, [stageId]: true}
                let request = BX.ajax.runComponentAction('renins:job_profile', 'updateStage', {
                    mode: 'class',
                    data: {
                        entityId: this.formData.id,
                        stageId: stageId,
                        data: {
                            CHECKED: value ? 'Y' : 'N'
                        },
                    }
                });
                request.then((response) => {
                    console.log(response);
                    this.stages = response.data;
                    this.updatingStages = {...this.updatingStages, [stageId]: false}
                }, function (response) {
                    console.log(response);
                    this.updatingStages = {...this.updatingStages, [stageId]: false}
                    alert('Ошибка!');
                });
            },
            addAdditionStage() {
                this.isAddFormSaving = true;
                let request = BX.ajax.runComponentAction('renins:job_profile', 'addAdditionStage', {
                    mode: 'class',
                    data: {
                        entityId: this.formData.id,
                        users: this.addFormData.users,
                    }
                });
                request.then((response) => {
                    console.log(response);
                    this.stages = response.data;
                    this.isAddFormSaving = false;
                    this.showAddForm = false;
                    this.addFormData.users = [];

                }, function (response) {
                    console.log(response);
                    this.isAddFormSaving = false;
                    alert('Ошибка!');
                });
            },
            deleteAdditionStage(id) {
                this.deletingStages = {...this.deletingStages, [id]: true}
                let request = BX.ajax.runComponentAction('renins:job_profile', 'deleteAdditionStage', {
                    mode: 'class',
                    data: {
                        entityId: this.formData.id,
                        stageId: id,
                    }
                });
                request.then((response) => {
                    console.log(response);
                    this.stages = response.data;
                }, function (response) {
                    console.log(response);
                    alert('Ошибка!');
                });
            },
            getToWork() {
                // Взять заявку в работу
                this.currentStepLast = this.currentStep;
                this.isProcessing = true;
                let request = BX.ajax.runComponentAction('renins:job_profile', 'getToWork', {
                    mode: 'class',
                    data: {
                        id: this.formData.id
                    }
                });
                request.then(() => {
                    this.load(() => {
                        this.isProcessing = false;
                        this.currentStep = this.currentStepLast;
                    });
                }, function (response) {
                    console.log(response);
                    this.isProcessing = false;
                    alert('Ошибка!');
                });
            },
            planning() {
                let res = [];

                if (this.formData.isShortTerm) res.push('Краткосрочный');
                if (this.formData.isMediumTerm) res.push('Среднесрочный');
                if (this.formData.isLongTerm) res.push('Долгосрочный');

                return res.join(', ');
            },
            premium() {
                let res = [];

                if (this.formData.premiumMonth) res.push('Ежемесячная');
                if (this.formData.premiumQuarter) res.push('Квартальная');
                if (this.formData.premiumHalfyear) res.push('Полугодовая');
                if (this.formData.premiumYear) res.push('Годовая');

                return res.join(', ');
            },
            downloadExcel() {
                var link = document.createElement('a');
                link.setAttribute('href', '/renins/job_profile/export.php?ID=' + this.formData.id);
                //link.setAttribute('download', 'report.xlsx');
                link.click();
            },
            downloadDi() {
                var link = document.createElement('a');
                link.setAttribute('href', '/renins/job_profile/exportDI.php?fileID=' + this.selectDiFile);
                link.setAttribute('target', '_blank');
                link.click();
            },
            updateManagers(cost) {
                if (cost) {
                    let request = BX.ajax.runComponentAction('renins:job_profile', 'getUserByPid', {
                        mode: 'class',
                        data: {
                            pid: cost.func1Pid,
                        }
                    });
                    request.then((response) => {
                        this.formDataEdit.func1Pid = response.data.id;
                        this.formDataEdit.managerExcoFio = response.data.fio;
                        this.formDataEdit.managerExcoIsObserver = true;
                    }, function (response) {
                        console.log(response);
                        alert('Ошибка!');
                    });

                    let request2 = BX.ajax.runComponentAction('renins:job_profile', 'getUserByPid', {
                        mode: 'class',
                        data: {
                            pid: cost.func2Pid,
                        }
                    });
                    request2.then((response) => {
                        this.formDataEdit.func2Pid = response.data.id;
                        this.formDataEdit.managerLineFio = response.data.fio;
                        this.formDataEdit.managerLineIsObserver = true;
                    }, function (response) {
                        console.log(response);
                        alert('Ошибка!');
                    });
                }
            },
            downloadDI() {
                if (this.diFiles.length > 1) {
                    this.isShowDownloadDIModal = true;
                } else if (this.diFiles.length === 1) {
                    this.selectDiFile = this.diFiles[0].Id;
                    this.downloadDi();
                } else {
                    this.downloadDIError = true;
                    setTimeout(() => {
                        document.getElementById("download-di-error").scrollIntoView();
                    });
                }
            },
            sendNotifyChangeApprove(){
                console.log('notify')
                let request = BX.ajax.runComponentAction('renins:job_profile', 'sendNotifyChangeApprove', {
                    mode: 'class',
                    data: {
                        messageFields: {
                            userID: this.editStageFormData.RESPONSIBLE_USER,
                            job: this.formDataEdit.positionName,
                            podraz: this.formDataEdit.department,
                            func1Name: this.formDataEdit.func1Name,
                            func2Name: this.formDataEdit.func1Name,
                            costCenter: this.formDataEdit.costCenter,
                            linkToProfile: window.location.hostname + '/renins/job_profile/?DRAFT=' + this.formDataEdit.id,
                            getToProfileExcel: window.location.hostname + '/renins/job_profile/export.php?ID=' + this.formDataEdit.id,
                        },
                    }
                });
                request.then((response) => {
                    console.log('SUCCESS')
                }, (response) => {
                    alert('Ошибка! Уведомление не было отправлено')
                });
            },
        },
        watch:
            {
                formDataEdit: {
                    handler(val) {
                        console.log('formDataEdit', val);
                        console.log('this.errors', this.errors);
                        this.revalidateStep(this.currentStep);
                        if (this.isSaveButtonDisabled) {
                            let _errors = this.errors;
                            if (Object.keys(_errors).every(key => (
                                !_errors[key]
                                || (Object.keys(_errors[key]).length === 0)
                                || Object.keys(_errors[key]).every(key2 => !_errors[key][key2])
                            )))
                                this.isSaveButtonDisabled = false;
                        }
                    }
                    ,
                    deep: true
                }
                ,
                'formDataEdit.admManager'() {
                    setTimeout(() => {
                        this.formDataEdit.admManagerPosition = this.$refs.adm?.$refs.position.value;
                    }, 50);
                }
                ,
                'formDataEdit.funcManager'() {
                    setTimeout(() => {
                        this.formDataEdit.funcManagerPosition = this.$refs.func?.$refs.position.value;
                    }, 50);
                }
                ,
                'formDataEdit.costCenter'(value) {
                    let cost = this.costCenters.find(item => item.value === value);
                    if (cost) {
                        this.formDataEdit.func1Name = cost.func1Name;
                        this.formDataEdit.func1Pid = cost.func1Pid;
                        this.formDataEdit.func2Name = cost.func2Name;
                        this.formDataEdit.func2Pid = cost.func2Pid;

                        this.updateManagers(cost);
                    } else {
                        this.formDataEdit.func1Name = null;
                        this.formDataEdit.func1Pid = null;
                        this.formDataEdit.managerExcoFio = null;
                        this.formDataEdit.managerExcoIsObserver = false;

                        this.formDataEdit.func2Name = null;
                        this.formDataEdit.func2Pid = null;
                        this.formDataEdit.managerLineFio = null;
                        this.formDataEdit.managerLineIsObserver = false;
                    }
                }
                ,
                'formDataEdit.departmentGoals'() {
                    if (!this.formDataEdit.departmentGoals || (this.formDataEdit.departmentGoals.length === 0)) {
                        this.formDataEdit.departmentGoals = [];
                        this.addDepartmentGoal();
                    }
                }
                ,
                'formDataEdit.positionGoals'() {
                    if (!this.formDataEdit.positionGoals || (this.formDataEdit.positionGoals.length === 0)) {
                        this.formDataEdit.positionGoals = [];
                        this.addPositionGoal();
                    }
                }
                ,
                'formDataEdit.mainDuties'() {
                    if (!this.formDataEdit.mainDuties || (this.formDataEdit.mainDuties.length === 0)) {
                        this.formDataEdit.mainDuties = [];
                        this.addMainDuty();
                    }
                }
                ,
                'formDataEdit.addDuties'() {
                    if (!this.formDataEdit.addDuties || (this.formDataEdit.addDuties.length === 0)) {
                        this.formDataEdit.addDuties = [];
                        this.addAdditionalDuty();
                    }
                }
                ,

                'formDataEdit.hasSubs'(value) {
                    if (value) {
                        if (parseInt(this.formDataEdit.subordinatesCount) === 0)
                            this.formDataEdit.subordinatesCount = null;
                        if (parseInt(this.formDataEdit.allSubordinatesCount) === 0) ;
                        this.formDataEdit.allSubordinatesCount = null;
                    } else {
                        this.formDataEdit.subordinatesCount = 0;
                        this.formDataEdit.allSubordinatesCount = 0;
                    }
                }
                ,
                'formDataEdit.hasFuncSubs'(value) {
                    if (value) {
                        if (parseInt(this.formDataEdit.funcSubordinatesCount) === 0)
                            this.formDataEdit.funcSubordinatesCount = null;
                    } else
                        this.formDataEdit.funcSubordinatesCount = 0;
                }
                ,
                'formDataEdit.hasProjectSubs'(value) {
                    if (value) {
                        if (parseInt(this.formDataEdit.projectSubordinatesCount) === 0)
                            this.formDataEdit.projectSubordinatesCount = '';
                    } else
                        this.formDataEdit.projectSubordinatesCount = 0;
                }
                ,
                'formDataEdit.hasOutsourceSubs'(value) {
                    if (value) {
                        if (parseInt(this.formDataEdit.outsourceSubordinatesCount) === 0)
                            this.formDataEdit.outsourceSubordinatesCount = '';
                    } else
                        this.formDataEdit.outsourceSubordinatesCount = 0;
                }
                ,
                'formDataEdit.competencies'() {
                    if (!this.formDataEdit.competencies)
                        this.formDataEdit.competencies = {};
                }
                ,
                'formDataEdit.premiumMonth'(value) {
                    if (value) {
                        this.formDataEdit.premiumQuarter = false;
                        this.formDataEdit.premiumHalfyear = false;
                        this.formDataEdit.premiumYear = false;
                    }
                }
                ,
                'formDataEdit.premiumQuarter'(value) {
                    if (value) {
                        this.formDataEdit.premiumMonth = false;
                        this.formDataEdit.premiumHalfyear = false;
                    }
                }
                ,
                'formDataEdit.premiumHalfyear'(value) {
                    if (value) {
                        this.formDataEdit.premiumMonth = false;
                        this.formDataEdit.premiumQuarter = false;
                        this.formDataEdit.premiumYear = false;
                    }
                }
                ,
                'formDataEdit.premiumYear'(value) {
                    if (value) {
                        this.formDataEdit.premiumMonth = false;
                        this.formDataEdit.premiumHalfyear = false;
                    }
                }
                ,
                'formDataEdit.gradeNotDefined'(value) {
                    if (value)
                        this.formDataEdit.grade = '';
                }
                ,
                'formDataEdit.forkMid'(value) {
                    if (value) {
                        this.formDataEdit.forkLow = Math.floor(parseInt(value.replaceAll(' ', '')) * 0.7);
                        this.formDataEdit.forkHigh = Math.ceil(parseInt(value.replaceAll(' ', '')) * 1.3);
                    } else {
                        this.formDataEdit.forkLow = '';
                        this.formDataEdit.forkHigh = '';
                    }
                }
                ,
                'currentStep'(value) {
                    if (value === 1)
                        this.current_step_section = 'HR OD';

                    if (value >= 2 && value <= 13)
                        this.current_step_section = 'Руководитель';

                    if (value === 14)
                        this.current_step_section = 'T&D';

                    if (value === 15)
                        this.current_step_section = 'C&B';
                }
                ,
                'current_step_section'(value) {
                    if (value === 'HR OD')
                        this.currentStep = 1;

                    if ((value === 'Руководитель') && !(this.currentStep >= 2 && this.currentStep <= 13))
                        this.currentStep = 2;

                    if (value === 'T&D')
                        this.currentStep = 14;

                    if (value === 'C&B')
                        this.currentStep = 15;

                },
                dragItems: {
                    handler(newItems) {
                        // Синхронизируем изменения в dragItems с formData.step4.mainDuties
                        this.formData.mainDuties = newItems;
                    },
                    deep: true, // Важно: наблюдаем за изменениями внутри массива
                },
                dragItemsAdd: {
                    handler(newItems) {
                        // Синхронизируем изменения в dragItemsAdd с formData.step4.addDuties
                        this.formData.addDuties = newItems;
                    },
                    deep: true, // Важно: наблюдаем за изменениями внутри массива
                },
            }
        ,
        computed: {
            diFilesSelectList() {
                return this.diFiles.map(item => ({'label': item.full_path, 'value': item.Id}));
            }
            ,
            recommendFormat() {
                let a = this.formDataEdit.relationOutClients;
                let b = this.formDataEdit.relationInClients;
                let c = this.formDataEdit.physicService;
                let d = this.formDataEdit.difficultAttractComps;
                let e = this.formDataEdit.workModeSubs;

                if (!a && !b && !c && !d && !e)
                    return '';

                let if1 = ([a, b, c].indexOf('Да, нельзя делать удаленно') >= 0)
                    ? 'Офисный'
                    : (([a, b, c].indexOf('Да, частично можно делать удаленно') >= 0)
                        ? 'Гибридный'
                        : 'Удаленный');

                if (e === 'Да, есть подчиненные со стандартным режимом')
                    return 'Офисный';
                else {
                    if (e === 'Да, все подчиненные с комбинированным и дистанционным режимом') {
                        if ([a, b, c].indexOf('Да, нельзя делать удаленно') >= 0)
                            return 'Офисный';
                        else
                            return 'Гибридный';
                    } else {
                        if ([a, b, c].indexOf('Да, нельзя делать удаленно') >= 0)
                            return 'Офисный';
                        else {
                            if ([a, b, c].indexOf('Да, частично можно делать удаленно') >= 0)
                                return 'Гибридный';
                            else {
                                if ((if1 === 'Удаленный') && (d === 'Да'))
                                    return 'Удаленный';
                                else {
                                    if ([a, b, c].indexOf('Да, нельзя делать удаленно') >= 0)
                                        return 'Офисный';
                                    else {
                                        if ([a, b, c].indexOf('Да, частично можно делать удаленно') >= 0)
                                            return 'Гибридный';
                                        else
                                            return 'Удаленный';
                                    }
                                }
                            }
                        }
                    }
                }

                return '';
            }
        }
        ,
    }).mount('#job_profile');
})
;


