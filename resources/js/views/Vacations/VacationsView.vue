<template>
  <div class="pa-4 dashboard-container">
    <div class="d-flex justify-space-between align-center mb-6">
      <div class="d-flex align-center">
        <v-icon color="primary" size="32" class="mr-2">mdi-calendar-clock</v-icon>
        <h1 class="text-h5 font-weight-bold">الإجازات والغياب</h1>
      </div>
      <div class="d-flex gap-2">
        <v-btn
          icon="mdi-cog-outline"
          variant="text"
          color="secondary"
          @click="dialogSettings = true"
        ></v-btn>
        <AddVacationComponent />
      </div>
    </div>

    <!-- Settings Dialog -->
    <v-dialog v-model="dialogSettings" max-width="600">
      <v-card class="pa-4 border rounded-xl bg-surface">
        <v-card-title class="mb-4">إعدادات الإجازات</v-card-title>
        <v-card-text>
          <addRegularComponent />
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" variant="text" @click="dialogSettings = false">إغلاق</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Balance Stats -->
    <v-row class="mb-6">
      <v-col cols="12" sm="6" md="4">
        <v-card class="pa-6 border-primary-lg rounded-xl bg-surface elevation-3 h-100 active-balance-card">
          <div class="d-flex align-center mb-2">
            <v-icon color="primary" class="mr-2">mdi-calendar-check</v-icon>
            <span class="text-subtitle-2 font-weight-bold">الرصيد الاعتيادي</span>
            <v-spacer></v-spacer>
            <v-chip size="x-small" color="primary" variant="flat">العرض الحالي</v-chip>
          </div>
          <div class="text-h4 font-weight-black color-primary">{{ store.regular }} <small class="text-caption">أيام</small></div>
        </v-card>
      </v-col>
      <v-col cols="12" sm="6" md="4">
        <v-card
          to="/rest"
          class="pa-6 border rounded-xl bg-surface-variant bg-opacity-5 elevation-1 hover-card h-100"
        >
          <div class="d-flex align-center mb-2 text-decoration-none">
            <v-icon color="secondary" class="mr-2">mdi-clock-fast</v-icon>
            <span class="text-subtitle-2 font-weight-bold">بدل الراحة</span>
          </div>
          <div class="text-h4 font-weight-black color-secondary">{{ store.rest }} <small class="text-caption">أيام</small></div>
          <div class="text-caption mt-1 text-muted">اضغط لعرض التفاصيل ←</div>
        </v-card>
      </v-col>
    </v-row>

    <!-- Vacations Table -->
    <v-card class="border rounded-xl overflow-hidden elevation-1">
      <v-table hover density="comfortable" class="modern-table">
        <thead>
          <tr>
            <th class="text-right">#</th>
            <th class="text-right">نوع الإجازة</th>
            <th class="text-right">تاريخ الإجازة</th>
            <th class="text-right">الوصف</th>
            <th class="text-right">تاريخ الطلب</th>
            <th class="text-center">إجراءات</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(absence, index) in store.absences" :key="absence.id">
            <td>{{ index + 1 }}</td>
            <td>
              <v-chip size="x-small" :color="getTypeColor(absence.Type)" variant="flat" class="font-weight-bold">
                {{ $t(absence.Type) }}
              </v-chip>
            </td>
            <td class="font-weight-bold text-body-2">{{ absence.date }}</td>
            <td class="text-caption text-muted">{{ absence.description }}</td>
            <td class="text-caption">{{ store.formatDate(absence.created_at) }}</td>
            <td class="text-center">
              <v-menu location="bottom end">
                <template v-slot:activator="{ props }">
                  <v-btn icon="mdi-dots-vertical" variant="text" size="small" v-bind="props"></v-btn>
                </template>
                <v-list density="compact" class="rounded-lg elevation-4">
                  <v-list-item
                    prepend-icon="mdi-pencil-outline"
                    title="تعديل"
                    @click="showEditNotice"
                  ></v-list-item>
                  <v-divider class="my-1"></v-divider>
                  <v-list-item
                    prepend-icon="mdi-delete-outline"
                    title="حذف"
                    color="error"
                    @click="confirmDelete(absence)"
                  ></v-list-item>
                </v-list>
              </v-menu>
            </td>
          </tr>
        </tbody>
      </v-table>

      <div v-if="store.absences.length === 0" class="pa-12 text-center">
        <v-icon size="64" color="secondary" class="mb-4 bg-surface-variant bg-opacity-10 pa-8 rounded-circle">
          mdi-calendar-remove
        </v-icon>
        <div class="text-h6 text-muted">لا يوجد سجل إجازات متاح حالياً</div>
      </div>
    </v-card>

    <!-- Delete Confirmation -->
    <v-dialog v-model="dialogDelete" max-width="400">
      <v-card class="pa-4 text-center">
        <v-icon color="error" size="48" class="mb-2">mdi-delete-alert-outline</v-icon>
        <v-card-title>حذف الطلب؟</v-card-title>
        <v-card-text>هل تريد حقاً حذف سجل الإجازة بتاريخ <strong>{{ absenceActive?.date }}</strong>؟</v-card-text>
        <div class="d-flex justify-center gap-2 mt-4">
          <v-btn color="secondary" variant="text" @click="dialogDelete = false">إلغاء</v-btn>
          <v-btn color="error" class="rounded-lg" @click="funDelete">تأكيد الحذف</v-btn>
        </div>
      </v-card>
    </v-dialog>

    <!-- Notice Dialog -->
    <v-dialog v-model="dialogNotice" max-width="400">
      <v-card class="pa-4 text-center">
        <v-icon color="info" size="48" class="mb-2">mdi-information-outline</v-icon>
        <v-card-title>تنبيه</v-card-title>
        <v-card-text>خاصية التعديل المباشر غير متاحة حالياً. يرجى حذف الطلب وإضافته من جديد.</v-card-text>
        <v-btn color="primary" variant="text" @click="dialogNotice = false" class="mt-4">حسناً</v-btn>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import addRegularComponent from '../../components/Vacations/addRegularComponent.vue';
