<div class="ghost-cards">
    @for ($i = 0; $i < 10; $i++)
        <div class="ghost-card tint-bg-down-2" style="height: {{ 45 * rand(3,6) }}px;"></div>
    @endfor
    <style>
        .ghost-cards {
            display: flex;
            flex-direction: column;
            width: 100%;
            gap: 5px;
            padding-top: 5px;
        }

        @keyframes fade-in {
            0% {
                opacity: 0.1;
            }
            50% {
                opacity: 1;
            }
            100% {
                opacity: 0.1;
            }
        }

        .ghost-card {
            width: 100%;
            animation: fade-in .95s infinite linear;
        }
    </style>
</div>