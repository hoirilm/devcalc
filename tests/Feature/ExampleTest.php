<?php

test('the root application redirects to admin', function () {
    $response = $this->get('/');

    $response->assertRedirect('/admin');
});

test('admin login page returns a successful response', function () {
    $response = $this->get('/admin/login');

    $response->assertStatus(200);
});
