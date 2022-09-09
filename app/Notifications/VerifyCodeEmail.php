<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * 邮件验证
 *
 * @author
 *
 */
class VerifyCodeEmail extends Notification implements ShouldQueue
{
    use Queueable;

    protected $code;

    protected $minutes;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($code, $minutes = 30)
    {
        $this->code = $code;
        $this->minutes = $minutes;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [
            'mail'
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->code.' 是您的验证码')
            ->line('您好，您的验证码是:')
            ->line($this->code)
            ->line('本验证码在'.$this->minutes.'分钟内有效.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [];
    }
}
