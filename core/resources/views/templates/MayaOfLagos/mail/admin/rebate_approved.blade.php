@component('mail::message')
# Rebate Approved - User Notification Sent

Hello Admin,

A rebate has been successfully approved and the user has been notified.

## Approved Rebate Details
- **User:** {{ $user->firstname }} {{ $user->lastname }} ({{ $user->username }})
- **Email:** {{ $user->email }}
- **User ID:** #{{ $user->id }}
- **Program:** {{ $rebate->program->name }}
- **Purchase Amount:** {{ showAmount($rebate->purchase_amount) }} {{ $general->cur_text }}
- **Rebate Amount:** {{ showAmount($rebate->rebate_amount) }} {{ $general->cur_text }}
- **Tier Multiplier:** {{ $rebate->tier_multiplier }}x
- **Final Amount:** {{ showAmount($rebate->final_amount) }} {{ $general->cur_text }}
- **Submission ID:** #{{ $rebate->id }}
- **Approved:** {{ $rebate->approved_at->format('M d, Y \a\t g:i A') }}

## User Status Update
- **Current Tier:** {{ $tierInfo['tier'] }} ({{ $tierInfo['multiplier'] }}x multiplier)
- **Total Lifetime Earned:** {{ showAmount($tierInfo['total_earned']) }} {{ $general->cur_text }}

@if($tierProgress && $tierProgress['next_tier'])
## Tier Progress
- **Next Tier:** {{ $tierProgress['next_tier'] }}
- **Amount Needed:** {{ showAmount($tierProgress['amount_to_next']) }} {{ $general->cur_text }}
- **Progress:** {{ number_format($tierProgress['progress_percentage'], 1) }}%
@endif

## Review Information
@if($rebate->approved_by)
- **Approved By:** Admin #{{ $rebate->approved_by }}
@endif
@if($rebate->review_notes)
- **Review Notes:** {{ $rebate->review_notes }}
@endif

@component('mail::button', ['url' => route('admin.rebate.transactions.show', $rebate->id)])
View Transaction Details
@endcomponent

## Impact Summary
- **User Balance Updated:** +{{ showAmount($rebate->final_amount) }} {{ $general->cur_text }}
- **Tier Multiplier Applied:** {{ $rebate->tier_multiplier }}x
- **Customer Satisfaction:** ✅ Positive

The user has been automatically notified of this approval and can now withdraw their rebate funds.

Best regards,<br>
{{ config('app.name') }} Admin System

---
<small>This is an automated admin notification for approved rebate #{{ $rebate->id }}. The user notification has been sent successfully.</small>
@endcomponent