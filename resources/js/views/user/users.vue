<template>
  <div class="pa-4 dashboard-container">
    <div class="d-flex justify-space-between align-center mb-6">
      <div class="d-flex align-center">
        <v-icon color="primary" size="32" class="mr-2">mdi-account-group-outline</v-icon>
        <h1 class="text-h5 font-weight-bold">إدارة المستخدمين</h1>
      </div>
      <AddUserComponent />
    </div>

    <!-- Users Table -->
    <v-card class="border rounded-xl overflow-hidden elevation-1">
      <v-table hover density="comfortable" class="modern-table">
        <thead>
          <tr>
            <th class="text-right">#</th>
            <th class="text-right">الموظف</th>
            <th class="text-right">الاتصال</th>
            <th class="text-right">الوظيفة / القسم</th>
            <th class="text-right">الصلاحيات</th>
            <th class="text-right">تاريخ الانضمام</th>
            <th class="text-center">إجراءات</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(user, index) in store.users"
            :key="user.id"
            @dblclick="goToUser(user.id)"
            class="cursor-pointer"
          >
            <td>{{ index + 1 }}</td>
            <td>
              <div class="d-flex align-center py-2">
                <v-avatar color="primary" size="32" class="mr-3 text-white font-weight-bold">
                  {{ user.name ? user.name.charAt(0) : '?' }}
                </v-avatar>
                <div>
                  <div class="font-weight-bold">{{ user.name }}</div>
                  <div class="text-caption text-muted">{{ user.roles }}</div>
                </div>
              </div>
            </td>
            <td>
              <div class="text-body-2">{{ user.email }}</div>
              <div class="text-caption text-muted">{{ user.phone }}</div>
            </td>
            <td>
              <v-chip size="x-small" variant="tonal" color="secondary" class="font-weight-bold">
                {{ user.Job_title }}
              </v-chip>
            </td>
            <td>
              <v-chip
                size="x-small"
                :color="user.roles === 'admin' ? 'error' : 'info'"
                variant="flat"
              >
                {{ user.roles }}
              </v-chip>
            </td>
            <td class="text-caption">
              {{ store.formatDate(user.created_at) }}
            </td>
            <td class="text-center">
              <v-menu location="bottom end">
                <template v-slot:activator="{ props }">
                  <v-btn icon="mdi-dots-vertical" variant="text" size="small" v-bind="props"></v-btn>
                </template>
                <v-list density="compact" class="rounded-lg elevation-4">
                  <v-list-item
                    prepend-icon="mdi-account-outline"
                    title="عرض الملف"
                    @click="goToUser(user.id)"
                  ></v-list-item>
                  <v-list-item
                    prepend-icon="mdi-pencil-outline"
                    title="تعديل البيانات"
                    @click="openEditDialog(user)"
                  ></v-list-item>
                  <v-divider class="my-1"></v-divider>
                  <v-list-item
                    prepend-icon="mdi-delete-outline"
                    title="حذف المستخدم"
                    color="error"
                    @click="confirmDelete(user)"
                  ></v-list-item>
                </v-list>
              </v-menu>
            </td>
          </tr>
        </tbody>
      </v-table>

      <div v-if="store.users.length === 0" class="pa-12 text-center">
        <v-icon size="64" color="secondary" class="mb-4">mdi-account-off-outline</v-icon>
        <div class="text-h6 text-muted">لا يوجد مستخدمين مسجلين حالياً</div>
      </div>
    </v-card>

    <!-- Delete Confirmation Dialog -->
    <v-dialog v-model="dialogDelete" max-width="400">
      <v-card class="pa-4 text-center">
        <v-icon color="error" size="48" class="mb-2">mdi-delete-alert-outline</v-icon>
        <v-card-title>حذف المستخدم؟</v-card-title>
        <v-card-text>أنت على وشك حذف <strong>{{ userActive?.name }}</strong>. لا يمكن التراجع عن هذا الإجراء.</v-card-text>
        <div class="d-flex justify-center gap-2 mt-4">
          <v-btn color="secondary" variant="text" @click="dialogDelete = false">إلغاء</v-btn>
          <v-btn color="error" class="rounded-lg" @click="funDelete">تأكيد الحذف</v-btn>
        </div>
      </v-card>
    </v-dialog>

    <!-- Edit User Dialog -->
    <v-dialog v-model="dialogEdit" max-width="500">
      <v-card class="rounded-xl border overflow-hidden bg-surface">
        <div class="pa-6 border-b bg-surface-variant bg-opacity-5 d-flex align-center">
          <v-icon color="primary" class="mr-3">mdi-account-edit-outline</v-icon>
          <span class="text-h6 font-weight-bold">تعديل بيانات المستخدم</span>
          <v-spacer></v-spacer>
          <v-btn icon="mdi-close" variant="text" size="small" @click="dialogEdit = false"></v-btn>
        </div>

        <v-card-text class="pa-6">
          <v-form @submit.prevent="editProfil">
            <v-text-field v-model="userActive.name" label="الاسم الكامل" variant="outlined" prepend-inner-icon="mdi-account" class="mb-4"></v-text-field>
            <v-text-field v-model="userActive.email" label="البريد الإلكتروني" variant="outlined" prepend-inner-icon="mdi-email-outline" class="mb-4"></v-text-field>
            <v-text-field v-model="userActive.phone" label="رقم الهاتف" variant="outlined" prepend-inner-icon="mdi-phone-outline" class="mb-4"></v-text-field>
            
            <div class="pa-3 rounded-lg border bg-surface-variant bg-opacity-5 mb-4">
              <v-checkbox
                v-model="changePass"
                label="تغيير كلمة المرور"
                density="compact"
                color="primary"
                hide-details
              ></v-checkbox>

              <v-expand-transition>
                <div v-if="changePass" class="mt-4">
                  <v-text-field
                    v-model="userActive.password"
                    :type="store.passToggle ? 'password' : 'text'"
                    label="كلمة المرور الجديدة"
                    variant="outlined"
                    prepend-inner-icon="mdi-lock-outline"
                    :append-inner-icon="store.passToggle ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                    @click:appendInner="store.passToggle = !store.passToggle"
                    hide-details
                  ></v-text-field>
                </div>
              </v-expand-transition>
            </div>
          </v-form>
        </v-card-text>

        <div class="pa-6 border-t d-flex justify-end gap-2 bg-surface-variant bg-opacity-5">
          <v-btn color="secondary" variant="text" @click="dialogEdit = false" class="rounded-lg px-6">إلغاء</v-btn>
          <v-btn color="primary" @click="editProfil" class="rounded-lg px-8 font-weight-bold">حفظ التعديلات</v-btn>
        </div>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import AddUserComponent from '../../components/user/AddUserComponent.vue';
