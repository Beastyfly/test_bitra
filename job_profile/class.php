<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Errorable;
use Bitrix\Main\Type;
use Renins\BP;
use Renins\BP\JobProfile;
use Renins\IB;
use Renins\Orm\JobProfileApprovalsTable as JobApprovals;

enum fieldTypes
{
    case string;
    case boolean;
    case number;
    case json;
    case list;
}

class JobProfileComponent extends CBitrixComponent implements Controllerable, Errorable
{
    protected $errorCollection;

    public function executeComponent()
    {
        \Bitrix\Main\Loader::includeModule('renins');
        //Вынесенная логика для шаблона
        $templateClassFile = __DIR__ . '/templates/' . $this->getTemplateName() . '/class.php';
        if (file_exists($templateClassFile))
        {
            $class = include $templateClassFile;
            if (method_exists($class, 'execute'))
            {
                $class->setContext($this);
                $class->execute();
            }
        }
        $this->includeComponentTemplate();
    }

    public function saveAction($formData)
    {
        $ib = new \Renins\IB('job_profile');

        if (!isset($formData['id']) || empty($formData['id']))
        {
            $formData['id'] = $ib->add([
                'NAME' => 'Заявка на утверждение профиля должности от ' . date('d.m.Y'),
                'PROPERTY_VALUES' => [
                    'STAGE' => 'create',
                    'STATUS' => 'draft'
                ]
            ]);
            JobApprovals::initStages( $formData['id'] );
            // Запуск SLA заполнения
            $jobProfile = new JobProfile( $formData['id'] );
            $jobProfile->startSLA(JobApprovals::STAGE_CREATE);
        }
        else
        {
            if ($formData['editMode'] && $formData['delegate'])
            {
                $this->delegateAction($formData['id'], $formData['delegate']);
            }
        }

        $props = [];
        foreach ($formData as $key => $value)
        {
            foreach ($this->fieldsMap as $row)
            {
                if ($row[0] === $key)
                {
                    switch ($row[2])
                    {
                        case fieldTypes::boolean:
                            $value = $value === 'N' || $value === 'false' || !$value ? 'N' : 'Y';
                            break;
                        case fieldTypes::number:
                            $value = preg_replace('/[^\d\.\,]/', '', $value);
                            break;
                        case fieldTypes::json:
                            if (in_array($key, ['languages', 'review']))
                                $value = array_filter($value, function($item) {
                                    return !!$item['name'];
                                });

                            if (in_array($key, ['mainDuties', 'addDuties']))
                                $value = array_filter($value, function($item) {
                                    return !!$item['duty'] && !!$item['result'];
                                });

                            $value = json_encode($value);
                            break;
                        case fieldTypes::list:
                            $value = $ib->getEnumIdByValue($row[1], $value);
                            break;
                    }
                    $props[ $row[1] ] = $value;
                }
            }
        }

        $ib->updateProps($formData['id'], $props);

        // Установка согласующих
        if (!$formData['sendToApprove'] &&
            count(array_intersect(array_keys($formData), ['admManager', 'funcManager', 'headAdmManager', 'addApprovers'])))
            JobApprovals::setApprovers($formData['id']);

        $item = $ib->GetByID($formData['id']);
        $jobProfile = new JobProfile($formData['id']);

        $stages = JobApprovals::getRecordsArray($formData['id']);

        $roleSLA = $jobProfile->getStageSLA($jobProfile->getStage());
        if ($roleSLA)   $formattedRoleSLA = BP::formatWorkHoursDeltaDates($roleSLA);
        else            $formattedRoleSLA = null;

        return [
            'props' => $props,
            'formData' => $formData,
            'initiator' => \Renins\User::getById($item['CREATED_BY']),
            'status' => JobApprovals::STATUSES_MAP[ $item['PROPS']['STATUS']['VALUE'] ],
            'statusClass' => JobApprovals::STATUSES_CLASS[ $item['PROPS']['STATUS']['VALUE'] ],
            'stage' => JobApprovals::STAGES_MAP[ $item['PROPS']['STAGE']['VALUE'] ],
            'stages' => $stages,
            'processingUser' => $jobProfile->getProcessingUser(),
            'processingUsers' => $jobProfile->getProcessingUsers(),
            'createDate' => date('d.m.Y', strtotime($item['DATE_CREATE'])),
            'updateDate' => date('d.m.Y', strtotime($item['TIMESTAMP_X'])),
            'work' => BP::formatWorkHoursDeltaDates(
                BP::getWorkHoursDeltaDates(
                    $item['DATE_CREATE'],
                    $item['PROPS']['finished_agreement']['VALUE'],
                )
            ),
            'roleSLA' => $formattedRoleSLA,
            'modelRole' => $jobProfile->getModelRole(),
        ];
    }

    /**
     * Отправка на согласование
     *
     * @param $id
     * @param $comment
     * @return bool[]
     * @throws Exception
     */
    public function sendAction($id, $comment = '')
    {
        global $USER;
        $ib = new \Renins\IB('job_profile');
        $ib->updateProp($id, 'KOMMENTARII_OTPRAVKI', $comment);
        $ods = \Renins\User::getUsersFromIB('recruitment_od');

        $records = JobApprovals::getRecordsArray($id);
        foreach ($records as $record)
        {
            // Согласование этапа создания
            if (($record['STAGE'] === JobApprovals::STAGE_CREATE) && ($record['STATUS'] !== JobApprovals::STATUS_SIGNED))
            {
                JobApprovals::update(
                    $record['ID'], ['END_TIME' => new Type\DateTime(), 'STATUS' => JobApprovals::STAGE_STATUS_SIGNED]
                );
                // Остановка SLA создания
                JobApprovals::stopSLA($id, JobApprovals::STAGE_CREATE);
            }
            // Завершение этапа заполнения формы и запуск БП согласования (если не ОД)
            if (($record['STAGE'] === JobApprovals::STAGE_FILLING) && ($record['STATUS'] !== JobApprovals::STATUS_SIGNED))
            {
                $ib::stopBP($id);
                if (in_array($USER->GetID(), $ods))
                    JobApprovals::update($record['ID'], [
                        'END_TIME' => new Type\DateTime(),
                        'STATUS' => JobApprovals::STAGE_STATUS_SIGNED
                    ]);
                else
                    JobApprovals::approveStage($record['ID'], $comment);
            }
            // Завершение этапа проверки OD и запуск БП согласования (если ОД)
            if (($record['STAGE'] === JobApprovals::STAGE_CHECKING_OD) && ($record['STATUS'] !== JobApprovals::STATUS_SIGNED))
            {
                if (in_array($USER->GetID(), $ods))
                {
                    $ib::stopBP($id);
                    JobApprovals::update($record['ID'], [
                        'RESPONSIBLE_USER' => $USER->GetID(),
                        'END_TIME' => new Type\DateTime(),
                        'STATUS' => JobApprovals::STAGE_STATUS_SIGNED
                    ]);
                    JobApprovals::approveStage($record['ID'], $comment);
                }
            }
        }
        return ['status' => true];
    }

    /**
     * Получение данных из заявки
     *
     * @param $id
     * @return array[]
     */
    public function loadAction($id)
    {
        $ib = new \Renins\IB('job_profile');
        $item = $ib->GetByID($id);

        $formData['id'] = $item['ID'];

        if ($item)
        {
            foreach ($this->fieldsMap as $row)
            {
                $value = $item['PROPS'][$row[1]]['~VALUE'];
                switch ($row[2])
                {
                    case fieldTypes::boolean:
                        $value = $value === 'Y';
                        break;
                    case fieldTypes::json:
                        $value = json_decode($value, true);
                        break;
                }
                $formData[ $row[0] ] = $value;
            }
        }

        $formData['admManagerFio'] = $this->getManagersData([ $formData['admManager'] ]);
        $formData['funcManagerFio'] = $this->getManagersData([ $formData['funcManager'] ]);
        $formData['headAdmManagerFio'] = $this->getManagersData([ $formData['headAdmManager'] ]);
        $formData['delegateFio'] = $this->getManagersData([ $formData['delegate'] ]);
        foreach ($formData['addApprovers'] as $approver)
        {
            $formData['addApproversFio'][] = $this->getManagersData([ $approver ]);
        }
        foreach ($formData['addObservers'] as $observer)
        {
            $formData['addObserversFio'][] = $this->getManagersData([ $observer ]);
        }
        if (count($formData['addObservers'] ?:[]) == 0)
            $formData['addObserversFio'] = [];

        return [
            'formData' => $formData
        ];
    }

    /**
     * Получение данных из заявки для формы просмотра
     *
     * @param $id
     */
    public function loadDetailAction($id)
    {
        global $USER;
        \Bitrix\Main\Loader::includeModule('renins');

        $ib = new \Renins\IB('job_profile');
        $item = $ib->GetByID($id);

        $stages = JobApprovals::getRecordsArray($id);

        $jobProfile = new JobProfile($id);
        $roleSLA = $jobProfile->getStageSLA($jobProfile->getStage());
        if ($roleSLA)   $formattedRoleSLA = BP::formatWorkHoursDeltaDates($roleSLA);
        else            $formattedRoleSLA = null;

        $formData = $this->loadAction($id)['formData'];

        $costCenters = [];
        $functions = \Renins\Orm\BossCostCenterTable::getRecordsArray();
        foreach ($functions as $function)
        {
            $costCenters[] = [
                'label' =>  $function['BOSS_ID'],
                'value' => $function['BOSS_ID'],
                'description' => $function['NAME'],
                'func1Name' => $function['FUNCTION_NAME'],
                'func1Pid' => $function['FUNC1_PID'],
                'func2Name' => $function['FUNCTION2_NAME'],
                'func2Pid' => $function['FUNC2_PID'],
            ];
        }

        $data = $this->getBossData($id);

        $role = $jobProfile->getRole($USER->GetID());
        $isApprover = in_array($role, [ JobProfile::ROLE_ADM_MANAGER, JobProfile::ROLE_FUNC_MANAGER,
            JobProfile::ROLE_ADM_MANAGER_HEAD, JobProfile::ROLE_APPROVER ]);

        return [
            'currentUserId' => $USER->GetID(),
            'isOD' => \Renins\User::isInIB($USER->GetID(), 'recruitment_od'),
            'isFiller' => ($role == JobProfile::ROLE_FILLER),
            'isApprover' => $isApprover,
            'isTnD' => ($role == JobProfile::ROLE_TND),
            'isCnB' => ($role == JobProfile::ROLE_CNB),
            'formData' => $formData,
            'initiator' => \Renins\User::getById($item['CREATED_BY']),
            'inTrash' => ($item['PROPS']['ARCHIVE']['VALUE'] == 1),
            'status' => JobApprovals::STATUSES_MAP[ $item['PROPS']['STATUS']['VALUE'] ],
            'statusId' => $item['PROPS']['STATUS']['VALUE'],
            'statusClass' => JobApprovals::STATUSES_CLASS[ $item['PROPS']['STATUS']['VALUE'] ],
            'stage' => JobApprovals::STAGES_MAP[ $item['PROPS']['STAGE']['VALUE'] ],
            'stageId' => $item['PROPS']['STAGE']['VALUE'],
            'processingUser' => $jobProfile->getProcessingUser(),
            'processingUsers' => $jobProfile->getProcessingUsers(),
            'createDate' => date('d.m.Y', strtotime($item['DATE_CREATE'])),
            'updateDate' => date('d.m.Y', strtotime($item['TIMESTAMP_X'])),
            'work' => BP::formatWorkHoursDeltaDates(
                BP::getWorkHoursDeltaDates(
                    $item['DATE_CREATE'],
                    $item['PROPS']['finished_agreement']['VALUE'],
                )
            ),
            'modelRole' => $jobProfile->getModelRole(),
            'roleSLA' => $formattedRoleSLA,
            'stages' => $stages,
            'isFullyApproved' => ($jobProfile->getStage() === JobApprovals::STAGE_EMBEDDING)
                && ($jobProfile->getStatus() === JobApprovals::STATUS_SIGNED),
            'allowedSend' => $jobProfile->allowedSend(),
            'allowedDelete' => $jobProfile->allowedDelete(),
            'allowedChangeStages' => $this->allowedChangeStages($item, $stages),
            'allowedApprove' => $this->allowedApprove($item, $stages),
            'allowedApproveEarlier' => $this->allowedApproveEarlier($stages),
            'allowedGetToWork' => $this->allowedGetToWork($item, $stages),
            'allowedRevoke' => $this->allowedRevoke($stages),
            'returningStages' => $this->returningStages($stages),
            'editStageFormData' => [],
            'costCenters' => $costCenters,
            'departments' => $data['departments'],
            'diFiles' => $data['diFiles'],
            'branches' => $data['branches'],
            'locations' => $data['locations'],
            'requiredFields' => $this->getRequiredFields(),
            'isAccessPage' => $ib->checkIBlockID($item['IBLOCK_ID']),
        ];
    }


