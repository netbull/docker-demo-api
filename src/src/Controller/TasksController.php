<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\Task;
use App\Form\TaskType;

/**
 * Class TasksController
 * @package App\Controller
 */
class TasksController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function indexAction()
    {
        $data = $this->getDoctrine()->getManager()->getRepository(Task::class)->getTasks();

        $tasks = [];
        foreach ($data as $task) {
            $task['createdAt'] = $task['createdAt']->format('Y-m-d H:i:s');
            $task['updatedAt'] = $task['updatedAt']->format('Y-m-d H:i:s');
            $tasks[] = $task;
        }

        return $this->json($tasks);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addAction(Request $request)
    {
        if (Request::METHOD_OPTIONS === $request->getMethod()) {
            return $this->json('success');
        }

        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->submit($request->request->all());
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($task);
            $em->flush();

            return $this->json([
                'id' => $task->getId(),
                'title' => $task->getTitle(),
                'done' => $task->isDone(),
            ]);
        }

        return $this->json('Something went wrong.', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateAction(int $id, Request $request)
    {
        if (Request::METHOD_OPTIONS === $request->getMethod()) {
            return $this->json('success');
        }

        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository(Task::class)->find($id);

        if (!$task) {
            return $this->json('The task was not found!', Response::HTTP_BAD_REQUEST);
        }

        $form = $this->createForm(TaskType::class, $task);

        $params = $request->request->all();
        if (isset($params['done'])) {
            $params['done'] = ('true' === $params['done'] || '1' === $params['done'] || 1 === $params['done']);
        }
        $form->submit($params);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($task);
            $em->flush();

            return $this->json([
                'id' => $task->getId(),
                'title' => $task->getTitle(),
                'done' => $task->isDone(),
            ]);
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->json($form->getErrors(true), Response::HTTP_BAD_REQUEST);
        }

        return $this->json('Something went wrong.', Response::HTTP_BAD_GATEWAY);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteAction(int $id, Request $request)
    {
        if (Request::METHOD_OPTIONS === $request->getMethod()) {
            return $this->json('success');
        }

        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository(Task::class)->find($id);

        if (!$task) {
            return $this->json('The task was not found!', Response::HTTP_BAD_REQUEST);
        }

        $em->remove($task);
        $em->flush();

        return $this->json('The task was deleted successfully');
    }
}
