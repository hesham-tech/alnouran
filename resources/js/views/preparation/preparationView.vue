<template>
  <div class="pa-4 dashboard-container">
    <div class="d-flex justify-space-between align-center mb-6">
      <div class="d-flex align-center">
        <v-icon color="primary" size="32" class="mr-2">mdi-flask-outline</v-icon>
        <h1 class="text-h5 font-weight-bold">إدارة التحضيرات</h1>
      </div>
      <div class="d-flex gap-2">
        <v-btn
          color="primary"
          prepend-icon="mdi-plus"
          @click="dialogAddTypePreparation = true"
          class="text-none rounded-lg"
        >
          نوع تحضيرة جديد
        </v-btn>
        <addPreparationComponent />
      </div>
    </div>

    <!-- Add Type Preparation Dialog -->
    <v-dialog v-model="dialogAddTypePreparation" max-width="500">
      <v-card class="pa-4 card-compact">
        <v-card-title class="text-center mb-4">إضافة نوع تحضيرة جديد</v-card-title>
        
        <v-form @submit.prevent="addTypePreparation">
          <v-text-field
            v-model="newTypePreparation.name"
            label="اسم نوع التحضيرة"
            placeholder="مثال: تحضيرة الجسم الأول"
            class="mb-4"
          ></v-text-field>

          <v-text-field
            v-model="newTypePreparation.description"
            label="وصف التحضيرة"
            placeholder="وصف مختصر للغرض من التحضيرة"
            class="mb-4"
          ></v-text-field>

          <v-select
            v-model="newTypePreparation.station_id"
            :items="stations"
            item-title="name"
            item-value="id"
            label="المحطة التابعة لها"
            class="mb-6"
          ></v-select>

          <div class="d-flex justify-end gap-2">
            <v-btn color="secondary" variant="text" @click="dialogAddTypePreparation = false">إلغاء</v-btn>
            <v-btn color="primary" type="submit" class="rounded-lg px-6">إضافة</v-btn>
          </div>
        </v-form>
      </v-card>
    </v-dialog>

    <!-- Loading State -->
    <div v-if="store.overlay" class="d-flex justify-center py-12">
      <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
    </div>

    <!-- Content -->
    <div v-else>
      <v-expansion-panels variant="accordion" class="custom-panels">
        <v-expansion-panel
          v-for="typePrep in typePreparationAll"
          :key="typePrep.id"
          class="mb-2 border rounded-lg overflow-hidden"
        >
          <v-expansion-panel-title class="py-4">
            <template v-slot:default="{ expanded }">
              <div class="d-flex align-center w-100">
                <v-icon
                  :color="expanded ? 'primary' : 'secondary'"
                  class="mr-3"
                >
                  {{ expanded ? 'mdi-flask-empty' : 'mdi-flask' }}
                </v-icon>
                <span class="font-weight-bold">{{ typePrep.name }}</span>
                <v-spacer></v-spacer>
                <v-chip size="x-small" color="primary" variant="tonal" class="mr-4">
                  {{ typePrep.preparations?.length || 0 }} تحضيرة
                </v-chip>
              </div>
            </template>
          </v-expansion-panel-title>

          <v-expansion-panel-text class="pa-0">
            <v-table density="compact" hover class="modern-table">
              <thead>
                <tr>
                  <th>وقت التحضير</th>
                  <th>طن الشرائح</th>
                  <th>الوردية</th>
                  <th>الكمية (كجم)</th>
                  <th>الساعات</th>
                  <th>ppm</th>
                  <th>المسؤول</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="preparation in typePrep.preparations" :key="preparation.id">
                  <td class="font-weight-medium">{{ date(preparation.actual_time) }}</td>
                  <td>{{ preparation.slices_ton }}</td>
                  <td>
                    <v-chip size="x-small" variant="outlined" color="secondary">
                      {{ preparation.shift }}
                    </v-chip>
                  </td>
                  <td>{{ preparation.quantity }}</td>
                  <td>{{ preparation.cont_hours }}</td>
                  <td>
                    <v-chip size="x-small" color="info" variant="tonal">
                      {{ preparation.ppm }}
                    </v-chip>
                  </td>
                  <td class="text-muted">{{ preparation.user?.name || 'N/A' }}</td>
                </tr>
              </tbody>
            </v-table>
          </v-expansion-panel-text>
        </v-expansion-panel>
      </v-expansion-panels>
    </div>
  </div>
</template>

<script setup>
import addPreparationComponent from '../../components/preparation/addPreparationComponent.vue';
import { usemainStore } from '../../store/mainStore';
import { onMounted, ref } from 'vue';
import axios from 'axios';
import moment from 'moment';

const store = usemainStore();
const newTypePreparation = ref({ 
  user_id: `${store.user.id}`,
  name: '',
  description: '',
  station_id: null
});
const dialogAddTypePreparation = ref(false);
const stations = ref([]);
const typePreparationAll = ref([]);

onMounted(() => {
  store.overlay = true;
  
  // Fetch Stations
  axios
    .get(`Stations?current_user=${store.user.id}`)
    .then(res => {
      stations.value = res.data;
      if (stations.value.length > 0) {
        newTypePreparation.value.station_id = stations.value[0].id;
      }
    })
    .catch(() => {
      store.startSnack('حدث خطأ في تحميل المحطات', 'no', 'danger');
    });

  // Fetch Preparations
  axios.get(`typePre?relation=preparations.user`).then(res => {
    typePreparationAll.value = res.data;
    store.overlay = false;
  });
});

function date(d) {
  return moment(d).locale('ar').format('dddd :- h:mm A - MM/DD');
}

function addTypePreparation() {
  axios
    .post(`typePre`, newTypePreparation.value)
    .then(() => {
      newTypePreparation.value = { 
        user_id: `${store.user.id}`,
        name: '',
        description: '',
        station_id: stations.value[0]?.id
      };
      dialogAddTypePreparation.value = false;
      store.getTypePre('preparations.user').then(data => {
        typePreparationAll.value = data;
      });
      store.startSnack('تمت إضافة نوع التحضيرة بنجاح', 'no', 'success');
    })
    .catch(() => {
      store.startSnack('حدث خطأ أثناء الإضافة', 'no', 'danger');
    });
}
</script>

<style scoped>
.modern-table {
  background: transparent !important;
}

.modern-table thead th {
  background: var(--bg-main) !important;
  color: var(--text-muted) !important;
  text-transform: uppercase;
  font-size: 0.75rem !important;
  font-weight: 700 !important;
  letter-spacing: 0.05em;
  border-bottom: 1px solid var(--border-color) !important;
}

.modern-table tbody td {
  border-bottom: 1px solid var(--border-color) !important;
  font-size: 0.875rem !important;
}

.custom-panels :deep(.v-expansion-panel-text__wrapper) {
  padding: 0 !important;
}

.gap-2 {
  gap: 8px;
}

.text-muted {
  color: var(--text-muted) !important;
}
</style>
