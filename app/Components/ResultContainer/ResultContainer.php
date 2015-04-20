<?php
/**
 * PHP Version 5
 *
 * @category  H24
 * @package
 * @author    "Yury Kozyrev" <yury.kozyrev@home24.de>
 * @copyright 2015 Home24 GmbH
 * @license   Proprietary license.
 * @link      http://www.home24.de
 */

namespace App\Components\ResultContainer;


class ResultContainer
{
    protected $id;
    protected $code;
    protected $message;
    protected $messageStatus;
    protected $documents = [];


    protected $statusMessage = [
        200 => "Your visa is ready",
        404 => "Your visa result not found",
        503 => "Invalid number",
    ];
    protected $statusColors  = [
        200 => "green",
        404 => "red",
        503 => "red",
    ];
    protected $documentCodes = [
        "RP" => "Загранпаспорт",
        "IP" => "Общегражданский паспорт",
        "KV" => "Страховой полис и его копия",
        "WS" => "Пропуск",
        "NU" => "Подтверждение из университета о том, что обучение может быть начато после официального начала семестра",
        "NSK" => "Подтверждение из университета / языковой школы, что обучение на языковых курсах может быть начато позже",
        "ZU" => "Актуальный допуск к занятиям.",
        "Imma" => "Подтверждение о зачислении",
        "SprachZ" => "Языковой сертификат (например, B1,TOEFL)",
        "SK" => "Выписка с блокированного счёта",
        "DIP" => "Диплом о высшем образовании с переводом",
        "ZNST" => "Подтверждение о перечислении стипендии",
        "ERG" => "Результат вступительного теста в подготовительный колледж (Studienkolleg)/результат вступительного экзамена",
        "PF" => "Справка из полиции об отсутствии судимостей",
        "TER" => "Подтверждение конкретной даты бракосочетания",
        "AES" => "Подтверждение из ЗАГСа о подаче заявления на заключение брака с указанием даты.",
        "VE" => "Заявление о принятии на себя расходов",
        "KA" => "Актуальная выписка со счета/справка из банка",
        "PSO" => "Подтверждение о передаче прав опеки",
        "BERUF" => "Гарантия разрешения на профессиональную деятельность",
        "HU" => "Оригинал свидетельства о браке",
        "GU" => "Оригинал свидетельства о рождении",
        "Apost" => "Апостиль",
        "ÜB" => "Перевод",
        "FamS" => "Штамп о семейном положении в общегражданском паспорте",
    ];


    public function __construct($message, $code = 200)
    {
        $this->code    = $code;
        $this->message = $message;
        $this->decodeDocuments();
    }

    protected function decodeDocuments()
    {
        if (!$this->isSuccess()) {
            return;
        }
        $string          = preg_replace('/^.*\s\s(.+)\s\s.*$/iu', '$1', $this->getMessage());
        $list            = preg_split("/[\s\,]+/iu", $string);
        $intersect       = array_intersect_key($this->documentCodes, array_flip($list));
        $this->documents = $intersect;

    }

    public function isSuccess()
    {
        return $this->code === 200;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    public function getStatusMessage()
    {
        return $this->statusMessage[$this->getCode()];
    }

    public function getColor()
    {
        return $this->statusColors[$this->getCode()];
    }

    /**
     * @return array
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


}