    /**
     * Разрешено изменять этапы согласования
     *
     * @param $item
     * @param $stages
     * @return bool
     */
    public function allowedChangeStages($item, $stages)
    {
        global $USER;
        // Организационное проектирование
        $isOD = \Renins\User::isInIB($USER->GetID(), 'recruitment_od');
        if ($isOD)
        {
            return true;

            foreach ($stages as $stage)
            {
                if (in_array($stage['STAGE'],[ JobApprovals::STAGE_CREATE, JobApprovals::STAGE_CHECKING_OD ])
                    && ($stage['STATUS'] !== JobApprovals::STAGE_STATUS_SIGNED))
                    return true;
            }
        }

        return false;
    }

    /**
     * Разрешено делегировать заполнение
     *
     * @param $item
     * @param $stages
     * @return bool
     */
    public function allowedDelegateFilling($stages)
    {
        global $USER;
        $isOD = \Renins\User::isInIB($USER->GetID(), 'recruitment_od');

        if(!$isOD) {
            // HR OD может делегировать на этапе заполнения
            return false;
        } else {
            if (!count($stages)) {
                // Заявка еще не сохранялась
                return true;
            }
        }

        $hasOnApprovalStage = false;
        foreach ($stages as $stage)
        {
            if (in_array($stage['STATUS'], [ JobApprovals::STAGE_STATUS_ON_APPROVAL]))
            {
                if ($stage['STAGE'] === JobApprovals::STAGE_CREATE)
                {
                    return true;
                }
                $hasOnApprovalStage = true;
            }
        }
        return $hasOnApprovalStage ? false : $isOD;
    }

    /**
     * Разрешено согласовывать
     *
     * @param $item
     * @param $stages
     * @return bool
     */
    private function allowedApprove($item, $stages)
    {
        global $USER;

        foreach ($stages as $stage)
        {
            if (in_array($stage['STATUS'], [ JobApprovals::STAGE_STATUS_ON_APPROVAL]))
            {
                if (in_array($stage['STAGE'], [ JobApprovals::STAGE_CREATE, JobApprovals::STAGE_FILLING ]))
                    return false;

                // На согласовании у сотрудника
                if ($stage['RESPONSIBLE_USER'] === $USER->GetID())
                    return true;
            }
        }
        return false;
    }

    /**
     * Ранее участвовавший
     *
     * @param $stages
     * @return bool
     */
    private function allowedApproveEarlier($stages)
    {
        global $USER;

        foreach ($stages as $stage)
        {
            if (in_array($stage['STATUS'], [ JobApprovals::STAGE_STATUS_ON_APPROVAL]))
                return false;

            if (!in_array($stage['STAGE'], [ JobApprovals::STAGE_CREATE, JobApprovals::STAGE_FILLING ])
                && ($stage['RESPONSIBLE_USER'] === $USER->GetID()))
                return true;
        }
        return false;
    }

    /**
     * Разрешено отзывать
     *
     * @param $item
     * @param $stages
     * @return bool
     */
    private function allowedRevoke($stages)
    {
        global $USER;

        foreach ($stages as $stage)
        {
            if (in_array($stage['STATUS'], [ JobApprovals::STAGE_STATUS_ON_APPROVAL]))
            {
                if (in_array($stage['STAGE'], [
                    JobApprovals::STAGE_CREATE,
                    JobApprovals::STAGE_FILLING,
                    JobApprovals::STAGE_CHECKING_OD,
                    JobApprovals::STAGE_EMBEDDING
                ]))
                    return false;

                if ($stage['RESPONSIBLE_USER'] !== $USER->GetID())
                    return true;
            }
        }
        return false;
    }

    /**
     * Разрешено брать в работу
     *
     * @param $item
     * @param $stages
     * @return bool
     */
    private function allowedGetToWork($item, $stages)
    {
        global $USER;
        $isOD = Renins\User::isInIB($USER->GetID(), 'recruitment_od');
        $isCnB = Renins\User::isInIB($USER->GetID(), 'sb');
        $isTnD = Renins\User::isInIB($USER->GetID(), 'td_approvers');

        $canGetToWork = false;
        foreach ($stages as $stage)
        {
            if ($isOD && in_array($stage['STAGE'], [ JobApprovals::STAGE_CHECKING_OD, JobApprovals::STAGE_EMBEDDING ]) && $stage['STATUS'] === JobApprovals::STAGE_STATUS_ON_APPROVAL)
                $canGetToWork = true;

            if ($isTnD && $stage['STAGE'] === JobApprovals::STAGE_TND && $stage['STATUS'] === JobApprovals::STAGE_STATUS_ON_APPROVAL)
                $canGetToWork = true;

            if ($isCnB && $stage['STAGE'] === JobApprovals::STAGE_CNB && $stage['STATUS'] === JobApprovals::STAGE_STATUS_ON_APPROVAL)
                $canGetToWork = true;
        }
        return $canGetToWork;
    }

    /**
     * Получить ФИО сотрудника
     *
     * @param array $ids
     * @return string
     */
    private function getManagersData(array $ids): string
    {
        $managersFio = [];
        foreach ($ids as $managerId)
        {
            $managersFio[] = \Renins\User::getFioById($managerId);
        }
        return implode(', ', $managersFio);
    }

    private function getIDSFilterfromOD()
    {
        global $USER;
        $ib = new Renins\IB('recruitment_od', [
            'USER',
        ]);
        $ib->setArSort(['ID' => 'ASC']);
        $ib->setNavParams([
            'nPageSize' => 100,
            'iNumPage' => 1
        ]);
        $list = $ib->getList();
        $resultFilter = [
            'LOGIC' => 'OR',
            [ 'PROPERTY_APPROVAL_EMPLOYEE' => false ],
            [ 'PROPERTY_APPROVAL_EMPLOYEE' => $USER->GetID() ],
        ];
        foreach ($list as $user){
            $resultFilter[] =  [ 'PROPERTY_APPROVAL_EMPLOYEE' => $user['PROPERTY_USER_VALUE']];
        }
        return $resultFilter;
    }

