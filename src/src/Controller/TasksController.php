<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TasksController
 * @package App\Controller
 */
class TasksController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function tasksAction()
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
    public function addTaskAction(Request $request)
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

        return $this->json([
            'Something went wrong..'
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function editTaskAction(int $id, Request $request)
    {
        if (Request::METHOD_OPTIONS === $request->getMethod()) {
            return $this->json('success');
        }

        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository(Task::class)->find($id);

        if (!$task) {
            return $this->json([
                'The task was not found!',
            ], Response::HTTP_BAD_REQUEST);
        }

        $form = $this->createForm(TaskType::class, $task);

        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($task);
            $em->flush();

            return $this->json([
                'id' => $task->getId(),
                'title' => $task->getTitle(),
                'done' => $task->isDone(),
            ]);
        }

        return $this->json([
            'Something went wrong..'
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteTaskAction(int $id, Request $request)
    {
        if (Request::METHOD_OPTIONS === $request->getMethod()) {
            return $this->json('success');
        }

        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository(Task::class)->find($id);

        if (!$task) {
            return $this->json([
                'The task was not found!',
            ], Response::HTTP_BAD_REQUEST);
        }

        $em->remove($task);
        $em->flush();

        return $this->json([
            'Something went wrong..'
        ], Response::HTTP_BAD_REQUEST);
    }
}
