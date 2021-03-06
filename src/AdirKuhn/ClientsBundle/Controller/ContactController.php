<?php

namespace AdirKuhn\ClientsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AdirKuhn\ClientsBundle\Entity\Contact;
use AdirKuhn\ClientsBundle\Form\ContactType;

/**
 * Contact controller.
 *
 */
class ContactController extends Controller
{

    /**
     * Lists all Contact entities.
     *
     */
    public function indexAction(Request $request)
    {
        if (false === $this->get('security.authorization_checker')->isGranted(array('ROLE_ADMIN', 'ROLE_CLIENTSBUNDLE_READ'))) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ClientsBundle:Contact')->findAll();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $request->query->get('page', 1) /*page number*/,
            50 /*limit per page*/
        );

        return $this->render('ClientsBundle:Contact:index.html.twig', array(
            'pagination' => $pagination,
        ));
    }
    /**
     * Creates a new Contact entity.
     *
     */
    public function createAction(Request $request)
    {
        if (false === $this->get('security.authorization_checker')->isGranted(array('ROLE_ADMIN', 'ROLE_CLIENTSBUNDLE_WRITE'))) {
            throw $this->createAccessDeniedException();
        }

        $entity = new Contact();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('clients_contact_show', array('id' => $entity->getId())));
        }

        return $this->render('ClientsBundle:Contact:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Contact entity.
     *
     * @param Contact $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Contact $entity)
    {
        $form = $this->createForm(new ContactType(), $entity, array(
            'action' => $this->generateUrl('clients_contact_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Contact entity.
     *
     */
    public function newAction()
    {
        if (false === $this->get('security.authorization_checker')->isGranted(array('ROLE_ADMIN', 'ROLE_CLIENTSBUNDLE_WRITE'))) {
            throw $this->createAccessDeniedException();
        }

        $entity = new Contact();
        $form   = $this->createCreateForm($entity);

        return $this->render('ClientsBundle:Contact:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Contact entity.
     *
     */
    public function showAction($id)
    {
        if (false === $this->get('security.authorization_checker')->isGranted(array('ROLE_ADMIN', 'ROLE_CLIENTSBUNDLE_READ'))) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ClientsBundle:Contact')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contact entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ClientsBundle:Contact:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Contact entity.
     *
     */
    public function editAction($id)
    {
        if (false === $this->get('security.authorization_checker')->isGranted(array('ROLE_ADMIN', 'ROLE_CLIENTSBUNDLE_UPDATE'))) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ClientsBundle:Contact')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contact entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ClientsBundle:Contact:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Contact entity.
    *
    * @param Contact $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Contact $entity)
    {
        $form = $this->createForm(new ContactType(), $entity, array(
            'action' => $this->generateUrl('clients_contact_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Contact entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        if (false === $this->get('security.authorization_checker')->isGranted(array('ROLE_ADMIN', 'ROLE_CLIENTSBUNDLE_UPDATE'))) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ClientsBundle:Contact')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contact entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        $successUpdated = false;
        if ($editForm->isValid()) {
            $em->flush();

            $successUpdated = true;
            //return $this->redirect($this->generateUrl('clients_contact_edit', array('id' => $id)));
        }

        return $this->render('ClientsBundle:Contact:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'successUpdated' => $successUpdated
        ));
    }
    /**
     * Deletes a Contact entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        if (false === $this->get('security.authorization_checker')->isGranted(array('ROLE_ADMIN', 'ROLE_CLIENTSBUNDLE_DELETE'))) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ClientsBundle:Contact')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Contact entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('contact'));
    }

    /**
     * Creates a form to delete a Contact entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('clients_contact_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
