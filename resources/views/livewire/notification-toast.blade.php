<div>
    @if($message)
        <div class="fixed top-4 right-4 z-50 animate-fade-in-down">
            <div class="
                px-6 py-4 rounded-lg shadow-lg text-white font-medium
                @if($type === 'success') bg-green-500
                @elseif($type === 'error') bg-red-500
                @elseif($type === 'warning') bg-yellow-500
                @else bg-blue-500
                @endif
            ">
                <div class="flex items-center gap-3">
                    @if($type === 'success')
                        <span class="text-xl">✅</span>
                    @elseif($type === 'error')
                        <span class="text-xl">❌</span>
                    @elseif($type === 'warning')
                        <span class="text-xl">⚠️</span>
                    @else
                        <span class="text-xl">ℹ️</span>
                    @endif
                    {{ $message }}
                </div>
            </div>
        </div>

        <style>
            @keyframes fadeInDown {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-fade-in-down {
                animation: fadeInDown 0.3s ease-out;
            }
        </style>

        <script>
            setTimeout(() => {
                @this.message = '';
            }, {{ $duration }});
        </script>
    @endif
</div>
