<?php

use Renins\BP;
use Renins\BP\JobProfile;
use Renins\Orm\JobProfileApprovalsTable;

return new class extends \Renins\Component\BaseTemplateClass {
	public function execute()
	{
        global $USER;
		$ID = $_GET['DRAFT'] ? (int)$_GET['DRAFT'] : '';
		$formData =  $ID ? $this->getContext()->loadAction($ID) : [];

        $signed = true;
		$stages = $ID ? JobProfileApprovalsTable::getRecordsArray($ID) : [];
        foreach ($stages as &$stage)
        {
            $stage['END_TIME_2'] = $stage['END_TIME']
                ? date('d.m.Y H:i', strtotime($stage['END_TIME']))
                : '';

            if ($stage['STATUS'] == JobProfileApprovalsTable::STAGE_STATUS_ON_APPROVAL)
                $signed = false;

            if ($signed)
                $stage['CAN_CHANGE_RESPONSIBLE'] = 'N';
        }

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

        $data = $this->getContext()->getBossData($ID);

        $ib = new \Renins\IB('job_profile');
        $item = $ib->GetByID($ID);
        $jobProfile = new JobProfile($ID);

        $roleSLA = $jobProfile->getStageSLA($jobProfile->getStage());
        if ($roleSLA)   $formattedRoleSLA = BP::formatWorkHoursDeltaDates($roleSLA);
        else            $formattedRoleSLA = null;

		$this['config'] = [
			'id' => $_GET['DRAFT'] ? (int)$_GET['DRAFT'] : '',
            'currentUserId' => $USER->GetID(),
			'formData' => $formData['formData'],
            'initiator' => \Renins\User::getById($item['CREATED_BY']),
            'status' => JobProfileApprovalsTable::STATUSES_MAP[ $item['PROPS']['STATUS']['VALUE'] ],
            'statusId' => $item['PROPS']['STATUS']['VALUE'],
            'statusClass' => JobProfileApprovalsTable::STATUSES_CLASS[ $item['PROPS']['STATUS']['VALUE'] ],
            'stageId' => $item['PROPS']['STAGE']['VALUE'],
            'stage' => JobProfileApprovalsTable::STAGES_MAP[ $item['PROPS']['STAGE']['VALUE'] ],
            'processingUser' => $jobProfile->getProcessingUser(),
            'processingUsers' => $jobProfile->getProcessingUsers(),
            'createDate' => date('d.m.Y', strtotime($item['DATE_CREATE'])),
            'updateDate' => date('d.m.Y', strtotime($item['TIMESTAMP_X'])),
            'work' => BP::formatWorkHoursDeltaDates(
                BP::getWorkHoursDeltaDates(
                    $item['DATE_CREATE'],
                    '',
                )
            ),
            'modelRole' => $jobProfile->getModelRole(),
            'roleSLA' => $formattedRoleSLA,
            'stages' => $stages,
            'isOD' => \Renins\User::isInIB($USER->GetID(), 'recruitment_od'),
			'allowedDelegateFilling' => $this->getContext()->allowedDelegateFilling($stages),
            'allowedChangeStages' => $this->getContext()->allowedChangeStages($item, $stages),
			'isFillingStage' => $this->isFillingStage($stages),
			'isAccessPage' => $this->isAccessPage($ID, $stages),
            'costCenters' => $costCenters,
            'departments' => $data['departments'],
            'branches' => $data['branches'],
            'locations' => $data['locations'],
            'requiredFields' => $this->getContext()->getRequiredFields()
        ];
	}

	private function isFillingStage($stages)
	{
		foreach ($stages as $stage)
		{
			if (in_array($stage['STATUS'], [ JobProfileApprovalsTable::STAGE_STATUS_ON_APPROVAL]))
			{
				if ($stage['STAGE'] === JobProfileApprovalsTable::STAGE_FILLING)
				{
					return true;
				}
			}
		}
		return false;
	}


	/**
	 * Имеется доступ на страницу создания/заполнения формы
	 * @param $stages
	 * @return bool
	 */

	private function isAccessPage($ID, $stages)
	{
		global $USER;
		$isOD = \Renins\User::isInIB($USER->GetID(), 'recruitment_od');

        if (!empty($ID)) {
            $ib = new \Renins\IB('job_profile');
            $item = $ib->GetByID($ID);
            if (!$ib->checkIBlockID($item['IBLOCK_ID'])) {
                // ID принадлежит другому инфоблоку
                return false;
            }
        }



		foreach ($stages as $stage)
		{
			if (in_array($stage['STATUS'], [ JobProfileApprovalsTable::STAGE_STATUS_ON_APPROVAL]) && $stage['STAGE'] === JobProfileApprovalsTable::STAGE_FILLING)
			{
				if ($stage['RESPONSIBLE_USER'] === $USER->GetID())
				{
					// На согласовании у сотрудника
					return true;
				}
			}
		}
		return $isOD;
	}


};
