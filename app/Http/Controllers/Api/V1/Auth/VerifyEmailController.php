<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Verify the user's email address.
     * This is the link clicked from the email — it redirects to the frontend after verifying.
     */
    public function verify(Request $request, int $id, string $hash): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Check hash matches
        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return redirect(env('FRONTEND_URL') . '/login?verified=error');
        }

        // Check signed URL is still valid
        if (! $request->hasValidSignature()) {
            return redirect(env('FRONTEND_URL') . '/login?verified=expired');
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        return redirect(env('FRONTEND_URL') . '/login?verified=1');
    }

    /**
     * Resend the verification email.
     */
    public function resend(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.']);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification email sent. Please check your inbox.']);
    }
}
