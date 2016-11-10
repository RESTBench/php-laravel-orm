<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Domains\Contacts\Contact;

class ContactApiTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions, WithoutMiddleware;

    protected $prefix = '/api/contacts';

    protected function setUp()
    {
        parent::setUp();
        factory(Contact::class)->create();
        factory(Contact::class)->create();
    }

    public function testIndex()
    {
        $response = $this->call('GET', $this->prefix . '/');
        $content = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $content, 'Invalid JSON');
        $actual = count($content);
        $expected = 2;
        $this->assertEquals($expected, $actual);
    }

    public function testFind()
    {
        $response = $this->call('GET', $this->prefix . '/2');
        $content = json_decode($response->getContent(), true);
        $this->assertInternalType('array', $content, 'Invalid JSON');

        $actual = $response->getStatusCode();
        $expected = \Illuminate\Http\Response::HTTP_OK;
        $this->assertEquals($expected, $actual);

        $actual = $content['id'];
        $expected = 2;
        $this->assertEquals($expected, $actual);
    }

    public function testFailFind()
    {
        $response = $this->call('GET', $this->prefix . '/3');
        $actual = $response->getStatusCode();
        $expected = \Illuminate\Http\Response::HTTP_NOT_FOUND;
        $this->assertEquals($expected, $actual);
    }

    public function testCreateContact()
    {
        $data = factory(Contact::class)->make()->toArray();
        $response = $this->call(
            'POST',
            $this->prefix . '/',
            $data,
            [],
            [],
            $this->transformHeadersToServerVars([
                'Accept' => 'application/json',
                'CONTENT_TYPE' => 'x-www-form-urlencoded'
            ])
        );

        $actual = $response->getStatusCode();
        $expected = \Illuminate\Http\Response::HTTP_NO_CONTENT;
        $this->assertEquals($expected, $actual);

        $response = $this->call('GET', $this->prefix . '/');
        $content = json_decode($response->getContent(), true);
        $this->assertInternalType('array', $content, 'Invalid JSON');

        $actual = count($content);
        $expected = 3;
        $this->assertEquals($expected, $actual);
    }
    
    public function testFailToCreateContact()
    {
        $data = [];
        $response = $this->call(
            'POST',
            $this->prefix . '/',
            $data,
            [],
            [],
            $this->transformHeadersToServerVars([
                'Accept' => 'application/json',
                'CONTENT_TYPE' => 'x-www-form-urlencoded'
            ])
        );

        $actual = $response->getStatusCode();
        $expected = \Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR;
        $this->assertEquals($expected, $actual);

    }

    public function testUpdate()
    {

        $newName = 'Gabriel Developer';
        $response = $this->call('PUT', $this->prefix . '/2', [
            'first_name' => $newName
        ], [
            'Accept' => 'application/json',
            'CONTENT_TYPE' => 'x-www-form-urlencoded'
        ]);

        $actual = $response->getStatusCode();
        $expected = \Illuminate\Http\Response::HTTP_NO_CONTENT;
        $this->assertEquals($expected, $actual);

        $response = $this->call('GET', $this->prefix . '/2');
        $content = json_decode($response->getContent(), true);
        $this->assertInternalType('array', $content, 'Invalid JSON');

        $actual = $content['first_name'];
        $expected = $newName;
        $this->assertEquals($expected, $actual);
    }
    
    public function testFailUpdateBecauseNotFindContact()
    {

        $newName = 'Gabriel Developer';
        $response = $this->call('PUT', $this->prefix . '/5', [
            'first_name' => $newName
        ], [
            'Accept' => 'application/json',
            'CONTENT_TYPE' => 'x-www-form-urlencoded'
        ]);

        $actual = $response->getStatusCode();
        $expected = \Illuminate\Http\Response::HTTP_NOT_FOUND;
        $this->assertEquals($expected, $actual);

    }

    public function testDelete()
    {
        $response = $this->call('DELETE', $this->prefix . '/2');

        $actual = $response->getStatusCode();
        $expected = \Illuminate\Http\Response::HTTP_NO_CONTENT;
        $this->assertEquals($expected, $actual);

        $response = $this->call('GET', $this->prefix . '/');
        $content = json_decode($response->getContent(), true);
        $this->assertInternalType('array', $content, 'Invalid JSON');

        $actual = count($content);
        $expected = 1;
        $this->assertEquals($expected, $actual);
    }

    public function testDeleteBecauseNotFindContact()
    {
        $response = $this->call('DELETE', $this->prefix . '/5');

        $actual = $response->getStatusCode();
        $expected = \Illuminate\Http\Response::HTTP_NOT_FOUND;
        $this->assertEquals($expected, $actual);
    }
}
