<?php

namespace SmsAuth\Test;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Kineticamobile\SmsAuth\SmsAuthServiceProvider;
use Kineticamobile\SmsAuth\SmsToken;
use Orchestra\Testbench\TestCase;

class SmsAuthTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [SmsAuthServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']);
        $app['config']->set('sms-auth.enable', true);
        $app['config']->set('sms-auth.ubicual_token', 'x');
        $app['config']->set('sms-auth.user_model', User::class);
        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('view.paths', [__DIR__.'/views']);
    }

    private function makeUsers()
    {
        Schema::create('users', function ($t) { $t->increments('id'); $t->string('email')->nullable(); $t->string('password')->nullable(); $t->rememberToken(); $t->timestamps(); });
        $this->artisan('migrate')->run();
    }

    public function testLoginFlow()
    {
        $this->makeUsers();
        $user = User::create(['phone' => '600000000']);
        Http::fake();

        // unknown user: error must be visible
        $this->from('/sms_auth')->followingRedirects()->post('/sms_login', ['phone' => '611111111'])->assertSee('Usuario no encontrado');

        $this->post('/sms_login', ['phone' => '600000000'])->assertOk()->assertSee('Verificar');
        $token = SmsToken::first();
        $this->assertSame(8, strlen((string) $token->token));
        Http::assertSent(function ($r) { return strpos($r->url(), 'to=600000000') !== false; });

        // second request while token alive: visible wait error
        $this->from('/sms_auth')->post('/sms_login', ['phone' => '600000000'])->assertRedirect('/sms_auth')->assertSessionHasErrors('phone');

        // expired token must be rejected
        $token->available_at = now()->subMinute();
        $token->save();
        $this->post('/sms_verify', ['token' => $token->token])->assertOk()->assertSee('Código incorrecto', false);
        $this->assertGuest();
        $token->available_at = now()->addMinutes(5);
        $token->save();

        $this->post('/sms_verify', ['token' => 'bad'])->assertOk()->assertSee('Código incorrecto', false);
        $this->assertGuest();
        $this->post('/sms_verify', ['token' => $token->token])->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
        $this->assertSame(0, SmsToken::count());
        $this->get('/sms_auth')->assertRedirect('/');
    }

    public function testVerifyIsThrottled()
    {
        $this->makeUsers();
        for ($i = 0; $i < 5; $i++) {
            $this->post('/sms_verify', ['token' => 'x'])->assertOk();
        }
        $this->post('/sms_verify', ['token' => 'x'])->assertStatus(429);
    }

    public function testCustomUsernameColumn()
    {
        $this->app['config']->set('sms-auth.username', 'mobile');
        $this->makeUsers();
        $this->assertTrue(Schema::hasColumn('users', 'mobile'));
        User::create(['mobile' => '622222222']);
        Http::fake();
        $this->post('/sms_login', ['phone' => '622222222'])->assertOk()->assertSee('Verificar');
        Http::assertSent(function ($r) { return strpos($r->url(), 'to=622222222') !== false; });
    }
}
