// --- MTRGEN ---
// name: e3-controller
// filename: <% entity|upperFirst %>Controller.php
// path: /Users/matronator/Documents/Work/blueghost.nosync/eshop-3/back/src/Controller/Admin
// --- MTRGEN ---
<?php

namespace App\Controller\Admin;

use App\Entity\BaseIdModel;
use App\Entity\<% entity %>;
use App\Exception\Admin\AdminErrorException;
use App\Service\<% parent|upperFirst %>\<% entity %>ListPageBuilder;
use App\Service\<% parent|upperFirst %>\<% entity %>Manager;
use App\Service\<% parent|upperFirst %>\<% entity %>Validator;
use App\Utils\Breadcrumb;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/<% parent|lower %>", name="<% parent|lower %>_")
 */
class <% entity %>Controller extends BaseEntityController
{
    public function __construct(<% entity %>Manager $manager, <% entity %>Validator $validator, <% entity %>ListPageBuilder $listPageBuilder)
    {
        $this->editTemplate = 'admin/<% templateDir %>/detail.html.twig';
        $this->listTemplate = 'admin/<% templateDir %>/list_page.html.twig';
        $this->entityName = '<% entity|snakeCase %>';
        $this->manager = $manager;
        $this->validator = $validator;
        $this->listPageBuilder = $listPageBuilder;
    }

    /**
     * @Route("/<% listRoute %>", name="<% shortName|lower %>_list", methods={"GET"})
     */
    public function listPage(Request $request): Response
    {
        return parent::listPage($request);
    }

    /**
     * @Route("/<% editRoute %>", name="<% shortName|lower %>_form", methods={"GET", "POST"})
     */
    public function edit(Request $request): Response
    {
        return parent::edit($request);
    }

    /**
     * Vytvori novy zaznam entity. Vrati ID entity pri uspechu a false pri selhani.
     *
     * @return int|bool
     */
    protected function handleInsert(Request $request)
    {
        try {
            $data = $request->request->all('admin');
            $id = $this->manager->create($data);
            $this->addFlash(self::STATUS_SUCCESS, '<% czech|upperFirst %> byl úspěšně vytvořen.');

            return $id;
        } catch (AdminErrorException $ex) {
            $this->addFlash(self::STATUS_ERROR, $ex->getMessage());

            return false;
        }
    }

    /**
     * Edituje existujici zaznam entity.
     */
    protected function handleEdit(Request $request): void
    {
        try {
            $data = $request->request->all('admin');
            $this->manager->update($data);
            $this->addFlash(self::STATUS_SUCCESS, '<% czech|upperFirst %> byl úspěšně upraven.');
        } catch (AdminErrorException $ex) {
            $this->addFlash(self::STATUS_ERROR, $ex->getMessage());
        }
    }

    /**
     * Odstrani zaznam entity.
     */
    protected function handleDelete(Request $request): void
    {
        try {
            $this->manager->delete($request->query->get('id'));
            $this->addFlash(self::STATUS_SUCCESS, '<% czech|upperFirst %> byl úspěšně vymazán.');
        } catch (AdminErrorException $ex) {
            $this->addFlash(self::STATUS_ERROR, $ex->getMessage());
        }
    }

    /**
     * Vrati instanci modelu konkretni entity dle predanych dat.
     *
     * @param int|array|null $data
     */
    protected function getEntity($data = null): BaseIdModel
    {
        if (!isset($data)) {
            $entity = new <% entity %>();
        } elseif (!is_array($data)) {
            $entity = $this->manager->get($data);
        } else {
            $entity = new <% entity %>();
            $entity->deserialize($this->em, $data);

            $this->manager->fillRelationsFromPostData($entity, $data);
        }

        return $entity;
    }

    protected function getListPageBreadcrumbs(): array
    {
        $breadcrumbs = [];
        $breadcrumbs[] = new Breadcrumb('Správa událostí', $this->generateUrl('<% parent|lower %>_menu'));
        $breadcrumbs[] = new Breadcrumb('Typy událostí', $this->generateUrl('<% entity|snakeCase %>_list'));

        return $breadcrumbs;
    }

    protected function getEditBreadcrumbs(): array
    {
        $breadcrumbs = $this->getListPageBreadcrumbs();

        $breadcrumbs[] = new Breadcrumb('Správa typu');

        return $breadcrumbs;
    }
}