    public function listLoadRecordsAction($sort = 'ID', $order = 'ASC', $page = 1, $pageSize = 10000, $tab = 'active', $q = [])
    {
        global $USER;
        $isOD = Renins\User::isInIB($USER->GetID(), 'recruitment_od');
        $isTnD = Renins\User::isInIB($USER->GetID(), 'td_approvers');
        $isCnB = Renins\User::isInIB($USER->GetID(), 'sb');

        $ib = new Renins\IB('job_profile', [
            'NAZVANIE_DOLZHNOSTI',
            'STAGE',
            'STATUS',
            'COST_CENTER',
            'FUNC1_NAME',
            'FUNC2_NAME',
            'APPROVAL_EMPLOYEE',
            'APPROVAL_CNB_EMPLOYEE',
            'APPROVAL_TND_EMPLOYEE',
            'finished_agreement',
            'ARCHIVE',
            'PODRAZDELENIE'
        ]);
        $ib->setArSort([$sort => $order]);
        $ib->setNavParams([
            'nPageSize' => $pageSize,
            'iNumPage' => $page
        ]);

        if ($tab == 'my-action')
        {
            $ORfilter = [ 'PROPERTY_APPROVAL_EMPLOYEE' => $USER->GetID() ]; // Согласующий сотрудник

            if ($isOD)
            {
                $ORfilter = [];
                $odFilterUser = $this->getIDSFilterfromOD();
                $ORfilter[] = [
                    'LOGIC' => 'AND',
                    'PROPERTY_STAGE' => [
                        JobApprovals::STAGE_CHECKING_OD,
                        JobApprovals::STAGE_EMBEDDING,
                        JobApprovals::STAGE_CREATE,
                        JobApprovals::STAGE_FILLING
                    ],
                    $odFilterUser
                ];
            } else {
                // Мои заявки на этапе создания
                $ORfilter[] = [
                    'LOGIC' => 'AND',
                    'PROPERTY_STAGE' => JobApprovals::STAGE_CREATE,
                    'CREATED_BY' => $USER->GetID()
                ];
                // На заполнении у ответственного
                $ORfilter[] = [
                    'LOGIC' => 'AND',
                    'PROPERTY_OTVETSTVENNYY_ZA_ZAPOLNENIE' => $USER->GetID(),
                    'PROPERTY_STAGE' => JobApprovals::STAGE_FILLING
                ];
            }
            if ($isTnD)
            {
                $ORfilter[] = [
                    'LOGIC' => 'AND',
                    'PROPERTY_STAGE' => [
                        JobApprovals::STAGE_TND,
                        JobApprovals::STAGE_CNB_AND_TND,
                    ],
                    [
                        'LOGIC' => 'OR',
                        [ 'PROPERTY_APPROVAL_EMPLOYEE' => $USER->GetID() ],
                        [ 'PROPERTY_APPROVAL_EMPLOYEE' => false ]
                    ]
                ];
            }
            if($isCnB)
            {
                $ORfilter[] = [
                    'LOGIC' => 'AND',
                    'PROPERTY_STAGE' => [
                        JobApprovals::STAGE_CNB,
                        JobApprovals::STAGE_CNB_AND_TND,
                    ],
                    [
                        'LOGIC' => 'OR',
                        [ 'PROPERTY_APPROVAL_EMPLOYEE' => $USER->GetID() ],
                        [ 'PROPERTY_APPROVAL_EMPLOYEE' => false ]
                    ]
                ];
            }
            // Мои заявки на этапе создания
            $ORfilter[] = [
                'LOGIC' => 'AND',
                'CREATED_BY' => $USER->GetID(),
                'PROPERTY_STAGE' => JobApprovals::STAGE_CREATE,
            ];

            // На заполнении у ответственного
            $ORfilter[] = [
                'LOGIC' => 'AND',
                'PROPERTY_OTVETSTVENNYY_ZA_ZAPOLNENIE' => $USER->GetID(),
                'PROPERTY_STAGE' => JobApprovals::STAGE_FILLING
            ];


            // Заявки на мне или не взяты в работу на моём этапе (cnb или tnd)
            $ib->setFilterLogicParam($ORfilter);
            // Не согласованная полностью
            $ib->setFilterLogicParam([
                '!PROPERTY_STAGE' => JobApprovals::STAGE_EMBEDDING,
                '!PROPERTY_STATUS' => JobApprovals::STATUS_SIGNED
            ]);
            $ib->setFilterParam('!PROPERTY_ARCHIVE', 1);
            $ib->setFilterParam('!PROPERTY_STATUS', JobApprovals::STATUS_ARCHIVE);
        }
        else if ($tab == 'active')
        {
            if ($isOD)
            {
                $ORfilter = [];
            } else {
                $ORfilter = ['CREATED_BY' => $USER->GetID()];
                $ORfilter[] = ['PROPERTY_FUNC1_PID' => $USER->GetID()];
                $ORfilter[] = ['PROPERTY_FUNC2_PID' => $USER->GetID()];
                $ORfilter[] = ['PROPERTY_ADD_NABLYUDATELI' => $USER->GetID()];
                $ORfilter[] = ['PROPERTY_SHOW_FOR_APPROVALS' => $USER->GetID()];
            }
            $ib->setFilterLogicParam($ORfilter);
            // Не согласованная полностью
            $ib->setFilterLogicParam([
                '!PROPERTY_STAGE' => JobApprovals::STAGE_EMBEDDING,
                '!PROPERTY_STATUS' => JobApprovals::STATUS_SIGNED
            ]);
            $ib->setFilterParam('!PROPERTY_STAGE', JobApprovals::STAGE_CREATE);
            $ib->setFilterParam('!PROPERTY_STATUS', [
                JobApprovals::STATUS_DRAFT,
                JobApprovals::STATUS_REJECT,
                JobApprovals::STATUS_REVOKED
            ]);
            $ib->setFilterParam('finished_agreement', false);
            $ib->setFilterParam('!PROPERTY_STATUS', JobApprovals::STATUS_ARCHIVE);
            $ib->setFilterParam('!PROPERTY_ARCHIVE', 1);
        }
        else if ($tab == 'approved')
        {
            // Утвержденные
            if($isOD){
                $ib->setFilterLogicParam([]);
            } else {
                $ib->setFilterLogicParam([
                    'CREATED_BY' => $USER->GetID(), // Инициатор заявки
                    'PROPERTY_FUNC1_PID' => $USER->GetID(),
                    'PROPERTY_FUNC2_PID' => $USER->GetID(),
                    'PROPERTY_ADD_NABLYUDATELI' => $USER->GetID(),
                    'PROPERTY_SHOW_FOR_APPROVALS' => $USER->GetID(), // Участник согласований
                    'PROPERTY_APPROVAL_CNB_EMPLOYEE' => $USER->GetID(), // Согласующий сотрудник
                    'PROPERTY_APPROVAL_TND_EMPLOYEE' => $USER->GetID(), // Согласующий сотрудник
                ]);
            }
            $ib->setFilterParam('PROPERTY_STAGE', JobApprovals::STAGE_EMBEDDING);
            $ib->setFilterParam('PROPERTY_STATUS', JobApprovals::STATUS_SIGNED);
            $ib->setFilterParam('!PROPERTY_STATUS', JobApprovals::STATUS_ARCHIVE);
            $ib->setFilterParam('!PROPERTY_ARCHIVE', 1);
        }
        else if ($tab == 'trash')
        {
            $ib->setFilterParam('PROPERTY_ARCHIVE', 1);
        }
        else if ($tab == 'archive')
        {
            if($isOD){
                $ib->setFilterLogicParam([]);
            } else {
                // Утвержденные
                $ib->setFilterLogicParam([
                    'CREATED_BY' => $USER->GetID(), // Инициатор заявки
                    'PROPERTY_FUNC1_PID' => $USER->GetID(),
                    'PROPERTY_FUNC2_PID' => $USER->GetID(),
                    'PROPERTY_ADD_NABLYUDATELI' => $USER->GetID(),
                    'PROPERTY_SHOW_FOR_APPROVALS' => $USER->GetID(), // Участник согласований
                    'PROPERTY_APPROVAL_CNB_EMPLOYEE' => $USER->GetID(), // Согласующий сотрудник
                    'PROPERTY_APPROVAL_TND_EMPLOYEE' => $USER->GetID(), // Согласующий сотрудник
                ]);
            }
            $ib->setFilterParam('PROPERTY_STATUS', JobApprovals::STATUS_ARCHIVE);
            $ib->setFilterParam('!PROPERTY_ARCHIVE', 1);
        }
        else if ($tab == 'all')
        {
            $ib->setFilterParam('!PROPERTY_ARCHIVE', 1);
        }

        $list = $ib->getList();

        if ($isOD)
            $processingUsers = $this->getApprovers('recruitment_od');
        else if ($isTnD)
            $processingUsers = $this->getApprovers('td_approvers');
        else if($isCnB)
            $processingUsers = $this->getApprovers('sb');
        else
            $processingUsers = [];

        $processingUsers = array_map(static function ($userId)
        {
            $user = \Renins\User::getByIdLight($userId);
            return [
                'value' => $user['id'],
                'label' => $user['fio'],
                'description' => $user['position']
            ];
        }, $processingUsers);

        // Данные для фильтра
        $costCenters = [];
        $functions = \Renins\Orm\BossCostCenterTable::getRecordsArray();
        foreach ($functions as $function)
        {
            $costCenters[ $function['BOSS_ID'] ] = [
                'label' =>  $function['BOSS_ID'],
                'value' => $function['BOSS_ID'],
                'description' => $function['NAME'],
                'func1Name' => $function['FUNCTION_NAME'],
                'func1Pid' => $function['FUNC1_PID'],
                'func2Name' => $function['FUNCTION2_NAME'],
                'func2Pid' => $function['FUNC2_PID'],
            ];
        }

        $createdCostCenters = [];
        $createdFunc1 = [];
        $createdFunc2 = [];
        $createdStatuses = [];
        $createdIDs = [];
        $createdPodraz = [];
        $createdOtvets = [];


        $list = array_map(static function ($item) use ($USER, $isOD, $isCnB, $isTnD, $processingUsers, $q)
        {
            // Фильтрация
            if (($q[0] && ($q[0] != 'null') && ($q[0] != $item['PROPERTY_COST_CENTER_VALUE']))
                || ($q[1] && ($q[1] != 'null') && ($q[1] != $item['PROPERTY_FUNC1_NAME_VALUE']))
                || ($q[2] && ($q[2] != 'null') && ($q[2] != $item['PROPERTY_FUNC2_NAME_VALUE']))
                || ($q[3] && ($q[3] != 'null') && ($q[3] != $item['PROPERTY_STATUS_VALUE']))
                || ($q[4] && ($q[4] != 'null') && ($q[4] != $item['ID']))
                || ($q[5] && ($q[5] != 'null') && ($q[5] != $item['PROPERTY_PODRAZDELENIE_VALUE']))
            )
                return null;

            $processingUser = null;
            $processingUserId = $item['PROPERTY_APPROVAL_EMPLOYEE_VALUE'];

            if ($processingUserId){
                $processingUser = \Renins\User::getByIdLight($processingUserId);
                $userInfo = \CUser::GetByID($processingUserId);
                if($arUser = $userInfo->Fetch()) {
                    $processingUser['phone'] = $arUser['PERSONAL_PHONE'];
                    $processingUser['photo'] = \CFile::GetPath($arUser['PERSONAL_PHOTO']);
                }
            }

            $canDelete = false;
            if (($isOD
                    && in_array($item['PROPERTY_STATUS_VALUE'], [
                        JobApprovals::STATUS_DRAFT,
                        JobApprovals::STATUS_REJECT,
                        JobApprovals::STATUS_REVOKED,
                        JobApprovals::STATUS_ON_REFINEMENT,
                        JobApprovals::STATUS_TRASH,
                        JobApprovals::STATUS_ARCHIVE,
                    ]))
                || ($item['PROPERTY_ARCHIVE_VALUE'] == 1))
            {
                $canDelete = true;
            }

            // Определение может есть ли права брать в работу на шагах cnb и tnd
            $onApprovalInWork = [ JobApprovals::STATUS_ON_APPROVAL, JobApprovals::STATUS_IN_WORK ];
            $stages = JobApprovals::getRecordsArray($item['ID']);
            $canGetToWork = false;
            foreach ($stages as $stage)
            {
                if ($isOD && in_array($stage['STAGE'], [ JobApprovals::STAGE_CHECKING_OD, JobApprovals::STAGE_EMBEDDING ]) && in_array($stage['STATUS'], $onApprovalInWork))
                    $canGetToWork = true;

                if ($isTnD && ($stage['STAGE'] === JobApprovals::STAGE_TND) && in_array($stage['STATUS'], $onApprovalInWork))
                    $canGetToWork = true;

                if ($isCnB && ($stage['STAGE'] === JobApprovals::STAGE_CNB) && in_array($stage['STATUS'], $onApprovalInWork))
                    $canGetToWork = true;
            }

            $item['DATE_CREATE_FORMATTED'] = date('d.m.Y', strtotime($item['DATE_CREATE']));
            if(date('d.m.Y', strtotime($item['PROPERTY_FINISHED_AGREEMENT_VALUE'])) == '01.01.1970'){
                $item['PROPERTY_FINISHED_AGREEMENT_FORMATTED'] = '-';
            } else {
                $item['PROPERTY_FINISHED_AGREEMENT_FORMATTED'] = date('d.m.Y', strtotime($item['PROPERTY_FINISHED_AGREEMENT_VALUE']));
            }
            $item['STAGE'] = JobApprovals::STAGES_MAP[$item['PROPERTY_STAGE_VALUE']];
            $item['STATUS'] = JobApprovals::STATUSES_MAP[$item['PROPERTY_STATUS_VALUE']];
            $item['STATUS_CLASS'] = JobApprovals::STATUSES_CLASS[$item['PROPERTY_STATUS_VALUE']];
            $item['canDelete'] = $canDelete;
            $item['canGetToWork'] = $canGetToWork;
            $item['processingUser'] = $processingUser;
            $item['processingUsers'] = $processingUsers;
            return $item;
        }, $list);
        $list = array_values(array_filter($list));
        foreach ($list as $row)
        {
            if ($row['PROPERTY_COST_CENTER_VALUE'])
                $createdCostCenters[ $row['PROPERTY_COST_CENTER_VALUE'] ] = [
                    'label' =>  $row['PROPERTY_COST_CENTER_VALUE'] . ' - '
                        . $costCenters[ $row['PROPERTY_COST_CENTER_VALUE'] ]['description'],
                    'value' => $row['PROPERTY_COST_CENTER_VALUE'],
                ];
            if ($row['PROPERTY_FUNC1_NAME_VALUE'])
                $createdFunc1[ $row['PROPERTY_FUNC1_NAME_VALUE'] ] = [
                    'label' => $row['PROPERTY_FUNC1_NAME_VALUE'],
                    'value' => $row['PROPERTY_FUNC1_NAME_VALUE']
                ];
            if ($row['PROPERTY_FUNC2_NAME_VALUE'])
                $createdFunc2[ $row['PROPERTY_FUNC2_NAME_VALUE'] ] = [
                    'label' => $row['PROPERTY_FUNC2_NAME_VALUE'],
                    'value' => $row['PROPERTY_FUNC2_NAME_VALUE']
                ];
            if ($row['PROPERTY_STATUS_VALUE'])
                $createdStatuses[ $row['PROPERTY_STATUS_VALUE'] ] = [
                    'label' => JobApprovals::STATUSES_MAP[ $row['PROPERTY_STATUS_VALUE'] ],
                    'value' => $row['PROPERTY_STATUS_VALUE']
                ];
            if ($row['ID'])
                $createdIDs[ $row['ID'] ] = [
                    'label' => $row['ID'],
                    'value' => $row['ID']
                ];
            if ($row['PROPERTY_PODRAZDELENIE_VALUE'])
                $createdPodraz[ $row['PROPERTY_PODRAZDELENIE_VALUE'] ] = [
                    'label' => $row['PROPERTY_PODRAZDELENIE_VALUE'],
                    'value' => $row['PROPERTY_PODRAZDELENIE_VALUE']
                ];
            if ($row['PROPERTY_APPROVAL_EMPLOYEE_VALUE'])
                $createdOtvets[ $row['PROPERTY_APPROVAL_EMPLOYEE_VALUE'] ] = [
                    'label' => $row['PROPERTY_APPROVAL_EMPLOYEE_VALUE'],
                    'value' => $row['PROPERTY_APPROVAL_EMPLOYEE_VALUE']
                ];
        }

        $list = array_map(static function ($item) use ($USER, $isOD, $isCnB, $isTnD, $processingUsers, $q)
        {
            // Фильтрация
            if (($q[0] && ($q[0] != 'null') && ($q[0] != $item['PROPERTY_COST_CENTER_VALUE']))
                || ($q[1] && ($q[1] != 'null') && ($q[1] != $item['PROPERTY_FUNC1_NAME_VALUE']))
                || ($q[2] && ($q[2] != 'null') && ($q[2] != $item['PROPERTY_FUNC2_NAME_VALUE']))
                || ($q[3] && ($q[3] != 'null') && ($q[3] != $item['PROPERTY_STATUS_VALUE'])))
                return null;

            $processingUser = null;
            $processingUserId = $item['PROPERTY_APPROVAL_EMPLOYEE_VALUE'];

            if ($processingUserId){
                $processingUser = \Renins\User::getByIdLight($processingUserId);
                $userInfo = \CUser::GetByID($processingUserId);
                if($arUser = $userInfo->Fetch()) {
                    $processingUser['phone'] = $arUser['PERSONAL_PHONE'];
                    $processingUser['photo'] = \CFile::GetPath($arUser['PERSONAL_PHOTO']);
                }
            }

            $canDelete = false;
            if (($isOD
                    && in_array($item['PROPERTY_STATUS_VALUE'], [
                        JobApprovals::STATUS_DRAFT,
                        JobApprovals::STATUS_REJECT,
                        JobApprovals::STATUS_REVOKED,
                        JobApprovals::STATUS_ON_REFINEMENT,
                        JobApprovals::STATUS_TRASH,
                        JobApprovals::STATUS_ARCHIVE,
                    ]))
                || ($item['PROPERTY_ARCHIVE_VALUE'] == 1))
            {
                $canDelete = true;
            }

            // Определение может есть ли права брать в работу на шагах cnb и tnd
            $onApprovalInWork = [ JobApprovals::STATUS_ON_APPROVAL, JobApprovals::STATUS_IN_WORK ];
            $stages = JobApprovals::getRecordsArray($item['ID']);
            $canGetToWork = false;
            foreach ($stages as $stage)
            {
                if ($isOD && in_array($stage['STAGE'], [ JobApprovals::STAGE_CHECKING_OD, JobApprovals::STAGE_EMBEDDING ]) && in_array($stage['STATUS'], $onApprovalInWork))
                    $canGetToWork = true;

                if ($isTnD && ($stage['STAGE'] === JobApprovals::STAGE_TND) && in_array($stage['STATUS'], $onApprovalInWork))
                    $canGetToWork = true;

                if ($isCnB && ($stage['STAGE'] === JobApprovals::STAGE_CNB) && in_array($stage['STATUS'], $onApprovalInWork))
                    $canGetToWork = true;
            }

            $item['DATE_CREATE_FORMATTED'] = date('d.m.Y', strtotime($item['DATE_CREATE']));
            if(date('d.m.Y', strtotime($item['PROPERTY_FINISHED_AGREEMENT_VALUE'])) == '01.01.1970'){
                $item['PROPERTY_FINISHED_AGREEMENT_FORMATTED'] = '-';
            } else {
                $item['PROPERTY_FINISHED_AGREEMENT_FORMATTED'] = date('d.m.Y', strtotime($item['PROPERTY_FINISHED_AGREEMENT_VALUE']));
            }
            $item['STAGE'] = JobApprovals::STAGES_MAP[$item['PROPERTY_STAGE_VALUE']];
            $item['STATUS'] = JobApprovals::STATUSES_MAP[$item['PROPERTY_STATUS_VALUE']];
            $item['STATUS_CLASS'] = JobApprovals::STATUSES_CLASS[$item['PROPERTY_STATUS_VALUE']];
            $item['canDelete'] = $canDelete;
            $item['canGetToWork'] = $canGetToWork;
            $item['processingUser'] = $processingUser;
            $item['processingUsers'] = $processingUsers;
            return $item;
        }, $list);
        $list = array_values(array_filter($list));

        return [
            'list' => $list,
            'rows_count' => $ib->getRowsCount(),
            'tabName' => $tab,
            'tabs' => $this->countAllTabsRecordsAction(),
            'createdCostCenters' => array_values($createdCostCenters),
            'createdFunc1' => array_values($createdFunc1),
            'createdFunc2' => array_values($createdFunc2),
            'createdStatuses' => array_values($createdStatuses),
            'createdIDs' => array_values($createdIDs),
            'createdPodraz' => array_values($createdPodraz),
            'createdOtvets' => array_values($createdOtvets),
        ];
    }

