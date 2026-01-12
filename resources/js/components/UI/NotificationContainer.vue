<template>
    <div class="fixed top-4 right-4 z-[9999] space-y-2">
        <TransitionGroup name="notification" tag="div">
            <div
                v-for="notification in notificationStore.notifications"
                :key="notification.id"
                :class="[
                    'min-w-[340px] max-w-md w-full bg-white dark:bg-gray-800 backdrop-blur-sm rounded-xl pointer-events-auto shadow-2xl ring-1 ring-black/5 dark:ring-white/10 overflow-hidden transform transition-all duration-300 hover:shadow-3xl hover:scale-[1.02]',
                    {
                        'bg-gradient-to-r from-green-50 to-white dark:from-green-900/20 dark:to-gray-800 border-l-4 border-green-500':
                            notification.type === 'success',
                        'bg-gradient-to-r from-red-50 to-white dark:from-red-900/20 dark:to-gray-800 border-l-4 border-red-500':
                            notification.type === 'error',
                        'bg-gradient-to-r from-yellow-50 to-white dark:from-yellow-900/20 dark:to-gray-800 border-l-4 border-yellow-500':
                            notification.type === 'warning',
                        'bg-gradient-to-r from-blue-50 to-white dark:from-blue-900/20 dark:to-gray-800 border-l-4 border-blue-500':
                            notification.type === 'info',
                    },
                ]"
            >
                <div class="p-5">
                    <div class="flex items-start gap-4">
                        <!-- Icon with colored background -->
                        <div
                            :class="[
                                'flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center shadow-sm',
                                {
                                    'bg-green-100 dark:bg-green-900/50':
                                        notification.type === 'success',
                                    'bg-red-100 dark:bg-red-900/50':
                                        notification.type === 'error',
                                    'bg-yellow-100 dark:bg-yellow-900/50':
                                        notification.type === 'warning',
                                    'bg-blue-100 dark:bg-blue-900/50':
                                        notification.type === 'info',
                                },
                            ]"
                        >
                            <!-- Success Icon -->
                            <svg
                                v-if="notification.type === 'success'"
                                class="h-5 w-5 text-green-600 dark:text-green-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2.5"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                            <!-- Error Icon -->
                            <svg
                                v-else-if="notification.type === 'error'"
                                class="h-5 w-5 text-red-600 dark:text-red-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2.5"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                            <!-- Warning Icon -->
                            <svg
                                v-else-if="notification.type === 'warning'"
                                class="h-5 w-5 text-yellow-600 dark:text-yellow-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2.5"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
                                />
                            </svg>
                            <!-- Info Icon -->
                            <svg
                                v-else
                                class="h-5 w-5 text-blue-600 dark:text-blue-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2.5"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p
                                class="text-base font-semibold text-gray-900 dark:text-white leading-snug"
                            >
                                {{ notification.title }}
                            </p>
                            <p
                                v-if="notification.message"
                                class="mt-1.5 text-sm text-gray-600 dark:text-gray-400 leading-relaxed"
                            >
                                {{ notification.message }}
                            </p>
                        </div>

                        <!-- Close Button -->
                        <div
                            v-if="notification.closable"
                            class="flex-shrink-0"
                        >
                            <button
                                @click="
                                    notificationStore.removeNotification(
                                        notification.id
                                    )
                                "
                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800"
                                title="Close notification"
                            >
                                <svg
                                    class="h-4 w-4"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Progress bar for timed notifications -->
                <div
                    v-if="notification.duration > 0"
                    :class="[
                        'h-1.5 w-full bg-gray-100 dark:bg-gray-700/50',
                    ]"
                >
                    <div
                        :class="[
                            'h-full transition-all duration-300',
                            {
                                'bg-gradient-to-r from-green-400 to-green-600 dark:from-green-500 dark:to-green-400':
                                    notification.type === 'success',
                                'bg-gradient-to-r from-red-400 to-red-600 dark:from-red-500 dark:to-red-400':
                                    notification.type === 'error',
                                'bg-gradient-to-r from-yellow-400 to-yellow-600 dark:from-yellow-500 dark:to-yellow-400':
                                    notification.type === 'warning',
                                'bg-gradient-to-r from-blue-400 to-blue-600 dark:from-blue-500 dark:to-blue-400':
                                    notification.type === 'info',
                            },
                        ]"
                        :style="{
                            animation: `shrink ${notification.duration}ms linear forwards`,
                        }"
                    ></div>
                </div>
            </div>
        </TransitionGroup>
    </div>
</template>

<script setup>
import { useNotificationStore } from "../../stores/notification";

const notificationStore = useNotificationStore();
</script>

<style scoped>
.notification-enter-active,
.notification-leave-active {
    transition: all 0.3s ease;
}

.notification-enter-from {
    opacity: 0;
    transform: translateX(100%);
}

.notification-leave-to {
    opacity: 0;
    transform: translateX(100%);
}

.notification-move {
    transition: transform 0.3s ease;
}

@keyframes shrink {
    from {
        width: 100%;
    }
    to {
        width: 0%;
    }
}
</style>
