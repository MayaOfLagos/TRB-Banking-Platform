@component('mail::message')
# Rebate Submission Update

Hello {{ $user->firstname }},

We have reviewed your recent rebate submission and unfortunately it has been declined.

## Rebate Details
- **Program:** {{ $rebate->program->name }}
- **Purchase Amount:** {{ showAmount($rebate->purchase_amount) }} {{ $general->cur_text }}
- **Requested Rebate:** {{ showAmount($rebate->rebate_amount) }} {{ $general->cur_text }}
- **Status:** Declined ❌
- **Submission Date:** {{ $rebate->created_at->format('M d, Y') }}

## Reason for Decline
{{ $rebate->review_notes ?? 'The submitted documentation did not meet our program requirements. Please ensure all images are clear and receipts match the program criteria.' }}

## What You Can Do
1. **Review Program Requirements:** Make sure you understand the specific requirements for this rebate program
2. **Check Image Quality:** Ensure your product and receipt images are clear and readable
3. **Verify Purchase Details:** Confirm that your purchase matches the program criteria
4. **Resubmit if Applicable:** If you believe this was an error, you may submit a new request with corrected documentation

@component('mail::button', ['url' => route('user.rebate.programs')])
Browse Programs
@endcomponent

@component('mail::button', ['url' => route('user.rebate.upload')])
Submit New Rebate
@endcomponent

## Need Help?
If you have questions about this decision or need assistance with future submissions, please don't hesitate to contact our support team.

@component('mail::button', ['url' => route('ticket.open')])
Contact Support
@endcomponent

We appreciate your understanding and look forward to processing your future rebate submissions successfully.

Best regards,<br>
{{ config('app.name') }} Team

---
<small>This email was sent to {{ $user->email }}. If you believe this decision was made in error, please contact our support team with your rebate ID: #{{ $rebate->id }}</small>
@endcomponent