    public function countAllTabsRecordsAction()
    {
        global $USER;
        $result = [];

        try {
            $isOD = Renins\User::isInIB($USER->GetID(), 'recruitment_od');
            $isTnD = Renins\User::isInIB($USER->GetID(), 'td_approvers');
            $isCnB = Renins\User::isInIB($USER->GetID(), 'sb');

            if($isOD){
                $tabs = [
                    'my-action' => 'У меня в работе',
                    'active' => 'Активные',
                    'approved' => 'Утвержденные',
                    'archive' => 'Архив',
                    'all' => 'Все профили',
                    'trash' => 'Корзина',
                ];
            }else
            {
                $tabs = [
                    'my-action' => 'У меня в работе',
                    'active' => 'Активные',
                    'approved' => 'Утвержденные',
                ];
            }
            // Определяем структуру вкладок


            foreach ($tabs as $tab => $title) {
                $ib = new Renins\IB('job_profile', ['ID']); // Выбираем только ID для минимизации нагрузки

                // Применяем фильтры для каждой вкладки
                switch ($tab) {
                    case 'my-action':
                        $this->applyMyActionFilters($ib, $USER, $isOD, $isTnD, $isCnB);
                        break;

                    case 'active':
                        $this->applyActiveFilters($ib, $USER, $isOD);
                        break;

                    case 'approved':
                        $this->applyApprovedFilters($ib, $USER, $isOD);
                        break;

                    case 'archive':
                        $this->applyArchiveFilters($ib, $USER, $isOD);
                        break;

                    case 'all':
                        $ib->setFilterParam('!PROPERTY_ARCHIVE', 1);
                        break;
                    case 'trash':
                        $ib->setFilterParam('PROPERTY_ARCHIVE', 1);
                        break;
                }


                // Получаем количество записей
                $count = $ib->getRowsCount();

                // Если getRowsCount() возвращает 0 или null, используем getList()
                if (empty($count)) {
                    $list = $ib->getList();
                    $count = count($list);
                }

                $result[] = [
                    'title' => $title,
                    'value' => $tab,
                    'counter' => (string)$count // Преобразуем в строку
                ];
            }

        } catch (Exception $e) {
            return array_map(function($tab) {
                return [
                    'title' => $tab['title'],
                    'value' => $tab['value'],
                    'counter' => "0"
                ];
            }, $tabs);
        }

        return $result;
    }

