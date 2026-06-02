<template>
  <div class="d-inline-block">
    <v-btn
      icon="mdi-pencil-outline"
      variant="text"
      color="secondary"
      @click="openEditDialog"
    ></v-btn>

    <v-dialog v-model="dialogEditPreparation" max-width="600">
      <v-card class="rounded-xl border bg-surface" style="max-height: 90vh; overflow-y: auto;">
        <div class="pa-6 border-b bg-surface-variant bg-opacity-5 d-flex align-center">
          <v-icon color="primary" class="mr-3">mdi-flask-outline</v-icon>
          <span class="text-h6 font-weight-bold">تعديل التحضيرة</span>
          <v-spacer></v-spacer>
          <v-btn
            icon="mdi-close"
            variant="text"
            size="small"
            @click="dialogEditPreparation = false"
          ></v-btn>
        </div>

        <v-card-text class="pa-6">
          <v-form ref="form" @submit.prevent="updatePreparation">
            <v-row dense>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="editedPreparation.actual_time"
                  label="وقت التحضير"
                  type="datetime-local"
                  variant="outlined"
                  class="mb-2"
                  prepend-inner-icon="mdi-clock-outline"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="editedPreparation.ppm"
                  label="PPM"
                  type="number"
                  variant="outlined"
                  class="mb-2"
                  prepend-inner-icon="mdi-gauge"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="editedPreparation.cont_hours"
                  label="عدد الساعات"
                  type="number"
                  variant="outlined"
                  class="mb-2"
                  prepend-inner-icon="mdi-timer-outline"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="editedPreparation.slices_ton"
                  label="طن الشرائح"
                  type="number"
                  variant="outlined"
                  class="mb-2"
                  prepend-inner-icon="mdi-weight-kilogram"
                ></v-text-field>
              </v-col>

              <v-col cols="12">
                <div
                  class="d-flex gap-4 mb-4 pa-4 rounded-lg bg-surface-variant bg-opacity-5 border border-dashed text-center"
                >
                  <div class="flex-1">
                    <div class="text-caption text-muted mb-1">الكمية (كيلو)</div>
                    <div class="text-h5 font-weight-black color-primary">
                      {{ editedPreparation.quantity || 0 }}
                    </div>
                  </div>
                  <v-divider vertical></v-divider>
                  <div class="flex-1">
                    <div class="text-caption text-muted mb-1">الكمية (سم)</div>
                    <div class="text-h5 font-weight-black color-secondary">
                      {{ editedPreparation.quantityGallon || 0 }}
                    </div>
                  </div>
                </div>
              </v-col>

              <v-col cols="12">
                <v-text-field
                  v-model="editedPreparation.note"
                  label="ملاحظات إضافية"
                  variant="outlined"
                  class="mb-2"
                  prepend-inner-icon="mdi-note-text-outline"
                ></v-text-field>
              </v-col>

              <v-col cols="12">
                <div class="text-subtitle-2 mb-2">الوردية</div>
                <v-radio-group v-model="editedPreparation.shift" inline hide-details class="mt-0">
                  <v-radio label="الوردية الأولى" value="الاولي" color="primary"></v-radio>
                  <v-radio label="الوردية الثانية" value="الثانية" color="primary"></v-radio>
                </v-radio-group>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>

        <div class="pa-6 border-t d-flex justify-end gap-2 bg-surface-variant bg-opacity-5">
          <v-btn
            color="secondary"
            variant="text"
            @click="dialogEditPreparation = false"
            class="rounded-lg px-6"
            >إلغاء</v-btn
          >
          <v-btn color="primary" @click="updatePreparation" class="rounded-lg px-8 font-weight-bold"
            >تحديث التحضيرة</v-btn
          >
        </div>
      </v-card>
    </v-dialog>

    <v-dialog v-model="dialogError" max-width="400">
      <v-card class="pa-6 rounded-xl border elevation-4 text-center" style="max-height: 90vh; overflow-y: auto;">
        <v-icon color="error" size="64" class="mb-4">mdi-alert-circle-outline</v-icon>
        <v-card-title class="justify-center font-weight-bold">خطأ في البيانات</v-card-title>
        <v-card-text>
          <div v-if="messageError.ppm" class="mb-2">حقل PPM مطلوب</div>
          <div v-if="messageError.actual_time" class="mb-2">وقت التحضير مطلوب</div>
          <div v-if="messageError.slices_ton" class="mb-2">طن الشرائح مطلوب</div>
        </v-card-text>
        <v-btn color="primary" class="rounded-lg mt-4" @click="dialogError = false">فهمت</v-btn>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { ref, watchEffect } from 'vue';
import { usemainStore } from '../../store/mainStore';
import axios from 'axios';

const props = defineProps({
  preparation: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['refresh']);

const store = usemainStore();
const editedPreparation = ref({ ...props.preparation });
const dialogEditPreparation = ref(false);
const dialogError = ref(false);
const messageError = ref('');

function openEditDialog() {
  editedPreparation.value = { ...props.preparation };
  dialogEditPreparation.value = true;
}

watchEffect(() => {
  if (
    !isNaN(parseFloat(editedPreparation.value.ppm)) &&
    !isNaN(parseFloat(editedPreparation.value.cont_hours)) &&
    !isNaN(parseFloat(editedPreparation.value.slices_ton))
  ) {
    editedPreparation.value.quantity = (
      (editedPreparation.value.slices_ton *
        editedPreparation.value.cont_hours *
        editedPreparation.value.ppm) /
      1000
    ).toFixed(2);
    editedPreparation.value.quantityGallon = (editedPreparation.value.quantity / 14.8).toFixed(2);
  }
});

function updatePreparation() {
  if (!editedPreparation.value.prep_id) {
    console.error('prep_id is missing!', editedPreparation.value);
    store.startSnack('خطأ: لا يوجد معرف للتحضيرة', 'no', 'danger');
    return;
  }

  axios
    .put(`Pre/${editedPreparation.value.prep_id}`, editedPreparation.value)
    .then(() => {
      dialogEditPreparation.value = false;
      store.startSnack('تم تحديث التحضيرة بنجاح', 'no', 'success');
      emit('refresh');
    })
    .catch(e => {
      console.error('Update error:', e.response?.status, e.response?.data);
      if (e.response?.data) {
        messageError.value = e.response.data;
        dialogError.value = true;
      }
      store.startSnack('حدث خطأ أثناء التحديث: ' + (e.response?.status || ''), 'no', 'danger');
    });
}
</script>

<style scoped>
.color-primary {
  color: var(--primary) !important;
}
.color-secondary {
  color: var(--secondary) !important;
}
.text-muted {
  color: var(--text-muted) !important;
}
.gap-4 {
  gap: 16px;
}
.flex-1 {
  flex: 1;
}
</style>
