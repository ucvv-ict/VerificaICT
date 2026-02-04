<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Entity;
use App\Models\EntitySecurityTask;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SecurityStatusReminder extends Notification
{
    use Queueable;

    /**
     * @param  array<int, EntitySecurityTask>  $redTasks
     * @param  array<int, EntitySecurityTask>  $orangeTasks
     */
    public function __construct(
        private readonly Entity $entity,
        private readonly array $redTasks,
        private readonly array $orangeTasks,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage())
            ->subject("Promemoria sicurezza - {$this->entity->nome}")
            ->greeting('Promemoria stato attività di sicurezza')
            ->line("Ente: {$this->entity->nome}")
            ->line('Attività ROSSE: ' . count($this->redTasks))
            ->line('Attività ARANCIONI: ' . count($this->orangeTasks));

        if ($this->redTasks !== []) {
            $mail->line('Elenco ROSSE:');

            foreach ($this->redTasks as $task) {
                $mail->line('- ' . $task->securityTask->titolo);
            }
        }

        if ($this->orangeTasks !== []) {
            $mail->line('Elenco ARANCIONI:');

            foreach ($this->orangeTasks as $task) {
                $mail->line('- ' . $task->securityTask->titolo);
            }
        }

        return $mail->line('Nota: questa notification è preparata ma non ancora collegata all\'invio automatico.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'entity_id' => $this->entity->id,
            'red_count' => count($this->redTasks),
            'orange_count' => count($this->orangeTasks),
        ];
    }
}