    private function applyMyActionFilters($ib, $user, $isOD, $isTnD, $isCnB)
    {
        $ORfilter = [];

        if ($isOD) {
            $odFilterUser = $this->getIDSFilterfromOD();
            $ORfilter[] = [
                'LOGIC' => 'AND',
                'PROPERTY_STAGE' => [
                    JobApprovals::STAGE_CHECKING_OD,
                    JobApprovals::STAGE_EMBEDDING,
                    JobApprovals::STAGE_CREATE,
                    JobApprovals::STAGE_FILLING
                ],
                $odFilterUser
            ];
        } else {
            $ORfilter[] = [
                'LOGIC' => 'AND',
                'PROPERTY_STAGE' => JobApprovals::STAGE_CREATE,
                'CREATED_BY' => $user->GetID()
            ];
            $ORfilter[] = [
                'LOGIC' => 'AND',
                'PROPERTY_OTVETSTVENNYY_ZA_ZAPOLNENIE' => $user->GetID(),
                'PROPERTY_STAGE' => JobApprovals::STAGE_FILLING
            ];
        }

        if ($isTnD) {
            $ORfilter[] = [
                'LOGIC' => 'AND',
                'PROPERTY_STAGE' => [JobApprovals::STAGE_TND, JobApprovals::STAGE_CNB_AND_TND],
                [
                    'LOGIC' => 'OR',
                    ['PROPERTY_APPROVAL_EMPLOYEE' => $user->GetID()],
                    ['PROPERTY_APPROVAL_EMPLOYEE' => false]
                ]
            ];
        }

        if ($isCnB) {
            $ORfilter[] = [
                'LOGIC' => 'AND',
                'PROPERTY_STAGE' => [JobApprovals::STAGE_CNB, JobApprovals::STAGE_CNB_AND_TND],
                [
                    'LOGIC' => 'OR',
                    ['PROPERTY_APPROVAL_EMPLOYEE' => $user->GetID()],
                    ['PROPERTY_APPROVAL_EMPLOYEE' => false]
                ]
            ];
        }

        $ib->setFilterLogicParam($ORfilter);
        $ib->setFilterLogicParam([
            '!PROPERTY_STAGE' => JobApprovals::STAGE_EMBEDDING,
            '!PROPERTY_STATUS' => JobApprovals::STATUS_SIGNED
        ]);
        $ib->setFilterParam('!PROPERTY_ARCHIVE', 1);
        $ib->setFilterParam('!PROPERTY_STATUS', JobApprovals::STATUS_ARCHIVE);
    }

    private function applyActiveFilters($ib, $user, $isOD)
    {
        $ORfilter = [];

        if (!$isOD) {
            $ORfilter = ['CREATED_BY' => $user->GetID()];
            $ORfilter[] = ['PROPERTY_FUNC1_PID' => $user->GetID()];
            $ORfilter[] = ['PROPERTY_FUNC2_PID' => $user->GetID()];
            $ORfilter[] = ['PROPERTY_ADD_NABLYUDATELI' => $user->GetID()];
            $ORfilter[] = ['PROPERTY_SHOW_FOR_APPROVALS' => $user->GetID()];
        }

        $ib->setFilterLogicParam($ORfilter);
        $ib->setFilterLogicParam([
            '!PROPERTY_STAGE' => JobApprovals::STAGE_EMBEDDING,
            '!PROPERTY_STATUS' => JobApprovals::STATUS_SIGNED
        ]);
        $ib->setFilterParam('!PROPERTY_STAGE', JobApprovals::STAGE_CREATE);
        $ib->setFilterParam('!PROPERTY_STATUS', [
            JobApprovals::STATUS_DRAFT,
            JobApprovals::STATUS_REJECT,
            JobApprovals::STATUS_REVOKED
        ]);
        $ib->setFilterParam('finished_agreement', false);
        $ib->setFilterParam('!PROPERTY_STATUS', JobApprovals::STATUS_ARCHIVE);
        $ib->setFilterParam('!PROPERTY_ARCHIVE', 1);
    }

    private function applyApprovedFilters($ib, $user, $isOD)
    {
        if (!$isOD) {
            $ib->setFilterLogicParam([
                'CREATED_BY' => $user->GetID(),
                'PROPERTY_FUNC1_PID' => $user->GetID(),
                'PROPERTY_FUNC2_PID' => $user->GetID(),
                'PROPERTY_ADD_NABLYUDATELI' => $user->GetID(),
                'PROPERTY_SHOW_FOR_APPROVALS' => $user->GetID(),
                'PROPERTY_APPROVAL_CNB_EMPLOYEE' => $user->GetID(),
                'PROPERTY_APPROVAL_TND_EMPLOYEE' => $user->GetID(),
            ]);
        }

        $ib->setFilterParam('PROPERTY_STAGE', JobApprovals::STAGE_EMBEDDING);
        $ib->setFilterParam('PROPERTY_STATUS', JobApprovals::STATUS_SIGNED);
        $ib->setFilterParam('!PROPERTY_STATUS', JobApprovals::STATUS_ARCHIVE);
        $ib->setFilterParam('!PROPERTY_ARCHIVE', 1);
    }

    private function applyArchiveFilters($ib, $user, $isOD)
    {
        if (!$isOD) {
            $ib->setFilterLogicParam([
                'CREATED_BY' => $user->GetID(),
                'PROPERTY_FUNC1_PID' => $user->GetID(),
                'PROPERTY_FUNC2_PID' => $user->GetID(),
                'PROPERTY_ADD_NABLYUDATELI' => $user->GetID(),
                'PROPERTY_SHOW_FOR_APPROVALS' => $user->GetID(),
                'PROPERTY_APPROVAL_CNB_EMPLOYEE' => $user->GetID(),
                'PROPERTY_APPROVAL_TND_EMPLOYEE' => $user->GetID(),
            ]);
        }

        $ib->setFilterParam('PROPERTY_STATUS', JobApprovals::STATUS_ARCHIVE);
        $ib->setFilterParam('!PROPERTY_ARCHIVE', 1);
    }
    /**
     * Отправка заявки в архив
     *
     * @param $id
     * @return bool[]
     */
    public function removeItemAction($id)
    {
        return [ 'del' => $this->moveToArchive($id) ];
    }

    /**
     * Отправка заявок в архив
     *
     * @param $ids
     * @return bool[]
     */
    public function removeItemsAction($ids, $deleting = 'N')
    {
        $result = true;

        foreach ($ids as $id)
        {
            if ($deleting === 'Y')
            {
                if (!\CIBlockElement::Delete($id))
                    $result = false;
            }
            else
            {
                if (!$this->moveToArchive($id))
                    $result = false;
            }
        }

        $trashList = $this->listLoadRecordsAction('ID', 'DESC', 1, 100, 'trash');

        return [ 'del' => $result, 'count' => $trashList['rows_count'] ];
    }

    /**
     * Архивация заявки
     *
     * @param $id
     * @return bool
     * @throws \Bitrix\Main\LoaderException
     */
    public function moveToArchive($id)
    {
        // Проверка прав на архивацию заявки
        $jobProfile = new \Renins\BP\JobProfile($id);
        $allowedDelete = $jobProfile->allowedDelete();

        if (!$allowedDelete)
            return false;

        // Отправка заявки в архив
        Loader::includeModule('iblock');
        if (\CIBlockElement::SetPropertyValueCode($id, 'ARCHIVE', 1)
            && \CIBlockElement::SetPropertyValueCode($id, 'STATUS', JobApprovals::STATUS_TRASH))
            return true;
        else
            return false;
    }

    /**
     * Копирование заявок
     *
     * @param $ids
     * @return bool
     * @throws \Bitrix\Main\LoaderException
     */
    public function copyItemsAction($ids)
    {
        foreach ($ids as $id)
        {
            $formData = $this->loadAction($id)['formData'];
            unset($formData['id']);
            $formData['positionName'] = 'Копия ' . $formData['positionName'];
            $this->saveAction($formData);
        }

        return true;
    }

    public function delegateAction($id, $userId)
    {
        $jobProfile = new JobProfile($id);
        $ib = new \Renins\IB('job_profile');
        // TODO Проверка прав

        // Обновляем статус
        $records = JobApprovals::getRecordsArray($id);
        foreach ($records as $record)
        {
            if ($record['STAGE'] === JobApprovals::STAGE_CREATE)
            {
                if ($record['STATUS'] !== JobApprovals::STATUS_SIGNED)
                {
                    JobApprovals::update(
                        $record['ID'], ['END_TIME' => new Type\DateTime(), 'STATUS' => JobApprovals::STAGE_STATUS_SIGNED]
                    );
                    // Остановка SLA создания
                    JobApprovals::stopSLA($id, JobApprovals::STAGE_CREATE);
                }
            }
            // Переводим этап заполнения заявки в работу
            else if ($record['STAGE'] === JobApprovals::STAGE_FILLING)
            {
                switch ($record['STATUS'])
                {
                    case JobApprovals::STAGE_STATUS_NULL:
                        // Запуск SLA
                        $jobProfile->startSLA($record['STAGE']);
                        // На согласование
                        JobApprovals::update(
                            $record['ID'], ['START' => new Type\DateTime(), 'STATUS' => JobApprovals::STATUS_ON_APPROVAL]
                        );
                    // И продолжаем обработку...
                    case JobApprovals::STAGE_STATUS_ON_APPROVAL:
                        // Установка заполняющего
                        JobApprovals::delegateStage($id, $record['STAGE'], $userId);
                        $ib->updateProp($id, 'APPROVAL_EMPLOYEE', $userId);
                        $ib::stopBP($id);
                        $ib->startBPByDescription($id, '#agreement_step#', ['ID' => $record['ID']]);
                        // И уведомление заполняющего
                        $jobProfile->sendApprovingNotify([ $userId ], null, 'ожидает заполнения');
                        JobApprovals::sendNotification($record);
                        break;
                }
            }
            else if ($record['STATUS'] === JobApprovals::STAGE_STATUS_ON_APPROVAL)
            {
                JobApprovals::delegateStage($id, $record['STAGE'], $userId);
                $jobProfile->sendApprovingNotify($userId, 'Делегировано');
                break;
            }
        }
        JobApprovals::updateOrderStatus($id);

        return \Renins\User::getByIdLight($userId);
    }

