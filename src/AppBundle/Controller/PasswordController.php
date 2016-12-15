<?php

namespace AppBundle\Controller;

use AppBundle\Ajax\AjaxError;
use AppBundle\Ajax\AjaxResponse;
use AppBundle\Entity\Group;
use AppBundle\Entity\Password;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Password controller.
 *
 * @Route("password")
 */
class PasswordController extends Controller
{
    /**
     * Lists all password entities.
     *
     * @Route("/", name="password_index")
     * @Route("/group/{group}", name="password_index")
     * @Method("GET")
     */
    public function indexAction(Group $group = null)
    {
        if ($group && !$this->getUser()->getGroups()->contains($group)) {
            return $this->createNotFoundException();
        }

        $groupIds = $group ? [$group->getId()] : $this->getUser()->getGroupIds();

        $em = $this->getDoctrine()->getManager();
        $passwords = $em->getRepository('AppBundle:Password')->findByGroups($groupIds);

        return $this->render('password/index.html.twig', array(
            'passwords' => $passwords,
        ));
    }

    /**
     * Creates a new password entity.
     *
     * @Route("/new", name="password_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $password = new Password();
        $form = $this->createForm('AppBundle\Form\PasswordType', $password);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password->addGroup($this->getUser()->getPrivateGroup());
            $em = $this->getDoctrine()->getManager();
            $em->persist($password);
            $em->flush();

            return $this->redirectToRoute('password_index');
        }

        return $this->render('password/new.html.twig', array(
            'password' => $password,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a password.
     *
     * @Route("/{id}", name="ajax_password_show")
     * @Method("POST")
     *
     * @param Password $password
     *
     * @return JsonResponse
     */
    public function showAction(Password $password)
    {
        if ($password->canBeViewedBy($this->getUser())) {
            return new AjaxResponse($password->getPassword());
        }

        return new AjaxError('User isn\'t eligible to view the password.');
    }

    /**
     * Displays a form to edit an existing password entity.
     *
     * @Route("/{id}/edit", name="password_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request  $request
     * @param Password $password
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Password $password)
    {
        $editForm = $this->createForm('AppBundle\Form\PasswordType', $password);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('password_index');
        }

        return $this->render('password/edit.html.twig', array(
            'password' => $password,
            'form' => $editForm->createView(),
            'form_delete' => $this->createDeleteForm($password)->createView(),
        ));
    }

    /**
     * Deletes a password entity.
     *
     * @Route("/{id}", name="password_delete")
     * @Method("DELETE")
     *
     * @param Request  $request
     * @param Password $password
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Password $password)
    {
        $form = $this->createDeleteForm($password);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->getRepository('AppBundle:Password')->removeByUser($password, $this->getUser());
            $em->flush();
        }

        return $this->redirectToRoute('password_index');
    }

    /**
     * Creates a form to delete a password entity.
     *
     * @param Password $password The password entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Password $password)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('password_delete', array('id' => $password->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
