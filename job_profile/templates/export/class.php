<?php
use PhpOffice\PhpSpreadsheet\IOFactory;
use Bitrix\Main\Type;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

return new class extends \Renins\Component\BaseTemplateClass {
	public function execute()
	{
        $ID = (int) $this->getContext()->arParams['ID'];
        if (!$ID) {
            $this['error'] = 'Не указан ID профиля должности';
        } else {
            $data = $this->getContext()->loadDetailAction($ID);
            $this['formData'] = $data['formData'];

            $this->excel($data['formData']);
        }
	}

    public function excel($data) {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $ActiveSheet = $spreadsheet->setActiveSheetIndex(0);
        // $ActiveSheet->getColumnDimension('A')->setAutoSize(true);
        // $ActiveSheet->getColumnDimension('B')->setAutoSize(true);
        $ActiveSheet->getColumnDimension('A')->setWidth('80');
        $ActiveSheet->getColumnDimension('B')->setWidth('80');
        $row = 1;

        $ActiveSheet->setCellValue('A' . $row++, 'Дата: ');
        $ActiveSheet->setCellValue('A' . $row++, 'Штатная единица: ');
        $ActiveSheet->setCellValue('A' . $row++, 'Профиль должности: ');

        $this->addHeader($ActiveSheet, $row++, 'Профиль должности');

        $this->addSubHeader($ActiveSheet, $row++, 'Общая информация по должности');
        $this->addRow($ActiveSheet, $row++, 'Наименование должности', $data['positionName']);
        $this->addRow($ActiveSheet, $row++, 'Cost Center (Центр возникновения затрат)', $data['costCenter']);
        $this->addRow($ActiveSheet, $row++, 'Branch (Территориальная принадлежность)', $data['branch']);
        $this->addRow($ActiveSheet, $row++, 'Location (место нахождения функции/должности)', $data['location']);
        $text = ($data['isManager'] ? '☑' : '☐') . " Руководитель". PHP_EOL ;
        $text .= ($data['isShiftSchedule'] ? '☑' : '☐') . " Сменный график" . PHP_EOL;
        $text .= ($data['isItinerantWork'] ? '☑' : '☐') . " Разъездной характер работы" .  PHP_EOL;
        $text .= "Процент полевой работы, {$data['fieldPercent']}% " . PHP_EOL;
        $text .= (($data['schedule'] === 'Удаленный' || $data['schedule'] === 'Гибридный') ? '☑' : '☐') . " Дистант" . PHP_EOL;
        $distantPercent = '';
        if ($data['schedule'] === 'Удаленный') {
            $distantPercent = '80-100%';
        }
        if ($data['schedule'] === 'Гибридный') {
            $distantPercent = $data['distantPercent'];
        }
        $text .= "Процент удаленной работы, {$distantPercent}%" . PHP_EOL;
        $this->addRow($ActiveSheet, $row++, '', $text);
        $this->addRow($ActiveSheet, $row++, 'Принадлежность к организационной структуре' .PHP_EOL.'(полный путь подразделения)', $data['department']);

        $admManagerFio = '';
        if($data['admManager']) {
            $manager = \Renins\User::getByIdLight($data['admManager']);
            $admManagerFio = $manager['fio'];
        }
        $this->addRow($ActiveSheet, $row++, 'Административный руководитель', $admManagerFio);
        $funcManagerFio = '';
        if($data['funcManager']) {
            $manager = \Renins\User::getByIdLight($data['funcManager']);
            $funcManagerFio = $manager['fio'];
        }
        $this->addRow($ActiveSheet, $row++, 'Функциональный руководитель', $funcManagerFio);

        $this->addSubHeader($ActiveSheet, $row++, 'Подчинение');
        $this->addRow($ActiveSheet, $row++, 'Подчиненные по организационной структуре', '');
        $this->addRow($ActiveSheet, $row++, 'Должности и подразделения прямых подчиненных', $data['subordinatesComment'], ['right']);
        $this->addRow($ActiveSheet, $row++, 'Общее кол-во подчиненных всех уровней, чел', $data['allSubordinatesCount'], ['right'], ['left']);

        $text = ($data['hasFuncSubs'] ? '☑' : '☐') . " Функциональные подчиненные". PHP_EOL ;
        $text .= ($data['hasProjectSubs'] ? '☑' : '☐') . " Проектные подчиненные" .  PHP_EOL;
        $text .= ($data['isItinerantWork'] ? '☑' : '☐') . " Внешние подчиненные (аутсорс)" . PHP_EOL;
        $this->addRow($ActiveSheet, $row++, 'Другие подчиненные: ', $text);
        $this->addRow($ActiveSheet, $row++, 'Общее кол-во подчиненных, чел. ', (int)$data['funcSubordinatesCount'] + (int)$data['projectSubordinatesCount'] + (int)$data['outsourceSubordinatesCount'], ['right'], ['left']);
        $this->addRow($ActiveSheet, $row++, 'Должности и подразделения подчиненных', $data['outsourceComment'], ['right']);

        $this->addSubHeader($ActiveSheet, $row++, 'Цели');
        $this->addRow($ActiveSheet, $row++, 'Основные цели - предназначение подразделения, в котором находится должность', implode(PHP_EOL, (array)$data['departmentGoals']), ['left'], ['left']);
        $this->addRow($ActiveSheet, $row++, 'Непосредственные цели - предназначение самой должности', implode(PHP_EOL, (array)$data['positionGoals']), ['left'], ['left']);

        $this->addSubHeader($ActiveSheet, $row++, 'Должностные обязанности и Результаты деятельности');
        $text = ($data['isShortTerm'] ? '☑' : '☐') ." Краткосрочный (месяц, квартал, полугодие)".  PHP_EOL ;
        $text .= ($data['isMediumTerm'] ? '☑' : '☐') ." Среднесрочный (1–2 года)" .  PHP_EOL;
        $text .= ($data['isLongTerm'] ? '☑' : '☐') . " Долгосрочный (3–5 лет)" . PHP_EOL;
        $this->addRow($ActiveSheet, $row++, 'На какой период устанавливается горизонт планирования для должности?', $text);

        $text = '';
        foreach ($data['mainDuties'] as $item) {
            $text .= 'Обязанность: ' . $item['duty'] . PHP_EOL;
            $text .= 'Результат: ' . $item['result'] . PHP_EOL. PHP_EOL;
        }
        $this->addRow($ActiveSheet, $row++, 'Перечислите основные должностные обязанности и желаемый результат по каждой из них', $text);

        $text = '';
        foreach ($data['addDuties'] as $item) {
            $text .= 'Обязанность: ' . $item['duty'] . PHP_EOL;
            $text .= 'Результат: ' . $item['result'] . PHP_EOL. PHP_EOL;
        }
        $this->addRow($ActiveSheet, $row++, 'Укажите дополнительные обязанности — функции в рамках временных проектов, рабочих групп и желаемый результат', $text);

        $this->addSubHeader($ActiveSheet, $row++, 'Вклад должности в общие результаты компании');
        $text = ($data['positionContribution'] === 'Оперативный' ? '☑' : '☐') . " Оперативный".  PHP_EOL ;
        $text .= ($data['positionContribution'] === 'Тактический' ? '☑' : '☐') . " Тактический" .  PHP_EOL;
        $text .= ($data['positionContribution'] === 'Стратегический' ? '☑' : '☐') ." Стратегический" .  PHP_EOL;
        $this->addRow($ActiveSheet, $row++, 'Вклад должности в результаты деятельности', $text);
        $this->addRow($ActiveSheet, $row++, 'Обоснование стратегического вклада', $data['positionContributionDescription']);

        $this->addSubHeader($ActiveSheet, $row++, 'Полномочия в принятии решений');
        $this->addRow($ActiveSheet, $row++, 'Какие решения должность может принимать самостоятельно?', $data['decisions']);

        $this->addSubHeader($ActiveSheet, $row++, 'Ответственность за финансовый результат (ДА / НЕТ)');

        $text = ($data['financialResultGeneration'] === 'Да' ? '☑' : '☐') . ' Да' . PHP_EOL ;
        $text .= ($data['financialResultGeneration'] === 'Нет' ? '☑' : '☐') . ' Нет'. PHP_EOL ;
        $this->addRow($ActiveSheet, $row++, 'Вносит ли должность личный вклад в генерацию финансового результата? / 
Стоят ли перед должность личные цели и задачи, являющиеся частью корпоративных финансовых целей и задач?', $text);
        $this->addRow($ActiveSheet, $row++, 'Сколько он составляет по EBIT (руб/год)', $data['EBIT'], ['left'], ['left']);
        $this->addRow($ActiveSheet, $row++, 'Сколько он составляет по WP (подпис.премия) (руб/год)', $data['WP'], ['left'], ['left']);

        $this->addSubHeader($ActiveSheet, $row++, 'Ответственность за бюджет по расходам ( ДА / НЕТ )');

        $text = ($data['isNotInvolvedInBudgetManagement'] ? '☑' : '☐') . ' Не участвует в управлении бюджетом по расходам' . PHP_EOL ;
        $text .= ($data['isControlTargetBudget'] ? '☑' : '☐') . 'Контролирует выполнение целевого расходования бюджета (политики, процедуры, согласования и пр.)'. PHP_EOL ;
        $text .= ($data['isPrepareProposalsToSpendBudget'] ? '☑' : '☐') . 'Готовит предложения о путях целевого расходования бюджета (без полномочий принятия решений)'. PHP_EOL ;
        $text .= ($data['hasAuthorityToMakeDecisions'] ? '☑' : '☐') . 'Имеет полномочия принимать решения о конкретном пути расходования бюджета'. PHP_EOL ;
        $this->addRow($ActiveSheet, $row++, 'Имеет ли должность полномочия управления (или контроля) бюджетом по расходам?', $text);
        $this->addRow($ActiveSheet, $row++, 'Сколько он составляет по С&B (руб/год)', $data['CnBSum'], ['left'], ['left']);
        $this->addRow($ActiveSheet, $row++, 'Сколько он составляет по non-C&B (руб/год)', $data['nonCnBSum'], ['left'], ['left']);

        $this->addSubHeader($ActiveSheet, $row++, 'Уровень инновационности деятельности');
        $levels = [
            'Поддержка существующих стандартов работы',
            'Некоторая оптимизация, улучшение существующих стандартов работы (<10% изменений)',
            'Существенная оптимизация, улучшение существующих стандартов работы (10-25% изменений)',
            'Кардинальное изменение существующих стандартов работы на основе прогрессивных тенденций (>25% изменений)',
            'Внедрение инновационных изменений - революционных рыночных практик',
        ];
        $text = ($data['levelOfInnovativeness'] === $levels[0] ? '☑' : '☐') . ' ' . $levels[0] . PHP_EOL ;
        $text .= ($data['levelOfInnovativeness'] === $levels[1] ? '☑' : '☐') . ' ' . $levels[1] . PHP_EOL ;
        $text .= ($data['levelOfInnovativeness'] === $levels[2] ? '☑' : '☐') . ' ' . $levels[2] . PHP_EOL ;
        $text .= ($data['levelOfInnovativeness'] === $levels[3] ? '☑' : '☐') . ' ' . $levels[3] . PHP_EOL ;
        $text .= ($data['levelOfInnovativeness'] === $levels[4] ? '☑' : '☐') . ' ' . $levels[4] . PHP_EOL ;
        $this->addRow($ActiveSheet, $row++, 'Уровень инновационности деятельности', $text);

        $this->addSubHeader($ActiveSheet, $row++, 'Коммуникация в рамках должностных обязанностей');
        $this->addRow($ActiveSheet, $row++, 'Круг взаимодействия внутри Компании: подразделения, уровень должностей взаимодействия', $data['interactionCircleWithinTheCompany']);

        $text = 'Клиенты B2B' . PHP_EOL;
        $b2bClients = ['Крупные', 'Средние', 'Мелкие'];
        foreach ($b2bClients as $item) {
            $text .= (in_array($item, (array)$data['b2bClients']) ? '☑' : '☐') . ' ' . $item . PHP_EOL ;
        }
        $text .= PHP_EOL;
        $text .= 'Клиенты B2C' . PHP_EOL;
        $b2cClients = ['VIP', 'Средние', 'Мелкие'];
        foreach ($b2cClients as $item) {
            $text .= (in_array($item, (array)$data['b2cClients']) ? '☑' : '☐') . ' ' . $item . PHP_EOL ;
        }
        $text .= PHP_EOL;
        $text .= 'А также:' . PHP_EOL;
        $otherClients = ['Гос. органы', 'Общественные организации', 'Партнеры', 'Дилеры', 'Агенты'];
        foreach ($otherClients as $item) {
            $text .= (in_array($item, (array)$data['otherClients'])? '☑' : '☐') . ' ' . $item . PHP_EOL ;
        }
        $this->addRow($ActiveSheet, $row++, 'Круг взаимодействия во внешней среде:', $text);
        $this->addRow($ActiveSheet, $row++, 'Название внешних организаций, уровень должностей взаимодействия', $data['namesOfExternalOrganizations']);

        $list = [
            'Прием/передача информации',
            'Консультирование, объяснение существующих правил, стремление к соглашению',
            'Взаимодействие и влияние с применением профессиональной аргументации',
            'Участие в переговорах рабочего / тактического уровня',
            'Ключевое участие в переговорах тактического уровня, авторитетное влияние на результаты переговоров',
            'Ведение стратегических переговоров',
        ];
        $text = ($data['isTransmittingInformation'] ? '☑' : '☐') . ' ' . $list[0] . PHP_EOL ;
        $text .= ($data['isConsulting'] ? '☑' : '☐') . ' ' . $list[1] . PHP_EOL ;
        $text .= ($data['isInteraction'] ? '☑' : '☐') . ' ' . $list[2] . PHP_EOL ;
        $text .= ($data['isParticipationNegotiations'] ? '☑' : '☐') . ' ' . $list[3] . PHP_EOL ;
        $text .= ($data['isAuthoritativeInfluence'] ? '☑' : '☐') . ' ' . $list[4] . PHP_EOL ;
        $text .= ($data['isStrategicNegotiations'] ? '☑' : '☐') . ' ' . $list[5] . PHP_EOL ;
        $this->addRow($ActiveSheet, $row++, 'Преобладающий характер коммуникаций', $text);

        $list = [
            'Редко',
            'Иногда',
            'Часто',
        ];
        $text = ($data['amountOfCommunications'] === $list[0] ? '☑' : '☐') . ' ' . $list[0] . PHP_EOL ;
        $text .= ($data['amountOfCommunications'] === $list[1] ? '☑' : '☐') . ' ' . $list[1] . PHP_EOL ;
        $text .= ($data['amountOfCommunications'] === $list[2] ? '☑' : '☐') . ' ' . $list[2] . PHP_EOL ;
        $this->addRow($ActiveSheet, $row++, 'Коммуникации в ситуациях конфликта интересов сторон', $text);

        $this->addSubHeader($ActiveSheet, $row++, 'Требования, необходимые для выполнения должностных обязанностей');
        $levels = [
            'Среднее общее',
            'Среднее специальное',
            'Неполное высшее',
            'Высшее',
            'MBA',
            'Ученая степень (кандидат/доктор наук)',
        ];
        $text = ($data['minimumLevelOfEducation'] === $levels[0] ? '☑' : '☐') . ' ' . $levels[0] . PHP_EOL ;
        $text .= ($data['minimumLevelOfEducation'] === $levels[1] ? '☑' : '☐') . ' ' . $levels[1] . PHP_EOL ;
        $text .= ($data['minimumLevelOfEducation'] === $levels[2] ? '☑' : '☐') . ' ' . $levels[2] . PHP_EOL ;
        $text .= ($data['minimumLevelOfEducation'] === $levels[3] ? '☑' : '☐') . ' ' . $levels[3] . PHP_EOL ;
        $text .= ($data['minimumLevelOfEducation'] === $levels[4] ? '☑' : '☐') . ' ' . $levels[4] . PHP_EOL ;
        $text .= ($data['minimumLevelOfEducation'] === $levels[5] ? '☑' : '☐') . ' ' . $levels[5] . PHP_EOL ;
        $this->addRow($ActiveSheet, $row++, 'Образование', $text);
        $this->addRow($ActiveSheet, $row++, 'Специализация / Квалификация', $data['Qualification']);
        $this->addRow($ActiveSheet, $row++, 'Сертификация (если необходима для должности)', $data['Certification']);
        $this->addRow($ActiveSheet, $row++, 'Соответствие квалификационным требованиям (профстандарт)', $data['professionalStandard']);
        $this->addRow($ActiveSheet, $row++, 'Знания, умения, навыки', '');
        $this->addRow($ActiveSheet, $row++, 'Знание методик и практик:', $data['knowledgeOfMethods'], ['right'], ['left']);
        $this->addRow($ActiveSheet, $row++, 'Знание средств и технологий / компьютерных программ:', $data['knowledgeOfComputerPrograms'], ['right'], ['left']);
        $this->addRow($ActiveSheet, $row++, 'Знание текущей ситуации в функциональной области:', $data['knowledgeOfSituation'], ['right'], ['left']);
        $this->addRow($ActiveSheet, $row++, 'Деловые качества сотрудника, замещающего должность', $data['businessQualities']);

        $competencesQuestions = [
            'q1' => "Учитывает мотивы, чувства и потребности окружающих",
            'q2' => "Предвосхищает потребности",
            'q3' => "Неравнодушен к проблемам других, оказывает помощь",
            'q4' => "Выходит за рамки инструкций",
            'q5' => "Оперативно реагирует на запросы, выполняет взятые обязательства",
            'q6' => "Озвучивает мысли ясно и понятно",
            'q7' => "Объясняет причины отказа, предлагает решения",
            'q8' => "Качественно анализирует и синтезирует информацию",
            'q9' => "Опирается на данные и аналитику",
            'q10' => "Предотвращает возможные риски",
            'q11' => "Пилотирует решения",
            'q12' => "Честен и открыт с окружающими",
            'q13' => "Настойчив в достижении цели",
            'q14' => "Берет ответственность за решения",
            'q15' => "Действует для изменения ситуации",
            'q16' => "Развивается и самосовершенствуется",
            'q17' => "Ставит перед собой новые амбициозные цели",
            'q18' => "Изучает новые технологии",
            'q19' => "Внедряет новые подходы",
            'q20' => "Привлекает в команду сильных людей",
            'q21' => "Вносит предложения по улучшению процессов и регламентов смежных подразделений",
            'q22' => "Ориентируется на цели и интересы компании",
            'q23' => "Сотрудничает с коллегами, нацелен на общий результат",
            'q24' => "Учится на ошибках",
            'q25' => "Поддерживает и помогает другим в развитии",
            'q26' => "Дает обратную связь",
            'q27' => "Принимает обратную связь",
            'q28' => "Уважает время и ресурсы коллег",
            'q29' => "Своевременно отвечает на вопросы окружающих",
        ];
        $text = '';
        foreach ($data['competencies'] as $key => $value) {
            $text .= $competencesQuestions[$key] . ': ' . $value . PHP_EOL;
        }
        $this->addRow($ActiveSheet, $row++, 'Соответствие модели компетенций работника, действующей в Обществе для данного уровня', $text);

        $text = '';
        $englishLevels = ['Не обязателен', 'Elementary (A1)', 'Pre-intermediate (A2)', 'Intermediate (B1)', 'Upper-intermediate (B2)', 'Advanced (C1)', 'Proficiency (C2)'];
        foreach ($englishLevels as $level) {
            $text .= (strcasecmp($data['englishLevel'], $level) == 0 ? '☑' : '☐') . ' ' . $level . PHP_EOL ;
        }
        $this->addRow($ActiveSheet, $row++, 'Уровень владения английским языком *заполняется обязательно', $text);
        $text = '';
        foreach ($data['languages'] as $language) {
            $text .= 'Язык: ' . $language['name'] . PHP_EOL;
            $text .= 'Уровень владения: ' . $language['level'] . PHP_EOL. PHP_EOL;
        }
        $this->addRow($ActiveSheet, $row++, 'Уровень владения другими иностранными языками', $text);
        $this->addRow($ActiveSheet, $row++, 'Опыт', '');
        $this->addRow($ActiveSheet, $row++, 'Тип опыта', 'Управленческий', ['right'], ['left']);
        $this->addRow($ActiveSheet, $row++, 'В областях и сферах:', $data['fieldOfManagementActivity'], ['right'], ['left']);
        $this->addRow($ActiveSheet, $row++, 'Количество лет от:', $data['managementExperienceYears'], ['right'], ['left']);

        $this->addRow($ActiveSheet, $row++, 'Тип опыта', 'Профессиональный', ['right'], ['left']);
        $this->addRow($ActiveSheet, $row++, 'В областях и сферах:', $data['fieldOfActivity'], ['right'], ['left']);
        $this->addRow($ActiveSheet, $row++, 'Количество лет от:', $data['professionalExperienceYears'], ['right'], ['left']);

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="ПД_'.$data['positionName'].'.xlsx"');
        header('Cache-Control: max-age=0');
        mb_internal_encoding('latin1');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    private function addHeader($ActiveSheet, $row, $title): void
    {
        $ActiveSheet->setCellValue('A' . $row, $title);
        $ActiveSheet->mergeCells('A'.$row.':B'.$row);

        $style = $ActiveSheet->getStyle('A'.$row.':B'.$row);
        $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('50287d');
        $style->getFont()->getColor()->setRGB('FFFFFF');
        $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $style->getAlignment()->setHorizontal (Alignment::HORIZONTAL_CENTER);

        $ActiveSheet->getRowDimension($row)->setRowHeight(32);
    }

    private function addSubHeader($ActiveSheet, $row, $title): void
    {
        $ActiveSheet->setCellValue('A' . $row, $title);
        $ActiveSheet->mergeCells('A'.$row.':B'.$row);

        $style = $ActiveSheet->getStyle('A'.$row.':B'.$row);
        $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('cedc00');
        $style->getFont()->getColor()->setRGB('1e222e');
        $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $style->getAlignment()->setHorizontal (Alignment::HORIZONTAL_LEFT);

        $ActiveSheet->getRowDimension($row)->setRowHeight(24);
    }
    private function addRow($ActiveSheet, $row, $name, $value, $name_options = [], $value_options = []): void
    {
        $ActiveSheet->setCellValue('A' . $row, (string)$name);
        $ActiveSheet->setCellValue('B' . $row, (string)$value);
        $style = $ActiveSheet->getStyle('A'.$row.':B'.$row);
        $style->getAlignment()->setWrapText(true);
        $style->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        $style = $ActiveSheet->getStyle('A'.$row.':A'.$row);
        foreach ($name_options as $option) {
            switch ($option) {
                case 'right':
                    $style->getAlignment()->setHorizontal (Alignment::HORIZONTAL_RIGHT);
                    break;
                case 'left':
                    $style->getAlignment()->setHorizontal (Alignment::HORIZONTAL_LEFT);
                    break;
            }
        }
        $style = $ActiveSheet->getStyle('B'.$row.':B'.$row);
        foreach ($value_options as $option) {
            switch ($option) {
                case 'right':
                    $style->getAlignment()->setHorizontal (Alignment::HORIZONTAL_RIGHT);
                    break;
                case 'left':
                    $style->getAlignment()->setHorizontal (Alignment::HORIZONTAL_LEFT);
                    break;
            }
        }

    }

};
