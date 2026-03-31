// Custom Audio Notification Helper
// Features: Plays custom audio file + Visual Popup

function playTTSNotification(message) {
    console.log('Triggering Audio Notification:', message);

    // 1. Visual Popup (Toast)
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: '🔔 Pengingat Agenda',
            text: message, // Uses message passed from checker (e.g. "Kegiatan Anda akan segera dimulai")
            icon: 'info',
            timer: 30000, // 30 seconds
            timerProgressBar: true,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            showCloseButton: true,
            background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
            color: document.documentElement.classList.contains('dark') ? '#e2e8f0' : '#1e293b',
            customClass: {
                popup: 'shadow-2xl border border-indigo-500/30'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    }

    // 2. Play Audio File
    // Use global variable from Blade if available, else fallback
    const audioPath = window.audioPath || '/audio/notification.mp3';
    console.log('Attempting to play audio from:', audioPath);

    const audio = new Audio(audioPath);

    // Attempt play
    audio.play().catch(e => {
        console.warn('Audio play failed:', e);
        // Common reasons: 
        // 1. File not found (404)
        // 2. Autoplay policy (needs user interaction first)

        if (e.name === 'NotAllowedError') {
            // Log for debugging, but we handled this with our "click anywhere" listener
            console.log('Autoplay blocked. User needs to interact with page.');
        }
    });
}
