/**
 * NProgress Preset Configurations
 *
 * Copy and paste any of these presets into your nprogress.js file
 * to quickly change the appearance and behavior of the loading bar.
 */

// ============================================
// PRESET 1: Default Blue (Current)
// ============================================
export const defaultBlue = {
    config: {
        minimum: 0.08,
        speed: 400,
        showSpinner: false,
        trickle: true,
        trickleSpeed: 200,
        easing: 'ease',
    },
    style: `
        #nprogress .bar {
            background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%) !important;
            height: 3px !important;
            box-shadow: 0 0 10px #3b82f6, 0 0 5px #3b82f6 !important;
        }
        #nprogress .peg {
            box-shadow: 0 0 10px #3b82f6, 0 0 5px #3b82f6 !important;
        }
    `
};

// ============================================
// PRESET 2: Fast & Smooth
// ============================================
export const fastSmooth = {
    config: {
        minimum: 0.1,
        speed: 200,
        showSpinner: false,
        trickle: true,
        trickleSpeed: 100,
        easing: 'ease-out',
    },
    style: `
        #nprogress .bar {
            background: linear-gradient(90deg, #06b6d4 0%, #0891b2 100%) !important;
            height: 2px !important;
            box-shadow: 0 0 8px #06b6d4 !important;
        }
        #nprogress .peg {
            box-shadow: 0 0 8px #06b6d4 !important;
        }
    `
};

// ============================================
// PRESET 3: Bold & Thick
// ============================================
export const boldThick = {
    config: {
        minimum: 0.08,
        speed: 500,
        showSpinner: true,
        trickle: true,
        trickleSpeed: 200,
        easing: 'ease-in-out',
    },
    style: `
        #nprogress .bar {
            background: linear-gradient(90deg, #8b5cf6 0%, #7c3aed 100%) !important;
            height: 5px !important;
            box-shadow: 0 0 15px #8b5cf6, 0 0 8px #8b5cf6 !important;
        }
        #nprogress .peg {
            box-shadow: 0 0 15px #8b5cf6, 0 0 8px #8b5cf6 !important;
        }
        #nprogress .spinner-icon {
            border-top-color: #8b5cf6 !important;
            border-left-color: #8b5cf6 !important;
        }
    `
};

// ============================================
// PRESET 4: Minimal (No Glow)
// ============================================
export const minimal = {
    config: {
        minimum: 0.05,
        speed: 300,
        showSpinner: false,
        trickle: true,
        trickleSpeed: 150,
        easing: 'linear',
    },
    style: `
        #nprogress .bar {
            background: #10b981 !important;
            height: 2px !important;
            box-shadow: none !important;
        }
        #nprogress .peg {
            box-shadow: none !important;
        }
    `
};

// ============================================
// PRESET 5: YouTube Style
// ============================================
export const youtubeStyle = {
    config: {
        minimum: 0.08,
        speed: 350,
        showSpinner: false,
        trickle: true,
        trickleSpeed: 150,
        easing: 'ease',
    },
    style: `
        #nprogress .bar {
            background: #ff0000 !important;
            height: 3px !important;
            box-shadow: 0 0 5px rgba(255, 0, 0, 0.5) !important;
        }
        #nprogress .peg {
            box-shadow: 0 0 10px #ff0000, 0 0 5px #ff0000 !important;
        }
    `
};

// ============================================
// PRESET 6: GitHub Style
// ============================================
export const githubStyle = {
    config: {
        minimum: 0.08,
        speed: 400,
        showSpinner: false,
        trickle: true,
        trickleSpeed: 200,
        easing: 'ease',
    },
    style: `
        #nprogress .bar {
            background: #2da44e !important;
            height: 2px !important;
            box-shadow: none !important;
        }
        #nprogress .peg {
            box-shadow: 0 0 10px #2da44e !important;
        }
    `
};

