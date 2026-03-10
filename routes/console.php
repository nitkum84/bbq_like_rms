<?php
use Illuminate\Support\Facades\Schedule;

Schedule::command('menu:daily-reset')->dailyAt('00:00');
Schedule::command('bookings:send-reminders')->hourly();
