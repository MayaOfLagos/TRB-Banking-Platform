@component('mail::message')
# New Rebate Submission - Review Required

Hello Admin,

A new rebate submission has been received and is pending review.

## Submission Details
- **User:** {{ $user->firstname }} {{ $user->lastname }} ({{ $user->username }})
- **Email:** {{ $user->email }}
- **User ID:** #{{ $user->id }}
- **Program:** {{ $rebate->program->name }}
- **Purchase Amount:** {{ showAmount($rebate->purchase_amount) }} {{ $general->cur_text }}
- **Estimated Rebate:** {{ showAmount($rebate->rebate_amount) }} {{ $general->cur_text }}
- **Tier Multiplier:** {{ $rebate->tier_multiplier }}x
- **Submission ID:** #{{ $rebate->id }}
- **Submitted:** {{ $rebate->created_at->format('M d, Y \a\t g:i A') }}

## User Information
- **Tier:** {{ $tierInfo['tier'] }} ({{ $tierInfo['multiplier'] }}x multiplier)
- **Total Earned:** {{ showAmount($tierInfo['total_earned']) }} {{ $general->cur_text }}
- **Current Pending:** {{ showAmount($pendingAmount) }} {{ $general->cur_text }}
- **Member Since:** {{ $user->created_at->format('M d, Y') }}

## Purchase Details
@if($rebate->productUpload)
- **Store:** {{ $rebate->productUpload->store_name }}
- **Purchase Date:** {{ $rebate->productUpload->purchase_date }}
- **Products:** {{ $rebate->productUpload->product_name }}
@if($rebate->productUpload->description)
- **Description:** {{ $rebate->productUpload->description }}
@endif
@endif

@component('mail::button', ['url' => route('admin.rebate.transactions.index')])
Review Submission
@endcomponent

## Processing Timeline
@if($tierInfo['tier'] == 'Gold')
- **Priority Processing:** 1-2 business days (Gold Member)
@elseif($tierInfo['tier'] == 'Platinum')
- **Immediate Processing:** Same day (Platinum Member)
@else
- **Standard Processing:** 2-3 business days
@endif

## System Information
- **IP Address:** {{ $rebate->ip_address ?? 'N/A' }}
- **User Agent:** {{ $rebate->productUpload->user_agent ?? 'N/A' }}

Please review this submission promptly to maintain customer satisfaction.

Best regards,<br>
{{ config('app.name') }} Notification System

---
<small>This is an automated admin notification for rebate submission #{{ $rebate->id }}. Login to the admin panel to review and process this submission.</small>
@endcomponent