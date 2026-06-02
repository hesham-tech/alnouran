<template>
  <div class="pa-4 dashboard-container" v-if="user && user !== 'noUser'">
    <!-- Profile Header -->
    <v-card class="mb-6 border rounded-xl overflow-hidden bg-surface elevation-2">
      <div class="profile-header-bg pa-8 d-flex flex-column align-center text-white">
        <v-avatar color="white" size="96" class="mb-4 elevation-4">
          <v-icon size="48" color="primary">mdi-account</v-icon>
        </v-avatar>
        <h1 class="text-h4 font-weight-bold mb-1">{{ user.name }}</h1>
        <v-chip color="rgba(255,255,255,0.2)" class="text-white backdrop-blur font-weight-bold">
          {{ user.Job_title }}
        </v-chip>
      </div>

      <!-- Balances Row -->
      <div class="pa-4 d-flex justify-center flex-wrap gap-4 border-b">
        <v-card v-if="user.rest_balance" class="pa-4 rounded-lg border flex-1 text-center min-w-150" variant="tonal" color="info">
          <div class="text-caption font-weight-bold mb-1">بدل راحة</div>
          <div class="text-h5 font-weight-black">{{ user.rest_balance.balance }}</div>
        </v-card>
        
        <v-card v-if="user.regular_balance" class="pa-4 rounded-lg border flex-1 text-center min-w-150" variant="tonal" color="primary">
          <div class="text-caption font-weight-bold mb-1">الرصيد الاعتيادي</div>
          <div class="text-h5 font-weight-black">{{ user.regular_balance.balance }}</div>
        </v-card>
      </div>
    </v-card>

    <v-row>
      <!-- Activity Tabs -->
      <v-col cols="12" md="8">
        <v-card class="border rounded-xl bg-surface">
          <v-tabs v-model="mainTab" color="primary" align-tabs="start" class="border-b px-4">
            <v-tab value="reports" class="text-none font-weight-bold">التقارير الفنية</v-tab>
            <v-tab value="absences" class="text-none font-weight-bold">الاجازات</v-tab>
            <v-tab value="allowances" class="text-none font-weight-bold">البدلات</v-tab>
          </v-tabs>

          <v-window v-model="mainTab" class="pa-4">
            <!-- Reports Tab -->
            <v-window-item value="reports">
              <v-tabs v-model="stationTab" density="compact" class="mb-4" color="secondary">
                <v-tab v-for="station in user.stations" :key="station.id" :value="station.name">
                  {{ station.name }}
                </v-tab>
              </v-tabs>
              
              <v-window v-model="stationTab">
                <v-window-item v-for="station in user.stations" :key="station.id" :value="station.name">
                  <div v-if="station.reports?.length === 0" class="text-center py-8 text-muted">
                    لا توجد تقارير منشورة في هذه المحطة.
                  </div>
                  <div v-else>
                    <v-card
                      v-for="report in station.reports"
                      :key="report.id"
                      class="mb-4 pa-4 border rounded-lg bg-surface-variant bg-opacity-5"
                    >
                      <div class="d-flex justify-space-between mb-2">
                        <span class="text-caption text-muted">{{ date(report.created_at) }}</span>
                        <v-btn icon="mdi-open-in-new" variant="text" size="x-small" :to="`/report/${report.id}`"></v-btn>
                      </div>
                      <div class="text-body-2 mb-3 white-space-pre">{{ report.body }}</div>
                      <div class="d-flex align-center gap-2">
                        <v-icon size="small" color="secondary">mdi-comment-outline</v-icon>
                        <span class="text-caption">{{ report.comments.length }} تعليق</span>
                      </div>
                    </v-card>
                  </div>
                </v-window-item>
              </v-window>
            </v-window-item>

            <!-- Absences Tab -->
            <v-window-item value="absences">
              <v-table density="comfortable" hover class="modern-table border rounded-lg overflow-hidden">
                <thead>
                  <tr>
                    <th>النوع</th>
                    <th>التاريخ</th>
                    <th>الوصف</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="absence in user.absences" :key="absence.id">
                    <td><v-chip size="x-small" variant="flat" color="primary">{{ $t(absence.Type) }}</v-chip></td>
                    <td class="text-body-2">{{ absence.date }}</td>
                    <td class="text-caption text-muted">{{ absence.description }}</td>
                  </tr>
                </tbody>
              </v-table>
              <div v-if="user.absences?.length === 0" class="text-center py-8 text-muted">لا توجد اجازات مسجلة.</div>
            </v-window-item>

            <!-- Allowances Tab -->
            <v-window-item value="allowances">
              <v-table density="comfortable" hover class="modern-table border rounded-lg overflow-hidden" v-if="user.restallowance">
                <thead>
                  <tr>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th>الوصف</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><v-chip size="x-small" variant="flat" color="info">{{ user.restallowance.state }}</v-chip></td>
                    <td class="text-body-2">{{ user.restallowance.date }}</td>
                    <td class="text-caption text-muted">{{ user.restallowance.description }}</td>
                  </tr>
                </tbody>
              </v-table>
              <div v-if="!user.restallowance" class="text-center py-8 text-muted">لا يوجد بدل راحة متاح.</div>
            </v-window-item>
          </v-window>
        </v-card>
      </v-col>

      <!-- Sidebar Info -->
      <v-col cols="12" md="4">
        <v-card class="pa-4 border rounded-xl bg-surface elevation-1 mb-6">
          <div class="text-subtitle-1 font-weight-bold mb-4 border-b pb-2">معلومات التواصل</div>
          <div class="mb-4">
            <div class="text-caption text-muted mb-1">البريد الإلكتروني</div>
            <div class="text-body-2 font-weight-bold">{{ user.email }}</div>
          </div>
          <div>
            <div class="text-caption text-muted mb-1">رقم الهاتف</div>
            <div class="text-body-2 font-weight-bold">{{ user.phone }}</div>
          </div>
        </v-card>

        <v-card class="pa-4 border rounded-xl bg-surface elevation-1">
          <div class="text-subtitle-1 font-weight-bold mb-4 border-b pb-2">المحطات المسؤولة</div>
          <div class="d-flex flex-wrap gap-2">
            <v-chip v-for="station in user.stations" :key="station.id" variant="tonal" size="small" color="secondary">
              {{ station.name }}
            </v-chip>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </div>

  <div v-else-if="user === 'noUser'" class="pa-12 text-center">
    <v-icon size="64" color="error" class="mb-4">mdi-account-alert-outline</v-icon>
    <div class="text-h6">المستخدم غير موجود</div>
    <v-btn to="/users" variant="text" color="primary" class="mt-4">العودة للمستخدمين</v-btn>
  </div>
