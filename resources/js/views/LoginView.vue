<template>
  <div class="auth-wrapper d-flex align-center justify-center">
    <v-card width="100%" max-width="450" class="auth-card pa-8 rounded-xl elevation-12">
      <div class="text-center mb-8">
        <v-avatar color="primary" size="80" class="mb-4 elevation-4">
          <v-icon size="40" color="white">mdi-shield-lock-outline</v-icon>
        </v-avatar>
        <h1 class="text-h4 font-weight-bold mb-2">{{ $t('LogIn') }}</h1>
        <p class="text-muted text-body-2">مرحباً بك مجدداً في Alnouran</p>
      </div>

      <v-form @submit.prevent="toLogIn" class="mt-4">
        <v-text-field
          v-model="userLog.email"
          :label="$t('Email')"
          prepend-inner-icon="mdi-email-outline"
          type="email"
          class="mb-4"
        ></v-text-field>

        <v-text-field
          v-model="userLog.password"
          :type="store.passToggle ? 'password' : 'text'"
          :label="$t('enterPassword')"
          prepend-inner-icon="mdi-lock-outline"
          :append-inner-icon="store.passToggle ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
          @click:appendInner="store.passToggle = !store.passToggle"
          class="mb-6"
        ></v-text-field>

        <v-btn
          block
          size="large"
          color="primary"
          class="text-none font-weight-bold rounded-lg elevation-2"
          height="52"
          type="submit"
        >
          {{ $t('LogIn') }}
        </v-btn>
      </v-form>

      <div class="text-center mt-8 pt-4 border-t">
        <span class="text-muted text-body-2">{{ $t('dontAccount') }}</span>
        <v-btn variant="text" color="primary" to="/auth/register" class="text-none px-2 font-weight-bold">
          {{ $t('register') }}
        </v-btn>
      </div>
    </v-card>
  </div>
</template>

<script setup>
import { usemainStore } from '@/store/mainStore';
import { ref } from 'vue';
import axios from 'axios';

const store = usemainStore();
const userLog = ref({
  email: '',
  password: ''
});

function toLogIn() {
  axios.get('csrf-cookie').then(() => {
    axios
      .post(`login`, userLog.value)
      .then(res => {
        if (res.data.token && res.data.user) {
          localStorage.setItem('token', res.data.token);
          localStorage.setItem('user', JSON.stringify(res.data.user));
          store.user = res.data.user;
          store.setAuthHeaderNew(res.data.token);
          store.getUser();
          store.auth = true;
          store.startSnack('تم تسجيل الدخول بنجاح', 'home', 'success', false, 200);
        } else {
          store.startSnack('فشل تسجيل الدخول: بيانات غير مكتملة', 'no', 'danger');
        }
      })
      .catch(e => {
        if (e.response?.data?.error == 'Error in email or password.') {
          store.startSnack('البريد الإلكتروني أو كلمة المرور غير صحيحة', 'no', 'danger');
        } else {
          store.startSnack('حدث خطأ أثناء تسجيل الدخول', 'no', 'danger');
        }
      });
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
