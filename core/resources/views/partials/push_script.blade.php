<script src="{{asset('assets/global/js/firebase/firebase-8.3.2.js')}}"></script>

<script>
    "use strict";

    var permission = null;
    var authenticated = '{{ auth()->user() ? true : false }}';
    var pushNotify = @json(gs('pn'));
    var firebaseConfig = @json(gs('firebase_config'));
    var notificationFeedSelector = '[data-notification-feed]';
    var notificationToggleSelector = '[data-notification-toggle]';
    var notificationEmptySelector = '[data-notification-empty]';
    var notificationLabels = {
        push: @json(__('Push')),
        alert: @json(__('Alert')),
    admin: @json(__('Admin')),
        notification: @json(__('Notification')),
        rebate: @json(__('Rebate')),
        rebateApproved: @json(__('Rebate Approved')),
        rebateRejected: @json(__('Rebate Rejected')),
        rebateSubmitted: @json(__('Rebate Submitted')),
        tier: @json(__('Tier')),
        tierAdvancement: @json(__('Tier Advancement')),
        security: @json(__('Security')),
        securityAlert: @json(__('Security Alert')),
        reference: @json(__('Reference')),
        justNow: @json(__('Just now'))
    };

    function resolveNotificationMeta(data) {
        var type = (data.type || '').toLowerCase();
        var status = (data.status || '').toLowerCase();
        var meta = {
            id: data.id || ('push-' + Date.now()),
            title: data.title || notificationLabels.notification,
            message: data.body || '',
            meta: data.meta || '',
            icon: data.icon || 'las la-bell',
            bg: data.bg || 'bg-purple-100 dark:bg-purple-900/30',
            color: data.color || 'text-purple-600 dark:text-purple-400',
            label: data.label || notificationLabels.alert,
            unread: true,
            image: data.image || null
        };

        if (type === 'rebate' || type === 'rebate_status') {
            if (status === 'approved' || status === 'processed') {
                meta.title = data.title || notificationLabels.rebateApproved;
                meta.icon = 'las la-badge-dollar';
                meta.bg = 'bg-emerald-100 dark:bg-emerald-900/30';
                meta.color = 'text-emerald-600 dark:text-emerald-400';
            } else if (status === 'rejected' || status === 'failed') {
                meta.title = data.title || notificationLabels.rebateRejected;
                meta.icon = 'las la-times-circle';
                meta.bg = 'bg-red-100 dark:bg-red-900/30';
                meta.color = 'text-red-600 dark:text-red-400';
            } else {
                meta.title = data.title || notificationLabels.rebateSubmitted;
                meta.icon = 'las la-hourglass-half';
                meta.bg = 'bg-amber-100 dark:bg-amber-900/30';
                meta.color = 'text-amber-600 dark:text-amber-400';
            }
            meta.label = notificationLabels.rebate;
            if (data.program && data.amount) {
                meta.message = data.program + ' • ' + data.amount;
            }
            if (!meta.meta && data.reference) {
                meta.meta = notificationLabels.reference + ' #' + data.reference;
            }
        } else if (type === 'admin_manual_push') {
            meta.title = data.title || notificationLabels.notification;
            meta.icon = 'las la-paper-plane';
            meta.bg = 'bg-sky-100 dark:bg-sky-900/30';
            meta.color = 'text-sky-600 dark:text-sky-400';
            meta.label = notificationLabels.admin;
        } else if (type === 'tier_advancement') {
            meta.title = data.title || notificationLabels.tierAdvancement;
            meta.icon = 'las la-trophy';
            meta.bg = 'bg-blue-100 dark:bg-blue-900/30';
            meta.color = 'text-blue-600 dark:text-blue-400';
            meta.label = notificationLabels.tier;
        } else if (type === 'fraud_alert') {
            meta.title = data.title || notificationLabels.securityAlert;
            meta.icon = 'las la-shield-alt';
            meta.bg = 'bg-orange-100 dark:bg-orange-900/30';
            meta.color = 'text-orange-600 dark:text-orange-400';
            meta.label = notificationLabels.security;
        }

        if (typeof data.unread !== 'undefined') {
            meta.unread = Boolean(data.unread);
        }

        return meta;
    }

    function ensureNotificationIndicator() {
        var toggle = document.querySelector(notificationToggleSelector);
        if (!toggle) {
            return;
        }
        if (!toggle.querySelector('[data-notification-indicator]')) {
            var dot = document.createElement('span');
            dot.className = 'absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full';
            dot.setAttribute('data-notification-indicator', '');
            toggle.appendChild(dot);
        }
    }

    function enforceNotificationLimit(feed, limit = 10) {
        if (!feed) {
            return;
        }
        var items = feed.querySelectorAll('.notification-feed-item');
        if (items.length > limit) {
            Array.from(items).slice(limit).forEach(function(item) {
                item.remove();
            });
        }
    }

    function appendNotificationToFeed(data) {
        var feed = document.querySelector(notificationFeedSelector);
        if (!feed) {
            return;
        }

        var emptyState = feed.querySelector(notificationEmptySelector);
        if (emptyState) {
            emptyState.remove();
        }

        var meta = resolveNotificationMeta(data);

        var wrapper = document.createElement('div');
        wrapper.className = 'relative px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors notification-feed-item';
        wrapper.setAttribute('data-notification-id', meta.id);

        var inner = document.createElement('div');
        inner.className = 'flex items-start space-x-3';

        var iconWrapper = document.createElement('div');
        iconWrapper.className = 'w-8 h-8 rounded-full ' + meta.bg + ' flex items-center justify-center flex-shrink-0';
        var icon = document.createElement('i');
        icon.className = meta.icon + ' ' + meta.color;
        iconWrapper.appendChild(icon);

        var content = document.createElement('div');
        content.className = 'min-w-0 flex-1';

    var header = document.createElement('div');
    header.className = 'flex items-start gap-2';

        var titleEl = document.createElement('p');
        titleEl.className = 'text-sm font-semibold text-gray-900 dark:text-white';
        titleEl.textContent = meta.title;
        header.appendChild(titleEl);

        content.appendChild(header);

        if (meta.message) {
            var message = document.createElement('p');
            message.className = 'text-xs text-gray-500 dark:text-gray-400 mt-1';
            message.textContent = meta.message;
            content.appendChild(message);
        }

        if (meta.meta) {
            var metaLine = document.createElement('p');
            metaLine.className = 'text-[11px] text-gray-400 dark:text-gray-500 mt-1';
            metaLine.textContent = meta.meta;
            content.appendChild(metaLine);
        }

        if (meta.image) {
            var imageWrapper = document.createElement('div');
            imageWrapper.className = 'mt-2';
            var img = document.createElement('img');
            img.src = meta.image;
            img.alt = meta.title;
            img.className = 'w-full max-h-32 object-cover rounded-lg border border-gray-100 dark:border-gray-700';
            imageWrapper.appendChild(img);
            content.appendChild(imageWrapper);
        }

        var time = document.createElement('p');
        time.className = 'text-xs text-gray-400 dark:text-gray-500 mt-1';
        time.textContent = notificationLabels.justNow;
        content.appendChild(time);

        if (meta.unread) {
            var unreadDot = document.createElement('span');
            unreadDot.className = 'absolute top-3 right-3 w-2 h-2 bg-primary-500 rounded-full';
            wrapper.appendChild(unreadDot);
        }

        inner.appendChild(iconWrapper);
        inner.appendChild(content);
        wrapper.appendChild(inner);

        if (feed.firstChild) {
            feed.insertBefore(wrapper, feed.firstChild);
        } else {
            feed.appendChild(wrapper);
        }

        enforceNotificationLimit(feed);
        ensureNotificationIndicator();
    }

    function pushNotifyAction(){
        permission = Notification.permission;

        if(!('Notification' in window)){
            notify('info', 'Push notifications not available in your browser. Try Chromium.')
        }
        else if(permission === 'denied' || permission == 'default'){ //Notice for users dashboard
            $('.notice').append(`
                <div class="row notification-alert">
                    <div class="col-lg-12">
                        <div class="card mb-4">
                            <div class="card-header justify-content-between d-flex flex-wrap notice_notify">
                                <h5 class="alert-heading">@lang('Please Allow / Reset Browser Notification') <i class='las la-bell text--danger'></i></h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0 small">@lang('If you want to get push notification then you have to allow notification from your browser')</p>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        }
    }

    //If enable push notification from admin panel
    if(pushNotify == 1){
        pushNotifyAction();
    }

    //When users allow browser notification
    if(permission != 'denied' && firebaseConfig){

        //Firebase
        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        navigator.serviceWorker.register("{{ asset('assets/global/js/firebase/firebase-messaging-sw.js') }}")

        .then((registration) => {
            messaging.useServiceWorker(registration);

            function initFirebaseMessagingRegistration() {
                messaging
                .requestPermission()
                .then(function () {
                    return messaging.getToken()
                })
                .then(function (token){
                    $.ajax({
                        url: '{{ route("user.add.device.token") }}',
                        type: 'POST',
                        data: {
                            token: token,
                            '_token': "{{ csrf_token() }}"
                        },
                        success: function(response){
                        },
                        error: function (err) {
                        },
                    });
                }).catch(function (error){
                });
            }

            messaging.onMessage(function (payload){
                const notificationPayload = payload.notification || {};
                const dataPayload = payload.data || {};
                const title = notificationPayload.title || notificationLabels.push;
                const options = {
                    body: notificationPayload.body,
                    icon: dataPayload.icon,
                    image: notificationPayload.image,
                    click_action: dataPayload.click_action,
                    vibrate: [200, 100, 200]
                };
                new Notification(title, options);
                appendNotificationToFeed({
                    title: notificationPayload.title,
                    body: notificationPayload.body,
                    image: notificationPayload.image
                });
            });

            //For authenticated users
            if(authenticated){
                initFirebaseMessagingRegistration();
            }

        });

    }
</script>
