<?php

use PhpOffice\PhpWord\IOFactory;
use Bitrix\Main\Type;

return new class extends \Renins\Component\BaseTemplateClass {
    public function execute()
    {
        $fileID = (int)$this->getContext()->arParams['fileID'];
        if (!$fileID) {
            $this['error'] = 'Не указан fileID';
        } else {
            $boss     = Renins\Integration\Boss::getInstance();
            $fileData = $boss->getDIFile($fileID);
            $ext      = pathinfo($fileData['filename'], PATHINFO_EXTENSION);
            $ext      = mb_strtolower($ext);
            switch ($ext) {
                case 'pdf':
                    $this->pdf($fileData);
                    break;
                case 'doc':
                    $this->word($fileData);
                    break;
                default:
                    $this['error'] = 'Неизвестный тип файла';
            }

        }
    }

    public function word($data)
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        header("Content-type: application/vnd.ms-word;");
        header('Content-Disposition: attachment;filename="' . $data['filename'] . '"');
        header('Cache-Control: max-age=0');
        echo $data['di_file'];
        exit;
    }

    public function pdf($data)
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        header("Content-type:application/pdf");
        header('Content-Disposition: attachment;filename="' . $data['filename'] . '"');
        header('Cache-Control: max-age=0');
        echo $data['di_file'];
        exit;
    }

};
