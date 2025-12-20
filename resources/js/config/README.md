# NProgress Configuration Guide

This document explains how to customize the NProgress loading bar in your application.

## Configuration File

All NProgress settings are centralized in `resources/js/config/nprogress.js`

## Available Options

### Basic Configuration

Edit the `NProgress.configure()` options in the config file:

```javascript
NProgress.configure({
    minimum: 0.08,        // Starting percentage (0-1)
    speed: 400,           // Animation speed in milliseconds
    showSpinner: false,   // Show/hide the loading spinner
    trickle: true,        // Auto-increment the progress bar
    trickleSpeed: 200,    // Trickle speed in milliseconds
    easing: 'ease',       // CSS easing function
});
```

### Configuration Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `minimum` | number | 0.08 | Minimum percentage to start with (0-1) |
| `speed` | number | 400 | Animation speed in milliseconds |
| `showSpinner` | boolean | false | Whether to show the loading spinner |
| `trickle` | boolean | true | Auto-increment the progress bar |
| `trickleSpeed` | number | 200 | How fast to trickle in milliseconds |
| `easing` | string | 'ease' | CSS easing: 'linear', 'ease', 'ease-in', 'ease-out', 'ease-in-out' |

## Visual Customization

### Progress Bar Color

Modify the gradient in the config file:

```css
#nprogress .bar {
    background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%) !important;
}
```

**Examples:**

**Red theme:**
```css
background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%) !important;
```

**Green theme:**
```css
background: linear-gradient(90deg, #10b981 0%, #059669 100%) !important;
```

**Purple theme:**
```css
background: linear-gradient(90deg, #8b5cf6 0%, #7c3aed 100%) !important;
```

### Progress Bar Height

```css
#nprogress .bar {
    height: 3px !important; /* Change to your desired height */
}
```

### Glow Effect

```css
#nprogress .bar {
    box-shadow: 0 0 10px #3b82f6, 0 0 5px #3b82f6 !important;
}
```

Remove glow:
```css
#nprogress .bar {
    box-shadow: none !important;
}
```

### Spinner Customization

Enable the spinner first:
```javascript
showSpinner: true,
```

Then customize:
```css
#nprogress .spinner {
    top: 15px !important;
    right: 15px !important;
}

#nprogress .spinner-icon {
    width: 18px !important;
    height: 18px !important;
    border-top-color: #3b82f6 !important;
    border-left-color: #3b82f6 !important;
}
```

## Manual Control

Use the exported helper functions for manual control:

```javascript
import { nprogressHelper } from '@/config/nprogress';

// Start progress bar
nprogressHelper.start();

// Set to specific percentage (0-1)
nprogressHelper.set(0.4); // 40%

// Increment by amount
nprogressHelper.inc(0.2); // +20%

// Complete and hide
nprogressHelper.done();

// Check if started
if (nprogressHelper.isStarted()) {
    console.log('Progress bar is running');
}
```

## Router Integration

NProgress is already integrated with Vue Router in `app.js`:

```javascript
router.beforeEach(() => {
    NProgress.start();
});

router.afterEach(() => {
    NProgress.done();
});
```

## Common Customizations

### Fast and Smooth
```javascript
minimum: 0.1,
speed: 200,
trickleSpeed: 100,
easing: 'ease-out'
```

### Slow and Steady
```javascript
minimum: 0.05,
speed: 800,
trickleSpeed: 400,
easing: 'ease-in-out'
```

### Instant (No Animation)
```javascript
minimum: 0.5,
speed: 0,
trickle: false
```

### With Spinner
```javascript
showSpinner: true,
minimum: 0.08,
speed: 400
```

## Dark Mode Support

For dark mode compatibility, you can add theme-aware colors:

```javascript
const isDarkMode = document.documentElement.classList.contains('dark');

const barColor = isDarkMode
    ? 'linear-gradient(90deg, #60a5fa 0%, #3b82f6 100%)'
    : 'linear-gradient(90deg, #3b82f6 0%, #2563eb 100%)';

style.textContent = `
    #nprogress .bar {
        background: ${barColor} !important;
    }
`;
```

## Troubleshooting

### Progress bar not showing
- Check if NProgress is imported correctly in `app.js`
- Verify the z-index is high enough (default: 9999)
- Ensure the bar height is visible (minimum 2px recommended)

### Progress bar stays at 100%
- Make sure `NProgress.done()` is called in router's `afterEach` hook
- Check for JavaScript errors in console

### Colors not applying
- Ensure `!important` is used in CSS rules
- Check if other CSS is overriding the styles
- Clear browser cache

## Additional Resources

- [NProgress GitHub](https://github.com/rstacruz/nprogress)
- [NProgress Documentation](http://ricostacruz.com/nprogress/)