    public $fieldsMap = [
        // STEP 1
        ["positionName", "NAZVANIE_DOLZHNOSTI", fieldTypes::string],
        ["costCenter", "COST_CENTER", fieldTypes::string],
        ["func1Name", "FUNC1_NAME", fieldTypes::string],
        ["func1Pid", "FUNC1_PID", fieldTypes::string],
        ["managerExcoFio", "MANAGER_EXCO_FIO", fieldTypes::string],
        ["managerExcoIsObserver", "MANAGER_EXCO_NABLYUDATEL", fieldTypes::boolean],
        ["func2Name", "FUNC2_NAME", fieldTypes::string],
        ["func2Pid", "FUNC2_PID", fieldTypes::string],
        ["managerLineFio", "MANAGER_LINE_FIO", fieldTypes::string],
        ["managerLineIsObserver", "MANAGER_LINE_NABLYUDATEL", fieldTypes::boolean],
        ["department", "PODRAZDELENIE", fieldTypes::string],
        ["branch", "BRANCH", fieldTypes::string],
        ["location", "LOCATION", fieldTypes::string],
        ["admManager", "ADMINISTRATIVNYY_RUKOVODITEL", fieldTypes::string],
        ["admManagerPosition", "DOLZHNOST_ADM_RUKOVODITELYA", fieldTypes::string],
        ["funcManager", "FUNKTSIONALNYY_RUKOVODITEL", fieldTypes::string],
        ["funcManagerPosition", "DOLZHNOST_FUNC_RUKOVODITELYA", fieldTypes::string],
        ["needAdmApprove", "TREBUETSYA_SOGLASOVANIE_ADM", fieldTypes::boolean],
        ["needFuncApprove", "TREBUETSYA_SOGLASOVANIE_FUNK", fieldTypes::boolean],
        ["delegate", "OTVETSTVENNYY_ZA_ZAPOLNENIE", fieldTypes::string],
        ["addApprovers", "DOPOLNITELNYE_SOGLASUYUSCHIE", fieldTypes::string],
        ["headAdmManager", "VYSHESTOYASCHIJ_ADM_RUKOVODITEL", fieldTypes::string],
        ["sendComment", "KOMMENTARII_OTPRAVKI", fieldTypes::string],
        ["addObservers", "ADD_NABLYUDATELI", fieldTypes::string],
        // STEP 2
        ["hasSubs", "PODCHINENNYE_PO_ORG_STRUCTURE", fieldTypes::boolean],
        ["hasFuncSubs", "FUNKCYONALNYE_PODCHINENNYE", fieldTypes::boolean],
        ["hasProjectSubs", "PROEKTNYE_PODCHINENNYE", fieldTypes::boolean],
        ["hasOutsourceSubs", "VNESHNIE_PODCHINENNYE", fieldTypes::boolean],
        ["subordinatesCount", "PRYAMYE_PODCHINENNYE_CHEL", fieldTypes::number],
        ["subordinatesComment", "PRYAMYE_PODCHINENNYE_DOLZHNOSTI_I_PODRAZDELENIYA", fieldTypes::string],
        ["allSubordinatesCount", "PODCHINENNYE_VSEKH_UROVNEY_CHEL", fieldTypes::number],
        ["funcSubordinatesCount", "FUNKTSIONALNYE_PODCHINENNYE_CHEL", fieldTypes::number],
        ["projectSubordinatesCount", "PROEKTNYE_PODCHINENNYE_CHEL", fieldTypes::number],
        ["outsourceSubordinatesCount", "VNESHNIE_AUTSORSING_CHEL", fieldTypes::number],
        ["outsourceComment", "ROLI_I_PODRYADCHIKI", fieldTypes::string],
        ["isManager", "RUKOVODITEL", fieldTypes::boolean],
        ["isShiftSchedule", "SMENNYY_GRAFIK", fieldTypes::boolean],
        ["isItinerantWork", "RAZEZDNOY_KHARAKTER_RABOTY", fieldTypes::boolean],
        ["fieldPercent", "BUSINESS_TRIP_PERCENT", fieldTypes::number],
        ["isRemote", "DISTANT", fieldTypes::boolean],
        ["calculator", "CALCULATOR", fieldTypes::boolean],
        ["relationOutClients", "VZAIMODEYSTVIE_S_VNESHNIMI_CLIENTAMI", fieldTypes::string],
        ["relationInClients", "VZAIMODEYSTVIE_S_VNUTRENNIMI_CLIENTAMI", fieldTypes::string],
        ["physicService", "FISICHESKOE_OBSLYZHIVANIE_OBORUDOVANIYA", fieldTypes::string],
        ["difficultAttractComps", "SLOZHNO_PRIVLECH_KOMPETENCII", fieldTypes::string],
        ["workModeSubs", "RUKOVODITEL_I_REZHIM_PODCHINENNYH", fieldTypes::string],
        ["recommendFormat", "RECOMENDUEMYY_FORMAT", fieldTypes::string],
        ["scheduleBalance", "DISTANT_KALKULYATOR", fieldTypes::number],
        ["schedule", "GRAFIK", fieldTypes::string],
        ["distantPercent", "DISTANT_PERCENT", fieldTypes::number],
        ["diffModeComment", "NE_REKOMEDOVANNYY_COMMENT", fieldTypes::string],
        // STEP 3
        ["departmentGoals", "TSELI_PODRAZDELENIYA", fieldTypes::string],
        ["positionGoals", "TSELI_DOLZHNOSTI", fieldTypes::string],
        // STEP 4
        ["mainDuties", "OSNOVNYE_DOLZHNOSTNYE_OBYAZANNOSTI", fieldTypes::json],
        ["addDuties", "DOPOLNITELNYE_DOLZHNOSTNYE_OBYAZANNOSTI", fieldTypes::json],
        // STEP 5
        ["isShortTerm", "IS_SHORT_TERM", fieldTypes::boolean],
        ["isMediumTerm", "IS_MEDIUM_TERM", fieldTypes::boolean],
        ["isLongTerm", "IS_LONG_TERM", fieldTypes::boolean],
        ["positionContribution", "VKLAD_DOLZHNOSTI_V_RESULTATY", fieldTypes::list],
        ["positionContributionDescription", "VKLAD_COMMENT", fieldTypes::string],
        // STEP 6
        ["decisions", "DECISIONS", fieldTypes::string],
        // STEP 7
        ["financialResultGeneration", "VNOSIT_LI_DOLZHNOST_LICHNYY_VKLAD", fieldTypes::list],
        ["EBIT", "SKOLKO_SOSTAVLYAET_EBIT", fieldTypes::number],
        ["WP", "SKOLKO_SOSTAVLYAET_WP", fieldTypes::number],
        // STEP 8
        ["isNotInvolvedInBudgetManagement", "IS_NOT_INVOLVED_IN_BUDGET_MANAGEMENT", fieldTypes::boolean],
        ["isControlTargetBudget", "IS_CONTROL_TARGET_BUDGET", fieldTypes::boolean],
        ["isPrepareProposalsToSpendBudget", "IS_PREPARE_PROPOSALS_TO_SPEND_BUDGET", fieldTypes::boolean],
        ["hasAuthorityToMakeDecisions", "HAS_AUTHORITY_TO_MAKE_DECISIONS", fieldTypes::boolean],
        ["CnBSum", "SKOLKO_SOSTAVLYAET_CB", fieldTypes::number],
        ["nonCnBSum", "SKOLKO_SOSTAVLYAET_NON_CB", fieldTypes::number],
        // STEP 9
        ["levelOfInnovativeness", "UROVEN_INNOCAIONNOSTI", fieldTypes::list],
        // STEP 10
        ["interactionCircleWithinTheCompany", "KRUG_VZAIMODEYSTVIYA_VNUTRI", fieldTypes::string],
        // STEP 11
        ["b2bClients", "B2B_CLIENTS", fieldTypes::string],
        ["b2cClients", "B2C_CLIENTS", fieldTypes::string],
        ["otherClients", "OTHER_CLIENTS", fieldTypes::string],
        ["namesOfExternalOrganizations", "NAZVANIE_VNESHNIKH_ORGANIZATSIY", fieldTypes::string],
        ["isTransmittingInformation", "IS_TRANSMITTING_INFORMATION", fieldTypes::boolean],
        ["isConsulting", "IS_CONSULTING", fieldTypes::boolean],
        ["isInteraction", "IS_INTERACTION", fieldTypes::boolean],
        ["isParticipationNegotiations", "IS_PARTICIPATION_NEGOTIATIONS", fieldTypes::boolean],
        ["isAuthoritativeInfluence", "IS_AUTHORITATIVE_INFLUENCE", fieldTypes::boolean],
        ["isStrategicNegotiations", "IS_STRATEGIC_NEGOTIATIONS", fieldTypes::boolean],
        ["amountOfCommunications", "KOMMUNIKATSII_V_SITUATSIYAKH_KONFLIKTA", fieldTypes::list],
        // STEP 12
        ["minimumLevelOfEducation", "OBRAZOVANIE", fieldTypes::list],
        ["Qualification", "SPETSIALIZATSIYA", fieldTypes::string],
        ["Certification", "SERTIFIKATSIYA", fieldTypes::string],
        ["professionalStandard", "SOOTVETSTVIE_KVALIFIKATSIONNYM_TREBOVANIYAM", fieldTypes::string],
        // STEP 13
        ["knowledgeOfMethods", "ZNANIE_METODIK", fieldTypes::string],
        ["knowledgeOfComputerPrograms", "ZNANIE_SREDSTV", fieldTypes::string],
        ["knowledgeOfSituation", "ZNANIE_SITUATSII", fieldTypes::string],
        ["businessQualities", "DELOVYE_KACHESTVA_SOTRUDNIKA", fieldTypes::string],
        ["englishLevel", "UROVEN_VLADENIYA_ANGLIYSKIM", fieldTypes::list],
        ["languages", "VLADENIE_DRUGIMI_YAZYKAMI", fieldTypes::json],
        // STEP 14
        ["managementExperience", "MANAGEMENT_EXPERIENCE", fieldTypes::boolean],
        ["professionalExperience", "PROFESSIONAL_EXPERIENCE", fieldTypes::boolean],
        ["typeOfExperience", "TYPE_OF_EXPERIENCE", fieldTypes::string],
        ["fieldOfActivity", "FIELD_OF_ACTIVITY", fieldTypes::string],
        ["professionalExperienceYears", "PROFESSIONAL_EXPERIENCE_YEARS", fieldTypes::number],
        ["typeOfManagementExperience", "TYPE_OF_MANAGEMENT_EXPERIENCE", fieldTypes::string],
        ["fieldOfManagementActivity", "FIELD_OF_MANAGEMENT_ACTIVITY", fieldTypes::string],
        ["managementExperienceYears", "MANAGEMENT_EXPERIENCE_YEARS", fieldTypes::number],
        // STEP 15
        ["competencies", "COMPETENCIES", fieldTypes::json],
        // STEP 16
        ["review", "OBZOR", fieldTypes::json],
        ["premiumMonth", "PREMIYA_EZHEMESYACHNAYA", fieldTypes::boolean],
        ["premiumQuarter", "PREMIYA_KVARTALNAYA", fieldTypes::boolean],
        ["premiumHalfyear", "PREMIYA_POLUGODOVAYA", fieldTypes::boolean],
        ["premiumYear", "PREMIYA_GODOVAYA", fieldTypes::boolean],
        ["premiumPercent", "PREMIYA_PROCENT", fieldTypes::number],
        ["grade", "GREID", fieldTypes::number],
        ["gradeNotDefined", "GREID_NE_OPREDELEN", fieldTypes::boolean],
        ["forkLow", "VILKA_NIZHNEE", fieldTypes::number],
        ["forkMid", "VILKA_SREDNEE", fieldTypes::number],
        ["forkHigh", "VILKA_VERHNEE", fieldTypes::number],
    ];

    public function getApprovers($IBlockCode)
    {
        $approvers = new \Renins\IB($IBlockCode, ['USER']);
        $users = [];
        foreach ($approvers->getList() as $item)
        {
            $users[] = $item['PROPERTY_USER_VALUE'];
        }
        return $users;
    }

