<?php

declare(strict_types=1);


namespace App\CustomServices;

use App\Entity\LegalPage;
use App\Entity\Trick;
use App\Entity\User;
use Symfony\Component\Form\FormInterface;

/**
 * Class AdminLister
 * Organizes lists treatment wuth the repositories
 * @package App\CustomServices
 */
class AdminLister extends AbstractLister
{
    protected function setQueryDefaultParameters():void
    {
        $this->queryParameters = [
            self::OFFSET_FIELD => 0,
            self::DIRECTION_FIELD => 'DESC',
            self::LIMIT_FIELD => 5,
            self::FILTER_FIELD => 'all'
        ];

        switch ($this->className) {
            case LegalPage::class:
            case Trick::class:
                $this->queryParameters[self::ORDER_FIELD] = 'name';
                break;
            case User::class:
                $this->queryParameters[self::ORDER_FIELD] = 'username';
                break;
            default:break;
        }
    }

    /**
     * @param FormInterface $paginationForm
     * @param int $page
     * @param string $formName
     * @return void
     */
    protected function setQueryParametersFromForm(FormInterface $paginationForm, int $page, string $formName):void
    {
        //Sets the new parameters for the query
        $this->queryParameters[self::LIMIT_FIELD] = $paginationForm->get(self::LIMIT_FIELD)->getData();
        $this->queryParameters[self::ORDER_FIELD] = $paginationForm->get(self::ORDER_FIELD)->getData();
        $this->queryParameters[self::DIRECTION_FIELD] = $paginationForm->get(self::DIRECTION_FIELD)->getData();

        //Sets the offset
        if ($this->page > 0) {
            $this->queryParameters[self::OFFSET_FIELD] = ($this->page - 1)*$this->queryParameters[self::LIMIT_FIELD];
        }
    }
}
