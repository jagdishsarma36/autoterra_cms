<?php

namespace App\Http\Controllers;

use App\Models\FormCms;
use App\Models\FormField;
use App\Models\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FormController extends Controller
{
    public function show(FormCms $form)
    {
        if (!$form->is_active) {
            abort(404);
        }
        $fields = $form->fields()->orderBy('sort_order')->get();
        return view('pages.form', compact('form', 'fields'));
    }

    public function submit(Request $request, FormCms $form)
    {
        if (!$form->is_active) {
            abort(404);
        }

        $fields = $form->fields()->orderBy('sort_order')->get();
        $rules = [];
        foreach ($fields as $field) {
            $rule = [];
            if ($field->is_required) $rule[] = 'required';
            if ($field->type === 'email') $rule[] = 'email';
            $rules['field_' . $field->name] = $rule;
        }
        $rules['name'] = 'nullable|string|max:255';
        $rules['email'] = 'nullable|email|max:255';

        $validated = $request->validate($rules);

        $data = [];
        foreach ($fields as $field) {
            $value = $validated['field_' . $field->name] ?? null;
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $data[$field->name] = $value;
        }

        $submission = FormSubmission::create([
            'form_id' => $form->id,
            'name' => $validated['name'] ?? null,
            'email' => $validated['email'] ?? null,
            'data' => $data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Send notification email if configured
        if ($form->notification_email) {
            try {
                // Simple mail notification
                Mail::raw("New form submission on {$form->name}:\n\n" .
                    "Name: " . ($submission->name ?? 'N/A') . "\n" .
                    "Email: " . ($submission->email ?? 'N/A') . "\n\n" .
                    "Data:\n" . collect($data)->map(fn ($v, $k) => "{$k}: {$v}")->implode("\n"),
                    function ($mail) use ($form, $submission) {
                        $mail->to($form->notification_email)
                            ->subject("New submission: {$form->name}");
                    }
                );
            } catch (\Exception $e) {
                // Silently fail
            }
        }

        if ($form->redirect_url) {
            return redirect($form->redirect_url)
                ->with('form_success', $form->success_message);
        }

        return back()->with('form_success', $form->success_message);
    }
}
