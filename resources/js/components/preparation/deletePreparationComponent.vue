<template>
  <div class="d-inline-block">
    <v-btn
      icon="mdi-trash-can-outline"
      variant="text"
      color="error"
      @click="dialogDelete = true"
    ></v-btn>

    <v-dialog v-model="dialogDelete" max-width="400">
      <v-card class="pa-6 rounded-xl border elevation-4 text-center">
        <v-icon color="error" size="64" class="mb-4">mdi-alert-circle-outline</v-icon>
        <v-card-title class="justify-center font-weight-bold">تأكيد الحذف</v-card-title>
        <v-card-text class="text-body-1">
          هل أنت متأكد من حذف هذه التحضيرة؟ لا يمكن التراجع عن هذا الإجراء.
        </v-card-text>

        <div class="d-flex gap-2 mt-6 justify-center">
          <v-btn
            color="secondary"
            variant="text"
            @click="dialogDelete = false"
            class="rounded-lg px-6"
            >إلغاء</v-btn
          >
          <v-btn
            color="error"
            @click="deletePreparation"
            :loading="loading"
            class="rounded-lg px-6 font-weight-bold"
            >حذف نهائي</v-btn
          >
        </div>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { usemainStore } from '../../store/mainStore';
import axios from 'axios';

const props = defineProps({
  preparation: {
    type: Object,
    required: true,
  },
});

const store = usemainStore();
const emit = defineEmits(['refresh']);
const dialogDelete = ref(false);
const loading = ref(false);

function deletePreparation() {
  if (!props.preparation.prep_id) {
    console.error('prep_id is missing!', props.preparation);
    store.startSnack('خطأ: لا يوجد معرف للتحضيرة', 'no', 'danger');
    return;
  }

  loading.value = true;
  axios
    .delete(`Pre/${props.preparation.prep_id}`)
    .then(() => {
      dialogDelete.value = false;
      store.startSnack('تم حذف التحضيرة بنجاح', 'no', 'success');
      emit('refresh');
    })
    .catch((e) => {
      console.error('Delete error:', e.response?.status, e.response?.data);
      store.startSnack('حدث خطأ أثناء الحذف: ' + (e.response?.status || ''), 'no', 'danger');
    })
    .finally(() => {
      loading.value = false;
    });
}
</script>

<style scoped>
.gap-2 {
  gap: 8px;
}
</style>
