<?php

declare(strict_types=1);

namespace App\Livewire\Public\Auth;

use App\Models\District;
use App\Models\User;
use App\Models\Village;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Pendaftaran Akun Warga — SAPA SOSIAL Kab. Blitar')]
class Register extends Component
{
    public string $name = '';

    public string $nik = '';

    public string $phone = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public ?int $district_id = null;

    public ?int $village_id = null;

    public function updatedDistrictId(): void
    {
        $this->village_id = null;
    }

    public function register(): void
    {
        $this->validate([
            'name' => 'required|string|min:3|max:100',
            'nik' => 'required|digits:16|unique:users,nik',
            'phone' => 'required|string|min:9|max:16|unique:users,phone',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'required|exists:villages,id',
        ], [
            'name.required' => 'Nama lengkap wajib diisi sesuai KTP.',
            'nik.required' => 'NIK 16 digit wajib diisi.',
            'nik.digits' => 'NIK harus tepat berjumlah 16 digit angka.',
            'nik.unique' => 'NIK ini sudah terdaftar. Silakan masuk atau gunakan fitur lupa sandi.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.unique' => 'Nomor WhatsApp ini sudah terdaftar.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'district_id.required' => 'Pilih kecamatan domisili.',
            'village_id.required' => 'Pilih desa/kelurahan domisili.',
        ]);

        $user = User::create([
            'name' => $this->name,
            'nik' => $this->nik,
            'phone' => $this->phone,
            'email' => strtolower(trim($this->email)),
            'password' => Hash::make($this->password),
            'district_id' => $this->district_id,
            'village_id' => $this->village_id,
            'is_active' => true,
        ]);

        // Assign 'masyarakat' role
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('masyarakat');
        }

        Auth::login($user);
        session()->regenerate();

        $this->redirect(route('citizen.dashboard'));
    }

    public function render()
    {
        $districts = District::orderBy('name')->get();
        $villages = $this->district_id
            ? Village::where('district_id', $this->district_id)->orderBy('name')->get()
            : collect();

        return view('livewire.public.auth.register', [
            'districts' => $districts,
            'villages' => $villages,
        ]);
    }
}
