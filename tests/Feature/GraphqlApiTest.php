<?php

use App\Models\User;

it('creates a group via graphql', function () {
    $user = User::factory()->create();
  $csrfToken = 'test-token';

    $query = <<<'GQL'
mutation {
  createGroep(naam: "GraphQL", klas: "4D") {
    id
    naam
    code
  }
}
GQL;

    $response = $this->actingAs($user)
      ->withSession(['_token' => $csrfToken])
      ->withHeader('X-CSRF-TOKEN', $csrfToken)
      ->postJson('/graphql', [
        'query' => $query,
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.createGroep.naam', 'GraphQL');
});

  it('requires auth for graphiql ui', function () {
    $this->get('/graphiql')->assertRedirect(route('login'));
  });

  it('allows authenticated access to graphiql ui', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/graphiql')->assertOk();
  });

  it('redirects docs entry for authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/docs')->assertRedirect('/docs/api');
  });