// ============================================
// PRESET 7: Gradient Rainbow
// ============================================
export const gradientRainbow = {
    config: {
        minimum: 0.08,
        speed: 400,
        showSpinner: false,
        trickle: true,
        trickleSpeed: 200,
        easing: 'ease',
    },
    style: `
        #nprogress .bar {
            background: linear-gradient(90deg,
                #ff0080 0%,
                #ff8c00 25%,
                #40e0d0 50%,
                #9370db 75%,
                #ff1493 100%
            ) !important;
            height: 4px !important;
            box-shadow: 0 0 10px rgba(255, 0, 128, 0.6) !important;
        }
        #nprogress .peg {
            box-shadow: 0 0 15px rgba(255, 0, 128, 0.8) !important;
        }
    `
};

// ============================================
// PRESET 8: Dark Mode Optimized
// ============================================
export const darkMode = {
    config: {
        minimum: 0.08,
        speed: 400,
        showSpinner: false,
        trickle: true,
        trickleSpeed: 200,
        easing: 'ease',
    },
    style: `
        #nprogress .bar {
            background: linear-gradient(90deg, #60a5fa 0%, #3b82f6 100%) !important;
            height: 3px !important;
            box-shadow: 0 0 10px #60a5fa, 0 0 5px #60a5fa !important;
        }
        #nprogress .peg {
            box-shadow: 0 0 10px #60a5fa, 0 0 5px #60a5fa !important;
        }
    `
};

// ============================================
// PRESET 9: Success Green
// ============================================
export const successGreen = {
    config: {
        minimum: 0.08,
        speed: 400,
        showSpinner: false,
        trickle: true,
        trickleSpeed: 200,
        easing: 'ease',
    },
    style: `
        #nprogress .bar {
            background: linear-gradient(90deg, #10b981 0%, #059669 100%) !important;
            height: 3px !important;
            box-shadow: 0 0 10px #10b981, 0 0 5px #10b981 !important;
        }
        #nprogress .peg {
            box-shadow: 0 0 10px #10b981, 0 0 5px #10b981 !important;
        }
    `
};

// ============================================
// PRESET 10: Warning Orange
// ============================================
export const warningOrange = {
    config: {
        minimum: 0.08,
        speed: 400,
        showSpinner: false,
        trickle: true,
        trickleSpeed: 200,
        easing: 'ease',
    },
    style: `
        #nprogress .bar {
            background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%) !important;
            height: 3px !important;
            box-shadow: 0 0 10px #f59e0b, 0 0 5px #f59e0b !important;
        }
        #nprogress .peg {
            box-shadow: 0 0 10px #f59e0b, 0 0 5px #f59e0b !important;
        }
    `
};

// ============================================
// PRESET 11: Ultra Minimal
// ============================================
export const ultraMinimal = {
    config: {
        minimum: 0.3,
        speed: 100,
        showSpinner: false,
        trickle: true,
        trickleSpeed: 50,
        easing: 'linear',
    },
    style: `
        #nprogress .bar {
            background: #000 !important;
            height: 1px !important;
            box-shadow: none !important;
        }
        #nprogress .peg {
            display: none !important;
        }
    `
};

// ============================================
// PRESET 12: Loading Bar with Spinner
// ============================================
export const withSpinner = {
    config: {
        minimum: 0.08,
        speed: 400,
        showSpinner: true,
        trickle: true,
        trickleSpeed: 200,
        easing: 'ease',
    },
    style: `
        #nprogress .bar {
            background: linear-gradient(90deg, #ec4899 0%, #db2777 100%) !important;
            height: 3px !important;
            box-shadow: 0 0 10px #ec4899, 0 0 5px #ec4899 !important;
        }
        #nprogress .peg {
            box-shadow: 0 0 10px #ec4899, 0 0 5px #ec4899 !important;
        }
        #nprogress .spinner {
            top: 15px !important;
            right: 15px !important;
        }
        #nprogress .spinner-icon {
            width: 18px !important;
            height: 18px !important;
            border-top-color: #ec4899 !important;
            border-left-color: #ec4899 !important;
        }
    `
};

/**
 * How to use:
 *
 * 1. Import the preset in nprogress.js:
 *    import { fastSmooth } from './nprogress.presets';
 *
 * 2. Replace the config:
 *    NProgress.configure(fastSmooth.config);
 *
 * 3. Replace the style content:
 *    style.textContent = fastSmooth.style + `
 *        // ... rest of your common styles
 *    `;
 */
