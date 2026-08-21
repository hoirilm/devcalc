<?php

test('the root application redirects to dashboard', function () {
    $response = $this->get('/');

    $response->assertRedirect('/dashboard');
});

test('login page returns a successful response', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});
