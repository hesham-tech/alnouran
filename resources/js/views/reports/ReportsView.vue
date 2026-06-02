<template>
  <div class="pa-4 dashboard-container">
    <div class="d-flex justify-space-between align-center mb-6">
      <div class="d-flex align-center">
        <v-icon color="primary" size="32" class="mr-2">mdi-file-chart-outline</v-icon>
        <h1 class="text-h5 font-weight-bold">التقارير الفنية</h1>
      </div>
      <v-btn
        color="primary"
        prepend-icon="mdi-plus"
        @click="dialog = true"
        class="text-none rounded-lg"
      >
        إضافة تقرير
      </v-btn>
    </div>

    <!-- Add Report Dialog -->
    <v-dialog v-model="dialog" max-width="600">
      <v-card class="pa-4 card-compact">
        <v-card-title class="text-center mb-4">كتابة تقرير جديد</v-card-title>
        
        <v-form @submit.prevent="addReport">
          <v-textarea
            v-model="newReport.body"
            label="محتوى التقرير"
            placeholder="اكتب ملاحظاتك هنا..."
            rows="5"
            variant="outlined"
            class="mb-4"
          ></v-textarea>

          <v-select
            v-model="newReport.station_id"
            :items="stations"
            item-title="name"
            item-value="id"
            label="المحطة المعنية"
            class="mb-6"
          ></v-select>

          <div class="d-flex justify-end gap-2">
            <v-btn color="secondary" variant="text" @click="dialog = false">إلغاء</v-btn>
            <v-btn color="primary" type="submit" class="rounded-lg px-6">نشر التقرير</v-btn>
          </div>
        </v-form>
      </v-card>
    </v-dialog>

    <ReportsComponent />
  </div>
</template>

<script setup>
import ReportsComponent from '@/components/ReportsComponent.vue';
import { usemainStore } from '@/store/mainStore';
import { onMounted, ref } from 'vue';
import axios from 'axios';

const store = usemainStore();
const stations = ref([]);
const newReport = ref({
  body: '',
  station_id: null
});
const dialog = ref(false);

function addReport() {
  const obReport = {
    body: newReport.value.body,
    station_id: newReport.value.station_id,
    user_id: store.user.id,
  };
  
  axios
    .post(`reports`, obReport)
    .then(() => {
      store.getReports();
      dialog.value = false;
      store.startSnack('تمت إضافة التقرير بنجاح', 'no', 'success');
      newReport.value.body = '';
    })
    .catch(() => {
      store.startSnack('حدث خطأ أثناء الإضافة', 'no', 'danger');
    });
}

onMounted(() => {
  store.getUser();
  axios
    .get(`Stations?current_user=${store.user.id}`)
    .then(res => {
      stations.value = res.data;
      if (stations.value.length > 0) {
        newReport.value.station_id = stations.value[0].id;
      }
    })
    .catch(() => {
      store.startSnack('حدث خطأ في تحميل المحطات', 'no', 'danger');
    });
});
</script>

<style scoped>
.gap-2 {
  gap: 8px;
}
</style>
