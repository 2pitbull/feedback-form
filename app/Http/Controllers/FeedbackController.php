<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Mail\FeedbackMessage;
use App\Services\CaptchaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class FeedbackController extends Controller
{
    public function index(CaptchaService $captchaService): View
    {
        return view('feedback.index', [
            'captchaQuestion' => $captchaService->generate(),
        ]);
    }

    public function store(
        StoreFeedbackRequest $request,
        CaptchaService $captchaService,
    ): JsonResponse {
        $validated = $request->validated();

        $subjects = [
            'consultation' => 'Консультация',
            'technical' => 'Технический вопрос',
            'cooperation' => 'Сотрудничество',
            'other' => 'Другое',
        ];

        $mailData = [
            'subject' => $subjects[$validated['subject']],
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'message' => $validated['message'],
        ];

        try {
            Mail::to($mailData['email'])
                ->send(new FeedbackMessage($mailData));
        } catch (\Throwable $exception) {
            Log::error('Failed to send feedback email.', [
                'exception' => $exception::class,
            ]);

            return response()->json([
                'message' => 'Не удалось отправить письмо. Попробуйте позже.',
            ], 500);
        }

        return response()->json([
            'message' => 'Обращение успешно отправлено.',
            'captchaQuestion' => $captchaService->generate(),
        ]);
    }
}
