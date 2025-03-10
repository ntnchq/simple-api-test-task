<?php

declare(strict_types=1);

namespace App\Tests\Functional\Article\Controller;

use App\Article\Entity\Article;
use App\Tag\Entity\Tag;
use App\Tests\DataFixtures\ArticleFixtures;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

class ArticleControllerTest extends FunctionalTestCase
{
    public function testGetArticles(): void
    {
        $this->client->request('GET', '/api/articles');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertCount(2, $responseData);

        $articleTitles = array_map(fn ($article) => $article['title'], $responseData);

        $this->assertContains(ArticleFixtures::TEST_ARTICLE_1, $articleTitles);
        $this->assertContains(ArticleFixtures::TEST_ARTICLE_2, $articleTitles);
    }

    public function testGetArticlesFilteredByTag(): void
    {
        $tagRepository = $this->entityManager->getRepository(Tag::class);
        $tag2 = $tagRepository->findOneBy(['name' => 'Test Tag 2']);

        $this->client->request('GET', '/api/articles?tagIds[]=' . $tag2->getId());

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertCount(1, $responseData);
        $this->assertEquals(ArticleFixtures::TEST_ARTICLE_2, $responseData[0]['title']);
    }

    public function testGetArticlesFilteredByMultipleTags(): void
    {
        $tagRepository = $this->entityManager->getRepository(Tag::class);
        $tag1 = $tagRepository->findOneBy(['name' => 'Test Tag 1']);
        $tag2 = $tagRepository->findOneBy(['name' => 'Test Tag 2']);

        $this->client->request('GET', '/api/articles?tagIds[]=' . $tag1->getId() . '&tagIds[]=' . $tag2->getId());

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertCount(1, $responseData);
        $this->assertEquals(ArticleFixtures::TEST_ARTICLE_2, $responseData[0]['title']);
    }

    public function testGetArticlesPagination(): void
    {
        for ($i = 3; $i <= 12; $i++) {
            $article = new Article("Test Article $i");
            $tagRepository = $this->entityManager->getRepository(Tag::class);
            $tag = $tagRepository->findOneBy(['name' => 'Test Tag 1']);
            $article->addTag($tag);
            $this->entityManager->persist($article);
        }
        $this->entityManager->flush();

        $this->client->request('GET', '/api/articles?page=1&limit=5');
        $this->assertResponseIsSuccessful();
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount(5, $responseData);

        $this->client->request('GET', '/api/articles?page=2&limit=5');
        $this->assertResponseIsSuccessful();
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount(5, $responseData);

        $this->client->request('GET', '/api/articles?page=3&limit=5');
        $this->assertResponseIsSuccessful();
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount(2, $responseData);
    }

    public function testGetArticle(): void
    {
        $articleRepository = $this->entityManager->getRepository(Article::class);
        $article = $articleRepository->findOneBy(['title' => ArticleFixtures::TEST_ARTICLE_1]);

        $this->client->request('GET', '/api/articles/'.$article->getId());

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertEquals($article->getId(), $responseData['id']);
        $this->assertEquals(ArticleFixtures::TEST_ARTICLE_1, $responseData['title']);

        $this->assertArrayHasKey('tags', $responseData);
        $this->assertNotEmpty($responseData['tags']);
    }

    public function testCreateArticle(): void
    {
        $tagRepository = $this->entityManager->getRepository(Tag::class);
        $tags = $tagRepository->findAll();
        $tagIds = array_map(fn ($tag) => $tag->getId(), $tags);

        $articleData = [
            'title' => 'New Test Article',
            'tagIds' => $tagIds,
        ];

        $this->client->request(
            'POST',
            '/api/articles',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($articleData)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals('New Test Article', $responseData['title']);

        $this->assertArrayHasKey('tags', $responseData);
        $this->assertNotEmpty($responseData['tags']);

        $articleRepository = $this->entityManager->getRepository(Article::class);
        $article = $articleRepository->findOneBy(['title' => 'New Test Article']);
        $this->assertNotNull($article);
    }

    public function testUpdateArticle(): void
    {
        $articleRepository = $this->entityManager->getRepository(Article::class);
        $article = $articleRepository->findOneBy(['title' => ArticleFixtures::TEST_ARTICLE_1]);

        $tagRepository = $this->entityManager->getRepository(Tag::class);
        $tag = $tagRepository->findAll()[0];

        $articleData = [
            'title' => 'Updated Test Article',
            'tagIds' => [$tag->getId()],
        ];

        $this->client->request(
            'PUT',
            '/api/articles/'.$article->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($articleData)
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertEquals($article->getId(), $responseData['id']);
        $this->assertEquals('Updated Test Article', $responseData['title']);

        $this->assertArrayHasKey('tags', $responseData);

        $this->entityManager->refresh($article);
        $this->assertEquals('Updated Test Article', $article->getTitle());
    }

    public function testDeleteArticle(): void
    {
        $articleRepository = $this->entityManager->getRepository(Article::class);
        $article = $articleRepository->findOneBy(['title' => ArticleFixtures::TEST_ARTICLE_1]);
        $articleId = $article->getId();

        $this->client->request('DELETE', '/api/articles/'.$articleId);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $this->entityManager->clear();
        $deletedArticle = $articleRepository->find($articleId);
        $this->assertNull($deletedArticle);
    }
}
