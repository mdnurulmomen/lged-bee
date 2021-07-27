<?php

namespace App\Providers;

use App\Models\OpActivityComment;
use App\Models\OpYearlyAuditCalendarActivity;
use App\Models\OpYearlyAuditCalendarResponsible;
use App\Observers\OpActivityCommentObserver;
use App\Observers\OpYearlyAuditCalendarActivityObserver;
use App\Observers\OpYearlyAuditCalendarResponsibleObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        OpYearlyAuditCalendarActivity::observe(OpYearlyAuditCalendarActivityObserver::class);
        OpYearlyAuditCalendarResponsible::observe(OpYearlyAuditCalendarResponsibleObserver::class);
        OpActivityComment::observe(OpActivityCommentObserver::class);
    }
}
