@component('mail::message')
# Rebate Rejected - User Notification Sent

Hello Admin,

A rebate has been rejected and the user has been notified with the reason for decline.

## Rejected Rebate Details
- **User:** {{ $user->firstname }} {{ $user->lastname }} ({{ $user->username }})
- **Email:** {{ $user->email }}
- **User ID:** #{{ $user->id }}
- **Program:** {{ $rebate->program->name }}
- **Purchase Amount:** {{ showAmount($rebate->purchase_amount) }} {{ $general->cur_text }}
- **Requested Rebate:** {{ showAmount($rebate->rebate_amount) }} {{ $general->cur_text }}
- **Submission ID:** #{{ $rebate->id }}
- **Rejected:** {{ $rebate->updated_at->format('M d, Y \a\t g:i A') }}

## Rejection Details
- **Reason:** {{ $rebate->review_notes ?? 'Standard rejection - documentation did not meet program requirements' }}
@if($rebate->reviewed_by)
- **Reviewed By:** Admin #{{ $rebate->reviewed_by }}
@endif

## User Information
- **Current Tier:** {{ $user->userRebate->tier ?? 'Bronze' }}
- **Previous Submissions:** {{ $user->rebateTransactions()->count() }}
- **Successful Rebates:** {{ $user->rebateTransactions()->where('status', 'approved')->count() }}
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

@component('mail::button', ['url' => route('admin.rebate.transactions.show', $rebate->id)])
View Rejection Details
@endcomponent

## Customer Service Impact
- **User Experience:** ⚠️ Negative - provide excellent support
- **Follow-up Needed:** User may contact support for clarification
- **Resubmission Likely:** User may submit corrected documentation

## Recommended Actions
1. **Monitor for support tickets** from this user
2. **Be prepared to provide detailed guidance** on program requirements
3. **Consider proactive outreach** if this is a valuable customer

The user has been notified with clear instructions on how to improve future submissions.

Best regards,<br>
{{ config('app.name') }} Admin System

---
<small>This is an automated admin notification for rejected rebate #{{ $rebate->id }}. The user has received a detailed explanation and guidance for future submissions.</small>
@endcomponent