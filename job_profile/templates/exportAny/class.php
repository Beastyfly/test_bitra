<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use Bitrix\Main\Type;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

return new class extends \Renins\Component\BaseTemplateClass {
    public function execute()
    {
        $ID = $this->getContext()->arParams['ID'];
        if (!$ID) {
            $this['error'] = 'Не указан ID профиля должности';
        } else {
            $this->excel($ID);
        }
    }

    public function excel($data)
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $ActiveSheet = $spreadsheet->setActiveSheetIndex(0);

        // Заголовки (A-BB)
        $headers = [
            'A' => 'ID',
            'B' => 'Подразделение',
            'C' => 'Должность',
            'D' => 'Этап',
            'E' => 'Кост центр',
            'F' => 'Функция 1',
            'G' => 'Функция 2',
            'H' => 'Статус',
            'I' => 'ФИО у кого на заполнении/согласовании',
            'J' => 'Административный руководитель',
            'K' => 'Функциональный руководитель',
            'L' => 'Общее кол-во подчиненных, чел.',
            'M' => 'Должности и подразделения подчиненных',
            'N' => 'Основные цели - предназначение подразделения, в котором находится должность',
            'O' => 'Непосредственные цели - предназначение самой должности',
            'P' => 'На какой период устанавливается горизонт планирования для должности?',
            'Q' => 'Перечислите основные должностные обязанности и желаемый результат по каждой из них',
            'R' => 'Укажите дополнительные обязанности — функции в рамках временных проектов, рабочих групп и желаемый результат',
            'S' => 'Вклад должности в результаты деятельности',
            'T' => 'Обоснование стратегического вклада',
            'U' => 'Какие решения должность может принимать самостоятельно?',
            'V' => 'Вносит ли должность личный вклад в генерацию финансового результата? / Стоят ли перед должность личные цели и задачи, являющиеся частью корпоративных финансовых целей и задач?',
            'W' => 'Сколько он составляет по EBIT (руб/год)',
            'X' => 'Сколько он составляет по WP (подпис.премия) (руб/год)',
            'Y' => 'Имеет ли должность полномочия управления (или контроля) бюджетом по расходам?',
            'Z' => 'Уровень инновационности деятельности',
            'AA' => 'Круг взаимодействия внутри Компании: подразделения, уровень должностей взаимодействия',
            'AB' => 'Круг взаимодействия во внешней среде:',
            'AC' => 'Название внешних организаций, уровень должностей взаимодействия',
            'AD' => 'Преобладающий характер коммуникаций',
            'AE' => 'Коммуникации в ситуациях конфликта интересов сторон',
            'AF' => 'Образование',
            'AG' => 'Специализация / Квалификация',
            'AH' => 'Сертификация (если необходима для должности)',
            'AI' => 'Соответствие квалификационным требованиям (профстандарт)',
            'AJ' => 'Знание методик и практик:',
            'AK' => 'Знание средств и технологий / компьютерных программ:',
            'AL' => 'Знание текущей ситуации в функциональной области:',
            'AM' => 'Деловые качества сотрудника, замещающего должность',
            'AN' => 'Соответствие модели компетенций работника, действующей в Обществе для данного уровня',
            'AO' => 'Уровень владения английским языком *заполняется обязательно',
            'AP' => 'Уровень владения другими иностранными языками',
            'AQ' => 'Тип опыта',
            'AR' => 'В областях и сферах:',
            'AS' => 'Количество лет от:',
            'AT' => 'Тип опыта',
            'AU' => 'В областях и сферах:',
            'AV' => 'Количество лет от:',
            'AW' => 'Код обзора',
            'AX' => 'Программа премирования',
            'AY' => 'Грейд',
            'AZ' => 'Оклад, руб.',
            'BA' => 'Зарплатная вилка, руб.'
        ];

        foreach ($headers as $col => $header) {
            $ActiveSheet->getColumnDimension($col)->setWidth(2.8, 'cm');
        }
        $headerStyle = [
            'font' => [
                'bold' => true,
                'size' => 7,
                'name' => 'Inter',
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // По центру по горизонтали
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, // По центру по вертикали
                'wrapText' => true, // Перенос по словам
            ],
        ];


        // Заполняем заголовки
        $row = 1;
        foreach ($headers as $col => $header) {
            $ActiveSheet->setCellValue($col . $row, $header);
            $ActiveSheet->getStyle($col . $row)->applyFromArray($headerStyle);
        }

        $ActiveSheet->getRowDimension($row)->setRowHeight(3, 'cm');

        $dataStyle = [
            'font' => [
                'bold' => false,
                'size' => 7,
                'name' => 'Inter',
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // По центру по горизонтали
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, // По центру по вертикали
                'wrapText' => true, // Перенос по словам
            ],
        ];

        // Заполняем данные
        $row = 2;
        $data = array_reverse($data);
        foreach ($data as $id) {
            $data = $this->getContext()->loadDetailAction($id)['formData'];
            $zayavkaData = $this->getFromIB($id)[0];

            $ActiveSheet->setCellValue('A' . $row, $id);
            $ActiveSheet->setCellValue('B' . $row, $data['department']);
            $ActiveSheet->setCellValue('C' . $row, $data['positionName']);
            $stages = [
                'create' => 'Создание профиля',
                'filling' => 'Заполнение профиля',
                'checkingOD' => 'Проверка OD',
                'approval' => 'Согласование ПД',
                'cnbAndTnd' => 'T&D и C&B',
                'embedding' => 'Встраивание в орг. структуру',
                'cnb' => 'C&B',
                'tnd' => 'T&D',
                'adm_manager' => 'Административный руководитель',
                'func_manager' => 'Функциональный руководитель',
                'adm_manager_head' => 'Вышестоящий административный руководитель',
                'additional' => 'Дополнительное согласование',
            ];
            $ActiveSheet->setCellValue('D' . $row, $stages[$zayavkaData['PROPERTY_STAGE_VALUE']]);
            $ActiveSheet->setCellValue('E' . $row, $data['costCenter']);
            $ActiveSheet->setCellValue('F' . $row, $zayavkaData['PROPERTY_FUNC1_NAME_VALUE']);
            $ActiveSheet->setCellValue('G' . $row, $zayavkaData['PROPERTY_FUNC2_NAME_VALUE']);

            $statuses = [
                'draft' => 'Черновик',
                'on_filling' => 'На заполнении',
                'on_approval' => 'На согласовании',
                'on_refinement' => 'На доработке',
                'rejected' => 'На доработке',
                'revoked' => 'На доработке',
                'in_work' => 'В работе',
                'signed' => 'Утвержден',
                'inactive' => 'Неактивен',
                'trash' => 'Дополнительное согласование',
                'archive' => 'Архив',
            ];
            $ActiveSheet->setCellValue('H' . $row, $statuses[$zayavkaData['PROPERTY_STATUS_VALUE']]);/*
            $userInfo = \CUser::GetByID($zayavkaData['PROPERTY_OTVETSTVENNYY_ZA_ZAPOLNENIE_VALUE'])->Fetch();*/

            $stageStatuses = [
                'create' => $zayavkaData['CREATED_BY'],
                'filling' => $zayavkaData['PROPERTY_OTVETSTVENNYY_ZA_ZAPOLNENIE_VALUE'],
                'adm_manager' => $zayavkaData['PROPERTY_ADMINISTRATIVNYY_RUKOVODITEL_VALUE'],
                'func_manager' => $zayavkaData['PROPERTY_FUNKTSIONALNYY_RUKOVODITEL_VALUE'],
                'adm_manager_head' => $zayavkaData['PROPERTY_VYSHESTOYASCHIJ_ADM_RUKOVODITEL_VALUE'],
                'additional' => $zayavkaData['PROPERTY_DOPOLNITELNYE_SOGLASUYUSCHIE_VALUE'],
            ];
            $userInfoSog = \CUser::GetByID($stageStatuses[$zayavkaData['PROPERTY_STAGE_VALUE']]);
            if($arUser = $userInfoSog->Fetch())
            {
                $userInfoSog = $arUser['NAME'].' '.$arUser['LAST_NAME'].' '.$arUser['SECOND_NAME'];
            }else
            {
                $userInfoSog = 'Согласующий отсутствует';
            }


            $ActiveSheet->setCellValue('I' . $row, $userInfoSog);


            // Административный руководитель
            $admManagerFio = $data['admManager'] ? \Renins\User::getByIdLight($data['admManager'])['fio'] : '';
            $ActiveSheet->setCellValue('J' . $row, $admManagerFio);

            // Функциональный руководитель
            $funcManagerFio = $data['funcManager'] ? \Renins\User::getByIdLight($data['funcManager'])['fio'] : '';
            $ActiveSheet->setCellValue('K' . $row, $funcManagerFio);

            // Остальные поля
            $ActiveSheet->setCellValue('L' . $row, (int)$data['funcSubordinatesCount'] + (int)$data['projectSubordinatesCount'] + (int)$data['outsourceSubordinatesCount']);
            $ActiveSheet->setCellValue('M' . $row, $data['subordinatesComment']);
            $ActiveSheet->setCellValue('N' . $row, implode(PHP_EOL, (array)$data['departmentGoals']));
            $ActiveSheet->setCellValue('O' . $row, implode(PHP_EOL, (array)$data['positionGoals']));

            // Горизонт планирования
            $planningHorizon = ($data['isShortTerm'] ? '☑' : '☐') . " Краткосрочный\n"
                . ($data['isMediumTerm'] ? '☑' : '☐') . " Среднесрочный\n"
                . ($data['isLongTerm'] ? '☑' : '☐') . " Долгосрочный";
            $ActiveSheet->setCellValue('P' . $row, $planningHorizon);
            //Основные обязанности
            $mainDuties = '';
            foreach ($data['mainDuties'] as $item) {
                $mainDuties .= 'Обязанность: ' . $item['duty'] . PHP_EOL;
                $mainDuties .= 'Результат: ' . $item['result'] . PHP_EOL . PHP_EOL;
            }
            $ActiveSheet->setCellValue('Q' . $row, $mainDuties);

            //Дополнительные обязанности
            $addDuties = '';
            foreach ($data['addDuties'] as $item) {
                $addDuties .= 'Обязанность: ' . $item['duty'] . PHP_EOL;
                $addDuties .= 'Результат: ' . $item['result'] . PHP_EOL . PHP_EOL;
            }
            $ActiveSheet->setCellValue('R' . $row, $addDuties);

            //Вклад должности в результаты деятельности
            $inJob = ($data['positionContribution'] === 'Оперативный' ? '☑' : '☐') . " Оперативный" . PHP_EOL;
            $inJob .= ($data['positionContribution'] === 'Тактический' ? '☑' : '☐') . " Тактический" . PHP_EOL;
            $inJob .= ($data['positionContribution'] === 'Стратегический' ? '☑' : '☐') . " Стратегический" . PHP_EOL;
            $ActiveSheet->setCellValue('S' . $row, $inJob);

            //Обоснование стратегического вклада
            $ActiveSheet->setCellValue('T' . $row, $data['positionContributionDescription']);

            //Самостоятельные решения
            $ActiveSheet->setCellValue('U' . $row, $data['decisions']);

            //Финансовый вклад
            $finIncome = ($data['financialResultGeneration'] === 'Да' ? '☑' : '☐') . ' Да' . PHP_EOL;
            $finIncome .= ($data['financialResultGeneration'] === 'Нет' ? '☑' : '☐') . ' Нет' . PHP_EOL;
            $ActiveSheet->setCellValue('V' . $row, $finIncome);

            //EBIT (руб/год)
            $ActiveSheet->setCellValue('W' . $row, $data['EBIT']);

            //WP (руб/год)
            $ActiveSheet->setCellValue('X' . $row, $data['WP']);

            //Имеет ли должность полномочия управления (или контроля) бюджетом по расходам?
            $text = ($data['isNotInvolvedInBudgetManagement'] ? '☑' : '☐') . ' Не участвует в управлении бюджетом по расходам' . PHP_EOL;
            $text .= ($data['isControlTargetBudget'] ? '☑' : '☐') . 'Контролирует выполнение целевого расходования бюджета (политики, процедуры, согласования и пр.)' . PHP_EOL;
            $text .= ($data['isPrepareProposalsToSpendBudget'] ? '☑' : '☐') . 'Готовит предложения о путях целевого расходования бюджета (без полномочий принятия решений)' . PHP_EOL;
            $text .= ($data['hasAuthorityToMakeDecisions'] ? '☑' : '☐') . 'Имеет полномочия принимать решения о конкретном пути расходования бюджета' . PHP_EOL;
            $ActiveSheet->setCellValue('Y' . $row, $text);

            //Уровень инновационности деятельности
            $levels = [
                'Поддержка существующих стандартов работы',
                'Некоторая оптимизация, улучшение существующих стандартов работы (<10% изменений)',
                'Существенная оптимизация, улучшение существующих стандартов работы (10-25% изменений)',
                'Кардинальное изменение существующих стандартов работы на основе прогрессивных тенденций (>25% изменений)',
                'Внедрение инновационных изменений - революционных рыночных практик',
            ];
            $text = ($data['levelOfInnovativeness'] === $levels[0] ? '☑' : '☐') . ' ' . $levels[0] . PHP_EOL;
            $text .= ($data['levelOfInnovativeness'] === $levels[1] ? '☑' : '☐') . ' ' . $levels[1] . PHP_EOL;
            $text .= ($data['levelOfInnovativeness'] === $levels[2] ? '☑' : '☐') . ' ' . $levels[2] . PHP_EOL;
            $text .= ($data['levelOfInnovativeness'] === $levels[3] ? '☑' : '☐') . ' ' . $levels[3] . PHP_EOL;
            $text .= ($data['levelOfInnovativeness'] === $levels[4] ? '☑' : '☐') . ' ' . $levels[4] . PHP_EOL;
            $ActiveSheet->setCellValue('Z' . $row, $text);

            //Круг взаимодействия внутри Компании: подразделения, уровень должностей взаимодействия
            $ActiveSheet->setCellValue('AA' . $row, $data['interactionCircleWithinTheCompany']);

            //Круг взаимодействия во внешней среде
            $text = 'Клиенты B2B' . PHP_EOL;
            $b2bClients = ['Крупные', 'Средние', 'Мелкие'];
            foreach ($b2bClients as $item) {
                $text .= (in_array($item, (array)$data['b2bClients']) ? '☑' : '☐') . ' ' . $item . PHP_EOL;
            }
            $text .= PHP_EOL;
            $text .= 'Клиенты B2C' . PHP_EOL;
            $b2cClients = ['VIP', 'Средние', 'Мелкие'];
            foreach ($b2cClients as $item) {
                $text .= (in_array($item, (array)$data['b2cClients']) ? '☑' : '☐') . ' ' . $item . PHP_EOL;
            }
            $text .= PHP_EOL;
            $text .= 'А также:' . PHP_EOL;
            $otherClients = ['Гос. органы', 'Общественные организации', 'Партнеры', 'Дилеры', 'Агенты'];
            foreach ($otherClients as $item) {
                $text .= (in_array($item, (array)$data['otherClients']) ? '☑' : '☐') . ' ' . $item . PHP_EOL;
            }
            $ActiveSheet->setCellValue('AB' . $row, $text);

            //Название внешних организаций, уровень должностей взаимодействия
            $ActiveSheet->setCellValue('AC' . $row, $data['namesOfExternalOrganizations']);

            //Преобладающий характер коммуникаций
            $list = [
                'Прием/передача информации',
                'Консультирование, объяснение существующих правил, стремление к соглашению',
                'Взаимодействие и влияние с применением профессиональной аргументации',
                'Участие в переговорах рабочего / тактического уровня',
                'Ключевое участие в переговорах тактического уровня, авторитетное влияние на результаты переговоров',
                'Ведение стратегических переговоров',
            ];
            $text = ($data['isTransmittingInformation'] ? '☑' : '☐') . ' ' . $list[0] . PHP_EOL;
            $text .= ($data['isConsulting'] ? '☑' : '☐') . ' ' . $list[1] . PHP_EOL;
            $text .= ($data['isInteraction'] ? '☑' : '☐') . ' ' . $list[2] . PHP_EOL;
            $text .= ($data['isParticipationNegotiations'] ? '☑' : '☐') . ' ' . $list[3] . PHP_EOL;
            $text .= ($data['isAuthoritativeInfluence'] ? '☑' : '☐') . ' ' . $list[4] . PHP_EOL;
            $text .= ($data['isStrategicNegotiations'] ? '☑' : '☐') . ' ' . $list[5] . PHP_EOL;
            $ActiveSheet->setCellValue('AD' . $row, $text);

            //Коммуникации в ситуациях конфликта интересов сторон
            $list = [
                'Редко',
                'Иногда',
                'Часто',
            ];
            $text = ($data['amountOfCommunications'] === $list[0] ? '☑' : '☐') . ' ' . $list[0] . PHP_EOL;
            $text .= ($data['amountOfCommunications'] === $list[1] ? '☑' : '☐') . ' ' . $list[1] . PHP_EOL;
            $text .= ($data['amountOfCommunications'] === $list[2] ? '☑' : '☐') . ' ' . $list[2] . PHP_EOL;
            $ActiveSheet->setCellValue('AE' . $row, $text);

            //Образование
            $levels = [
                'Среднее общее',
                'Среднее специальное',
                'Неполное высшее',
                'Высшее',
                'MBA',
                'Ученая степень (кандидат/доктор наук)',
            ];
            $text = ($data['minimumLevelOfEducation'] === $levels[0] ? '☑' : '☐') . ' ' . $levels[0] . PHP_EOL;
            $text .= ($data['minimumLevelOfEducation'] === $levels[1] ? '☑' : '☐') . ' ' . $levels[1] . PHP_EOL;
            $text .= ($data['minimumLevelOfEducation'] === $levels[2] ? '☑' : '☐') . ' ' . $levels[2] . PHP_EOL;
            $text .= ($data['minimumLevelOfEducation'] === $levels[3] ? '☑' : '☐') . ' ' . $levels[3] . PHP_EOL;
            $text .= ($data['minimumLevelOfEducation'] === $levels[4] ? '☑' : '☐') . ' ' . $levels[4] . PHP_EOL;
            $text .= ($data['minimumLevelOfEducation'] === $levels[5] ? '☑' : '☐') . ' ' . $levels[5] . PHP_EOL;
            $ActiveSheet->setCellValue('AF' . $row, $text);

            //Специализация / Квалификация
            $ActiveSheet->setCellValue('AG' . $row, $data['Qualification']);

            //Сертификация (если необходима для должности)
            $ActiveSheet->setCellValue('AH' . $row, $data['Certification']);

            //Соответствие квалификационным требованиям (профстандарт)
            $ActiveSheet->setCellValue('AI' . $row, $data['professionalStandard']);

            //Знание методик и практик:
            $ActiveSheet->setCellValue('AJ' . $row, $data['knowledgeOfMethods']);

            //Знание средств и технологий / компьютерных программ:
            $ActiveSheet->setCellValue('AK' . $row, $data['knowledgeOfComputerPrograms']);

            //Знание текущей ситуации в функциональной области:
            $ActiveSheet->setCellValue('AL' . $row, $data['knowledgeOfSituation']);

            //Деловые качества сотрудника, замещающего должность
            $ActiveSheet->setCellValue('AM' . $row, $data['businessQualities']);

            //Соответствие модели компетенций работника, действующей в Обществе для данного уровня
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
            $ActiveSheet->setCellValue('AN' . $row, $text);

            //Уровень владения английским языком
            $text = '';
            $englishLevels = ['Не обязателен', 'Elementary (A1)', 'Pre-intermediate (A2)', 'Intermediate (B1)', 'Upper-intermediate (B2)', 'Advanced (C1)', 'Proficiency (C2)'];
            foreach ($englishLevels as $level) {
                $text .= (strcasecmp($data['englishLevel'], $level) == 0 ? '☑' : '☐') . ' ' . $level . PHP_EOL;
            }
            $ActiveSheet->setCellValue('AO' . $row, $text);

            //Уровень владения другими иностранными языками
            $text = '';
            foreach ($data['languages'] as $language) {
                $text .= 'Язык: ' . $language['name'] . PHP_EOL;
                $text .= 'Уровень владения: ' . $language['level'] . PHP_EOL . PHP_EOL;
            }
            $ActiveSheet->setCellValue('AP' . $row, $text);

            //Тип опыта
            $ActiveSheet->setCellValue('AQ' . $row, 'Тип опыта - Управленческий');
            $ActiveSheet->setCellValue('AR' . $row, 'В областях и сферах: '.$data['fieldOfManagementActivity']);
            $ActiveSheet->setCellValue('AS' . $row, 'Количество лет от: '.$data['managementExperienceYears']);

            $ActiveSheet->setCellValue('AT' . $row, 'Тип опыта - Профессиональный');
            $ActiveSheet->setCellValue('AU' . $row, 'В областях и сферах: '.$data['fieldOfActivity']);
            $ActiveSheet->setCellValue('AV' . $row, 'Количество лет от: '.$data['professionalExperienceYears']);

            //Код обзора
            $dataStr = json_decode($zayavkaData['PROPERTY_OBZOR_VALUE'], true);
            $ActiveSheet->setCellValue('AW' . $row, $dataStr[0]['code']);

            //Программа премирования
            $premArr = [
                'Полугодовая премия' => $zayavkaData['PROPERTY_PREMIYA_POLUGODOVAYA_VALUE'],
                'Квартальная премия' => $zayavkaData['PROPERTY_PREMIYA_KVARTALNAYA_VALUE'],
                'Ежемесячная премия' => $zayavkaData['PROPERTY_PREMIYA_EZHEMESYACHNAYA_VALUE'],
                'Годовая премия' => $zayavkaData['PROPERTY_PREMIYA_GODOVAYA_VALUE'],
            ];
            $text = '';
            foreach ($premArr as $key => $prem){
                if($prem === 'Y')
                {
                    $text .= $key . ' ☑' . PHP_EOL;
                }
                else
                {
                    $text .= $key . ' ☐' . PHP_EOL;
                }
            }
            $text .= 'Процент от оклада % - ' . $zayavkaData['PROPERTY_PREMIYA_PROCENT_VALUE'];
            $ActiveSheet->setCellValue('AX' . $row, $text);

            //Грейд
            if(!$zayavkaData['PROPERTY_GREID_VALUE']){
                $text = 'Не определен';
            } else {
                $text = $zayavkaData['PROPERTY_GREID_VALUE'];
            }
            $ActiveSheet->setCellValue('AY' . $row, $text);

            //Оклад, руб.
            $ActiveSheet->setCellValue('AZ' . $row, $zayavkaData['PROPERTY_VILKA_SREDNEE_VALUE']);

            //Зарплатная вилка, руб.
            $text = 'Верхнее значение - '.$zayavkaData['PROPERTY_VILKA_VERHNEE_VALUE'] . PHP_EOL;
            $text .= 'Среднее значение - '.$zayavkaData['PROPERTY_VILKA_SREDNEE_VALUE'] . PHP_EOL;
            $text .= 'Нижнеее значение - '.$zayavkaData['PROPERTY_VILKA_NIZHNEE_VALUE'] . PHP_EOL;
            $ActiveSheet->setCellValue('BA' . $row, $text);

            // Рассчитываем высоту строки на основе количества символов в столбце B
            $textInColumnB = $ActiveSheet->getCell('B' . $row)->getValue();
            $charsInColumnB = mb_strlen($textInColumnB); // Количество символов в столбце B

            $ActiveSheet->getRowDimension($row)->setRowHeight(3, 'cm');
            $ActiveSheet->getStyle('A' . $row . ':BA' . $row)->applyFromArray($dataStyle);

            $row++;
        }



        // Вывод
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Выгрузка_профилей_должности_' . date('d.m.Y') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function getFromIB($id)
    {
        // Создаем экземпляр класса для работы с инфоблоком
        $ib = new Renins\IB('job_profile', [
            'VILKA_VERHNEE',
            'VILKA_SREDNEE',
            'VILKA_NIZHNEE',
            'GREID',
            'PREMIYA_POLUGODOVAYA',
            'PREMIYA_KVARTALNAYA',
            'PREMIYA_EZHEMESYACHNAYA',
            'PREMIYA_GODOVAYA',
            'OBZOR',
            'STAGE',
            'FUNC1_NAME',
            'FUNC2_NAME',
            'STATUS',
            'OTVETSTVENNYY_ZA_ZAPOLNENIE',
            'ADMINISTRATIVNYY_RUKOVODITEL',
            'VYSHESTOYASCHIJ_ADM_RUKOVODITEL',
            'FUNKTSIONALNYY_RUKOVODITEL',
            'PREMIYA_PROCENT',
        ]);

        // Устанавливаем фильтр по ID
        $ib->setFilterParam('ID', $id);

        // Получаем список элементов по фильтру
        $list = $ib->getList();

        // Возвращаем результат
        return $list;
    }
};
