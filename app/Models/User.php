<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function state()
    {
        return $this->hasOne(State::class);
    }

    /**
     * Send the password-reset link. DigitalOcean blocks outbound SMTP, so when a
     * RESEND_KEY is set we deliver via the Resend HTTPS API (port 443) instead of
     * the mail transport. Falls back to the default mail notification otherwise.
     */
    public function sendPasswordResetNotification($token): void
    {
        $key = env('RESEND_KEY');
        $url = rtrim(config('app.url'), '/').'/?reset='.$token.'&email='.urlencode($this->email);

        if ($key) {
            try {
                $res = Http::withToken($key)->asJson()->post('https://api.resend.com/emails', [
                    'from' => env('MAIL_FROM_ADDRESS', 'onboarding@resend.dev'),
                    'to' => [$this->email],
                    'subject' => 'Reset password — Multi Tracker',
                    'html' => '<p>Hai,</p>'
                        .'<p>Permintaan untuk set semula password Multi Tracker telah dibuat.</p>'
                        .'<p><a href="'.e($url).'" style="display:inline-block;padding:10px 18px;background:#17915A;color:#fff;border-radius:8px;text-decoration:none">Set password baru</a></p>'
                        .'<p>Atau salin pautan ini: <br>'.e($url).'</p>'
                        .'<p style="color:#888;font-size:13px">Pautan sah selama 60 minit. Abaikan emel ini jika anda tidak memintanya.</p>',
                ]);

                if ($res->successful()) {
                    return;
                }
            } catch (\Throwable $e) {
                // fall through to the default mail notification
            }
        }

        parent::sendPasswordResetNotification($token);
    }
}
