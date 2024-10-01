<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Number;
use Illuminate\Queue\SerializesModels;
use App\Models\Stats;
use App\Models\Organ;

class StatsCollected extends Mailable
{
    use Queueable, SerializesModels;
    
    public ?Organ $organLikesMaxOrgan;

    /**
     * Create a new message instance.
     */
    public function __construct(public Stats $stats)
    {
        $this->organLikesMaxOrgan = Organ::find($stats->organ_likes_max_organ_id);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Stats Collected',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.stats-collected',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
