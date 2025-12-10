<?php

namespace App\Mail;

use App\Models\Professeur;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class RapportProfesseurMail extends Mailable
{
    use Queueable, SerializesModels;

    public Professeur $professeur;
    public float $noteFinale;
    public string $appreciation;
    public string $semestre;
    public string $pdfPath;

    /**
     * Create a new message instance.
     */
    public function __construct(
        Professeur $professeur,
        float $noteFinale,
        string $appreciation,
        string $semestre,
        string $pdfPath
    ) {
        $this->professeur = $professeur;
        $this->noteFinale = $noteFinale;
        $this->appreciation = $appreciation;
        $this->semestre = $semestre;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Rapport d\'Évaluation des Enseignements - Semestre ' . $this->semestre,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.rapport-professeur',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as('rapport_evaluation_' . str_replace(' ', '_', $this->professeur->full_name) . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
