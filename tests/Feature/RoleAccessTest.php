<?php

namespace Tests\Feature;

use App\Models\Coach;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use DatabaseTransactions;

    private function createUser(string $role, string $status = 'active'): User
    {
        return User::create([
            'name' => ucfirst($role) . ' Test',
            'email' => $role . '-' . Str::uuid() . '@example.test',
            'password' => Hash::make('TestPassword123!'),
            'role' => $role,
            'status' => $status,
        ]);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $user = $this->createUser('admin');

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_coach_can_access_coach_dashboard(): void
    {
        $user = $this->createUser('coach');

        Coach::create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('coach.dashboard'))
            ->assertOk();
    }

    public function test_customer_can_access_customer_dashboard(): void
    {
        $user = $this->createUser('customer');

        $this->actingAs($user)
            ->get(route('customer.dashboard'))
            ->assertOk();
    }

    public function test_users_cannot_access_other_role_dashboards(): void
    {
        $cases = [
            ['admin', 'coach.dashboard'],
            ['admin', 'customer.dashboard'],
            ['coach', 'admin.dashboard'],
            ['coach', 'customer.dashboard'],
            ['customer', 'admin.dashboard'],
            ['customer', 'coach.dashboard'],
        ];

        foreach ($cases as [$role, $route]) {
            $user = $this->createUser($role);

            $this->actingAs($user)
                ->get(route($route))
                ->assertForbidden();
        }
    }

    public function test_guest_is_redirected_to_login(): void
    {
        foreach ([
            'admin.dashboard',
            'coach.dashboard',
            'customer.dashboard',
        ] as $route) {
            auth()->logout();

            $this->get(route($route))
                ->assertRedirect(route('login'));
        }
    }

    public function test_inactive_account_cannot_access_dashboard(): void
    {
        $user = $this->createUser('admin', 'inactive');

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('auth');
    }
}
