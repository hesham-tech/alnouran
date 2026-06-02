<template>
  <v-navigation-drawer
    v-model="store.drawer"
    :rail="rail && pageWidth > 765"
    :permanent="pageWidth > 765"
    :temporary="pageWidth <= 765"
    elevation="0"
    class="border-e"
    :width="260"
  >
    <!-- User Profile Section -->
    <v-list class="pa-2">
      <v-list-item
        rounded="lg"
        class="mb-2"
        :prepend-avatar="
          'https://ui-avatars.com/api/?name=' + store.user.name + '&background=4f46e5&color=fff'
        "
        :title="store.user.name"
        subtitle="عضو نشط"
        to="/user/edit"
      >
        <template v-slot:append>
          <v-btn
            variant="text"
            icon="mdi-chevron-left"
            size="small"
            @click.stop.prevent="rail = !rail"
          ></v-btn>
        </template>
      </v-list-item>
    </v-list>

    <v-divider class="mx-4 mb-2"></v-divider>

    <!-- Main Navigation Items -->
    <v-list nav density="compact" class="pa-2">
      <v-list-item
        prepend-icon="mdi-view-dashboard-outline"
        title="الرئيسية"
        to="/"
        rounded="lg"
        color="primary"
      ></v-list-item>

      <v-list-item
        prepend-icon="mdi-calendar-check-outline"
        :title="$t('Vacations')"
        to="/vacations"
        rounded="lg"
        color="primary"
      ></v-list-item>

      <v-list-item
        prepend-icon="mdi-file-chart-outline"
        :title="$t('reports')"
        to="/reports"
        rounded="lg"
        color="primary"
      ></v-list-item>

      <v-list-item
        v-if="store.user.roles == 'admin'"
        prepend-icon="mdi-map-marker-radius-outline"
        title="المحطات"
        to="/stations"
        rounded="lg"
        color="primary"
      ></v-list-item>

      <v-list-item
        v-if="store.user.roles == 'admin'"
        prepend-icon="mdi-account-group-outline"
        title="المستخدمين"
        to="/users"
        rounded="lg"
        color="primary"
      ></v-list-item>

      <v-list-item
        prepend-icon="mdi-flask-outline"
        title="التحضيرات"
        to="/preparation"
        rounded="lg"
        color="primary"
      ></v-list-item>
    </v-list>

    <!-- Bottom Actions -->
    <template v-slot:append>
      <v-divider class="mx-4 mt-2"></v-divider>
      <div class="pa-4">
        <v-btn
          block
          color="error"
          variant="tonal"
          @click="toLogout"
          class="text-none rounded-xl font-weight-bold logout-btn"
          height="48"
        >
          <v-icon :start="!rail" size="22">mdi-logout</v-icon>
          <span v-if="!rail">تسجيل الخروج</span>
        </v-btn>
      </div>
    </template>
  </v-navigation-drawer>
</template>

<script setup>
import { usemainStore } from '@/store/mainStore';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const store = usemainStore();
const rail = ref(false);
const pageWidth = ref(window.innerWidth);

onMounted(() => {
  // On mobile, we never want rail mode. We want a full drawer that can be toggled.
  if (pageWidth.value <= 765) {
    rail.value = false;
    store.drawer = false;
  } else {
    rail.value = false; // Default to full on desktop too, or user can toggle
  }
  window.addEventListener('resize', handleResize);
});

const handleResize = () => {
  pageWidth.value = window.innerWidth;
  if (pageWidth.value <= 765) {
    rail.value = false;
  }
};

function toLogout() {
  axios
    .post(`logout`)
    .then(() => {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      store.setAuthHeaderNew();
      store.startSnack('success', 'تم تسجيل الخروج بنجاح', 'success');
      store.auth = false;
    })
    .catch(() => {
      store.startSnack('error', 'حدث خطأ أثناء تسجيل الخروج', 'danger');
    });
}
</script>

<style scoped>
.v-navigation-drawer {
  background: var(--bg-surface) !important;
  border-right: 1px solid var(--border-color) !important;
}

:deep(.v-list-item--active) {
  background: rgba(var(--v-theme-primary), 0.1) !important;
  color: var(--primary) !important;
}

:deep(.v-list-item__prepend .v-icon) {
  opacity: 1;
}

.logout-btn {
  transition: all 0.3s ease;
  border: 1px solid rgba(var(--v-theme-error), 0.1);
}

.logout-btn:hover {
  background-color: rgba(var(--v-theme-error), 0.15) !important;
  transform: translateY(-1px);
}
</style>
