<template>
  <v-app-bar :elevation="0" class="glass border-b">
    <v-app-bar-nav-icon
      class="hidden-md-and-up"
      @click="store.drawer = !store.drawer"
    ></v-app-bar-nav-icon>
    <v-app-bar-title class="font-weight-bold">
      <span class="text-primary">Alnouran</span>
    </v-app-bar-title>

    <v-spacer></v-spacer>

    <div class="d-flex align-center px-2 px-sm-4">
      <v-switch
        @change="toggleTheme"
        v-model="switchd"
        color="primary"
        hide-details
        density="compact"
        class="ml-2 ml-sm-4"
      >
        <template v-slot:label>
          <v-icon :icon="switchd ? 'mdi-weather-night' : 'mdi-weather-sunny'" size="small"></v-icon>
        </template>
      </v-switch>

      <v-btn
        variant="tonal"
        color="primary"
        icon="mdi-home-outline"
        to="/"
        class="text-none d-flex d-sm-none"
        size="small"
      ></v-btn>
      <v-btn
        variant="tonal"
        color="primary"
        prepend-icon="mdi-home-outline"
        to="/"
        class="text-none d-none d-sm-flex"
      >
        الرئيسية
      </v-btn>
    </div>
  </v-app-bar>
</template>
<script setup>
import { useTheme } from 'vuetify';
import { ref } from 'vue';
import { usemainStore } from '@/store/mainStore';

const theme = useTheme();
const store = usemainStore();
const switchd = ref(JSON.parse(localStorage.getItem('defaultTheme')) || false);

function toggleTheme() {
  const newTheme = switchd.value ? 'dark' : 'light';
  theme.global.name.value = newTheme;
  localStorage.setItem('defaultTheme', switchd.value);
  document.documentElement.setAttribute('data-v-theme', newTheme);
}
</script>
<style scoped>
.v-app-bar {
  border-bottom: 1px solid var(--border-color) !important;
  background: var(--bg-surface) !important;
}
.glass {
  backdrop-filter: blur(10px);
  background: rgba(var(--v-theme-surface), 0.8) !important;
}
</style>
