<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Auth;

use App\Domain\Admin\Models\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * @var list<string>
     */
    private const LOGIN_EMAILS = [
        'administrador@laboratoriocoahuila.com',
        'direcciongeneral@laboratoriocoahuila.com',
    ];

    public function create(): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        $admins = Admin::query()
            ->whereIn('email', self::LOGIN_EMAILS)
            ->orderBy('name')
            ->get(['email', 'name']);

        return view('admin.auth.login', compact('admins'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        $credentials = $request->validate([
            'email' => ['required', 'email', Rule::in(self::LOGIN_EMAILS)],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Credenciales incorrectas.',
            ])->onlyInput('email');
        }

        // Evita mezclar sesión de trabajador con la del admin
        $request->session()->forget('employee_id');
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
