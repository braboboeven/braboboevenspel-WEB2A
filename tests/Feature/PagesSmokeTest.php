<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

function resolveRoutePath(string $uri): string
{
    $uri = preg_replace_callback('/\{([^}]+)\}/', function (array $matches): string {
        $parameter = rtrim($matches[1], '?');

        return match ($parameter) {
            'id' => '1',
            'hash' => 'hash',
            'token' => 'token',
            'filename' => 'file',
            'component' => 'component',
            'path' => 'missing.txt',
            default => 'value',
        };
    }, $uri);

    if ($uri === '/') {
        return '/';
    }

    return '/'.ltrim($uri, '/');
}

it('loads all GET routes without server errors', function () {
    $routes = collect(Route::getRoutes())->filter(function ($route): bool {
        return in_array('GET', $route->methods(), true) || in_array('HEAD', $route->methods(), true);
    });

    foreach ($routes as $route) {
        $path = resolveRoutePath($route->uri());
        $response = $this->get($path);

        expect($response->getStatusCode())->toBeLessThan(500, $path.' returned '.$response->getStatusCode());
    }
});

it('renders protected pages for verified docents', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'is_docent' => true,
    ]);

    $this->actingAs($user)->get('/spel')->assertOk();
    $this->actingAs($user)->get('/docent')->assertOk();
    $this->actingAs($user)->get('/graphiql')->assertOk();
    $this->actingAs($user)->get('/docs')->assertRedirect('/docs/api');
    $this->actingAs($user)->get('/admin')->assertOk();
});
