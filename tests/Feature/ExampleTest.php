<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_home_page_can_be_rendered()
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Portfolio-ready messaging experience');
    }
}
