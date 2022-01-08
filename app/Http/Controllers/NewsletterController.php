<?php

namespace App\Http\Controllers;

use App\Services\Newsletter;
use Illuminate\Validation\ValidationException;

class NewsletterController extends Controller
{
    /**
     * using this method when we want to make this controller an one single action controller
     */
    public function __invoke(Newsletter $newsletter)
    {
        request()->validate(['subscribeEmail' => 'required|email']);

        try {
            $newsletter->subscribe(request('subscribeEmail'));
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'subscribeEmail' => 'This email could not be added to our newsletter list.'
            ]);
        }

        return redirect('/')->with('success', 'You are now signed up for our newsletter!');
    }
}
