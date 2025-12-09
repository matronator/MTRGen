// --- MTRGEN ---
// name: list-page-builder
// filename: <% entity|upperFirst %>ListPageBuilder.php
// path: /Users/matronator/Documents/Work/blueghost.nosync/eshop-3/back/src/Service/Event
// --- /MTRGEN ---
<?php

namespace App\Service\Event;

use App\Entity\BaseModel;
use App\Entity\<% entity %>;
use App\Service\PaginationListPageBuilder;
use Doctrine\ORM\EntityManagerInterface;

class <% entity %>ListPageBuilder extends PaginationListPageBuilder
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->repository = $em->getRepository(<% entity %>::class);
        $this->editRoute = '<% entity|snakeCase %>_form';
    }

    /**
     * @param <% entity %> $entity
     */
    public function getBaseListItem(BaseModel $entity): array
    {
        return [
            'name0' => $entity->getName0(),
            'name1' => $entity->getName1(),
            'name2' => $entity->getName2(),
            'name3' => $entity->getName3(),
            'name4' => $entity->getName4(),
            'id' => $entity->getId(),
            'entity' => $entity,
        ];
    }
}
