<template>
  <div class="pa-4 d-flex justify-center align-center min-vh-80">
    <v-card class="pa-8 border rounded-xl bg-surface elevation-4 w-100 max-w-600">
      <div class="text-center mb-8">
        <v-icon color="primary" size="48" class="mb-4">mdi-account-edit-outline</v-icon>
        <h1 class="text-h4 font-weight-bold">تعديل الملف الشخصي</h1>
        <p class="text-muted">قم بتحديث معلوماتك الشخصية أو كلمة المرور</p>
      </div>

      <v-form @submit.prevent="editProfil">
        <v-text-field
          v-model="user.name"
          label="الاسم الكامل"
          variant="outlined"
          class="mb-4"
          prepend-inner-icon="mdi-account-outline"
        ></v-text-field>

        <v-text-field
          v-model="user.email"
          label="البريد الإلكتروني"
          variant="outlined"
          class="mb-4"
          prepend-inner-icon="mdi-email-outline"
        ></v-text-field>

        <v-text-field
          v-model="user.phone"
          label="رقم الهاتف"
          variant="outlined"
          class="mb-6"
          prepend-inner-icon="mdi-phone-outline"
        ></v-text-field>

        <v-divider class="mb-6"></v-divider>

        <div class="d-flex align-center justify-space-between mb-4">
          <span class="font-weight-bold">تغيير كلمة المرور</span>
          <v-switch
            v-model="editPass"
            color="primary"
            hide-details
            density="compact"
          ></v-switch>
        </div>

        <v-expand-transition>
          <div v-if="editPass">
            <v-text-field
              v-model="user.password"
              :type="store.passToggle ? 'password' : 'text'"
              label="كلمة المرور الجديدة"
              variant="outlined"
              class="mb-4"
              prepend-inner-icon="mdi-lock-outline"
              :append-inner-icon="store.passToggle ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
              @click:appendInner="store.passToggle = !store.passToggle"
            ></v-text-field>
          </div>
        </v-expand-transition>

        <v-btn
          type="submit"
          color="primary"
          block
          size="large"
          class="rounded-lg font-weight-bold mb-4"
        >
          حفظ التغييرات
        </v-btn>

        <v-btn
          variant="text"
          color="error"
          block
          class="text-none"
          @click="dialog = true"
        >
          حذف الحساب نهائياً
        </v-btn>
      </v-form>
    </v-card>

    <!-- Delete Confirmation -->
    <v-dialog v-model="dialog" max-width="400">
      <v-card class="pa-4 text-center">
        <v-icon color="error" size="48" class="mb-2">mdi-alert-circle-outline</v-icon>
        <v-card-title>حذف الحساب؟</v-card-title>
        <v-card-text>هل أنت متأكد من رغبتك في حذف حسابك؟ سيتم فقدان جميع بياناتك نهائياً.</v-card-text>
        <div class="d-flex justify-center gap-2 mt-4">
          <v-btn color="secondary" variant="text" @click="dialog = false">إلغاء</v-btn>
          <v-btn color="error" class="rounded-lg" @click="funDelete">تأكيد الحذف</v-btn>
        </div>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { usemainStore } from "@/store/mainStore";
import axios from "axios";
import { ref } from "vue";

const store = usemainStore();
const dialog = ref(false);
const user = ref({ ...store.user });
const editPass = ref(false);

function editProfil() {
  const payload = { ...user.value };
  if (!editPass.value) delete payload.password;

  axios
    .put(`users/${store.user.id}`, payload)
    .then(() => {
      store.startSnack("تم تحديث الملف الشخصي بنجاح", "no", "success");
      if (editPass.value) {
        store.logout();
      }
    })
    .catch(() => {
      store.startSnack("حدث خطأ أثناء التحديث", "no", "danger");
    });
}

function funDelete() {
  axios
    .delete(`users/${store.user.id}`)
    .then(() => {
      dialog.value = false;
      store.logout();
      store.startSnack("تم حذف الحساب نهائياً", "login", "success");
    })
    .catch(() => {
      store.startSnack("حدث خطأ أثناء حذف الحساب", "no", "danger");
    });
}
</script>

<style scoped>
.min-vh-80 {
  min-height: 80vh;
}

.max-w-600 {
  max-width: 600px;
}

.text-muted {
  color: var(--text-muted) !important;
}

.gap-2 {
  gap: 8px;
}
</style>
