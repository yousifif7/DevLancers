(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    function updateBadges(data) {
        document.querySelectorAll('[data-count="messages"]').forEach(el => {
            el.textContent = data.messages;
            el.classList.toggle('has-new', data.messages > 0);
        });
        document.querySelectorAll('[data-count="alerts"]').forEach(el => {
            el.textContent = data.alerts;
            el.classList.toggle('has-new', data.alerts > 0);
        });
        document.querySelectorAll('[data-count="proposals"]').forEach(el => {
            el.textContent = data.proposals;
            el.classList.toggle('has-new', data.proposals > 0);
        });
        document.querySelectorAll('[data-count="reviews"]').forEach(el => {
            if (el) {
                el.textContent = data.pending_reviews || 0;
                el.classList.toggle('has-new', (data.pending_reviews || 0) > 0);
            }
        });
    }

    let lastAlertCount = parseInt(document.querySelector('[data-count="alerts"]')?.textContent || '0');
    let toastContainer = null;

    function showToast(title, message, url) {
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'dl-toast-container';
            document.body.appendChild(toastContainer);
        }
        const toast = document.createElement('div');
        toast.className = 'dl-toast';
        toast.innerHTML = `<strong>${title}</strong><p>${message}</p>`;
        if (url) {
            toast.style.cursor = 'pointer';
            toast.onclick = () => window.location.href = url;
        }
        toastContainer.appendChild(toast);
        setTimeout(() => toast.remove(), 6000);
    }

    function poll() {
        fetch('/api/poll', { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (data.alerts > lastAlertCount && data.alerts_list?.length) {
                    const latest = data.alerts_list[0];
                    showToast(latest.title, latest.message, latest.url);
                }
                lastAlertCount = data.alerts;
                updateBadges(data);
            })
            .catch(() => {});
    }

    if (document.querySelector('[data-count="alerts"]')) {
        setInterval(poll, 8000);
    }

    // Live chat
    const chatBox = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const partnerId = chatBox?.dataset.partner;

    if (chatBox && partnerId) {
        let lastTimestamp = chatBox.dataset.last || '';

        function renderMessage(msg) {
            const wrap = document.createElement('div');
            wrap.className = msg.is_mine ? 'dl-msg dl-msg-mine' : 'dl-msg dl-msg-theirs';
            wrap.innerHTML = `<div class="dl-msg-meta">${msg.is_mine ? 'You' : msg.sender_name} · ${msg.time}</div>${escapeHtml(msg.body)}`;
            wrap.dataset.id = msg.id;
            return wrap;
        }

        function escapeHtml(text) {
            const d = document.createElement('div');
            d.textContent = text;
            return d.innerHTML;
        }

        function appendMessages(messages) {
            messages.forEach(msg => {
                if (document.querySelector(`[data-id="${msg.id}"]`)) return;
                chatBox.appendChild(renderMessage(msg));
                lastTimestamp = msg.created_at;
            });
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function updateMessageBadge(count) {
            document.querySelectorAll('[data-count="messages"]').forEach(el => {
                el.textContent = count;
                el.classList.toggle('has-new', count > 0);
            });
        }

        function markThreadRead() {
            fetch(`/api/chat/${partnerId}/read`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
            })
                .then(r => r.json())
                .then(data => {
                    if (typeof data.messages === 'number') {
                        updateMessageBadge(data.messages);
                    }
                })
                .catch(() => {});
        }

        function fetchMessages() {
            let url = `/api/chat/${partnerId}/messages`;
            if (lastTimestamp) url += `?since=${encodeURIComponent(lastTimestamp)}`;
            fetch(url, { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    if (data.messages?.length) appendMessages(data.messages);
                })
                .catch(() => {});
        }

        chatForm?.addEventListener('submit', function (e) {
            e.preventDefault();
            const text = chatInput.value.trim();
            if (!text) return;

            chatInput.value = '';
            chatInput.disabled = true;
            fetch(`/api/chat/${partnerId}/send`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({ message: text }),
            })
                .then(r => {
                    if (!r.ok) throw new Error('Send failed');
                    return r.json();
                })
                .then(data => {
                    if (data.success && data.message) {
                        appendMessages([data.message]);
                    } else {
                        chatInput.value = text;
                    }
                })
                .catch(() => {
                    chatInput.value = text;
                    alert('Failed to send message');
                })
                .finally(() => { chatInput.disabled = false; chatInput.focus(); });
        });

        markThreadRead();
        setInterval(fetchMessages, 3000);
        chatBox.scrollTop = chatBox.scrollHeight;
        chatInput?.focus();
    }
})();
