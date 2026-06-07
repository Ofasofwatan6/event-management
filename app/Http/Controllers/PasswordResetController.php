<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetOtp;
use App\Models\User;
use App\Rules\PasswordPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class PasswordResetController extends Controller
{
    public function showForgot(Request $request)
    {
        return view('auth.forgot-password', [
            'context' => $request->query('context', 'user'),
        ]);
    }

    public function sendOtp(Request $request)
    {
        $rules = [
            'email' => ['required', 'email'],
            'context' => ['required', Rule::in(['user', 'organization'])],
        ];

        if ($request->context === 'user') {
            $rules['email'][] = 'regex:/@gmail\.com$/i';
        }

        $validated = $request->validate($rules, $this->messages(), $this->attributes());

        $user = User::where('email', $validated['email'])
            ->whereNull('deleted_at')
            ->where('role', $validated['context'] === 'organization' ? 'organization' : 'user')
            ->first();

        if (! $user) {
            return back()->withInput()->withErrors([
                'email' => 'Email tidak terdaftar untuk akun ini.',
            ]);
        }

        $otp = (string) random_int(100000, 999999);

        PasswordResetOtp::where('email', $validated['email'])
            ->where('context', $validated['context'])
            ->delete();

        PasswordResetOtp::create([
            'email' => $validated['email'],
            'otp' => $otp,
            'context' => $validated['context'],
            'expires_at' => now()->addMinutes(15),
        ]);

        Mail::raw(
            "Kode OTP VolunteerHub Anda: {$otp}\n\nBerlaku 15 menit. Jangan bagikan kode ini kepada siapa pun.",
            fn ($message) => $message
                ->to($validated['email'])
                ->subject('Kode Pemulihan Password VolunteerHub')
        );

        session([
            'password_reset_email' => $validated['email'],
            'password_reset_context' => $validated['context'],
        ]);

        return redirect()->route('password.reset', [
            'context' => $validated['context'],
        ])->with('success', 'Kode OTP telah dikirim ke email Anda.');
    }

    public function showReset(Request $request)
    {
        if (! session('password_reset_email')) {
            return redirect()->route('forgot-password', [
                'context' => $request->query('context', 'user'),
            ])->withErrors(['email' => 'Silakan minta kode OTP terlebih dahulu.']);
        }

        return view('auth.reset-password', [
            'email' => session('password_reset_email'),
            'context' => session('password_reset_context', 'user'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        // Validate OTP first so a wrong/expired OTP never surfaces password errors.
        $otpValidated = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'context' => ['required', Rule::in(['user', 'organization'])],
        ], $this->messages(), $this->attributes());

        $record = PasswordResetOtp::where('email', $otpValidated['email'])
            ->where('context', $otpValidated['context'])
            ->where('otp', $otpValidated['otp'])
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $record) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['otp' => 'Kode OTP salah']);
        }

        $passwordValidated = $request->validate([
            'password' => ['required', 'confirmed', new PasswordPolicy],
        ], $this->messages(), $this->attributes());

        $validated = array_merge($otpValidated, $passwordValidated);

        $user = User::where('email', $validated['email'])
            ->whereNull('deleted_at')
            ->where('role', $validated['context'] === 'organization' ? 'organization' : 'user')
            ->firstOrFail();

        $user->update(['password' => $validated['password']]);

        PasswordResetOtp::where('email', $validated['email'])
            ->where('context', $validated['context'])
            ->delete();

        session()->forget(['password_reset_email', 'password_reset_context']);

        $loginRoute = $validated['context'] === 'organization'
            ? route('admin.login')
            : route('login');

        return redirect($loginRoute)->with('success', 'Password berhasil diperbarui. Silakan masuk.');
    }

    private function messages(): array
    {
        return [
            'required' => 'Required.',
            'email.required' => 'Required.',
            'email.email' => 'Format email tidak valid.',
            'otp.required' => 'Required.',
            'otp.digits' => 'OTP harus 6 digit angka.',
            'password.required' => 'Required.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }

    private function attributes(): array
    {
        return [
            'email' => 'email',
            'otp' => 'kode OTP',
            'password' => 'password',
            'password_confirmation' => 'konfirmasi password',
        ];
    }
}
