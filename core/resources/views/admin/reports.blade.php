@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-md-12">
            <div class="card viser--table">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Message')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $report)
                                    <tr>
                                        <td>{{ @$report->req_type }}</td>
                                        <td class="text-center white-space-wrap">{{ @$report->message }}</td>
                                        <td><span class="badge badge--{{ @$report->status_class }}">{{ @$report->status_text }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bugModal" tabindex="-1" role="dialog" aria-labelledby="bugModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bugModalLabel">@lang('Report & Request')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action class="disableSubmission" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Type')</label>
                            <select class="form-control select2" data-minimum-results-for-search="-1" id="reportType" required>
                                <option value="bug">@lang('Report Bug')</option>
                                <option value="feature">@lang('Feature Request')</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Message')</label>
                            <textarea class="form-control" id="reportMessage" rows="5" placeholder="@lang('Describe your bug report or feature request...')" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>@lang('System Information')</label>
                            <div class="form-control-plaintext small text-muted">
                                <div>App: {{ systemDetails()['name'] }} v{{ systemDetails()['version'] }}</div>
                                <div>URL: {{ request()->getSchemeAndHttpHost() }}</div>
                                <div>Admin: {{ auth()->guard('admin')->user()->name ?? 'Unknown' }}</div>
                                <div>Date: {{ now()->format('Y-m-d H:i:s T') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--primary w-100 h-45" onclick="sendWhatsAppReport()">
                            <i class="fab fa-whatsapp"></i> @lang('Send via WhatsApp')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
function sendWhatsAppReport() {
    const type = document.getElementById('reportType').value;
    const message = document.getElementById('reportMessage').value.trim();
    
    if (!message) {
        notify('error', 'Please enter a message');
        return;
    }
    
    // System details
    const systemInfo = {
        app: '{{ systemDetails()["name"] }}',
        version: '{{ systemDetails()["version"] }}',
        url: '{{ request()->getSchemeAndHttpHost() }}',
        admin: '{{ auth()->guard("admin")->user()->name ?? "Unknown" }}',
        date: '{{ now()->format("Y-m-d H:i:s T") }}'
    };
    
    // Format WhatsApp message
    const reportTypeText = type === 'bug' ? '🐛 Bug Report' : '💡 Feature Request';
    let whatsappMessage = `${reportTypeText}\n\n`;
    whatsappMessage += `📝 Message:\n${message}\n\n`;
    whatsappMessage += `🖥️ System Information:\n`;
    whatsappMessage += `• App: ${systemInfo.app} v${systemInfo.version}\n`;
    whatsappMessage += `• URL: ${systemInfo.url}\n`;
    whatsappMessage += `• Admin: ${systemInfo.admin}\n`;
    whatsappMessage += `• Date: ${systemInfo.date}`;
    
    // WhatsApp support number (change this to your actual support number)
    const whatsappNumber = '+2348123326360'; // Change this to your support WhatsApp number
    
    // Create WhatsApp URL
    const whatsappUrl = `https://wa.me/${whatsappNumber.replace('+', '')}?text=${encodeURIComponent(whatsappMessage)}`;
    
    // Open WhatsApp
    window.open(whatsappUrl, '_blank');
    
    // Close modal
    $('#bugModal').modal('hide');
    
    // Clear form
    document.getElementById('reportType').selectedIndex = 0;
    document.getElementById('reportMessage').value = '';
    
    notify('success', 'WhatsApp opened successfully. Please send the message to our support team.');
}
</script>
@endpush

@push('breadcrumb-plugins')
    <button class="btn btn-sm btn-outline--warning" data-bs-toggle="modal" data-bs-target="#bugModal"><i class="las la-bug"></i> @lang('Report a bug')</button>
    <a href="https://wa.me/2348123326360" target="_blank" class="btn btn-sm btn-outline--success"><i class="las la-headset"></i> @lang('Request for Support')</a>
@endpush
