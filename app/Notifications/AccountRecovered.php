<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AccountRecovered extends Notification
{
    public function __construct(public string $token) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('İzEdebiyat hesabınıza yeniden erişim')
            ->greeting('Merhaba ' . $notifiable->name . ',')
            ->line('Hesap kurtarma talebiniz onaylandı. Giriş e-posta adresiniz: ' . $notifiable->email)
            ->line('Profiliniz: ' . route('user', $notifiable->slug))
            ->action('Yeni Şifre Belirle', route('password.reset', ['token' => $this->token, 'email' => $notifiable->email]))
            ->line('Bu bağlantı ' . config('auth.passwords.users.expire') . ' dakika geçerlidir.')
            ->line('Şifrenizi belirledikten sonra ' . route('login') . ' adresinde bu e-posta ve yeni şifrenizle giriş yapabilirsiniz.')
            ->line('Bağlantının süresi dolarsa yeni bağlantı isteyin: ' . route('password.request'));
    }
}
