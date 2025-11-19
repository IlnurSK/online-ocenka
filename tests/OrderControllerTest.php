<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OrderControllerTest extends WebTestCase
{
// Сценарий 1: Неавторизованный пользователь видит ошибку (или редирект на логин)
    public function testAnonymousAccessDenied(): void
    {
        $client = static::createClient();

        // Пытаемся зайти на страницу заказа без входа
        $client->request('GET', '/order');

        // Ожидаем редирект (302) на страницу логина
        $this->assertResponseRedirects('/login');
    }

    // Сценарий 2: Авторизованный пользователь видит форму и все поля
    public function testAuthenticatedUserSeesForm(): void
    {
        $client = static::createClient();

        // Логинимся тестовым юзером
        $user = $this->createUser($client);
        $client->loginUser($user);

        // Заходим на страницу
        $crawler = $client->request('GET', '/order');

        // Проверяем, что статус 200 (ОК)
        $this->assertResponseIsSuccessful();

        // Проверяем наличие заголовка
        $this->assertSelectorTextContains('h3', 'Заказать оценку');

        // Проверяем наличие полей
        $this->assertCount(1, $crawler->filter('select[name="order[serviceType]"]'));
        $this->assertCount(1, $crawler->filter('input[name="order[email]"]'));
    }

    // Сценарий 3: Ошибка при пустых данных
    public function testSubmitEmptyForm(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/order');

        // Находим кнопку отправки
        $buttonCrawlerNode = $crawler->filter('button[name="order[submit]"]');

        // Получаем форму
        $form = $buttonCrawlerNode->form();

        // Очищаем email (делаем невалидным)
        $form['order[email]'] = '';

        // Отправляем
        $client->submit($form);

        // Проверяем, что мы не ушли в редирект (значит успех не наступил)
        $this->assertResponseStatusCodeSame(422);
    }

    // Сценарий 4: Успешное создание заказа
    public function testCreateOrderSuccess(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/order');

        $buttonCrawlerNode = $crawler->filter('button[name="order[submit]"]');
        $form = $buttonCrawlerNode->form();

        // Заполняем данными
        $form['order[serviceType]'] = 'auto';
        $form['order[email]'] = 'client@test.com';

        $client->submit($form);

        // Должен быть редирект после успеха
        $this->assertResponseRedirects('/order');

        // Идем по редиректу
        $client->followRedirect();

        // Проверяем Flash-сообщение
        $this->assertSelectorExists('.alert-success');
    }

    // Вспомогательный метод для создания юзера в тестовой БД
    private function createUser($client): User
    {
        $container = $client->getContainer();
        $em = $container->get('doctrine')->getManager();
        $userRepo = $em->getRepository(User::class);

        // Проверяем, может юзер уже есть
        $user = $userRepo->findOneBy(['email' => 'tester@test.com']);

        if (!$user) {
            $user = new User();
            $user->setEmail('tester@test.com');
            $user->setPassword('$2y$13$Hg...'); // Какой-то хэш
            $user->setRoles(['ROLE_USER']);

            $em->persist($user);
            $em->flush();
        }

        return $user;
    }
}