</template>

<script setup>
import { usemainStore } from '@/store/mainStore';
import moment from 'moment';
import axios from 'axios';
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';

const store = usemainStore();
const route = useRoute();
const user = ref(null);
const mainTab = ref('reports');
const stationTab = ref(null);

onMounted(() => {
  axios
    .get(`users/${route.params.id}`)
    .then(res => {
      user.value = res.data;
      if (user.value.stations?.length > 0) {
        stationTab.value = user.value.stations[0].name;
      }
    })
    .catch(() => {
      user.value = 'noUser';
    });
});

function timeSinceReport(time) {
  return moment(time).locale('ar').fromNow();
}

function date(d) {
  return moment(d).locale('ar').format('dddd :- h:mm A - MM/DD');
}
</script>

<style scoped>
.profile-header-bg {
  background: linear-gradient(135deg, var(--primary) 0%, #312e81 100%);
}

.white-space-pre {
  white-space: pre-wrap;
}

.gap-4 { gap: 16px; }
.gap-2 { gap: 8px; }

.min-w-150 { min-width: 150px; }

.text-muted { color: var(--text-muted) !important; }

.modern-table thead th {
  background: var(--bg-main) !important;
  color: var(--text-muted) !important;
  font-weight: 700 !important;
  font-size: 0.75rem !important;
}

.backdrop-blur {
  backdrop-filter: blur(8px);
}
</style>
