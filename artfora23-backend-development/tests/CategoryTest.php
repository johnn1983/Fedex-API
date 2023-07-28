<?php

namespace App\Tests;

use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class CategoryTest extends TestCase
{
    protected $admin;
    protected $user;

    public function setUp() : void
    {
        parent::setUp();

        $this->admin = User::find(1);
        $this->user = User::find(2);
    }

    public function testCreate()
    {
        $data = $this->getJsonFixture('create_category_request.json');

        $response = $this->actingAs($this->admin)->json('post', '/categories', $data);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('create_category_response.json', $response->json());

        $this->assertDatabaseHas(
            'categories',
            $this->getJsonFixture('create_category_response.json')
        );
    }

    public function testCreateWithParent()
    {
        $data = $this->getJsonFixture('create_category_with_parent_request.json');

        $response = $this->actingAs($this->admin)->json('post', '/categories', $data);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('create_category_with_parent_response.json', $response->json());

        $this->assertDatabaseHas(
            'categories',
            $this->getJsonFixture('create_category_with_parent_response.json')
        );
    }

    public function testCreateWithoutPermission()
    {
        $data = $this->getJsonFixture('create_category_request.json');

        $response = $this->actingAs($this->user)->json('post', '/categories', $data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testCreateNoAuth()
    {
        $data = $this->getJsonFixture('create_category_request.json');

        $response = $this->json('post', '/categories', $data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUpdate()
    {
        $data = $this->getJsonFixture('update_category_request.json');

        $response = $this->actingAs($this->admin)->json('put', '/categories/1', $data);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('categories', $data);
    }

    public function testUpdateWithoutPermission()
    {
        $data = $this->getJsonFixture('update_category_request.json');

        $response = $this->actingAs($this->user)->json('put', '/categories/1', $data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testUpdateNotExists()
    {
        $data = $this->getJsonFixture('update_category_request.json');

        $response = $this->actingAs($this->admin)->json('put', '/categories/0', $data);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUpdateNoAuth()
    {
        $data = $this->getJsonFixture('update_category_request.json');

        $response = $this->json('put', '/categories/1', $data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDelete()
    {
        $response = $this->actingAs($this->admin)->json('delete', '/categories/1');

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('categories', [
            'id' => 1
        ]);
    }

    public function testDeleteWithoutPermission()
    {
        $response = $this->actingAs($this->user)->json('delete', '/categories/1');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testDeleteNotExists()
    {
        $response = $this->actingAs($this->admin)->json('delete', '/categories/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);

        $this->assertDatabaseMissing('categories', [
            'id' => 0
        ]);
    }

    public function testDeleteNoAuth()
    {
        $response = $this->json('delete', '/categories/1');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testGet()
    {
        $response = $this->actingAs($this->user)->json('get', '/categories/1');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_category.json', $response->json());
    }

    public function testGetWithParentAndChildren()
    {
        $response = $this->actingAs($this->user)->json('get', '/categories/2', [
            'with' => [ 'parent', 'children' ]
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_category_with_parent_and_children.json', $response->json());
    }

    public function testGetNotExists()
    {
        $response = $this->actingAs($this->user)->json('get', '/categories/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function getSearchFilters()
    {
        return [
            [
                'filter' => ['all' => 1],
                'result' => 'search_all.json'
            ],
            [
                'filter' => [
                    'page' => 2,
                    'per_page' => 2
                ],
                'result' => 'search_by_page_per_page.json'
            ],
            [
                'filter' => [
                    'with' => [ 'parent', 'children' ]
                ],
                'result' => 'search_with_parent_and_children.json'
            ],
            [
                'filter' => [
                    'only_parents' => 1
                ],
                'result' => 'search_only_parents.json'
            ],
        ];
    }

    /**
     * @dataProvider getSearchFilters
     *
     * @param array $filter
     * @param string $fixture
     */
    public function testSearch($filter, $fixture)
    {
        $response = $this->json('get', '/categories', $filter);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture($fixture, $response->json());
    }
}
