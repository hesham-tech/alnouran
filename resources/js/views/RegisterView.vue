<template>
  <div class="auth-wrapper d-flex align-center justify-center">
    <v-card width="100%" max-width="550" class="auth-card pa-8 rounded-xl elevation-12">
      <div class="text-center mb-6">
        <v-avatar color="primary" size="64" class="mb-4">
          <v-icon size="32" color="white">mdi-account-plus-outline</v-icon>
        </v-avatar>
        <h1 class="text-h4 font-weight-bold mb-2">{{ $t('register') }}</h1>
        <p class="text-muted text-body-2">انضم إلى Alnouran وابدأ في إدارة محطاتك</p>
      </div>

      <div class="d-flex justify-center py-4" v-if="stations == 'getData'">
        <v-progress-circular indeterminate color="primary"></v-progress-circular>
      </div>

      <v-alert
        v-else-if="stations == 'noData'"
        type="error"
        variant="tonal"
        class="mb-4"
        title="خطأ في تحميل البيانات"
        text="تعذر الحصول على قائمة المحطات، يرجى المحاولة لاحقاً."
      ></v-alert>

      <v-form v-else @submit.prevent="toRegister">
        <v-row dense>
          <v-col cols="12" sm="6">
            <v-text-field
              v-model="user.name"
              :label="$t('enterName')"
              prepend-inner-icon="mdi-account-outline"
              class="mb-2"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="6">
            <v-text-field
              v-model="user.email"
              :label="$t('enterEmail')"
              type="email"
              prepend-inner-icon="mdi-email-outline"
              class="mb-2"
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-text-field
              v-model="user.phone"
              :label="$t('enterTelephone')"
              type="tel"
              prepend-inner-icon="mdi-phone-outline"
              class="mb-2"
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-text-field
              v-model="user.password"
              :type="store.passToggle ? 'password' : 'text'"
              :label="$t('enterPassword')"
              prepend-inner-icon="mdi-lock-outline"
              :append-inner-icon="store.passToggle ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
              @click:appendInner="store.passToggle = !store.passToggle"
              class="mb-2"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="6">
            <v-select
              v-model="user.Job_title"
              :label="$t('Selectjob')"
              :items="Job_title"
              item-title="name"
              item-value="id"
              prepend-inner-icon="mdi-briefcase-outline"
              class="mb-2"
            ></v-select>
          </v-col>
          <v-col cols="12" sm="6">
            <v-select
              v-model="user.station_id"
              :label="$t('SelectPartment')"
              :items="stations"
              item-title="name"
              item-value="id"
              :multiple="user.Job_title == 'engeneer'"
              prepend-inner-icon="mdi-domain"
              class="mb-2"
            ></v-select>
          </v-col>
        </v-row>

        <v-btn
          block
          size="large"
          color="primary"
          class="text-none font-weight-bold rounded-lg mt-4"
          height="52"
          type="submit"
        >
          {{ $t('register') }}
        </v-btn>
      </v-form>

      <div class="text-center mt-6 pt-4 border-t">
        <span class="text-muted text-body-2">{{ $t('gotoAccount') }}</span>
        <v-btn variant="text" color="primary" to="/auth/login" class="text-none px-2 font-weight-bold">
          {{ $t('LogIn') }}
        </v-btn>
      </div>
    </v-card>
  </div>
</template>

<script setup>
import { usemainStore } from "@/store/mainStore";
import axios from "axios";
import { onMounted, ref } from "vue";

const store = usemainStore();
const stations = ref(["getData"]);
const user = ref({
  name: '',
  email: '',
  phone: '',
  password: '',
  Job_title: '',
  station_id: null
});

const Job_title = ref([
  { id: "engeneer", name: "مهندس" },
  { id: "technician", name: "فني" },
]);

onMounted(() => {
  axios
    .get(`Stations`)
    .then((res) => {
      stations.value = res.data;
    })
    .catch(() => {
      stations.value = "noData";
      store.startSnack("فشل تحميل قائمة المحطات", "no", "danger");
    });
});

function toRegister() {
  axios
    .post(`register`, user.value)
    .then(() => {
      store.startSnack("تم إنشاء الحساب بنجاح", "login", "success");
    })
    .catch(() => {
      store.startSnack("حدث خطأ أثناء إنشاء الحساب", "no", "danger");
    });
}
</script>

<style scoped>
.auth-wrapper {
  min-height: 100vh;
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  padding: 20px;
}

[data-v-theme="dark"] .auth-wrapper {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
}

.auth-card {
  background: var(--bg-surface) !important;
  border: 1px solid var(--border-color) !important;
}

.text-muted {
  color: var(--text-muted) !important;
}
</style>
