<template>
    <div id="app">
        <router-view />
        <NotificationContainer />
    </div>
</template>

<script setup>
import NotificationContainer from './components/UI/NotificationContainer.vue'
</script>
