<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CampaignMailController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

//email.opened
Route::get('/email/opened/{campaignMail}', function (\App\Models\CampaignMail $campaignMail){
    $campaignMail->update(['opened_at' => now()]);
    // returns a 1x1 pixel gif
    $file = public_path('images/1x1.png');
    $type = mime_content_type($file);
    header('Content-Type:'.$type);
    header('Content-Length: ' . filesize($file));
    readfile($file);    
    return response()->file($file);
})->name('email.opened');

Route::get('/email/view/{template}', function ($template){
    return view('emails.'.$template);
})->name('email.view');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/unsubscribe/{campaignMail}', [App\Http\Controllers\UnsubscribeController::class, 'unsubscribe'])->name('unsubscribe');
Route::get('/bounce', [App\Http\Controllers\UnsubscribeController::class, 'bounce'])->name('bounce.get');
Route::post('/bounce', [App\Http\Controllers\UnsubscribeController::class, 'bounce'])->name('bounce.post');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('contacts')->group(function(){
        Route::get('/', [ContactController::class, 'index'])->name('contacts.index');
        Route::get('/create', [ContactController::class, 'create'])->name('contacts.create');
        Route::post('/', [ContactController::class, 'store'])->name('contacts.store');
        Route::get('/{contact}', [ContactController::class, 'show'])->name('contacts.show');
        Route::get('/{contact}/edit', [ContactController::class, 'edit'])->name('contacts.edit');
        Route::put('/{contact}', [ContactController::class, 'update'])->name('contacts.update');
        Route::delete('/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');
    });

    Route::prefix('campaigns')->group(function(){
        Route::get('/', [CampaignController::class, 'index'])->name('campaigns.index');
        Route::get('/create', [CampaignController::class, 'create'])->name('campaigns.create');
        Route::post('/', [CampaignController::class, 'store'])->name('campaigns.store');
        Route::get('/{campaign}', [CampaignController::class, 'show'])->name('campaigns.show');
        Route::get('/{campaign}/edit', [CampaignController::class, 'edit'])->name('campaigns.edit');
        Route::put('/{campaign}', [CampaignController::class, 'update'])->name('campaigns.update');
        Route::delete('/{campaign}', [CampaignController::class, 'destroy'])->name('campaigns.destroy');
    });

    Route::prefix('campaign_mails')->group(function(){
        Route::get('/', [CampaignMailController::class, 'index'])->name('campaign_mails.index');
        Route::get('/create', [CampaignMailController::class, 'create'])->name('campaign_mails.create');
        Route::post('/', [CampaignMailController::class, 'store'])->name('campaign_mails.store');
        Route::get('/{campaign_mail}', [CampaignMailController::class, 'show'])->name('campaign_mails.show');
        Route::get('/{campaign_mail}/edit', [CampaignMailController::class, 'edit'])->name('campaign_mails.edit');
        Route::put('/{campaign_mail}', [CampaignMailController::class, 'update'])->name('campaign_mails.update');
        Route::delete('/{campaign_mail}', [CampaignMailController::class, 'destroy'])->name('campaign_mails.destroy');
    });
});

require __DIR__.'/auth.php';
