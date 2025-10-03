@component('mail::message')
# Rebate Submission Received

Hello {{ $user->firstname }},

Thank you for your rebate submission! We have received your request and it is now being processed.

## Submission Details
- **Program:** {{ $rebate->program->name }}
- **Purchase Amount:** {{ showAmount($rebate->purchase_amount) }} {{ $general->cur_text }}
- **Estimated Rebate:** {{ showAmount($rebate->rebate_amount) }} {{ $general->cur_text }}
- **Tier Multiplier:** {{ $rebate->tier_multiplier }}x
- **Submission ID:** #{{ $rebate->id }}
- **Submitted:** {{ $rebate->created_at->format('M d, Y \a\t g:i A') }}

## What Happens Next?
1. **Review Process:** Our team will review your submission within 2-3 business days
2. **Verification:** We'll verify your purchase details and receipt images
3. **Approval:** Once approved, your rebate will be credited to your account
4. **Notification:** You'll receive an email confirmation when the process is complete

## Processing Timeline
- **Standard Processing:** 2-3 business days
@if($tierInfo['tier'] == 'Gold')
- **Gold Member:** Expedited processing (1-2 business days)
@elseif($tierInfo['tier'] == 'Platinum')
- **Platinum Member:** Priority processing (same day)
@endif

@component('mail::button', ['url' => route('user.rebate.history')])
Track Your Submission
@endcomponent

## Your Current Status
- **Tier:** {{ $tierInfo['tier'] }} ({{ $tierInfo['multiplier'] }}x multiplier)
- **Total Earned:** {{ showAmount($tierInfo['total_earned']) }} {{ $general->cur_text }}
- **Pending Rebates:** {{ showAmount($pendingAmount) }} {{ $general->cur_text }}

## Need to Submit More?
Keep earning rewards by submitting more receipts from our participating programs.

@component('mail::button', ['url' => route('user.rebate.programs')])
Browse Programs
@endcomponent

## Questions?
If you have any questions about your submission or our rebate process, please don't hesitate to contact our support team.

@component('mail::button', ['url' => route('ticket.open')])
Contact Support
@endcomponent

Thank you for choosing our rebate program!

Best regards,<br>
{{ config('app.name') }} Team

---
<small>Keep this email for your records. Your submission ID is #{{ $rebate->id }}. This email was sent to {{ $user->email }}.</small>
@endcomponent