// --- MTRGEN ---
// name: e3-manager
// filename: <% entity|upperFirst %>Manager.php
// path: /Users/matronator/Documents/Work/blueghost.nosync/eshop-3/back/src/Service/Event
// --- MTRGEN ---
<?php

namespace App\Service\Event;

use App\Entity\BaseIdModel;
use App\Entity\<% entity %>;
use App\Service\AbstractEntityManager;
use App\Utils\ModuleStatus;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method <% entity %> get($data)
 */
class <% entity %>Manager extends AbstractEntityManager
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->subject = '<% czech|lower %>';
        $this->modelClass = <% entity %>::class;
        $this->searchResultRoute = '<% entity|snakeCase %>_form';

        parent::__construct($em);
    }

    /**
     * @param <% entity %> $entity
     */
    protected function getSearchIndexSearchString(BaseIdModel $entity): string
    {
        $data = [
            $entity->getName0(),
            $entity->getName1(),
            $entity->getName2(),
            $entity->getName3(),
            $entity->getName4(),
        ];

        return join(' ', $data);
    }

    /**
     * @param <% entity %> $entity
     */
    protected function getSearchIndexName(BaseIdModel $entity): string
    {
        return '<% czech|upperFirst %> události '.$entity->getName0();
    }
}
