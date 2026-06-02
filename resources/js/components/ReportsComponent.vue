<template>
  <div class="reports-feed">
    <!-- Delete Dialog -->
    <v-dialog v-model="dialogDelete" max-width="400">
      <v-card class="pa-4 text-center">
        <v-icon color="error" size="48" class="mb-2">mdi-delete-alert-outline</v-icon>
        <v-card-title>هل أنت متأكد من الحذف؟</v-card-title>
        <v-card-text>لا يمكن التراجع عن هذا الإجراء بعد التنفيذ.</v-card-text>
        <div class="d-flex justify-center gap-2 mt-4">
          <v-btn color="secondary" variant="text" @click="dialogDelete = false">إلغاء</v-btn>
          <v-btn color="error" class="rounded-lg" @click="deleteReport">تأكيد الحذف</v-btn>
        </div>
      </v-card>
    </v-dialog>

    <!-- Update Dialog -->
    <v-dialog v-model="dialogUpdate" max-width="600">
      <v-card class="pa-4 card-compact">
        <v-card-title class="mb-4">تعديل التقرير</v-card-title>
        <v-form @submit.prevent="updateReport">
          <v-textarea
            v-model="reportVar.body"
            label="محتوى التقرير"
            rows="5"
            class="mb-4"
          ></v-textarea>
          <v-select
            v-model="reportVar.station_id"
            :items="store.user.stations"
            item-title="name"
            item-value="id"
            label="المحطة"
            class="mb-6"
          ></v-select>
          <div class="d-flex justify-end gap-2">
            <v-btn color="secondary" variant="text" @click="dialogUpdate = false">إلغاء</v-btn>
            <v-btn color="primary" type="submit" class="rounded-lg px-6">حفظ التغييرات</v-btn>
          </div>
        </v-form>
      </v-card>
    </v-dialog>

    <!-- State Messages -->
    <div v-if="store.reports == 'getData'" class="d-flex justify-center py-12">
      <v-progress-circular indeterminate color="primary"></v-progress-circular>
    </div>
    
    <v-alert
      v-else-if="store.reports == 'noData'"
      type="info"
      variant="tonal"
      class="rounded-lg"
      text="لا توجد تقارير منشورة حالياً."
    ></v-alert>

    <!-- Reports List -->
    <div v-else>
      <v-card
        v-for="report in store.reports"
        :key="report.id"
        class="report-card mb-6 pa-0 overflow-hidden border rounded-xl"
      >
        <!-- Header -->
        <div class="pa-4 d-flex align-center justify-space-between report-header">
          <div class="d-flex align-center">
            <v-avatar color="primary" size="36" class="mr-3">
              <span class="text-caption text-white">{{ report.user.name.charAt(0) }}</span>
            </v-avatar>
            <div>
              <div class="text-subtitle-2 font-weight-bold">{{ report.user.name }}</div>
              <div class="text-caption text-muted">{{ timeSinceReport(report.created_at) }}</div>
            </div>
          </div>
          <v-chip size="small" variant="tonal" color="primary">
            {{ report.station.name }}
          </v-chip>
        </div>

        <!-- Body -->
        <router-link :to="`/report/${report.id}`" class="text-decoration-none">
          <div class="pa-4 text-body-1 report-body">
            {{ report.body }}
          </div>
        </router-link>

        <v-divider></v-divider>

        <!-- Actions Bar -->
        <div class="px-4 py-2 d-flex align-center">
          <v-btn
            variant="text"
            density="compact"
            prepend-icon="mdi-comment-outline"
            size="small"
            color="secondary"
            class="text-none mr-4"
            @click="report.showComments = !report.showComments"
          >
            {{ report.comments.length }} تعليقات
          </v-btn>

          <v-btn
            variant="text"
            density="compact"
            prepend-icon="mdi-share-variant-outline"
            size="small"
            color="secondary"
            class="text-none"
            @click="share(report)"
          >
            مشاركة
          </v-btn>

          <v-spacer></v-spacer>

          <template v-if="report.user.id == store.user.id || store.user.roles == 'admin'">
            <v-btn
              v-if="report.user.id == store.user.id"
              icon="mdi-pencil-outline"
              variant="text"
              size="small"
              color="secondary"
              @click="update(report)"
            ></v-btn>
            <v-btn
              icon="mdi-delete-outline"
              variant="text"
              size="small"
              color="error"
              @click="deleted(report.id)"
            ></v-btn>
          </template>
        </div>

        <!-- Comments Section -->
        <v-expand-transition>
          <div v-if="report.showComments" class="report-comments pa-4 border-t">
            <!-- Comment Input -->
            <div class="d-flex align-center mb-4">
              <v-text-field
                v-model="report.comment"
                placeholder="اكتب تعليقاً..."
                density="compact"
                hide-details
                variant="outlined"
                class="bg-surface rounded-lg mr-2"
                @keyup.enter="addComment(report)"
              ></v-text-field>
              <v-btn
                icon="mdi-send"
                color="primary"
                size="small"
                elevation="0"
                @click="addComment(report)"
              ></v-btn>
            </div>

            <!-- Comments List -->
            <div
              v-for="comment in report.comments"
              :key="comment.id"
              class="comment-item mb-3 pa-3 rounded-lg bg-surface border"
            >
              <div class="d-flex justify-space-between align-center mb-1">
                <span class="text-caption font-weight-bold color-primary">{{ comment.user.name }}</span>
                <span class="text-caption text-muted">{{ timeSinceReport(comment.created_at) }}</span>
              </div>
              <div class="text-body-2">{{ comment.body }}</div>
            </div>
          </div>
        </v-expand-transition>
      </v-card>
    </div>
  </div>
</template>

<script setup>
import { usemainStore } from '@/store/mainStore';
import { onMounted, ref } from 'vue';
import axios from 'axios';
import moment from 'moment';

const store = usemainStore();
const reportId = ref(null);
const reportVar = ref({});
const dialogDelete = ref(false);
const dialogUpdate = ref(false);

onMounted(() => {
  store.getReports();
});

function share(report) {
  const obComment = {
    title: `تقرير من ${report.user.name}`,
    text: report.body,
    url: window.location.origin + '/report/' + report.id,
  };
  if (navigator.share) {
    navigator.share(obComment);
  } else {
    // Fallback: Copy to clipboard
    navigator.clipboard.writeText(`${report.body}\n\nLink: ${obComment.url}`);
    store.startSnack('تم نسخ الرابط للحافظة', 'no', 'info');
  }
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
      store.getReports();
      dialogUpdate.value = false;
      store.startSnack('تم تحديث التقرير بنجاح', 'no', 'success');
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
    store.getReports();
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
      store.getReports();
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
  box-shadow: var(--shadow-md) !important;
}

.report-body {
  color: var(--text-main);
  line-height: 1.6;
  white-space: pre-wrap;
}

.text-muted {
  color: var(--text-muted) !important;
}

.gap-2 {
  gap: 8px;
}

.color-primary {
  color: var(--primary) !important;
}

.report-header {
  background-color: rgba(79, 70, 229, 0.04); /* Light indigo tint */
}

.report-comments {
  background-color: rgba(79, 70, 229, 0.02); /* Very light indigo tint */
}

[data-v-theme="dark"] .report-header {
  background-color: rgba(255, 255, 255, 0.03);
}

[data-v-theme="dark"] .report-comments {
  background-color: rgba(255, 255, 255, 0.01);
}
</style>