    public function getToWorkAction($id)
    {
        global $USER;
        $isOD = Renins\User::isInIB($USER->GetID(), 'recruitment_od');
        $isTnD = Renins\User::isInIB($USER->GetID(), 'td_approvers');
        $isCnB = Renins\User::isInIB($USER->GetID(), 'sb');

        // Обновляем статус
        $records = JobApprovals::getRecordsArray($id);
        foreach ($records as $record)
        {
            if (($record['STATUS'] == JobApprovals::STATUS_ON_APPROVAL)
                && (($isOD && in_array($record['STAGE'], [ JobApprovals::STAGE_CHECKING_OD, JobApprovals::STAGE_EMBEDDING ]))
                    || ($isTnD && ($record['STAGE'] === JobApprovals::STAGE_TND))
                    || ($isCnB && ($record['STAGE'] === JobApprovals::STAGE_CNB))))
            {
                JobApprovals::delegateStage($id, $record['STAGE'], $USER->GetID());
                JobApprovals::update($record['ID'], [ 'CHANGE_TIME' => new Type\DateTime() ]);
            }
        }
        JobApprovals::updateOrderStatus($id);

        return true;
    }

    public function addAdditionStageAction($entityId, $users)
    {
        foreach ($users as $userId)
        {
            JobApprovals::addAdditionStage($entityId, $userId);
        }
        $stages = JobApprovals::getRecordsArray($entityId);
        return $stages;
    }

    public function deleteAdditionStageAction($entityId, $stageId)
    {
        global $USER;
        $isOD = \Renins\User::isInIB($USER->GetID(), 'recruitment_od');
        if ($isOD)
        {
            JobApprovals::delete($stageId);
        }
        $stages = JobApprovals::getRecordsArray($entityId);
        return $stages;
    }

    public function updateStageAction($entityId, $stageId, $data)
    {
        global $USER;
        $isOD = \Renins\User::isInIB($USER->GetID(), 'recruitment_od');
        if ($isOD)
        {
            $stageData = [];
            if (isset($data['CHECKED']))
            {
                $stageData['CHECKED'] = $data['CHECKED'] === 'Y' ? 'Y' : 'N';
            }
            if (isset($data['RESPONSIBLE_USER']))
            {
                $stageData['CHECKED'] = 'Y';
                $stageData['RESPONSIBLE_USER'] = $data['RESPONSIBLE_USER'];
            }
            JobApprovals::update($stageId, $stageData);
        }

        $stages = JobApprovals::getRecordsArray($entityId);

        // Обновим согласовантов в заявке
        if (isset($data['RESPONSIBLE_USER']))
        {
            $field = null;
            $currentStage = [];
            $additionals = [];
            foreach ($stages as $stage)
            {
                if ($stage['ID'] == $stageId)
                {
                    switch ($stage['STAGE'])
                    {
                        case JobApprovals::STAGE_FILLING:
                            $field = 'OTVETSTVENNYY_ZA_ZAPOLNENIE';
                            $currentStage = $stage;
                            break;
                        case JobApprovals::STAGE_ADM_MANAGER:
                            $field = 'ADMINISTRATIVNYY_RUKOVODITEL';
                            $currentStage = $stage;
                            break;
                        case JobApprovals::STAGE_FUNC_MANAGER:
                            $field = 'FUNKTSIONALNYY_RUKOVODITEL';
                            $currentStage = $stage;
                            break;
                        case JobApprovals::STAGE_ADM_MANAGER_HEAD:
                            $field = 'VYSHESTOYASCHIJ_ADM_RUKOVODITEL';
                            $currentStage = $stage;
                            break;
                        case JobApprovals::STAGE_ADDITIONAL:
                            $field = 'DOPOLNITELNYE_SOGLASUYUSCHIE';
                            $currentStage = $stage;
                            break;
                    }
                }
                if ($stage['STAGE'] == JobApprovals::STAGE_ADDITIONAL)
                    $additionals[] = $stage['RESPONSIBLE_USER'];
            }

            if ($field)
            {
                $ib = new \Renins\IB('job_profile');

                if ($field == 'DOPOLNITELNYE_SOGLASUYUSCHIE')
                    $ib->updateProp($entityId, $field, $additionals);
                else
                    $ib->updateProp($entityId, $field, $data['RESPONSIBLE_USER']);

                JobApprovals::updateApprovals($entityId, true);

                if ($currentStage['STATUS'] == JobApprovals::STATUS_ON_APPROVAL)
                {
                    $ib->updateProp($entityId, 'APPROVAL_EMPLOYEE', $data['RESPONSIBLE_USER']);
                    $ib->stopBP($entityId, '#agreement_step#');
                    $ib->startBPByDescription($entityId, '#agreement_step#', [ 'ID' => $stageId ]);
                }
            }
        }

        return $stages;
    }

    public function approveAction($id, $comment)
    {
        global $USER;
        $dbRecordsList = \CBPTaskService::GetList([], [ 'DOCUMENT_ID' => $id, 'STATUS' => \CBPTaskStatus::Running ]);
        while ($arRecord = $dbRecordsList->getNext())
        {
            try
            {
                \CBPRuntime::SendExternalEvent($arRecord['WORKFLOW_ID'], $arRecord['ACTIVITY_NAME'], [
                    'USER_ID' => $USER->GetID(),
                    'APPROVE' => true,
                    'COMMENT' => $comment
                ]);
            }
            catch (Exception $e) {}
        }
        return true;
    }

    /**
     * @param $id
     * @param $comment
     * @return bool
     */
    public function rejectAction($id, $comment)
    {
        global $USER;

        $jobProfile = new JobProfile($id);
        $jobProfile->sendRejectNotify($comment);

        $records = JobApprovals::getRecordsArray($id);
        foreach ($records as $record)
        {
            if ($record['STAGE'] === JobApprovals::STAGE_CREATE)
                JobApprovals::sendNotification($record, ['reject' => true]);
        }

        $dbRecordsList = \CBPTaskService::GetList([], [ 'DOCUMENT_ID' => $id, 'STATUS' => \CBPTaskStatus::Running, 'USER_ID' => $USER->GetID() ]);
        if ($arRecord = $dbRecordsList->getNext())
        {
            try {
                \CBPRuntime::SendExternalEvent($arRecord['WORKFLOW_ID'], $arRecord['ACTIVITY_NAME'], [
                    'USER_ID' => $USER->GetID(),
                    'APPROVE' => false,
                    'COMMENT' => $comment
                ]);
            } catch (Exception $e) {
            }
        }

        return true;
    }

    /**
     * @param $id
     * @param $comment
     * @return bool
     */
    public function revokeAction($id, $comment)
    {
        $jobProfile = new JobProfile($id);
        $jobProfile->sendRejectNotify($comment, 'отозван');

        $ib = new \Renins\IB('job_profile');
        $ib->stopBP($id, '#agreement_step#');
        JobApprovals::resetToInitialState($id);
        $ib->updateProp($id, 'STATUS', JobApprovals::STATUS_REVOKED);

        return true;
    }

    /**
     * Возврат на этап
     *
     * @param $id
     * @param $stage
     * @param $comment
     * @return bool
     * @throws Exception
     */
    public function returnAction($id, $stage, $comment)
    {
        $ib = new IB('job_profile');
        $jobProfile = new JobProfile($id);

        $ib::stopBP($id);
        $ib->updateProp($id, 'STATUS', JobApprovals::STATUS_ON_REFINEMENT);

        $returned = false;
        $records = JobApprovals::getRecordsArray($id);
        foreach ($records as $record)
        {
            // Сделано так с учетом нескольких этапов доп.согласования, при этом возвращаем на первое
            if (!$returned)
            {
                if ($record['STAGE'] === $stage)
                {
                    $item = $ib->GetByID($id);
                    $ib->updateProp($id, 'ITERATION', (int)$item['PROPS']['ITERATION']['VALUE'] + 1);
                    $arRecord = [
                        'STATUS' => JobApprovals::STATUS_ON_APPROVAL,
                        'RESPONSIBLE_USER' => $record['RESPONSIBLE_USER'],
                        'START_TIME' => new Type\DateTime(),
                        'END_TIME' => null,
                        'CHANGE_TIME' => null,
                        'NOTIFY_TIME' => null,
                    ];

                    $resp = [];
                    if ($record['RESPONSIBLE_ROLE'])
                    {
                        $arRecord['RESPONSIBLE_USER'] = '';
                        $resp = \Renins\User::getUsersFromIB(JobApprovals::getResponsibleRoleByStage($record['STAGE']));
                    }
                    else if ($record['RESPONSIBLE_USER'])
                        $resp = [ $record['RESPONSIBLE_USER'] ];

                    $jobProfile->sendReturnNotify($resp, $record['STAGE'], $comment);
                    JobApprovals::sendNotification($record, [ 'return' => true, 'comment' => $comment ]);

                    $ib->updateProps($id, [
                        'STAGE' => $record['STAGE'],
                        'APPROVAL_EMPLOYEE' => $arRecord['RESPONSIBLE_USER']
                    ]);

                    JobApprovals::update($record['ID'], $arRecord);
                    $jobProfile->startSLA($record['STAGE']);
                    $ib->startBPByDescription($id, '#agreement_step#', ['ID' => $record['ID']]);
                    $returned = true;
                }
            }
            else
            {
                // Закрытие SLA
                JobApprovals::stopSLA($id, $record['STAGE']);
                JobApprovals::update($record['ID'], [
                    'STATUS' => JobApprovals::STAGE_STATUS_NULL,
                    'START_TIME' => null,
                    'END_TIME' => null,
                    'CHANGE_TIME' => null,
                    'NOTIFY_TIME' => null,
                ]);
            }
        }

        return true;
    }

    /**
     * Этапы, на которые возможен возврат
     *
     * @param $stages
     * @return array
     */
    private function returningStages($stages)
    {
        $result = [];
        $additionalAdded = false;

        foreach ($stages as $stage)
        {
            // Не черновик и утвержденный этап
            if (($stage['STAGE'] !== JobApprovals::STAGE_CREATE)
                && ($stage['STATUS'] === JobApprovals::STAGE_STATUS_SIGNED))
            {
                if (($stage['STAGE'] !== JobApprovals::STAGE_ADDITIONAL) || !$additionalAdded)
                    $result[] = [
                        'value' => $stage['STAGE'],
                        'label' => JobApprovals::STAGES_MAP[ $stage['STAGE'] ],
                    ];

                if ($stage['STAGE'] === JobApprovals::STAGE_ADDITIONAL)
                    $additionalAdded = true;
            }
        }

        return $result;
    }

