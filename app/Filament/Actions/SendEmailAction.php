<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestEmail;

class SendEmailAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'sendEmail';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Send Email')
            ->icon('heroicon-o-paper-airplane')
            ->color('info')
            ->form([
                Textarea::make('message')
                    ->required()
                    ->label('Message')
                    ->placeholder('Enter your message here...')
                    ->rows(5),
            ])
            ->action(function (array $data, $record) {
                // Send email to user
                Mail::to($record->email)->send(new TestEmail($data['message']));

                $this->success()->title('Email Sent')
                    ->body("Email has been sent to {$record->email}");
            })
            ->requiresConfirmation()
            ->modalHeading('Send Email to User')
            ->modalSubmitActionLabel('Send');
    }
}
