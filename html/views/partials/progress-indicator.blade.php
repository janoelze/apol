<div class="progress-indicator">
    <div class="progress-indicator-spinner">
        <div class="progress-indicator-spinner-inner"></div>
    </div>
    <style>
        .progress-indicator {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 100;
        }
        .progress-indicator-text {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .progress-indicator-spinner {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            border: 0.25rem solid rgba(0,0,0,0.1);
            border-top-color: rgba(255,255,255,0.4);
            animation: spin 1s infinite linear;
        }
        .progress-indicator-spinner-inner {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 0.25rem solid rgba(0,0,0,0.1);
            border-top-color: rgba(255,255,255,0.4);
            animation: spin 1s infinite linear;
        }
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</div>