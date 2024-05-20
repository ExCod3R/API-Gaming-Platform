<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\ContactFormMail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Created by: Charith Gamage
     * Created at: 2024-01-29
     *
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'subject' => 'required|string',
                'message' => 'required|string',
            ]);

            // Send email
            Mail::to('charith.zincat@gmail.com')->send(new ContactFormMail($request->all()));

            return response()->json(['message' => 'Email sent successfully']);
        } catch (\Exception $ex) {
            Log::error($ex);
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
