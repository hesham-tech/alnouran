<template>
  <!-- Dialogs -->

  <v-dialog v-model="dialogOpenIn" max-width="500">
    <v-card class="pa-4 card-compact" style="max-height: 90vh; overflow-y: auto;">
      <v-card-title class="d-flex align-center pb-2">
        <v-icon color="primary" class="mr-2">mdi-information-outline</v-icon>
        تفاصيل {{ PreparationData.name }}
      </v-card-title>
      <v-divider class="mb-4"></v-divider>

      <v-row dense>
        <v-col
          cols="6"
          v-for="(val, key) in {
            'وقت التحضير': date(PreparationData.actual_time),
            المدة: PreparationData.cont_hours + ' ساعة',
            'التركيز (ppm)': PreparationData.ppm,
            'طن شرائح': PreparationData.slices_ton,
            'كيلو خام': PreparationData.quantity,
            الوردية: PreparationData.shift,
            منذ: timeSince(PreparationData.actual_time) + ' ساعة',
            متبقي: timeSince2(PreparationData.actual_time, PreparationData.cont_hours) + ' ساعة',
            بواسطة: PreparationData.user_name,
            'تم الإنشاء': date(PreparationData.created),
          }"
          :key="key"
        >
          <div class="text-caption text-muted">{{ key }}</div>
          <div class="text-body-2 font-weight-medium">{{ val }}</div>
        </v-col>
      </v-row>

      <v-card-actions class="mt-4">
        <v-spacer></v-spacer>
        <v-btn color="primary" variant="text" @click="dialogOpenIn = false">إغلاق</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <div class="dashboard-container pa-4">
    <!-- Floating Add Button -->
    <div class="fab-container">
      <addPreparationComponent @refresh="typePreparFunc" />
    </div>

    <!-- User & Stations Header -->
    <v-card class="mb-6 pa-4 border shadow-sm rounded-lg bg-surface">
      <v-row align="center" no-gutters>
        <v-col cols="auto" class="mr-4">
          <v-avatar color="primary" size="64">
            <span class="text-h5">{{ store.user.name.charAt(0) }}</span>
          </v-avatar>
        </v-col>
        <v-col>
          <div class="text-h5 font-weight-bold">{{ store.user.name }}</div>
          <div class="text-caption text-muted mb-2">{{ store.user.email }}</div>
          <div class="d-flex flex-wrap gap-1">
            <v-chip
              v-for="station in store.user.stations"
              :key="station.id"
              size="x-small"
              variant="tonal"
              color="primary"
              :to="`/station/${station.id}`"
              class="mr-1"
            >
              {{ station.name }}
            </v-chip>
          </div>
        </v-col>
      </v-row>
    </v-card>

    <!-- Navigation Quick Links -->
    <v-slide-group class="mb-6" show-arrows>
      <v-slide-group-item v-for="(routerList, i) in routerLists" :key="i">
        <v-btn
          v-if="routerList.meta.show"
          :to="routerList.path"
          variant="tonal"
          color="secondary"
          size="small"
          rounded="pill"
          class="ma-1 text-none"
        >
          {{ routerList.meta.titleAr }}
        </v-btn>
      </v-slide-group-item>
    </v-slide-group>

    <!-- Preparations Grid -->
    <div class="section-title mb-4 d-flex align-center">
      <v-icon class="mr-2" color="primary">mdi-flask-outline</v-icon>
      <span class="text-h6 font-weight-bold">التحضيرات الحالية</span>
    </div>

    <v-row dense>
      <v-col
        cols="12"
        sm="6"
        md="4"
        lg="3"
        v-for="typePrep in typePreparationData"
        :key="typePrep.id"
      >
        <v-card class="preparation-card h-100 pa-3 border rounded-lg">
          <div class="d-flex justify-space-between align-start mb-2">
            <div class="text-subtitle-1 font-weight-bold truncate" style="max-width: 70%">
              {{ typePrep.name }}
            </div>
            <div class="d-flex">
              <v-btn
                icon="mdi-eye-outline"
                variant="text"
                color="secondary"
                @click="openInFun(typePrep)"
              ></v-btn>
              <div class="d-flex" v-if="canManage(typePrep)">
                <editPreparationComponent :preparation="typePrep" @refresh="typePreparFunc" />
                <deletePreparationComponent :preparation="typePrep" @refresh="typePreparFunc" />
              </div>
            </div>
          </div>

          <!-- Progress Section -->
          <div class="mb-3">
            <div class="d-flex justify-space-between text-caption mb-1">
              <span class="text-muted">المنسوب </span>
              <span
                :class="100 - typePrep.percentage <= 15 ? 'text-danger' : 'text-success'"
                class="text-h6 font-weight-black"
              >
                {{ percentageResalt(100 - typePrep.percentage) }}%
              </span>
            </div>
            <v-progress-linear
              :model-value="100 - typePrep.percentage"
              height="14"
              rounded="pill"
              striped
              :color="100 - typePrep.percentage <= 15 ? 'error' : 'success'"
              bg-color="surface-variant"
              bg-opacity="0.2"
            ></v-progress-linear>
          </div>

          <!-- Card Details Grid -->
          <v-row no-gutters class="text-caption">
            <v-col cols="6" class="mb-1">
              <v-icon size="14" class="mr-1 text-muted">mdi-timer-outline</v-icon>
              {{ typePrep.cont_hours }}س
            </v-col>
            <v-col cols="6" class="mb-1 text-right">
              <v-icon size="14" class="mr-1 text-muted">mdi-clock-check-outline</v-icon>
              {{ timeSince2(typePrep.actual_time, typePrep.cont_hours) }}س
            </v-col>
            <v-col cols="12" class="text-muted truncate">
              <v-icon size="14" class="mr-1">mdi-account-outline</v-icon>
              {{ typePrep.user_name }}
            </v-col>
            <v-col cols="12" class="mt-2">
              <v-chip
                size="x-small"
                variant="flat"
                color="surface-variant"
                class="w-100 justify-center"
              >
                {{ date(typePrep.actual_time) }}
              </v-chip>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>
