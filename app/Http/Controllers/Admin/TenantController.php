<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $tenants = Tenant::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%");
        })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.tenants.index', compact('tenants', 'search'));
    }

    public function create()
    {
        $groups = config('modules');

        return view('admin.tenants.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:tenants,name',
            'groups' => 'required|array|min:1',
            'groups.*' => 'required|string|in:core,factory',
        ]);

        $selectedGroups = array_values(array_unique($request->input('groups', [])));

        if (!in_array('core', $selectedGroups, true)) {
            $selectedGroups[] = 'core';
        }

        [$tenant, $adminEmail, $plainPassword] = DB::transaction(function () use ($request, $selectedGroups) {
            $tenant = Tenant::create([
                'name' => $request->name,
                'enabled_modules' => $selectedGroups,
            ]);

            $adminEmail = $this->buildUniqueTenantAdminEmail($tenant->name);
            $plainPassword = Str::password(8, true, true, false, false);

            $user = User::create([
                'name' => $tenant->name . ' Admin',
                'email' => $adminEmail,
                'password' => Hash::make($plainPassword),
            ]);

            $user->assignRole('erp');

            return [$tenant, $adminEmail, $plainPassword];
        });

        return redirect()
            ->route('admin.tenants.index')
            ->with('success', 'Unidade criada com sucesso. O usuário administrador foi criado automaticamente.')
            ->with('generated_admin_email', $adminEmail)
            ->with('generated_admin_password', $plainPassword);
    }

    private function buildUniqueTenantAdminEmail(string $tenantName): string
    {
        $base = Str::slug($tenantName, '.');
        $base = trim($base, '.');

        if ($base === '') {
            $base = 'tenant';
        }

        $localPart = $base . '.admin';
        $email = $localPart . '@aloha.com';
        $suffix = 1;

        while (User::where('email', $email)->exists()) {
            $email = $localPart . $suffix . '@aloha.com';
            $suffix++;
        }

        return $email;
    }
}
