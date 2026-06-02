<template>
  <div class="pa-4 dashboard-container">
    <div class="d-flex justify-space-between align-center mb-6">
      <div class="d-flex align-center">
        <v-icon color="primary" size="32" class="mr-2">mdi-clock-fast</v-icon>
        <h1 class="text-h5 font-weight-bold">بدلات الراحة</h1>
      </div>
      <AddVRestallowanceComponent />
    </div>

    <!-- Balance Stats -->
    <v-row class="mb-6">
      <v-col cols="12" sm="6" md="4">
        <v-card
          to="/vacations"
          class="pa-6 border rounded-xl bg-surface-variant bg-opacity-5 elevation-1 hover-card h-100"
        >
          <div class="d-flex align-center mb-2 text-decoration-none">
            <v-icon color="primary" class="mr-2">mdi-calendar-check</v-icon>
            <span class="text-subtitle-2 font-weight-bold">الرصيد الاعتيادي</span>
          </div>
          <div class="text-h4 font-weight-black color-primary">{{ store.regular }} <small class="text-caption">أيام</small></div>
          <div class="text-caption mt-1 text-muted">اضغط للعودة للإجازات ←</div>
        </v-card>
      </v-col>
      <v-col cols="12" sm="6" md="4">
        <v-card class="pa-6 border-secondary-lg rounded-xl bg-surface elevation-3 h-100 active-rest-card">
          <div class="d-flex align-center mb-2">
            <v-icon color="secondary" class="mr-2">mdi-clock-check-outline</v-icon>
            <span class="text-subtitle-2 font-weight-bold">رصيد البدلات</span>
            <v-spacer></v-spacer>
            <v-chip size="x-small" color="secondary" variant="flat">العرض الحالي</v-chip>
          </div>
          <div class="text-h4 font-weight-black color-secondary">{{ store.rest }} <small class="text-caption">أيام</small></div>
        </v-card>
      </v-col>
    </v-row>

    <!-- Restallowance Table -->
    <v-card class="border rounded-xl overflow-hidden elevation-1">
      <v-table hover density="comfortable" class="modern-table">
        <thead>
          <tr>
            <th class="text-right">#</th>
            <th class="text-right">الوصف</th>
            <th class="text-right">التاريخ</th>
            <th class="text-right">الحالة</th>
            <th class="text-right">تاريخ الطلب</th>
            <th class="text-center">إجراءات</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(absence, index) in store.restallowance" :key="absence.id">
            <td>{{ index + 1 }}</td>
            <td class="text-body-2">{{ absence.description }}</td>
            <td class="font-weight-bold text-body-2">{{ absence.date }}</td>
            <td>
              <v-chip
                size="x-small"
                :color="getStatusColor(absence.state)"
                variant="flat"
                class="font-weight-bold"
              >
                {{ getStatusText(absence.state) }}
              </v-chip>
            </td>
            <td class="text-caption">{{ store.formatDate(absence.created_at) }}</td>
            <td class="text-center">
              <v-menu location="bottom end">
                <template v-slot:activator="{ props }">
                  <v-btn
                    icon="mdi-dots-vertical"
                    variant="text"
                    size="small"
                    v-bind="props"
                  ></v-btn>
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

      <div
        v-if="!store.restallowance || store.restallowance.length === 0"
        class="pa-12 text-center"
      >
        <v-icon
          size="64"
          color="secondary"
          class="mb-4 bg-surface-variant bg-opacity-10 pa-8 rounded-circle"
        >
          mdi-clock-alert-outline
        </v-icon>
        <div class="text-h6 text-muted">لا يوجد سجل بدلات راحة متاح حالياً</div>
      </div>
    </v-card>

    <!-- Delete Confirmation -->
    <v-dialog v-model="dialogDelete" max-width="400">
      <v-card class="pa-4 text-center">
        <v-icon color="error" size="48" class="mb-2">mdi-delete-alert-outline</v-icon>
        <v-card-title>حذف السجل؟</v-card-title>

        <v-card-text v-if="absenceActive?.state == 0">
          تم تبديل <strong>{{ absenceActive?.description }}</strong
          >. لابد من حذف الطلب المقابل من قائمة الإجازات أولاً.
        </v-card-text>
        <v-card-text v-else>
          هل تريد حقاً حذف سجل بدل الراحة بتاريخ <strong>{{ absenceActive?.date }}</strong
          >؟
        </v-card-text>

        <div class="d-flex justify-center gap-2 mt-4">
          <v-btn color="secondary" variant="text" @click="dialogDelete = false">
            {{ absenceActive?.state == 0 ? 'إغلاق' : 'إلغاء' }}
          </v-btn>
          <v-btn
            v-if="absenceActive?.state != 0"
            color="error"
            class="rounded-lg"
            @click="funDelete"
            >تأكيد الحذف</v-btn
          >
        </div>
      </v-card>
    </v-dialog>

    <!-- Edit Notice -->
    <v-dialog v-model="dialogNotice" max-width="400">
      <v-card class="pa-4 text-center">
        <v-icon color="info" size="48" class="mb-2">mdi-information-outline</v-icon>
        <v-card-title>تنبيه</v-card-title>
        <v-card-text
          >خاصية التعديل المباشر غير متاحة حالياً. يرجى حذف السجل وإضافته من جديد.</v-card-text
        >
        <v-btn color="primary" variant="text" @click="dialogNotice = false" class="mt-4"
          >حسناً</v-btn
        >
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { usemainStore } from '../../store/mainStore';
import AddVRestallowanceComponent from '../../components/Vacations/AddRestallowanceComponent.vue';
import { onMounted, ref } from 'vue';
import axios from 'axios';

const store = usemainStore();
const dialogDelete = ref(false);
const dialogNotice = ref(false);
const absenceActive = ref(null);

onMounted(() => {
  store.getAbsences();
});

function getStatusColor(state) {
  if (state == 1) return 'success';
  if (state == 5) return 'warning';
  return 'secondary';
}

function getStatusText(state) {
  if (state == 1) return 'متاح';
  if (state == 5) return 'النصف فقط';
  return 'تم التبديل';
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
    .delete(`restallowance/${absenceActive.value.id}`)
    .then(() => {
      store.getAbsences();
      dialogDelete.value = false;
      store.startSnack('تم الحذف بنجاح', 'no', 'success');
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
  border-color: var(--primary) !important;
}

.color-primary {
  color: var(--primary) !important;
}
.color-secondary {
  color: var(--secondary) !important;
}

.text-muted {
  color: var(--text-muted) !important;
}

.border-secondary-lg {
  border: 2px solid var(--secondary) !important;
}

.active-rest-card {
  position: relative;
  overflow: hidden;
}

.active-rest-card::before {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 4px;
  height: 100%;
  background: var(--secondary);
}

.gap-2 { gap: 8px; }
</style>
