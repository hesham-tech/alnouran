<template>
  <div class="pa-4 dashboard-container">
    <div class="d-flex align-center mb-6">
      <v-btn icon="mdi-arrow-right" variant="text" @click="routerNav.back()" class="mr-2"></v-btn>
      <h1 class="text-h5 font-weight-bold">تفاصيل التقرير</h1>
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
    <v-dialog v-model="dialogUpdate" max-width="600">
      <v-card class="pa-4 card-compact">
        <v-card-title class="mb-4">تعديل التقرير</v-card-title>
        <v-form @submit.prevent="updateReport">
          <v-textarea v-model="reportVar.body" label="محتوى التقرير" rows="5" class="mb-4"></v-textarea>
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
            <v-btn color="primary" type="submit" class="rounded-lg px-6">حفظ</v-btn>
          </div>
        </v-form>
      </v-card>
    </v-dialog>

    <!-- State Messages -->
    <div v-if="report == 'getData'" class="d-flex justify-center py-12">
      <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
    </div>

    <v-alert v-else-if="report == 'noreport'" type="warning" variant="tonal" class="rounded-lg">
      هذا التقرير غير موجود أو تم حذفه.
    </v-alert>

    <!-- Report Details -->
    <v-row v-else>
      <v-col cols="12" md="8">
        <v-card class="pa-6 border rounded-xl bg-surface mb-6">
          <div class="d-flex align-center justify-space-between mb-6">
            <div class="d-flex align-center">
              <v-avatar color="primary" size="48" class="mr-3 text-white">
                {{ report.user.name.charAt(0) }}
              </v-avatar>
              <div>
                <div class="text-h6 font-weight-bold">{{ report.user.name }}</div>
                <div class="text-caption text-muted">
                  {{ date(report.created_at) }} • {{ timeSinceReport(report.created_at) }}
                </div>
              </div>
            </div>
            <v-chip color="primary" variant="tonal" class="font-weight-bold">
              {{ report.station.name }}
            </v-chip>
          </div>

          <div class="report-body-text text-body-1 mb-8">
            {{ report.body }}
          </div>

          <v-divider class="mb-4"></v-divider>

          <div class="d-flex align-center">
            <v-icon color="secondary" class="mr-2">mdi-comment-outline</v-icon>
            <span class="text-subtitle-2 font-weight-bold">{{ report.comments.length }} تعليقات</span>
            
            <v-spacer></v-spacer>

            <template v-if="report.user.id == store.user.id || store.user.roles == 'admin'">
              <v-btn
                v-if="report.user.id == store.user.id"
                icon="mdi-pencil-outline"
                variant="text"
                color="secondary"
                @click="update(report)"
              ></v-btn>
              <v-btn icon="mdi-delete-outline" variant="text" color="error" @click="deleted"></v-btn>
            </template>
          </div>
        </v-card>

        <!-- Comments List -->
        <div class="section-title mb-4 font-weight-bold">التعليقات</div>
        
        <!-- Add Comment -->
        <v-card class="pa-4 border rounded-xl mb-4 bg-surface">
          <div class="d-flex align-center">
            <v-text-field
              v-model="report.comment"
              placeholder="أضف تعليقاً..."
              variant="outlined"
              density="compact"
              hide-details
              class="mr-2"
              @keyup.enter="addComment(report)"
            ></v-text-field>
            <v-btn color="primary" icon="mdi-send" @click="addComment(report)"></v-btn>
          </div>
        </v-card>

        <v-card
          v-for="comment in report.comments"
          :key="comment.id"
          class="pa-4 mb-3 border rounded-lg bg-surface"
        >
          <div class="d-flex justify-space-between align-center mb-2">
            <span class="font-weight-bold text-primary">{{ comment.user.name }}</span>
            <span class="text-caption text-muted">{{ timeSinceReport(comment.created_at) }}</span>
          </div>
          <div class="text-body-2">{{ comment.body }}</div>
        </v-card>
      </v-col>

      <!-- Sidebar Info (Optional) -->
      <v-col cols="12" md="4">
        <v-card class="pa-4 border rounded-xl bg-surface-variant bg-opacity-10">
          <div class="text-subtitle-1 font-weight-bold mb-4">إحصائيات التقرير</div>
          <div class="d-flex justify-space-between mb-2">
            <span class="text-muted">الحالة</span>
            <v-chip size="x-small" color="success">نشط</v-chip>
          </div>
          <div class="d-flex justify-space-between mb-2">
            <span class="text-muted">المشاهدات</span>
            <span class="font-weight-bold">---</span>
          </div>
          <div class="d-flex justify-space-between">
            <span class="text-muted">التفاعلات</span>
            <span class="font-weight-bold">{{ report.comments.length }}</span>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router';
import { usemainStore } from '../../store/mainStore';
import { onMounted, ref } from 'vue';
import axios from 'axios';
import moment from 'moment';

const route = useRoute();
const routerNav = useRouter();
const store = usemainStore();
const report = ref('getData');
const reportVar = ref({});
const dialogDelete = ref(false);
const dialogUpdate = ref(false);

function getReport() {
  axios
    .get(`reports/${route.params.id}`)
    .then(res => {
      report.value = res.data;
    })
    .catch(() => {
      report.value = 'noreport';
    });
}

onMounted(() => {
  getReport();
});

function update(reportData) {
  reportVar.value = { ...reportData };
  dialogUpdate.value = true;
}

function updateReport() {
  axios
    .patch(`reports/${reportVar.value.id}`, reportVar.value)
    .then(() => {
      getReport();
      dialogUpdate.value = false;
      store.startSnack('تم تحديث التقرير بنجاح', 'no', 'success');
    })
    .catch(() => {
      store.startSnack('حدث خطأ أثناء التحديث', 'no', 'danger');
    });
}

function timeSinceReport(time) {
  return moment(time).locale('ar').fromNow();
}

function date(d) {
  return moment(d).locale('ar').format('dddd :- h:mm A - MM/DD');
}

function addComment(reportData) {
  if (!reportData.comment) return;
  const obComment = {
    body: reportData.comment,
    report_id: reportData.id,
    user_id: store.user.id,
  };
  axios
    .post(`Comments`, obComment)
    .then(() => {
      getReport();
      reportData.comment = '';
    })
    .catch(() => {
      store.startSnack('حدث خطأ أثناء إضافة التعليق', 'no', 'danger');
    });
}

function deleted() {
  dialogDelete.value = true;
}

function deleteReport() {
  axios.delete(`reports/${route.params.id}`).then(() => {
    dialogDelete.value = false;
    store.startSnack('تم حذف التقرير', 'no', 'success');
    routerNav.push('/reports');
  });
}
</script>

<style scoped>
.report-body-text {
  line-height: 1.8;
  color: var(--text-main);
  white-space: pre-wrap;
}

.text-muted {
  color: var(--text-muted) !important;
}

.gap-2 {
  gap: 8px;
}
</style>