<script setup>
import moment from 'moment';
import { onMounted, ref } from 'vue';
import { usemainStore } from '@/store/mainStore';
import addPreparationComponent from '../components/preparation/addPreparationComponent.vue';
import editPreparationComponent from '../components/preparation/editPreparationComponent.vue';
import deletePreparationComponent from '../components/preparation/deletePreparationComponent.vue';
import { useRouter } from 'vue-router';
const router = useRouter();
const routerLists = router.getRoutes();
const store = usemainStore();
const typePreparationData = ref([]);
const PreparationData = ref('');
const dialogOpenIn = ref(false);

function openInFun(typePrep) {
  PreparationData.value = typePrep;
  dialogOpenIn.value = true;
}
const dialogedit = ref(false);
function percentageResalt(percentage) {
  return percentage.toFixed();
}
function timeSince(time) {
  //  return moment(time).fromNow();
  const now = moment();
  return -moment(time).diff(now, 'hours', true).toFixed(2);
}
function timeSince2(actual_time, cont_hours) {
  const now = moment();
  const actua_time = -moment(actual_time).diff(now, 'hours', true);
  return (cont_hours - actua_time).toFixed(2);
}
function date(d) {
  return moment(d).locale('ar').format('dddd :- h:mm A - MM/DD');
}
onMounted(() => {
  store.getAbsences();
  store.getUser();
  store.getReports();
  typePreparFunc();
});

function typePreparFunc() {
  typePreparationData.value = [];
  store.getTypePre('latestPreparationActual.user').then(() => {
    for (let i = 0; i < store.typePreparation.length; i++) {
      const latest = store.typePreparation[i].latest_preparation_actual;
      if (!latest) continue;

      const hoursDifferenceValue = calculateHoursDifference(latest.actual_time);
      const percentageValue = (hoursDifferenceValue / latest.cont_hours) * 100;

      // Filter: If the displayed percentage (100 - percentageValue) is <= -200, skip it.
      if (100 - percentageValue <= -200) continue;

      const newPreparationData = {
        id: store.typePreparation[i].id,
        prep_id: latest.id,
        name: store.typePreparation[i].name,
        updated: latest.updated_at,
        created: latest.created_at,

        ppm: latest.ppm,
        shift: latest.shift,
        quantity: latest.quantity,
        slices_ton: latest.slices_ton,
        actual_time: latest.actual_time,
        cont_hours: latest.cont_hours,
        user_id: latest.user_id,
        user_name: latest.user?.name || 'N/A',
        hoursDifference: hoursDifferenceValue.toFixed(2),
        percentage: percentageValue.toFixed(2),
      };
      typePreparationData.value.push(newPreparationData);
      store.overlay = false;
    }
  });
}

function calculateHoursDifference(actualTime) {
  const dataTime = new Date(actualTime);
  const currentTime = new Date();
  const timeDifference = currentTime - dataTime;
  return timeDifference / (1000 * 60 * 60);
}

function canManage(typePrep) {
  // Only the creator can manage
  if (typePrep.user_id !== store.user.id) return false;

  // Check if it's been more than 3 hours
  const createdTime = new Date(typePrep.created);
  const currentTime = new Date();
  const diffInHours = (currentTime - createdTime) / (1000 * 60 * 60);

  return diffInHours <= 3;
}
</script>
<style scoped>
.dashboard-container {
  max-width: 1400px;
  margin: 0 auto;
}

.preparation-card {
  transition: all 0.2s ease-in-out;
  background: var(--bg-surface) !important;
  border: 1px solid var(--border-color) !important;
}

.preparation-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md) !important;
  border-color: var(--primary) !important;
}

.text-muted {
  color: var(--text-muted) !important;
}

.text-danger {
  color: var(--danger) !important;
}

.text-success {
  color: var(--success) !important;
}

.truncate {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.gap-1 {
  gap: 4px;
}

.fab-container {
  position: fixed;
  bottom: 24px;
  left: 24px;
  z-index: 100;
}

:deep(.v-chip) {
  font-weight: 500;
}

.shadow-sm {
  box-shadow: var(--shadow-sm) !important;
}
</style>