import AddVacationComponent from '../../components/Vacations/AddVacationComponent.vue';
import { usemainStore } from '../../store/mainStore';
import { onMounted, ref } from 'vue';
import axios from 'axios';

const store = usemainStore();
const dialogDelete = ref(false);
const dialogSettings = ref(false);
const dialogNotice = ref(false);
const absenceActive = ref(null);

onMounted(() => {
  store.getAbsences();
});

function getTypeColor(type) {
  const colors = {
    'Regular': 'primary',
    'Sick': 'error',
    'Emergency': 'warning',
    'Rest': 'secondary'
  };
  return colors[type] || 'info';
}

function showEditNotice() {
  dialogNotice.value = true;
}

function confirmDelete(absence) {
  absenceActive.value = absence;
  dialogDelete.value = true;
}

function funDelete() {
  axios
    .delete(`absence/${absenceActive.value.id}`)
    .then(() => {
      store.getAbsences();
      dialogDelete.value = false;
      store.startSnack('تم حذف سجل الإجازة بنجاح', 'no', 'success');
    })
    .catch(() => {
      store.startSnack('حدث خطأ أثناء الحذف', 'no', 'danger');
    });
}
</script>

<style scoped>
.modern-table {
  background: var(--bg-surface) !important;
}

.modern-table thead th {
  background: var(--bg-main) !important;
  color: var(--text-muted) !important;
  font-weight: 700 !important;
  font-size: 0.8rem !important;
  border-bottom: 1px solid var(--border-color) !important;
}

.hover-card {
  transition: all 0.2s ease;
  cursor: pointer;
  text-decoration: none !important;
}

.hover-card:hover {
  transform: translateY(-4px);
  border-color: var(--secondary) !important;
}

.color-primary { color: var(--primary) !important; }
.color-secondary { color: var(--secondary) !important; }

.text-muted { color: var(--text-muted) !important; }

.border-primary-lg {
  border: 2px solid var(--primary) !important;
}

.active-balance-card {
  position: relative;
  overflow: hidden;
}

.active-balance-card::before {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 4px;
  height: 100%;
  background: var(--primary);
}

.gap-2 { gap: 8px; }
</style>
