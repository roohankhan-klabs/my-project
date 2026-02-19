<?php

namespace App\Nova\Actions;

use App\Mail\TestEmail;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class EmailAccountProfile extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * The displayable name of the action.
     *
     * @var string
     */
    public $name = 'Send test email';

    /**
     * Perform the action on the given models.
     *
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models): ActionResponse
    {
        $subject = (string) $fields->subject;
        $sent = 0;

        foreach ($models as $model) {
            if (! $model instanceof User) {
                continue;
            }
            try {
                Mail::to($model->email)->send(new TestEmail($model, $subject));
                $this->markAsFinished($model);
                $sent++;
            } catch (Exception $e) {
                $this->markAsFailed($model, $e);
            }
        }

        $message = $sent === 1
            ? 'Test email sent to 1 user.'
            : "Test emails sent to {$sent} users.";

        return ActionResponse::message($message);
    }

    /**
     * Get the fields available on the action.
     *
     * @return array<int, \Laravel\Nova\Fields\Field>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            Text::make('Subject')
                ->rules('required', 'string', 'max:255')
                ->default('Test email from Nova'),
        ];
    }
}
