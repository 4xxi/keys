<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Group;
use AppBundle\Form\Type\GroupType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Group controller.
 *
 * @Route("group")
 */
class GroupController extends Controller
{
    /**
     * Lists all group entities.
     *
     * @Route("/", name="group_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $groups = $em->getRepository('AppBundle:Group')->findAll();

        return $this->render('group/index.html.twig', array(
            'groups' => $groups,
        ));
    }

    /**
     * Displays a form to create a new group entity.
     *
     * @Route("/new", name="group_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $groupManager = $this->get('fos_user.group_manager');
        /** @var Group $group */
        $group = $groupManager->createGroup('');

        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $group->setOwner($this->getUser());
            $this->getUser()->addGroup($group);
            $em->persist($group);
            $em->flush();

            $this->addFlash('success', 'Group was created!');

            return $this->redirectToRoute('password_index');
        }

        return $this->render('group/new.html.twig', array(
            'group' => $group,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a group entity.
     *
     * @Route("/{id}", name="group_show")
     * @Method("GET")
     */
    public function showAction(Group $group)
    {
        $deleteForm = $this->createDeleteForm($group);

        return $this->render('group/show.html.twig', array(
            'group' => $group,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing group entity.
     *
     * @Route("/{id}/edit", name="group_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Group $group)
    {
        $deleteForm = $this->createDeleteForm($group);
        $editForm = $this->createForm(GroupType::class, $group);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            foreach ($group->getUsers() as $user) {
                $user->addGroup($group);
            }
            $em->persist($group);
            $em->flush();

            $this->addFlash('success', 'Group was updated!');

            return $this->redirectToRoute('group_edit', array('id' => $group->getId()));
        }

        return $this->render('group/edit.html.twig', array(
            'group' => $group,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a group entity.
     *
     * @Route("/{id}", name="group_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Group $group)
    {
        $form = $this->createDeleteForm($group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($group);
            $em->flush($group);
        }

        return $this->redirectToRoute('group_index');
    }

    /**
     * Creates a form to delete a group entity.
     *
     * @param Group $group The group entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Group $group)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('group_delete', array('id' => $group->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
