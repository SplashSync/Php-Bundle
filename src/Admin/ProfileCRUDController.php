<?php

/**
 * This file is part of SplashSync Project.
 *
 * Copyright (C) Splash Sync <www.splashsync.com>
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * @author Bernard Paquier <contact@splashsync.com>
 */

namespace Splash\Bundle\Admin;

use Symfony\Component\HttpFoundation\Response;

use Sonata\AdminBundle\Controller\CRUDController;

/**
 * Description of ObjectCRUDController
 *
 * @author nanard33
 */
class ProfileCRUDController extends CRUDController {
    
    
    /**
     * List action.
     *
     * @throws AccessDeniedException If access is not granted
     *
     * @return Response
     */
    public function listAction()
    {
//        return new Response("ok");
        // NEXT_MAJOR: Remove this line and use commented line below it instead
        $template = $this->admin->getTemplate('list');
        // $template = $this->templateRegistry->getTemplate('list');
//dump($template);

        return $this->render("@Splash/CRUD/list.html.twig", array(
            'action' => 'list',
//            "base_template" =>  $this->admin->getTemplate('list')
        ));

        $request = $this->getRequest();

//dump($request);

        $this->admin->checkAccess('list');

        $preResponse = $this->preList($request);
        if (null !== $preResponse) {
            return $preResponse;
        }

        if ($listMode = $request->get('_list_mode')) {
            $this->admin->setListMode($listMode);
        }

        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();

dump($datagrid->getPager()->getResults());    
//dump($formView);    

        // set the theme for the current Admin Form
//        $this->setFormTheme($formView, $this->admin->getFilterTheme());

        // NEXT_MAJOR: Remove this line and use commented line below it instead
        $template = $this->admin->getTemplate('list');
        // $template = $this->templateRegistry->getTemplate('list');
dump($template);
        return $this->renderWithExtraParams($template, [
            'action' => 'list',
            'form' => $formView,
//            'datagrid' => $datagrid,
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
            'export_formats' => $this->has('sonata.admin.admin_exporter') ?
                $this->get('sonata.admin.admin_exporter')->getAvailableFormats($this->admin) :
                $this->admin->getExportFormats(),
        ], null);
    }
    
    //put your code here
}
