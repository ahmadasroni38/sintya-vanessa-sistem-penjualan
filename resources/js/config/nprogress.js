import NProgress from 'nprogress';
import 'nprogress/nprogress.css';

/**
 * NProgress Configuration
 * Customize the loading bar appearance and behavior
 */

// Configure NProgress
NProgress.configure({
    // Minimum percentage used upon starting (0.08 = 8%)
    minimum: 0.08,

    // Animation settings for how fast NProgress will fade in and out
    // Higher values = slower animation
    speed: 400,

    // Whether to show the spinner
    showSpinner: false,

    // Increment step between 0 and 1
    // Controls how much the progress bar grows per trickle
    trickle: true,

    // How often to trickle, in milliseconds
    trickleSpeed: 200,

    // Easing function for animations
    // Can be 'linear', 'ease', 'ease-in', 'ease-out', 'ease-in-out', or a cubic-bezier value
    easing: 'ease',

    // Adjust animation settings using `speed` and `ease`
    // Example: NProgress.configure({ speed: 500, ease: 'ease-in-out' })
});

/**
 * Custom CSS Overrides
 * You can customize the appearance by modifying the CSS variables below
 * or by adding custom styles in your main CSS file
 */

// Create a style element for custom NProgress styling
const style = document.createElement('style');
style.textContent = `
    /* NProgress Bar */
    #nprogress .bar {
        background: linear-gradient(90deg, #f472b6 0%, #ec4899 100%) !important;
        height: 3px !important;
        box-shadow: 0 0 10px #ec4899, 0 0 5px #ec4899 !important;
    }

    /* NProgress Peg (the moving part) */
    #nprogress .peg {
        box-shadow: 0 0 10px #ec4899, 0 0 5px #ec4899 !important;
    }

    /* NProgress Spinner (if enabled) */
    #nprogress .spinner {
        top: 15px !important;
        right: 15px !important;
    }

    #nprogress .spinner-icon {
        width: 18px !important;
        height: 18px !important;
        border: solid 2px transparent !important;
        border-top-color: #ec4899 !important;
        border-left-color: #ec4899 !important;
    }

    /* Animation tweaks */
    #nprogress {
        pointer-events: none;
    }

    #nprogress .bar {
        position: fixed;
        z-index: 9999;
        top: 0;
        left: 0;
        width: 100%;
    }
`;

// Append the style to document head
if (typeof document !== 'undefined') {
    document.head.appendChild(style);
}

/**
 * Helper functions for manual control
 */

export const nprogressHelper = {
    /**
     * Start the progress bar
     */
    start: () => {
        NProgress.start();
    },

    /**
     * Complete and hide the progress bar
     */
    done: () => {
        NProgress.done();
    },

    /**
     * Set progress to a specific percentage
     * @param {number} n - Number between 0 and 1
     */
    set: (n) => {
        NProgress.set(n);
    },

    /**
     * Increment the progress bar by a specific amount
     * @param {number} n - Number to increment by
     */
    inc: (n) => {
        NProgress.inc(n);
    },

    /**
     * Remove the progress bar
     */
    remove: () => {
        NProgress.remove();
    },

    /**
     * Check if NProgress is started
     * @returns {boolean}
     */
    isStarted: () => {
        return NProgress.isStarted();
    },
};

export default NProgress;
