<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// Атрибут аутентификации (пустит на страницу только залогиненного пользователя)
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Создаем заказ
        $order = new Order();

        // Создаем форму
        $form = $this->createForm(OrderType::class, $order);

        // Обрабатываем запрос / валидация
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Получаем текущего юзера
            $user = $this->getUser();
            $order->setUser($user);
            $service = $order->getServiceType();

            // Заглушка для цен, по ТЗ она должна меняться JS-ом, но на бэке тоже надо ставить
            $price = match($service) {
                'auto' => 500,
                'flat' => 1000,
                'business' => 5000,
                default => 0,
            };
            $order->setPrice($price);

            // Сохраняем в БД
            $entityManager->persist($order);
            $entityManager->flush();

            // Редирект или сообщение об успехе
            $this->addFlash('success', 'Заказ успешно создан!');
            return $this->redirectToRoute('app_order');
        }

        return $this->render('order/index.html.twig', [
            'orderForm' => $form->createView(),
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200));
    }
}
