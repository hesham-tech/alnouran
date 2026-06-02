<template>
  <div class="pa-4 dashboard-container">
    <div class="d-flex align-center mb-6">
      <v-icon color="primary" size="32" class="mr-2">mdi-domain</v-icon>
      <h1 class="text-h5 font-weight-bold">المحطات</h1>
    </div>

    <!-- Delete Dialog -->
    <v-dialog v-model="dialogDelete" max-width="400">
      <v-card class="pa-4 text-center">
        <v-icon color="error" size="48" class="mb-2">mdi-delete-alert-outline</v-icon>
        <v-card-title>حذف التقرير؟</v-card-title>
        <div class="d-flex justify-center gap-2 mt-4">
          <v-btn color="secondary" variant="text" @click="dialogDelete = false">إلغاء</v-btn>
          <v-btn color="error" class="rounded-lg" @click="deleteReport">تأكيد الحذف</v-btn>
        </div>
      </v-card>
    </v-dialog>

    <!-- Update Dialog -->
    <v-dialog v-model="dialogUpdate" max-width="550">
      <v-card class="rounded-xl border overflow-hidden bg-surface">
        <div class="pa-6 border-b bg-surface-variant bg-opacity-5 d-flex align-center">
          <v-icon color="primary" class="mr-3">mdi-pencil-box-outline</v-icon>
          <span class="text-h6 font-weight-bold">تعديل التقرير الفني</span>
          <v-spacer></v-spacer>
          <v-btn icon="mdi-close" variant="text" size="small" @click="dialogUpdate = false"></v-btn>
        </div>

        <v-card-text class="pa-6">
          <v-form @submit.prevent="updateReport">
            <v-textarea
              v-model="reportVar.body"
              label="محتوى التقرير"
              variant="outlined"
              rows="5"
              class="mb-4"
              auto-grow
              prepend-inner-icon="mdi-text-subject"
            ></v-textarea>
            
            <v-select
              v-model="reportVar.station_id"
              :items="store.user.stations"
              item-title="name"
              item-value="id"
              label="المحطة التابع لها"
              variant="outlined"
              prepend-inner-icon="mdi-map-marker-outline"
              class="mb-2"
            ></v-select>
          </v-form>
        </v-card-text>

        <div class="pa-6 border-t d-flex justify-end gap-2 bg-surface-variant bg-opacity-5">
          <v-btn color="secondary" variant="text" @click="dialogUpdate = false" class="rounded-lg px-6">إلغاء</v-btn>
          <v-btn color="primary" @click="updateReport" class="rounded-lg px-8 font-weight-bold">حفظ التغييرات</v-btn>
        </div>
      </v-card>
    </v-dialog>

    <!-- Stations Tabs -->
    <v-tabs
      v-model="tab"
      color="primary"
      align-tabs="start"
      class="mb-6 border-b"
      slider-color="primary"
    >
      <v-tab
        v-for="station in store.user.stations"
        :key="station.id"
        :value="station.name"
        @click="gitStation(station)"
        class="text-none font-weight-bold"
      >
        {{ station.name }}
      </v-tab>
    </v-tabs>

    <v-window v-model="tab">
      <v-window-item v-for="station in store.user.stations" :key="station.id" :value="station.name">
        <div v-if="stationIN.reports?.length === 0" class="d-flex flex-column align-center py-12">
          <v-icon size="64" color="secondary" class="mb-4 bg-surface-variant bg-opacity-10 pa-8 rounded-circle">
            mdi-file-hidden
          </v-icon>
          <div class="text-h6 text-muted">لا توجد تقارير لهذه المحطة حالياً</div>
        </div>

        <div v-else class="reports-grid">
          <v-card
            v-for="report in stationIN.reports"
            :key="report.id"
            class="report-card mb-6 pa-0 border rounded-xl overflow-hidden"
          >
            <!-- Report Header -->
            <div class="pa-4 d-flex align-center bg-surface-variant bg-opacity-5">
              <v-avatar color="primary" size="36" class="mr-3 text-white">
                {{ report.user.name.charAt(0) }}
              </v-avatar>
              <div>
                <div class="text-subtitle-2 font-weight-bold">{{ report.user.name }}</div>
                <div class="text-caption text-muted">{{ timeSinceReport(report.created_at) }}</div>
              </div>
              <v-spacer></v-spacer>
              <template v-if="report.user.id == store.user.id">
                <v-btn icon="mdi-pencil-outline" variant="text" size="small" color="secondary" @click="update(report)"></v-btn>
                <v-btn icon="mdi-delete-outline" variant="text" size="small" color="error" @click="deleted(report.id)"></v-btn>
              </template>
            </div>

            <!-- Report Content -->
            <router-link :to="`/report/${report.id}`" class="text-decoration-none">
              <div class="pa-4 text-body-1 report-body">
                {{ report.body }}
              </div>
            </router-link>

            <v-divider></v-divider>

            <!-- Comments Section -->
            <div class="pa-4 bg-surface-variant bg-opacity-5">
              <div class="d-flex align-center mb-4">
                <v-text-field
                  v-model="report.comment"
                  placeholder="إضافة تعليق..."
                  density="compact"
                  hide-details
                  variant="outlined"
                  class="bg-surface rounded-lg mr-2"
                  @keyup.enter="addComment(report)"
                ></v-text-field>
                <v-btn icon="mdi-send" color="primary" size="small" @click="addComment(report)"></v-btn>
              </div>

              <div
                v-for="comment in report.comments.slice(0, report.showComments ? undefined : 1)"
                :key="comment.id"
                class="mb-2 pa-2 bg-surface rounded border"
              >
                <div class="d-flex justify-space-between text-caption mb-1">
                  <span class="font-weight-bold color-primary">{{ comment.user?.name }}</span>
                  <span class="text-muted">{{ timeSinceReport(comment.created_at) }}</span>
                </div>
                <div class="text-body-2">{{ comment.body }}</div>
              </div>

              <v-btn
                v-if="report.comments.length > 1"
                variant="text"
                size="x-small"
                color="primary"
                class="mt-2 text-none"
                @click="report.showComments = !report.showComments"
              >
                {{ report.showComments ? 'إخفاء التعليقات' : `عرض ${report.comments.length - 1} تعليقات أخرى` }}
              </v-btn>
            </div>
          </v-card>
        </div>
      </v-window-item>
    </v-window>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router';