import { usemainStore } from '../../store/mainStore';
import { onMounted, ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const store = usemainStore();
const userActive = ref({});
const dialogDelete = ref(false);
const dialogEdit = ref(false);
const changePass = ref(false);

onMounted(() => {
  store.getUsers();
});

function goToUser(id) {
  router.push(`/user/${id}`);
}

function openEditDialog(user) {
  userActive.value = { ...user };
  changePass.value = false;
  dialogEdit.value = true;
}

function confirmDelete(user) {
  userActive.value = user;
  dialogDelete.value = true;
}

function funDelete() {
  axios
    .delete(`users/${userActive.value.id}`)
    .then(() => {
      store.getUsers();
      dialogDelete.value = false;
      store.startSnack('تم حذف المستخدم بنجاح', 'no', 'success');
    })
    .catch(() => {
      store.startSnack('فشل الحذف، يرجى المحاولة لاحقاً', 'no', 'danger');
    });
}

function editProfil() {
  const payload = { ...userActive.value };
  if (!changePass.value) delete payload.password;

  axios
    .put(`users/${userActive.value.id}`, payload)
    .then(() => {
      store.getUsers();
      dialogEdit.value = false;
      store.startSnack('تم تحديث البيانات بنجاح', 'no', 'success');
    })
    .catch(() => {
      store.startSnack('حدث خطأ أثناء التحديث', 'no', 'danger');
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

.modern-table tbody td {
  border-bottom: 1px solid var(--border-color) !important;
}

.cursor-pointer {
  cursor: pointer;
}

.text-muted {
  color: var(--text-muted) !important;
}

.gap-2 {
  gap: 8px;
}
</style>
