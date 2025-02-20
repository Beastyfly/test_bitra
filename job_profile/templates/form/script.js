BX.ready(function () {
    BX.BitrixVue.createApp({
        data() {
            return {
                dragItems: [],// Drag-and-Drop step4
                dragItemsAdd: [],// Drag-and-Drop step4
                currentUserId: null,
                currentStep: 1,
                step_sections: ["HR OD", "Руководитель", "T&D", "C&B"],
                current_step_section: "HR OD",
                breadcrumbs: [
                    { label: 'HR-сервисы', url: '/renins-kb/' },
                    { label: 'Профиль должности', url: '/renins/job_profile/list/' }
                ],
                initiator: null,
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
                allowedChangeStages: false,
                updatingStages: {},
                send_comment: null,
                isShowStageModal: false,
                isShowDelegateModal: false,
                isShowSendModal: false,
                autoSaveDelay: 1000,
                isSaving: false,
                isAutoSaving: false,
                isSending: false,
                isProcessing: false,
                revoke_comment: null,
                isRevoking: false,
                isShowRevokeModal: false,
                isShowSuccessfullyRevokedModal: false,
                isDeleting: false,
                isShowDeleteModal: false,
                isShowSuccessfullyArchivedModal: false,
                collapsed: {
                    stages: true,
                },
                formDataCopy: {},
                formData: {
                    step1: {
                        id: '',
                        positionName: null,
                        costCenter: null,
                        func1Name: null,
                        func1Pid: null,
                        managerExcoFio: null,
                        managerExcoIsObserver: false,
                        func2Name: null,
                        func2Pid: null,
                        managerLineFio: null,
                        managerLineIsObserver: false,
                        department: null,
                        branch: null,
                        location: null,
                        admManager: null,
                        admManagerPosition: null,
                        funcManager: null,
                        funcManagerPosition: null,
                        needAdmApprove: false,
                        needFuncApprove: false,
                        delegate: null,
                        addApprovers: [],
                        headAdmManager: null,
                        sendComment: '',
                        addObservers: [''],
                    },
                    step2: {
                        hasSubs: false,
                        hasFuncSubs: false,
                        hasProjectSubs: false,
                        hasOutsourceSubs: false,
                        subordinatesCount: null,
                        allSubordinatesCount: null,
                        subordinatesComment: null,
                        funcSubordinatesCount: null,
                        projectSubordinatesCount: null,
                        outsourceSubordinatesCount: null,
                        outsourceComment: null,
                        isManager: false,
                        isShiftSchedule: false,
                        isItinerantWork: false,
                        fieldPercent: null,
                        isRemote: false,
                        calculator: false,
                        relationOutClients: null,
                        relationInClients: null,
                        physicService: null,
                        difficultAttractComps: null,
                        workModeSubs: null,
                        schedule: null,
                        distantPercent: null,
                        diffModeComment: null,
                    },
                    step3: {
                        departmentGoals: [
                            '',
                            '',
                        ],
                        positionGoals: [
                            '',
                            '',
                        ]
                    },
                    step4: {
                        isShortTerm: false,
                        isMediumTerm: false,
                        isLongTerm: false,
                        mainDuties: [
                            {duty: '', result: '', id: ''},
                            {duty: '', result: '', id: ''},
                        ],
                        addDuties: [
                            {duty: '', result: '', id: ''},
                            {duty: '', result: '', id: ''},
                        ]
                    },
                    step5: {
                        positionContribution: null,
                        positionContributionDescription: null
                    },
                    step6: {
                        decisions: null
                    },
                    step7: {
                        financialResultGeneration: null,
                        EBIT: null,
                        WP: null,
                    },
                    step8: {
                        isNotInvolvedInBudgetManagement: null,
                        isControlTargetBudget: null,
                        isPrepareProposalsToSpendBudget: null,
                        hasAuthorityToMakeDecisions: null,
                        CnBSum: null,
                        nonCnBSum: null,
                        validationTriggered: false,
                    },
                    step9: {
                        levelOfInnovativeness: null,
                    },
                    step10: {
                        interactionCircleWithinTheCompany: null,
                        b2bClients: [],
                        b2cClients: [],
                        otherClients: [],
                        namesOfExternalOrganizations: null,
                        isTransmittingInformation: null,
                        isConsulting: null,
                        isInteraction: null,
                        isParticipationNegotiations: null,
                        isAuthoritativeInfluence: null,
                        isStrategicNegotiations: null,
                        amountOfCommunications: null,
                    },
                    step11: {
                        minimumLevelOfEducation: null,
                        Qualification: null,
                        Certification: null,
                        professionalStandard: null,
                    },
                    step12: {
                        knowledgeOfMethods: null,
                        knowledgeOfComputerPrograms: null,
                        knowledgeOfSituation: null,
                        businessQualities: null,
                        englishLevel: null,
                        languages: [
                            {name: '', level: ''},
                        ]
                    },
                    step13: {
                        managementExperience: null,
                        professionalExperience: null,
                        typeOfExperience: null,
                        fieldOfActivity: null,
                        professionalExperienceYears: null,
                        typeOfManagementExperience: null,
                        fieldOfManagementActivity: null,
                        managementExperienceYears: null,
                    },
                    step14: {
                        checksCompetencies: {},
                        competencies: {
                            q1: null,
                            q2: null,
                            q3: null,
                            q4: null,
                            q5: null,
                            q6: null,
                            q7: null,
                            q8: null,
                            q9: null,
                            q10: null,
                            q11: null,
                            q12: null,
                            q13: null,
                            q14: null,
                            q15: null,
                            q16: null,
                            q17: null,
                            q18: null,
                            q19: null,
                            q20: null,
                            q21: null,
                            q22: null,
                            q23: null,
                            q25: null,
                            q26: null,
                            q27: null,
                            q28: null,
                            q29: null,
                        }
                    },
                    step15: {
                        review: [
                            {name: '', code: ''}
                        ],
                        premiumMonth: false,
                        premiumQuarter: false,
                        premiumHalfyear: false,
                        premiumYear: false,
                        premiumPercent: null,
                        grade: null,
                        gradeNotDefined: null,
                        forkLow: null,
                        forkMid: null,
                        forkHigh: null,
                    },
                },
                costCenters: [],
                departments: [],
                branches: [],
                locations: [],

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
                    {id: 'q1', text: "Учитывает мотивы, чувства и потребности окружающих"},
                    {id: 'q2', text: "Предвосхищает потребности"},
                    {id: 'q3', text: "Неравнодушен к проблемам других, оказывает помощь"},
                    {id: 'q4', text: "Выходит за рамки инструкций"},
                    {id: 'q5', text: "Оперативно реагирует на запросы, выполняет взятые обязательства"},
                    {id: 'q6', text: "Озвучивает мысли ясно и понятно"},
                    {id: 'q7', text: "Объясняет причины отказа, предлагает решения"},
                    {id: 'q8', text: "Качественно анализирует и синтезирует информацию"},
                    {id: 'q9', text: "Опирается на данные и аналитику"},
                    {id: 'q10', text: "Предотвращает возможные риски"},
                    {id: 'q11', text: "Пилотирует решения"},
                    {id: 'q12', text: "Честен и открыт с окружающими"},
                    {id: 'q13', text: "Настойчив в достижении цели"},
                    {id: 'q14', text: "Берет ответственность за решения"},
                    {id: 'q15', text: "Действует для изменения ситуации"},
                    {id: 'q16', text: "Развивается и самосовершенствуется"},
                    {id: 'q17', text: "Ставит перед собой новые амбициозные цели"},
                    {id: 'q18', text: "Изучает новые технологии"},
                    {id: 'q19', text: "Внедряет новые подходы"},
                    {id: 'q20', text: "Привлекает в команду сильных людей"},
                    {id: 'q21', text: "Вносит предложения по улучшению процессов и регламентов смежных подразделений"},
                    {id: 'q22', text: "Ориентируется на цели и интересы компании"},
                    {id: 'q23', text: "Сотрудничает с коллегами, нацелен на общий результат"},
                    {id: 'q24', text: "Учится на ошибках"},
                    {id: 'q25', text: "Поддерживает и помогает другим в развитии"},
                    {id: 'q26', text: "Дает обратную связь"},
                    {id: 'q27', text: "Принимает обратную связь"},
                    {id: 'q28', text: "Уважает время и ресурсы коллег"},
                    {id: 'q29', text: "Своевременно отвечает на вопросы окружающих"},
                ],
                allowedDelegateFilling: false,
                isFillingStage: false,
                isAccessPage: false,
                isOD: false,
                version: 0,
                requiredFields: [],
                errors: {},
                stepsWithErrors: [],
                validatedSteps: [],
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
                this.allowedDelegateFilling = data.allowedDelegateFilling;
                this.isAccessPage = data.isAccessPage;
                this.isFillingStage = data.isOD ? false : data.isFillingStage;
                this.isOD = data.isOD;
                this.initiator = data.initiator;
                this.status = data.status;
                this.statusId = data.statusId;
                this.statusClass = data.statusClass;
                this.stage = data.stage;
                this.stageId = data.stageId;
                this.stages = data.stages;
                this.processingUser = data.processingUser;
                this.processingUsers = data.processingUsers;
                this.createDate = data.createDate;
                this.updateDate = data.updateDate;
                this.modelRole = data.modelRole;
                this.work = data.work;
                this.roleSLA = data.roleSLA;
                this.allowedChangeStages = data.allowedChangeStages;
                this.costCenters = data.costCenters;
                this.departments = data.departments;
                this.branches = data.branches;
                this.locations = data.locations;
                this.requiredFields = data.requiredFields;

                if (this.isFillingStage) {
                    // Заполнение формы осуществляется со второго шага
                    this.currentStep = 2;
                }
                this.formData.step1.id = data.id;

                if (this.formData.step1.id)
                    this.breadcrumbs.push({ label: this.formData.step1.id });

                // Заполнение данных формы ранее сохранёнными данными
                for (const step in this.formData) {
                    for (const field in this.formData[step]) {
                        if (data.formData.hasOwnProperty(field)) {
                            if (step === 'step14' && !data.formData[field]) {
                                continue;
                            }
                            this.formData[step][field] = data.formData[field];

                            if ((field === 'addApprovers' || field === 'addObservers') && !data.formData[field]) {
                                this.formData[step][field] = [];
                            }

                            if ((field === 'departmentGoals' || field === 'positionGoals') && !data.formData[field]) {
                                this.formData[step][field] = ['', ''];
                            }
                            if ((field === 'mainDuties' || field === 'addDuties')
                                && (!data.formData[field] || data.formData[field].length === 0)) {
                                this.formData[step][field] = [
                                    {duty: '', result: ''},
                                    {duty: '', result: ''},
                                ];
                            }
                            if ((field === 'languages') && !data.formData[field]) {
                                this.formData[step][field] = [
                                    {name: '', level: ''},
                                ];
                            }
                            if ((field === 'review') && !data.formData[field]) {
                                this.formData[step][field] = [
                                    {name: '', code: ''}
                                ];
                            }
                        }
                    }
                }

                if (this.currentUserId === this.formData.step1.delegate)
                    this.step_sections.pop();

                for (const comp in this.formData.step14.competencies)
                {
                    if (this.formData.step14.competencies[comp])
                        this.formData.step14.checksCompetencies[comp] = true;
                }

                if (this.formData.step12.languages.length < 1)
                {
                    this.formData.step12.languages = [
                        {name: '', level: ''},
                    ];
                }

                if (this.formData.step15.review.length < 1)
                {
                    this.formData.step15.review = [
                        {name: '', code: ''},
                    ];
                }

                this.copyFormForEdit();
            },
            //--methods Drag-and-Drop--
            getItemsList() {
                if (this.formData.step4.mainDuties && !Array.isArray(this.formData.step4.mainDuties)) {
                    this.formData.step4.mainDuties = Object.values(this.formData.step4.mainDuties);
                }

                let arrRes = []
                this.formData.step4.mainDuties.forEach(function (elem, ind) {
                    let promRes = {
                        'duty': elem.duty,
                        'result': elem.result,
                        'id': ind
                    }
                    arrRes.push(promRes)
                })
                this.dragItems = arrRes

                if (this.formData.step4.addDuties && !Array.isArray(this.formData.step4.addDuties)) {
                    this.formData.step4.addDuties = Object.values(this.formData.step4.addDuties);
                }

                arrRes = []
                this.formData.step4.addDuties.forEach(function (elem, ind) {
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
            //--end
            getFullForm(val) {
                val = val ? val : this.formData;
                return {
                    ...val.step1,
                    ...val.step2,
                    ...val.step3,
                    ...val.step4,
                    ...val.step5,
                    ...val.step6,
                    ...val.step7,
                    ...val.step8,
                    ...val.step9,
                    ...val.step10,
                    ...val.step11,
                    ...val.step12,
                    ...val.step13,
                    ...val.step14,
                    ...val.step15,
                };
            },
            copyFormForEdit() {
                // Копирование данных формы для автосохранения
                const formData = this.getFullForm();
                this.formDataCopy = JSON.parse(JSON.stringify(formData));
            },
            toggle(value) {
                this.$set(this.collapsed, value, !Boolean(this.collapsed[value]));
                console.log(this.collapsed[value]);
            },
            isCollapsed(value) {
                if (!this.collapsed.hasOwnProperty(value)) return false;
                return Boolean(this.collapsed[value]);
            },
            isStageUpdating(id) {
                return Boolean(this.updatingStages[id]);
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
                        entityId: this.formData.step1.id,
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

                    this.updatingStages = {...this.updatingStages, [stageId]: false};
                    this.isShowStageModal = false;
                    this.sendNotifyChangeApprove()
                }, function (response) {
                    console.log(response);
                    this.updatingStages = {...this.updatingStages, [stageId]: false};
                    alert('Ошибка!');
                });
            },
            toggleChecked(stageId, value) {
                this.updatingStages = {...this.updatingStages, [stageId]: true}
                let request = BX.ajax.runComponentAction('renins:job_profile', 'updateStage', {
                    mode: 'class',
                    data: {
                        entityId: this.formData.step1.id,
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
            nextStep() {
                if (this.currentStep === 8) {
                    this.formData.step8.validationTriggered = true;
                }
                // Валидация полей для текущего шага
                if (!this.validateStep(this.currentStep)) {
                    return;
                }
                this.currentStep++;
                document.getElementById("group-button").scrollIntoView();

                this.formData.step8.validationTriggered = false;
            },
            getStepClass(i) {
                if (this.stepHasErrors(i)) {
                    return {warning: true};
                }
                return {active: this.currentStep === i, success: this.validatedSteps.includes(i)};
            },
            addDepartmentGoal() {
                this.formData.step3.departmentGoals.push('');
            },
            addPositionGoal() {
                this.formData.step3.positionGoals.push('');
            },
            addMainDuty() {
                this.formData.step4.mainDuties.push({duty: '', result: '', id:''});
                this.dragItems.push({duty: '', result: '', id: ''});
            },
            addAdditionalDuty() {
                this.formData.step4.addDuties.push({duty: '', result: '', id:''});
                this.dragItemsAdd.push({duty: '', result: '', id: ''});
            },
            addLanguage() {
                this.formData.step12.languages.push({name: '', level: ''});
            },
            addReview() {
                this.formData.step15.review.push({name: '', code: ''});
            },
            addApprover() {
                this.formData.step1.addApprovers.push('');
            },
            removeApprover(index) {
                this.formData.step1.addApprovers.splice(index, 1);
            },
            addObserver() {
                this.formData.step1.addObservers.push('');
            },
            removeObserver(index) {
                this.formData.step1.addObservers.splice(index, 1);
            },
            getDifData(val) {
                const formData = this.getFullForm(val);

                // Фильтруем измененные поля
                return Object.keys(this.formDataCopy).reduce((acc, key) => {
                    if ((['addApprovers', 'addObservers', 'b2bClients', 'b2cClients', 'departmentGoals',
                        'otherClients', 'positionGoals', 'checksCompetencies']).indexOf(key) >= 0)
                    {
                        if (this.formDataCopy[key].length !== formData[key].length)
                            return { ...acc, [key]: formData[key] }

                        for (let i = 0; i < this.formDataCopy[key].length; i++)
                        {
                            if (!formData[key].hasOwnProperty(i)
                                || (this.formDataCopy[key][i] !== formData[key][i]))
                                return { ...acc, [key]: formData[key] }
                        }
                    }
                    else if ((['addDuties', 'review', 'languages', 'mainDuties']).indexOf(key) >= 0)
                    {
                        if (!this.isArrayEmpty(this.formDataCopy[key])
                            && !this.isArrayEmpty(formData[key])
                            && (this.formDataCopy[key].length !== formData[key].length))
                            return { ...acc, [key]: formData[key] }

                        for (let i = 0; i < this.formDataCopy[key].length; i++)
                        {
                            if (!formData[key].hasOwnProperty(i))
                                return { ...acc, [key]: formData[key] }

                            for (let item in this.formDataCopy[key][i])
                            {
                                if (this.formDataCopy[key][i][item] !== formData[key][i][item])
                                    return { ...acc, [key]: formData[key] }
                            }
                        }
                    }
                    else if (key === 'competencies')
                    {
                        for (const comp in this.formDataCopy[key])
                        {
                            if (this.formDataCopy[key][comp] !== formData[key][comp])
                                return { ...acc, [key]: formData[key] }
                        }
                    }
                    else if (this.formDataCopy[key] !== formData[key])
                    {
                        return { ...acc, [key]: formData[key] }
                    }
                    return acc;
                }, {});
            },
            isArrayEmpty(ar) {
                for (let i = 0; i < ar.length; i++)
                {
                    for (let item in ar[i])
                    {
                        if (ar[i][item].length > 0)
                            return false;
                    }
                }
                return true;
            },
            saveAndRedirect() {
                this.saveForm().then(() => {
                    this.redirectBack()
                });
            },
            saveForm(modifier) {
                this.isSaving = true;
                this.formData.step4.mainDuties = this.dragItems;
                this.formData.step4.addDuties = this.dragItemsAdd;

                if (!this.formData.step2.hasSubs)
                {
                    this.formData.step2.subordinatesCount = 0;
                    this.formData.step2.allSubordinatesCount = 0;
                }

                if (!this.formData.step2.hasFuncSubs)       this.formData.step2.funcSubordinatesCount = 0;
                if (!this.formData.step2.hasProjectSubs)    this.formData.step2.projectSubordinatesCount = 0;
                if (!this.formData.step2.hasOutsourceSubs)  this.formData.step2.outsourceSubordinatesCount = 0;

                let formData = this.getFullForm();
                formData[ modifier ] = true;

                let request = BX.ajax.runComponentAction('renins:job_profile', 'save', {
                    mode: 'class',
                    data: {
                        formData: formData,
                    }
                });
                return new Promise((resolve, reject) => {
                    request.then((response) => {
                            this.formData.step1.id = response.data.formData.id;
                            if (this.breadcrumbs.length === 3)
                                this.breadcrumbs.pop();
                            this.breadcrumbs.push({ label: 'ID ' + this.formData.step1.id });
                            window.history.replaceState(null, null, '/renins/job_profile/?DRAFT=' + this.formData.step1.id);
                            console.log(response);
                            this.isSaving = false;
                            resolve();
                        },
                        function (response) {
                            console.log(response);
                            this.isSaving = false;
                            alert('Ошибка!');
                            reject();
                        });
                });
            },
            sendNotifyChangeApprove(){
                let request = BX.ajax.runComponentAction('renins:job_profile', 'sendNotifyChangeApprove', {
                    mode: 'class',
                    data: {
                        messageFields: {
                            userID: this.editStageFormData.RESPONSIBLE_USER,
                            job: this.formData.step1.positionName,
                            podraz: this.formData.step1.department,
                            func1Name: this.formData.step1.func1Name,
                            func2Name: this.formData.step2.func1Name,
                            costCenter: this.formData.step1.costCenter,
                            linkToProfile: window.location.hostname + '/renins/job_profile/?DRAFT=' + this.step1.id,
                            getToProfileExcel: window.location.hostname + '/renins/job_profile/export.php?ID=' + this.step1.id,
                        },
                    }
                });
                request.then((response) => {
                    return true
                }, (response) => {
                    alert('Ошибка! Уведомление не было отправлено')
                });
            },
            send() {
                // Валидация формы (14 шагов)
                this.stepsWithErrors = [];
                const steps = {
                    1: 'Должность',
                    2: 'Параметры',
                    3: 'Цели',
                    4: 'Обязанности',
                    5: 'Вклад',
                    6: 'Полномочия',
                    7: 'Финансовый результат',
                    8: 'Бюджет',
                    9: 'Инновационность',
                    10: 'Коммуникации',
                    11: 'Требования',
                    12: 'Навыки',
                    13: 'Опыт',
                    14: 'Компетенции',
                };
                let formHasErrors = false;
                for (let i = 1; i <= 14; i++) {
                    if(!this.validateStep(i)){
                        formHasErrors = true;
                        if(steps.hasOwnProperty(i)) {
                            this.stepsWithErrors.push(steps[i]);
                        }
                    }
                }
                if (formHasErrors) {
                    return;
                }

                this.isSending = true;
                this.saveForm('sendToApprove').then(() => {
                    console.log('send');
                    let request = BX.ajax.runComponentAction('renins:job_profile', 'send', {
                        mode: 'class',
                        data: {
                            id: this.formData.step1.id,
                            comment: this.send_comment,
                        }
                    });
                    request.then((response) => {
                        this.redirectBack('filled');
                    }, (response) => {
                        this.isSending = false;
                        alert('Ошибка!')
                    });
                });
            },
            saveAndDelegate() {
                this.saveForm().then(() => {
                    this.delegate();
                });
            },
            delegate() {
                // Делегировать 2 этап сотруднику
                this.isProcessing = true;
                let request = BX.ajax.runComponentAction('renins:job_profile', 'delegate', {
                    mode: 'class',
                    data: {
                        id: this.formData.step1.id,
                        userId: this.formData.step1.delegate
                    }
                });
                request.then(() => {
                    // this.isProcessing = false;
                    this.redirectBack();
                }, function (response) {
                    console.log(response);
                    this.isProcessing = false;
                    alert('Ошибка!');
                });
            },
            revoke() {
                this.isRevoking = true;
                let request = BX.ajax.runComponentAction('renins:job_profile', 'revoke', {
                    mode: 'class',
                    data: {
                        id: this.formData.step1.id,
                        comment: this.revoke_comment
                    }
                });
                request.then(() => {
                    this.isShowRevokeModal = false;
                    this.isShowSuccessfullyRevokedModal = true;
                    this.isRevoking = false;
                    this.redirectBack();
                }, function (response) {
                    console.log(response);
                    this.isRevoking = false;
                    alert('Ошибка!');
                });
            },
            deleteElement() {
                this.isDeleting = true;
                let request = BX.ajax.runComponentAction('renins:job_profile', 'removeItems', {
                    mode: 'class',
                    data: {
                        id: [ this.formData.step1.id ],
                        deleting: (this.statusId === 'trash' ? 'Y' : 'N')
                    }
                });
                request.then(() => {
                    this.isDeleting = false;
                    this.isShowSuccessfullyArchivedModal = true;
                    this.isShowDeleteModal = false;
                    this.redirectBack();
                }, function (response) {
                    console.log(response);
                    this.isDeleting = false;
                    alert('Ошибка!');
                });
            },
            redirectBack(mode) {
                console.log('redirect');
                let query = '';
                if ((mode === 'filled') && !this.isOD)
                    query = '?' + mode + '=' + this.formData.step1.id;

                window.location.href = document.referrer;
            },
            autoSave(val, version) {
                // Ждем пока выполнится сохранение
                if (this.isAutoSaving === true)
                    setTimeout(this.autoSave, this.autoSaveDelay, val, version);
                else
                    // Если уже внесли новые изменения, то сохраняем последнее
                if (version === this.version)
                {
                    console.log('formData', val);

                    const formData = this.getDifData(val);
                    if (!formData || Object.keys(formData).length === 0
                        || (formData.hasOwnProperty('id') && Object.keys(formData).length === 1))
                        return;

                    if (formData.hasOwnProperty('addApprovers') && (formData.addApprovers.length === 0))
                        formData.addApprovers = [''];

                    if (formData.hasOwnProperty('addObservers') && (formData.addObservers.length === 0))
                        formData.addObservers = [''];

                    formData.id = this.formData.step1.id;
                    console.log('Изменения', formData);

                    this.isAutoSaving = true;
                    let request = BX.ajax.runComponentAction('renins:job_profile', 'save', {
                        mode: 'class',
                        data: {
                            formData: formData
                        }
                    }).then((response) => {
                            console.log('Автосохранение', response);
                            this.formData.step1.id = response.data.formData.id + '';

                            this.isSaving = false;

                            this.initiator = response.data.initiator;
                            this.status = response.data.status;
                            this.statusClass = response.data.statusClass;
                            this.stage = response.data.stage;
                            this.stages = response.data.stages;
                            this.processingUser = response.data.processingUser;
                            this.processingUsers = response.data.processingUsers;
                            this.createDate = response.data.createDate;
                            this.updateDate = response.data.updateDate;
                            this.modelRole = response.data.modelRole;
                            this.roleSLA = response.data.roleSLA;
                            this.isAutoSaving = false;

                            if (this.breadcrumbs.length === 3)
                                this.breadcrumbs.pop();
                            this.breadcrumbs.push({ label: 'ID ' + this.formData.step1.id });
                            window.history.replaceState(null, null, '/renins/job_profile/?DRAFT=' + this.formData.step1.id);
                        },
                        (response) => {
                            console.log('Ошибка автосохранения', response);
                            this.isAutoSaving = false;
                        });

                    this.copyFormForEdit();
                }
            },
            stepHasErrors(step) {
                let errorsCount = 0;
                const requiredFields = Object.keys(this.requiredFields).filter(key => this.requiredFields[key].step == step);
                requiredFields.forEach(key => {
                    if (this.errors[key]) {
                        errorsCount ++;
                    }
                });
                return errorsCount > 0;
            },
            validateStep(step, revalidate) {
                //this.errors = {};
                let errorsCount = 0;
                //return errorsCount === 0;
                const requiredFields = Object.keys(this.requiredFields).filter(key => {
                    if(revalidate && !this.errors.hasOwnProperty(key)) {
                        return false;
                    }

                    if (this.requiredFields[key].step == step) {
                        return true;
                    }
                    return false;
                });
                console.log('requiredFields', requiredFields);
                requiredFields.forEach(key => {
                    if(this.errors.hasOwnProperty(key)) {
                        delete this.errors[key];
                    }
                    let relHasValue = false;
                    let fieldValue = this.formData['step' + step][key];
                    const hasRel = Boolean(this.requiredFields[key].rel) || this.requiredFields[key].relFields.length > 0; // Зависиме поле
                    if (hasRel && this.requiredFields[key].rel) {
                        const relValue = this.formData['step' + step][this.requiredFields[key].rel];
                        if(this.requiredFields[key].relValue.length) {
                            relHasValue = this.requiredFields[key].relValue.includes(relValue);
                        } else {
                            relHasValue = Boolean(relValue);
                        }
                    }

                    this.requiredFields[key].relFields.forEach(item => {
                        const relValue = this.formData['step' + step][item];
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
                        fieldValue = Object.keys(fieldValue).filter(item=> fieldValue[item]).length >= this.requiredFields[key].minObjValues;
                    }

                    // Проверка заполнение одного из полей
                    this.requiredFields[key].radioFields.forEach(field => {
                        if (!fieldValue) {
                            fieldValue = this.formData['step' + step][field];
                        }
                    });

                    // Провезка зависимых полей
                    if (hasRel) {
                        if (!fieldValue && relHasValue) {
                            this.errors[key] = true
                            //console.log(`${key}: обязательное для заполнения (зависимое)`);
                            errorsCount++;
                        }
                    } else if (!fieldValue) {
                        this.errors[key] = true
                        // console.log(`${key}: обязательное для заполнения`);
                        errorsCount++;
                    }
                });
                this.errors = {...this.errors};

                // Помечаем что шаг проходил проверку
                if (!revalidate && !this.validatedSteps.includes(step)) {
                    this.validatedSteps.push(step);
                }


                if (step === 8) {
                    const {
                        isNotInvolvedInBudgetManagement,
                        isControlTargetBudget,
                        isPrepareProposalsToSpendBudget,
                        hasAuthorityToMakeDecisions,
                        CnBSum,
                        nonCnBSum
                    } = this.formData.step8;

                    // Проверяем, есть ли поля в requiredFields
                    const fieldsToCheck = [
                        'isNotInvolvedInBudgetManagement',
                        'isControlTargetBudget',
                        'isPrepareProposalsToSpendBudget',
                        'hasAuthorityToMakeDecisions',
                        'CnBSum',
                        'nonCnBSum'
                    ];

                    // Проверяем, есть ли хотя бы одно из полей в requiredFields
                    const shouldValidate = fieldsToCheck.some(field => this.requiredFields.hasOwnProperty(field));

                    if (shouldValidate) {
                        const needValidateSums = isControlTargetBudget
                            || isPrepareProposalsToSpendBudget
                            || hasAuthorityToMakeDecisions;

                        // Проверяем суммы только если был триггер валидации
                        if (this.formData.step8.validationTriggered && needValidateSums) {
                            let sumErrors = 0;

                            if (!CnBSum && this.requiredFields.hasOwnProperty('CnBSum')) {
                                this.errors['CnBSum'] = true;
                                sumErrors++;
                            }

                            if (!nonCnBSum && this.requiredFields.hasOwnProperty('nonCnBSum')) {
                                this.errors['nonCnBSum'] = true;
                                sumErrors++;
                            }

                            if (sumErrors > 0) {
                                errorsCount += sumErrors;
                            }
                        }
                    }
                }


                return errorsCount === 0;
            },
            revalidateStep(step) {
                this.validateStep(step, true);
            },
            updateManagers(cost)
            {
                if (cost)
                {
                    let request = BX.ajax.runComponentAction('renins:job_profile', 'getUserByPid', {
                        mode: 'class',
                        data: {
                            pid: cost.func1Pid,
                        }
                    });
                    request.then((response) => {
                        this.formData.step1.func1Pid = response.data.id;
                        this.formData.step1.managerExcoFio = response.data.fio;
                        this.formData.step1.managerExcoIsObserver = true;
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
                        this.formData.step1.func2Pid = response.data.id;
                        this.formData.step1.managerLineFio = response.data.fio;
                        this.formData.step1.managerLineIsObserver = true;
                    }, function (response) {
                        console.log(response);
                        alert('Ошибка!');
                    });
                }
            },
        },
        watch: {
            formData: {
                handler(val, old) {
                    let version = ++this.version;
                    setTimeout(this.autoSave, this.autoSaveDelay, val, version);
                    this.revalidateStep(this.currentStep);
                },
                deep: true
            },
            'formData.step1.admManager'(value) {
                setTimeout(() => {
                    if (value) {
                        if (this.$refs.adm.$refs.position.value)
                            this.formData.step1.admManagerPosition = this.$refs.adm.$refs.position.value;
                    } else {
                        this.formData.step1.admManagerPosition = '';
                    }
                }, 50);
            },
            'formData.step1.funcManager'(value) {
                setTimeout(() => {
                    if (value) {
                        if (this.$refs.func.$refs.position.value)
                            this.formData.step1.funcManagerPosition = this.$refs.func.$refs.position.value;
                    } else {
                        this.formData.step1.funcManagerPosition = '';
                    }
                }, 50);
            },
            'formData.step1.costCenter'(value) {
                let cost = this.costCenters.find(item => item.value === value);
                if (cost)
                {
                    this.formData.step1.func1Name = cost.func1Name;
                    this.formData.step1.func2Name = cost.func2Name;

                    this.updateManagers(cost);
                }
                else
                {
                    this.formData.step1.func1Name = null;
                    this.formData.step1.func1Pid = null;
                    this.formData.step1.managerExcoFio = null;
                    this.formData.step1.managerExcoIsObserver = false;

                    this.formData.step1.func2Name = null;
                    this.formData.step1.func2Pid = null;
                    this.formData.step1.managerLineFio = null;
                    this.formData.step1.managerLineIsObserver = false;
                }
            },
            'formData.step2.hasSubs'(value) {
                if (value)
                {
                    if (parseInt(this.formData.step2.subordinatesCount) === 0)
                        this.formData.step2.subordinatesCount = '';
                    if (parseInt(this.formData.step2.allSubordinatesCount) === 0);
                    this.formData.step2.allSubordinatesCount = '';
                }
                else
                {
                    this.formData.step2.subordinatesCount = 0;
                    this.formData.step2.allSubordinatesCount = 0;
                }
            },
            'formData.step2.hasFuncSubs'(value) {
                if (value)
                {
                    if (parseInt(this.formData.step2.funcSubordinatesCount) === 0)
                        this.formData.step2.funcSubordinatesCount = '';
                }
                else
                    this.formData.step2.funcSubordinatesCount = 0;
            },
            'formData.step2.hasProjectSubs'(value) {
                if (value)
                {
                    if (parseInt(this.formData.step2.projectSubordinatesCount) === 0)
                        this.formData.step2.projectSubordinatesCount = '';
                }
                else
                    this.formData.step2.projectSubordinatesCount = 0;
            },
            'formData.step2.hasOutsourceSubs'(value) {
                if (value)
                {
                    if (parseInt(this.formData.step2.outsourceSubordinatesCount) === 0)
                        this.formData.step2.outsourceSubordinatesCount = '';
                }
                else
                    this.formData.step2.outsourceSubordinatesCount = 0;
            },

            'formData.step15.premiumMonth'(value) {
                if (value)
                {
                    this.formData.step15.premiumQuarter = false;
                    this.formData.step15.premiumHalfyear = false;
                    this.formData.step15.premiumYear = false;
                }
            },
            'formData.step15.premiumQuarter'(value) {
                if (value)
                {
                    this.formData.step15.premiumMonth = false;
                    this.formData.step15.premiumHalfyear = false;
                }
            },
            'formData.step15.premiumHalfyear'(value) {
                if (value)
                {
                    this.formData.step15.premiumMonth = false;
                    this.formData.step15.premiumQuarter = false;
                    this.formData.step15.premiumYear = false;
                }
            },
            'formData.step15.premiumYear'(value) {
                if (value)
                {
                    this.formData.step15.premiumMonth = false;
                    this.formData.step15.premiumHalfyear = false;
                }
            },
            'formData.step15.gradeNotDefined'(value) {
                if (value)
                    this.formData.step15.grade = '';
            },
            'formData.step15.forkMid'(value) {
                if (value)
                {
                    this.formData.step15.forkLow = Math.floor(parseInt(value.replaceAll(' ', '')) * 0.7);
                    this.formData.step15.forkHigh = Math.ceil(parseInt(value.replaceAll(' ', '')) * 1.3);
                }
                else
                {
                    this.formData.step15.forkLow = '';
                    this.formData.step15.forkHigh = '';
                }
            },
            'currentStep'(value) {
                if (value === 1) {
                    this.current_step_section = 'HR OD';
                }
                if (value >= 2 && value <= 13) {
                    this.current_step_section = 'Руководитель';
                }
                if (value === 14) {
                    this.current_step_section = 'T&D';
                }
            },
            'current_step_section'(value) {
                if (value === 'HR OD') {
                    this.currentStep = 1;
                }
                if ((value === 'Руководитель') && !(this.currentStep >= 2 && this.currentStep <= 13)) {
                    this.currentStep = 2;
                }
                if (value === 'T&D') {
                    this.currentStep = 14;
                }
                if (value === 'C&B') {
                    this.currentStep = 15;
                }
            },
            'formData.step8.CnBSum'(newVal) {
                if (newVal) this.errors.CnBSum = false;
            },
            'formData.step8.nonCnBSum'(newVal) {
                if (newVal) this.errors.nonCnBSum = false;
            },
            dragItems: {
                handler(newItems) {
                    // Синхронизируем изменения в dragItems с formData.step4.mainDuties
                    this.formData.step4.mainDuties = newItems;
                },
                deep: true, // Важно: наблюдаем за изменениями внутри массива
            },
            dragItemsAdd: {
                handler(newItems) {
                    // Синхронизируем изменения в dragItemsAdd с formData.step4.addDuties
                    this.formData.step4.addDuties = newItems;
                },
                deep: true, // Важно: наблюдаем за изменениями внутри массива
            },
        },
        computed: {
            recommendFormat() {
                let a = this.formData.step2.relationOutClients;
                let b = this.formData.step2.relationInClients;
                let c = this.formData.step2.physicService;
                let d = this.formData.step2.difficultAttractComps;
                let e = this.formData.step2.workModeSubs;

                if (!a && !b && !c && !d && !e)
                    return '';

                let if1 = ([a, b, c].indexOf('Да, нельзя делать удаленно') >= 0)
                    ? 'Офисный'
                    : (([a, b, c].indexOf('Да, частично можно делать удаленно') >= 0)
                        ? 'Гибридный'
                        : 'Удаленный');

                if (e === 'Да, есть подчиненные со стандартным режимом')
                    return 'Офисный';
                else
                {
                    if (e === 'Да, все подчиненные с комбинированным и дистанционным режимом')
                    {
                        if ([a, b, c].indexOf('Да, нельзя делать удаленно') >= 0)
                            return 'Офисный';
                        else
                            return 'Гибридный';
                    }
                    else
                    {
                        if ([a, b, c].indexOf('Да, нельзя делать удаленно') >= 0)
                            return 'Офисный';
                        else
                        {
                            if ([a, b, c].indexOf('Да, частично можно делать удаленно') >= 0)
                                return 'Гибридный';
                            else
                            {
                                if ((if1 === 'Удаленный') && (d === 'Да'))
                                    return 'Удаленный';
                                else
                                {
                                    if ([a, b, c].indexOf('Да, нельзя делать удаленно') >= 0)
                                        return 'Офисный';
                                    else
                                    {
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
            },

        },
    }).mount('#job_profile');
});