import { usemainStore } from '../../store/mainStore';
import { onMounted, ref } from 'vue';
import axios from 'axios';
import moment from 'moment';

const route = useRoute();
const store = usemainStore();
const stationIN = ref({});
const reportVar = ref({});
const reportId = ref(null);
const dialogDelete = ref(false);
const dialogUpdate = ref(false);
const tab = ref(route.params.id || 0);

onMounted(() => {
  store.getUser().then(() => {
    const stationId = route.params.id || store.user.stations[0]?.id;
    if (stationId) {
      fetchStationData(stationId);
    }
  });
});

function gitStation(station) {
  fetchStationData(station.id);
}

function fetchStationData(id) {
  axios
    .get(`Stations/${id}`)
    .then(res => {
      stationIN.value = res.data;
    })
    .catch(() => {
      stationIN.value = { reports: [] };
      store.startSnack('المحطة غير موجودة', 'no', 'danger');
    });
}

function timeSinceReport(time) {
  return moment(time).locale('ar').fromNow();
}

function update(report) {
  reportVar.value = { ...report };
  dialogUpdate.value = true;
}

function updateReport() {
  axios
    .patch(`reports/${reportVar.value.id}`, reportVar.value)
    .then(() => {
      fetchStationData(stationIN.value.id);
      dialogUpdate.value = false;
      store.startSnack('تم التحديث بنجاح', 'no', 'success');
    })
    .catch(() => {
      store.startSnack('حدث خطأ أثناء التحديث', 'no', 'danger');
    });
}

function deleted(id) {
  reportId.value = id;
  dialogDelete.value = true;
}

function deleteReport() {
  axios.delete(`reports/${reportId.value}`).then(() => {
    dialogDelete.value = false;
    store.startSnack('تم حذف التقرير', 'no', 'success');
    fetchStationData(stationIN.value.id);
  });
}

function addComment(report) {
  if (!report.comment) return;
  const obComment = {
    body: report.comment,
    report_id: report.id,
    user_id: store.user.id,
  };
  axios
    .post(`Comments`, obComment)
    .then(() => {
      fetchStationData(stationIN.value.id);
      report.comment = '';
    })
    .catch(() => {
      store.startSnack('حدث خطأ أثناء إضافة التعليق', 'no', 'danger');
    });
}
</script>

<style scoped>
.report-card {
  transition: all 0.2s ease;
  background: var(--bg-surface) !important;
}

.report-card:hover {
  border-color: var(--primary) !important;
  box-shadow: var(--shadow-sm) !important;
}

.report-body {
  line-height: 1.6;
  color: var(--text-main);
  white-space: pre-wrap;
}

.text-muted {
  color: var(--text-muted) !important;
}

.color-primary {
  color: var(--primary) !important;
}

.gap-2 {
  gap: 8px;
}
</style>
