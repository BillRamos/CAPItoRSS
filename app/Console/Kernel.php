<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
  /**
   * The Artisan commands provided by your application.
   *
   * @var array
   */
  protected $commands = [
    'App\Console\Commands\GenerateStaticFeeds',
  ];

  /**
   * Define the application's command schedule.
   *
   * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
   * @return void
   */
  protected function schedule(Schedule $schedule)
  {
    // If the app is in production and the static file generation service is turned on, run the GenerateStaticFeeds command at the interval specified
    if(config('app.env') === 'production' && config('app.static')){
      // Change the method at the end of the line below to set the frequency at which the feeds should be checked/updated
      // Docs: https://laravel.com/docs/5.8/scheduling#schedule-frequency-options
      $schedule->command('command:generate_static_feeds')->dailyAt('03:00');
    }
  }
}
