@extends('teacher.layouts.main')

@section('content')

<!-- CSRF token meta tag (agar layout'da bo'lmasa) -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://cdn.tailwindcss.com"></script>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">

        <!-- Header -->
        <div class="bg-blue-600 text-white p-4">
            <h1 class="text-2xl font-bold">💬 Laravel Real-Time Chat</h1>
            <p class="text-sm">WebSocket orqali jonli chat</p>
        </div>

        <!-- Chat Messages -->
        <div id="messages" class="h-96 overflow-y-auto p-4 space-y-3 bg-gray-50">
            <div class="text-center text-gray-500 text-sm">
                👋 Chatga xush kelibsiz! Xabar yozing...
            </div>
        </div>

        <!-- Input Form -->
        <div class="border-t p-4 bg-white">
            <div class="mb-3">
                <input
                    type="text"
                    id="username"
                    placeholder="Ismingiz"
                    value="{{ auth()->user()->name ?? '' }}"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex gap-2">
                <input
                    type="text"
                    id="messageInput"
                    placeholder="Xabar yozing..."
                    class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button
                    onclick="sendMessage()"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Yuborish
                </button>
            </div>
        </div>

        <!-- Connection Status -->
        <div id="status" class="px-4 py-2 bg-gray-100 text-sm text-center">
            <span class="text-yellow-600">⏳ WebSocket ga ulanilmoqda...</span>
        </div>
    </div>
</div>

<!-- Laravel Echo + Pusher -->
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.3.0/dist/web/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>

<script>
    // Debug rejimini yoqish
    Pusher.logToConsole = true;

    // CSRF token - to'g'ridan-to'g'ri Blade dan
    const csrfToken = '{{ csrf_token() }}';
    
    console.log('🔧 CSRF Token:', csrfToken ? 'Mavjud ✅' : 'Yo\'q ❌');
    console.log('🔧 Echo sozlamalari boshlandi...');
    console.log('Host:', '127.0.0.1');
    console.log('Port:', 6001);

    // Laravel Echo sozlash
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: 'local',
        cluster: 'mt1',
        
        // TO'G'RIDAN-TO'G'RI IP manzil
        wsHost: '127.0.0.1',
        wsPort: 6001,
        wssPort: 6001,
        
        // MUHIM sozlamalar
        forceTLS: false,
        encrypted: false,
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
        
        // Auth endpoint (private channel uchun)
        authEndpoint: '/broadcasting/auth',
    });

    console.log('✅ Echo yaratildi');

    // Chat kanaliga ulanish
    const channel = window.Echo.channel('chat');
    console.log('📡 Chat kanaliga ulanilmoqda...');

    // Ulanish holati
    channel.subscribed(() => {
        console.log('✅ Kanalga muvaffaqiyatli ulandi!');
        updateStatus('✅ WebSocket ga ulandi!', 'text-green-600');
    });

    channel.error((error) => {
        console.error('❌ Kanal xatosi:', error);
        updateStatus('❌ Ulanish xatosi', 'text-red-600');
    });

    // Pusher ulanish hodisalari
    window.Echo.connector.pusher.connection.bind('connected', function() {
        console.log('🎉 Pusher ga ulandi!');
    });

    window.Echo.connector.pusher.connection.bind('error', function(err) {
        console.error('❌ Pusher xatosi:', err);
        updateStatus('❌ Pusher xatosi', 'text-red-600');
    });

    window.Echo.connector.pusher.connection.bind('unavailable', function() {
        console.error('❌ Pusher mavjud emas');
        updateStatus('❌ WebSocket mavjud emas', 'text-red-600');
    });

    // Xabar kelganda
    channel.listen('.message.sent', (data) => {
        console.log('📩 Yangi xabar keldi:', data);
        
        // ❗ MUHIM: Agar bu o'z xabarimiz bo'lsa, ignore qilamiz
        if (data.message_id && myMessageIds.has(data.message_id)) {
            console.log('⏭️ O\'z xabarimiz, o\'tkazib yuboramiz');
            return;
        }
        
        addMessage(data.username, data.message, data.time, false);
    });

    // Global o'zgaruvchi - o'z xabarlarimizni ID'sini saqlash
    let myMessageIds = new Set();

    // Xabar yuborish
    function sendMessage() {
        const username = document.getElementById('username').value.trim();
        const message = document.getElementById('messageInput').value.trim();

        if (!username) {
            alert('Iltimos, ismingizni kiriting!');
            return;
        }

        if (!message) {
            alert('Xabar bo\'sh bo\'lishi mumkin emas!');
            return;
        }

        // Xabar ID yaratish (timestamp + random)
        const messageId = Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        
        // O'z xabarlarimiz ro'yxatiga qo'shamiz
        myMessageIds.add(messageId);

        // O'z xabarimizni ko'rsatish
        const time = new Date().toLocaleTimeString('uz-UZ', {
            hour: '2-digit',
            minute: '2-digit'
        });
        addMessage(username, message, time, true);

        console.log('📤 Xabar yuborilmoqda...', { username, message, messageId });

        // Socket ID ni olish
        const socketId = window.Echo.socketId();
        console.log('🔌 Socket ID:', socketId);

        // Serverga yuborish
        fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Socket-ID': socketId || ''
                },
                body: JSON.stringify({
                    username: username,
                    message: message,
                    message_id: messageId // ✅ Xabar ID yuboramiz
                })
            })
            .then(response => {
                console.log('📥 Response status:', response.status);
                if (!response.ok) {
                    throw new Error('HTTP error! status: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('✅ Xabar serverga yuborildi:', data);
                document.getElementById('messageInput').value = '';
            })
            .catch(error => {
                console.error('❌ Fetch xatosi:', error);
                alert('Xabar yuborishda xato: ' + error.message);
            });
    }

    // Enter bosilganda yuborish
    document.getElementById('messageInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    // Xabar qo'shish
    function addMessage(username, message, time, isOwn) {
        const messagesDiv = document.getElementById('messages');

        // Agar birinchi xabar bo'lsa, xush kelibsiz xabarini o'chirish
        const welcomeMsg = messagesDiv.querySelector('.text-center');
        if (welcomeMsg && messagesDiv.children.length === 1) {
            welcomeMsg.remove();
        }

        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${isOwn ? 'justify-end' : 'justify-start'}`;

        messageDiv.innerHTML = `
            <div class="${isOwn ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800'} rounded-lg px-4 py-2 max-w-xs">
                <div class="font-semibold text-sm">${escapeHtml(username)}</div>
                <div class="mt-1">${escapeHtml(message)}</div>
                <div class="text-xs mt-1 opacity-75">${time}</div>
            </div>
        `;

        messagesDiv.appendChild(messageDiv);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    // XSS himoyasi
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // Status yangilash
    function updateStatus(text, colorClass) {
        const statusDiv = document.getElementById('status');
        statusDiv.innerHTML = `<span class="${colorClass}">${text}</span>`;
    }

    console.log('🚀 Chat tizimi tayyor!');
</script>
@endsection