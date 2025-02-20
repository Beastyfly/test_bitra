<?php

return new class extends \Renins\Component\BaseTemplateClass {
	public function execute()
	{
		global $USER;
        $isOD = \Renins\User::isInIB($USER->GetID(), 'recruitment_od');
		$sort = 'ID';
		$order = 'DESC';
		$perPage = (int)!empty($_GET['perPage']) ? $_GET['perPage'] : 10000;
		$page = (int)!empty($_GET['page']) ? $_GET['page'] : 1;
		$currentTab = !empty($_GET['tab']) ? $_GET['tab'] : false;
		$q = [ $_GET['q1'], $_GET['q2'], $_GET['q3'], $_GET['q4'] ];
		$data = [];

		$myActiveList = $this->getContext()->listLoadRecordsAction($sort, $order, $page, $perPage, 'my-action', $q);
		$activeList = $this->getContext()->listLoadRecordsAction($sort, $order, $page, $perPage, 'active', $q);
		$approvedList = $this->getContext()->listLoadRecordsAction($sort, $order, $page, $perPage, 'approved', $q);
		$archiveList = $this->getContext()->listLoadRecordsAction($sort, $order, $page, $perPage, 'archive', $q);
		$allList = $this->getContext()->listLoadRecordsAction($sort, $order, $page, $perPage, 'all', $q);

		$data['tabs'][] = [ "title" => "У меня в работе", "value" => "my-action", "counter" => $myActiveList['rows_count'] ];
		$data['tabs'][] = [ "title" => "Активные",        "value" => "active",    "counter" => $activeList['rows_count'] ];
		$data['tabs'][] = [ "title" => "Утвержденные",    "value" => "approved",  "counter" => $approvedList['rows_count'] ];


		if ($isOD)
        {
            $data['tabs'][] = [ "title" => "Архив",    "value" => "archive",  "counter" => $archiveList['rows_count'] ];
            $data['tabs'][] = [ "title" => "Все профили",    "value" => "all",  "counter" => $allList['rows_count'] ];
            $trashList = $this->getContext()->listLoadRecordsAction($sort, $order, $page, $perPage, 'trash', $q);
            $data['tabs'][] = [ "title" => "Корзина", "value" => "trash", "counter" => $trashList['rows_count'] ];
        }

		reset($data['tabs']);
		$firstTab = $currentTab ? ['value' => $currentTab] : $data['tabs'][key($data['tabs'])];
		$data["tab"] = $firstTab['value'];

		$data["perPage"] = $perPage;
		$data["sort"] = $sort;
		$data["order"] = $order;

		$data["isOD"] = $isOD;
		$data["canDownloadSLA"] = $isOD || $USER->IsAdmin();

        $request = \Bitrix\Main\Context::getCurrent()->getRequest();
		$data['filledId'] = $request->get('filled');

		switch ($data["tab"])
		{
			case 'my-action':
				$data["list"] = $myActiveList["list"];
				$data["rows_count"] = $myActiveList["rows_count"];
				break;
			case 'active':
				$data["list"] = $activeList["list"];
				$data["rows_count"] = $activeList["rows_count"];
				break;
			case 'approved':
				$data["list"] = $approvedList["list"];
				$data["rows_count"] = $approvedList["rows_count"];
				break;
			case 'trash':
				$data["list"] = $trashList["list"];
				$data["rows_count"] = $trashList["rows_count"];
				break;
            case 'archive':
                $data["list"] = $archiveList["list"];
                $data["rows_count"] = $archiveList["rows_count"];
                break;
            case 'all':
                $data["list"] = $allList["list"];
                $data["rows_count"] = $allList["rows_count"];
                break;
		}

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
        foreach ($data["list"] as $row)
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
                    'label' => \Renins\Orm\JobProfileApprovalsTable::STATUSES_MAP[ $row['PROPERTY_STATUS_VALUE'] ],
                    'value' => $row['PROPERTY_STATUS_VALUE']
                ];
            if ($row['ID'])
                $createdIDs[ $row['ID'] ] = [
                    'label' =>  $row['ID'],
                    'value' => $row['ID']
                ];
            if ($row['PROPERTY_PODRAZDELENIE_VALUE'])
                $createdPodraz[ $row['PROPERTY_PODRAZDELENIE_VALUE'] ] = [
                    'label' => $row['PROPERTY_PODRAZDELENIE_VALUE'],
                    'value' => $row['PROPERTY_PODRAZDELENIE_VALUE']
                ];
        }
        $data['createdCostCenters'] = array_values($createdCostCenters);
        $data['createdFunc1'] = array_values($createdFunc1);
        $data['createdFunc2'] = array_values($createdFunc2);
        $data['createdStatuses'] = array_values($createdStatuses);
        $data['createdIDs'] = array_values($createdIDs);
        $data['createdPodraz'] = array_values($createdPodraz);


        $userId = $USER->GetID();
        $data['currentUserId'] = $userId;
        $data['currentUserFio'] = \Renins\User::getFioById($userId);

		$this["config"] = $data;

	}


};