    /**
     * Получить данные из БОСС
     *
     * @return array
     */
    public function getBossData($profileId)
    {
        // todo dev comment
        $boss = \Renins\Integration\Boss::getInstance();
        $bossDeparments = $boss->getDepartments();
        $bossBranches = $boss->getBranches();
        $bossLocations = $boss->getLocations();
        $bossDiFiles = $boss->getDIFiles($profileId);

        $departments = [];
        $branches = [];
        $locations = [];

        foreach ($bossDeparments as $row)
        {
            $chain = explode(' / ', $row['full_path']);

            if ($chain[0])
                $departments[ $row['full_path'] ] = [
                    'label' => $row['full_path'],
                    'value' => $row['full_path'],
                ];
        }
        $departments = array_values($departments);

        foreach ($bossBranches as $row)
        {
            $str = $row['Code'] . ' - ' . $row['Name'];
            $branches[ $row['Name'] ] = [
                'label' =>  $str,
                'value' => $str,
            ];
        }
        ksort($branches);
        $branches = array_values($branches);

        foreach ($bossLocations as $row)
        {
            $str = $row['Location_Code'] . ' - ' . $row['Location_Name'];
            $locations[ $row['Location_Name'] ] = [
                'label' =>  $str,
                'value' => $str,
            ];
        }
        ksort($branches);
        $locations = array_values($locations);

        $diFiles = [];
        foreach ($bossDiFiles as $file) {
            $diFiles[$file['full_path']] = $file;
        }
        $diFiles = array_values($diFiles);

        return [
            'departments' => $departments,
            'branches' => $branches,
            'locations' => $locations,
            'diFiles' => $diFiles,
        ];
    }

    /**
     * Получить сотрудника по его PID
     *
     * @param $pid
     * @return array|false|mixed
     */
    public function getUserByPidAction($pid)
    {
        if ($pid)
        {
            $user = \Renins\User::getUserByPid($pid);
            if ($user)
                return $user;
        }
        return false;
    }

    public function addProfileToArchiveAction($elementIdS)
    {
        $ib = new \Renins\IB('job_profile');
        foreach ($elementIdS as $elementId){
            // Обновляем элемент
            $ib->updateProp($elementId,'STATUS', 'archive');
        }
        return true;
    }

    /**
     *  Обязательный поля при заполнении формы
     *  @return array
     */
    public function getRequiredFields(): array
    {
        global $USER;

        $fields = [];
        if (\COption::GetOptionString('renins', "JOB_PROFILE_REQUIRED_FIELDS") !== 'Y') {
            // Функционал обязательности полей выключен в админке
            return $fields;
        }

        $prepareField = function(int $step, string $relField = '', array $relValues = [], array $radioFields = [], array $relFields = [], int $minObjValues = 0) {
            return [
                'step' => $step, // Шаг заполнения
                'rel' => $relField, // Зависимое поле, если оно иммет значение, текуще поле будет обязательным
                'relValue' => $relValues, // Значение зависимого поля, если пусто, то любое
                'radioFields' => $radioFields, // Если одно из полей заполнено, то текущее будет не обязательным
                'relFields' => $relFields, // Если одно из полей заполнено, то текущее будет обязательным
                'minObjValues' => $minObjValues, // Минимальное количество значений
            ];
        };

        $fields['positionName'] = $prepareField(1);
        $fields['costCenter'] = $prepareField(1);
        $fields['branch'] = $prepareField(1);

        $fields['hasSubs'] = $prepareField(2, 'isManager');
        $fields['subordinatesCount'] = $prepareField(2, 'hasSubs');
        $fields['allSubordinatesCount'] = $prepareField(2, 'hasSubs');
        $fields['subordinatesComment'] = $prepareField(2, 'hasSubs');
        $fields['schedule'] = $prepareField(2);
        $fields['distantPercent'] = $prepareField(2, 'schedule', ['Гибридный']);

        $fields['departmentGoals'] = $prepareField(3);
        $fields['positionGoals'] = $prepareField(3);

        $fields['isShortTerm'] = $prepareField(4, '', [], ['isMediumTerm', 'isLongTerm']);
        $fields['isMediumTerm'] = $prepareField(4, '', [], ['isShortTerm', 'isLongTerm']);
        $fields['isLongTerm'] = $prepareField(4, '', [], ['isShortTerm', 'isMediumTerm']);

        $fields['mainDuties'] = $prepareField(4 );

        $fields['positionContribution'] = $prepareField(5 );
        $fields['positionContributionDescription'] = $prepareField(5 , 'positionContribution', ['Стратегический']);

        $fields['decisions'] = $prepareField(6 );

        $fields['financialResultGeneration'] = $prepareField(7 );
        $fields['EBIT'] = $prepareField(7 , 'financialResultGeneration', ['Да']);
        $fields['WP'] = $prepareField(7 , 'financialResultGeneration', ['Да']);

        $fields['isNotInvolvedInBudgetManagement'] = $prepareField(8, '', [], ['isControlTargetBudget', 'isPrepareProposalsToSpendBudget', 'hasAuthorityToMakeDecisions']);
        $fields['isControlTargetBudget'] = $prepareField(8, '', [], ['isNotInvolvedInBudgetManagement', 'isPrepareProposalsToSpendBudget', 'hasAuthorityToMakeDecisions']);
        $fields['isPrepareProposalsToSpendBudget'] = $prepareField(8, '', [], ['isControlTargetBudget', 'isNotInvolvedInBudgetManagement', 'hasAuthorityToMakeDecisions']);
        $fields['hasAuthorityToMakeDecisions'] = $prepareField(8, '', [], ['isControlTargetBudget', 'isPrepareProposalsToSpendBudget', 'isNotInvolvedInBudgetManagement']);
        $fields['CnBSum'] = $prepareField(8, '', [], [], ['isControlTargetBudget', 'isPrepareProposalsToSpendBudget', 'hasAuthorityToMakeDecisions']);
        $fields['nonCnBSum'] = $prepareField(8, '', [], [], ['isControlTargetBudget', 'isPrepareProposalsToSpendBudget', 'hasAuthorityToMakeDecisions']);

        $fields['levelOfInnovativeness'] = $prepareField(9);

        $fields['amountOfCommunications'] = $prepareField(10);
        $fields['isTransmittingInformation'] = $prepareField(10, '', [], ['isTransmittingInformation', 'isConsulting', 'isInteraction', 'isParticipationNegotiations', 'isAuthoritativeInfluence', 'isStrategicNegotiations']);
        $fields['isConsulting'] = $prepareField(10, '', [], ['isTransmittingInformation', 'isConsulting', 'isInteraction', 'isParticipationNegotiations', 'isAuthoritativeInfluence', 'isStrategicNegotiations']);
        $fields['isInteraction'] = $prepareField(10, '', [], ['isTransmittingInformation', 'isConsulting', 'isInteraction', 'isParticipationNegotiations', 'isAuthoritativeInfluence', 'isStrategicNegotiations']);
        $fields['isParticipationNegotiations'] = $prepareField(10, '', [], ['isTransmittingInformation', 'isConsulting', 'isInteraction', 'isParticipationNegotiations', 'isAuthoritativeInfluence', 'isStrategicNegotiations']);
        $fields['isAuthoritativeInfluence'] = $prepareField(10, '', [], ['isTransmittingInformation', 'isConsulting', 'isInteraction', 'isParticipationNegotiations', 'isAuthoritativeInfluence', 'isStrategicNegotiations']);
        $fields['isStrategicNegotiations'] = $prepareField(10, '', [], ['isTransmittingInformation', 'isConsulting', 'isInteraction', 'isParticipationNegotiations', 'isAuthoritativeInfluence', 'isStrategicNegotiations']);

        $fields['minimumLevelOfEducation'] = $prepareField(11);

        $fields['knowledgeOfMethods'] = $prepareField(12);
        $fields['knowledgeOfComputerPrograms'] = $prepareField(12);
        $fields['knowledgeOfSituation'] = $prepareField(12);
        $fields['businessQualities'] = $prepareField(12);
        $fields['englishLevel'] = $prepareField(12);

        $fields['professionalExperienceYears'] = $prepareField(13, 'professionalExperience');
        $fields['fieldOfActivity'] = $prepareField(13, 'professionalExperience');
        $fields['managementExperienceYears'] = $prepareField(13, 'managementExperience');
        $fields['fieldOfManagementActivity'] = $prepareField(13, 'managementExperience');

        $isTnD = \Renins\User::isInIB($USER->GetID(), 'td_approvers');
        if ($isTnD) {
            $fields['competencies'] = $prepareField(14, '', [], [], [], 11);
        }

        return $fields;
    }

    public static function sendNotifyChangeApproveAction($messageFields)
    {
        $message = self::getMessageTextNotifyChangeApprove($messageFields);
        $messageEventType = "SEND_NOTIFY_CHANGE_APPROVE_JP";
        self::sendNotifyEmail($messageFields, $message, $messageEventType);
        self::sendNotifyBell($messageFields, $message);

        return true;
    }

    public static function sendNotifyEmail($arrMessageFields, $getMessage, $messageEventType)
    {
        GLOBAL $USER;
        $emailTo = \CUser::GetByID($arrMessageFields['userID'])->fetch()['EMAIL'];
        \CEvent::Send(
            $messageEventType,
            SITE_ID,
            [
                "EMAIL_TO" => $emailTo,
                "MESSAGE" => $getMessage
            ]
        );
    }

    public static function sendNotifyBell($arrMessageFields, $getMessage)
    {
        $arFields = array(
            "NOTIFY_TITLE" => "Профиль должности",
            "MESSAGE" => $getMessage,
            "MESSAGE_TYPE" => IM_MESSAGE_SYSTEM,
            "TO_USER_ID" => $arrMessageFields['userID'],
            "NOTIFY_TYPE" => IM_NOTIFY_SYSTEM,
            "NOTIFY_MODULE" => "renins",
            "NOTIFY_EVENT" => "Смена ответственного",
        );
        \CIMNotify::Add($arFields);
    }



    public static function getMessageTextNotifyChangeApprove($arrMessageFields)
    {
        $message = "Уважаемый коллега,".PHP_EOL;
        $message .= "Вам отправлен на согласование Профиль должности.".PHP_EOL;
        $message .= "Должность: " . $arrMessageFields['job'] . PHP_EOL;
        $message .= "Подразделение: " . $arrMessageFields['podraz'] . PHP_EOL;
        $message .= "Cost-center: " . $arrMessageFields['costCenter'] . PHP_EOL;
        $message .= "Функция 1: " . $arrMessageFields['func1Name'] . PHP_EOL;
        $message .= "Функция 2: " . $arrMessageFields['func2Name'] . PHP_EOL;
        $message .= "Ссылка на профиль должности: <a href='" . $arrMessageFields['linkToProfile'] . "'>Профиль должности</a>" . PHP_EOL;
        $message .= "Скачать Профиль должности (excel): <a href='" . $arrMessageFields['getToProfileExcel'] . "'>Скачать Профиль должности</a>" . PHP_EOL;
        $message .= "С уважением,".PHP_EOL;
        $message .= "команда HR&OD".PHP_EOL;

        return $message;
    }


    public function onPrepareComponentParams($arParams)
    {
        $this->errorCollection = new ErrorCollection();
        return $arParams;
    }

    public function getErrors()
    {
        return $this->errorCollection->toArray();
    }

    public function getErrorByCode($code)
    {
        return $this->errorCollection->getErrorByCode($code);
    }

    public function configureActions()
    {
        return [];
    }
}
