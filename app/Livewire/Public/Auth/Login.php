<?php

declare(strict_types=1);

namespace App\Livewire\Public\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Masuk ke Akun SAPA SOSIAL — Dinsos Kab. Blitar')]
class Login extends Component
{
    public string $identifier = '';

    public string $password = '';

    public bool $remember = false;

    public string $errorMessage = '';

    public function login(): void
    {
        $this->errorMessage = '';

        $this->validate([
            'identifier' => 'required|string',
            'password' => 'required|string|min:4',
        ], [
            'identifier.required' => 'Masukkan Email, NIK, atau Nomor WhatsApp terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $cleanIdentifier = trim($this->identifier);

        // Find user by email, phone, or nik
        $user = User::where('email', $cleanIdentifier)
            ->orWhere('phone', $cleanIdentifier)
            ->orWhere('nik', $cleanIdentifier)
            ->first();

        if (! $user || ! Hash::check($this->password, $user->password)) {
            $this->errorMessage = 'Identitas atau kata sandi yang Anda masukkan tidak sesuai.';

            return;
        }

        if (! $user->is_active) {
            $this->errorMessage = 'Akun Anda sedang dinonaktifkan oleh administrator. Silakan hubungi dinas sosial.';

            return;
        }

        Auth::login($user, $this->remember);
        session()->regenerate();

        $this->redirectIntended(route('citizen.dashboard'));
    }

    public function render()
    {
        return view('livewire.public.auth.login');
    }
}
