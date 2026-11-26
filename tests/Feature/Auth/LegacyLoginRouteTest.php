<?php

test('the former login URL redirects to the staff login page', function () {
    $this->get('/login')
        ->assertRedirect(route('login'));
});

test('a form submitted to the former login URL keeps its POST request', function () {
    $this->post('/login', [
        'username' => 'stale-form-user',
        'password' => 'password',
    ])
        ->assertStatus(307)
        ->assertRedirect(route('login.store'));
});
