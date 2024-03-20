<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewsNotification extends Notification
{
    use Queueable;

    protected $news;

    public function __construct($news)
    {
        $this->news = $news;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
        ->line('Yeni haberler var!')
        ->line('Bugün yayınlanan haberler:')
        ->markdown('mails.news', ['news' => $this->news])
        ->action('Tüm Haberleri Görüntüle', "http::localhost:3000");
    }
}
