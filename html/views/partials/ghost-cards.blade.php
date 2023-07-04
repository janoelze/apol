<div class="ghost-cards">
    @for ($i = 0; $i < 10; $i++)
        <div class="ghost-card tint-bg-down-2"></div>
    @endfor
    <style>
        .ghost-cards {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            width: 100%;
            gap: 10px
        }

        @keyframes fade-in {
            0% {
                opacity: 0.1;
            }
            50% {
                opacity: 0.6;
            }
            100% {
                opacity: 0.1;
            }
        }

        .ghost-card {
            width: 100%;
            min-height: 150px;
            animation: fade-in .87s infinite linear;
        }
    </style>
</div>