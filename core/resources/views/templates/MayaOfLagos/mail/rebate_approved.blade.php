@component('mail::message')
# Rebate Approved!

Hello {{ $user->firstname }},

Great news! Your rebate has been approved and processed.

## Rebate Details
- **Program:** {{ $rebate->program->name }}
- **Purchase Amount:** {{ showAmount($rebate->purchase_amount) }} {{ $general->cur_text }}
- **Rebate Amount:** {{ showAmount($rebate->rebate_amount) }} {{ $general->cur_text }}
- **Tier Multiplier:** {{ $rebate->tier_multiplier }}x
- **Status:** Approved ✅

Your rebate amount has been credited to your account and is now available for withdrawal.

@component('mail::button', ['url' => route('user.rebate.history')])
View Rebate History
@endcomponent

## Current Tier Status
You are currently a **{{ $tierInfo['tier'] }}** member with a **{{ $tierInfo['multiplier'] }}x** multiplier on all rebates.

@if($tierProgress && $tierProgress['next_tier'])
You need {{ showAmount($tierProgress['amount_to_next']) }} {{ $general->cur_text }} more to reach **{{ $tierProgress['next_tier'] }}** tier!
@endif

Keep uploading receipts to earn more rewards and advance through our tier system.

@component('mail::button', ['url' => route('user.rebate.upload')])
Upload More Receipts
@endcomponent

Thank you for being a valued member of our rebate program!

Best regards,<br>
{{ config('app.name') }} Team

---
<small>This email was sent to {{ $user->email }}. If you have any questions, please contact our support team.</small>
@endcomponent