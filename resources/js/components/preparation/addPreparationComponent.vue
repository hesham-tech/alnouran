<template>
  <div class="d-inline-block">
    <v-btn
      color="success"
      prepend-icon="mdi-plus"
      @click="dialogAddPreparation = true"
      class="rounded-lg font-weight-bold"
    >
      إضافة تحضيرة
    </v-btn>

    <v-dialog v-model="dialogAddPreparation" max-width="600">
      <v-card class="rounded-xl border overflow-hidden bg-surface">
        <div class="pa-6 border-b bg-surface-variant bg-opacity-5 d-flex align-center">
          <v-icon color="success" class="mr-3">mdi-flask-plus-outline</v-icon>
          <span class="text-h6 font-weight-bold">إضافة تحضيرة جديدة</span>
          <v-spacer></v-spacer>
          <v-btn icon="mdi-close" variant="text" size="small" @click="dialogAddPreparation = false"></v-btn>
        </div>

        <v-card-text class="pa-6">
          <v-form ref="form" @submit.prevent="addPreparation">
            <v-row dense>
              <v-col cols="12" md="6">
                <v-select
                  v-model="typePreparation"
                  label="نوع التحضيرة"
                  :items="store.typePreparation"
                  item-title="name"
                  item-value="id"
                  variant="outlined"
                  return-object
                  @update:model-value="shangePrep"
                  class="mb-2"
                  prepend-inner-icon="mdi-flask-outline"
                ></v-select>
              </v-col>
              <v-col cols="12" md="6" v-if="stations.length > 1">
                <v-select
                  v-model="newPreparation.station_id"
                  label="المحطة"
                  :items="stations"
                  item-title="name"
                  item-value="id"
                  variant="outlined"
                  class="mb-2"
                  prepend-inner-icon="mdi-map-marker-outline"
                ></v-select>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="newPreparation.actual_time"
                  label="وقت التحضير"
                  type="datetime-local"
                  variant="outlined"
                  class="mb-2"
                  prepend-inner-icon="mdi-clock-outline"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="newPreparation.ppm"
                  label="PPM"
                  type="number"
                  variant="outlined"
                  class="mb-2"
                  prepend-inner-icon="mdi-gauge"
                  @update:model-value="quantityFin"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="newPreparation.cont_hours"
                  label="عدد الساعات"
                  type="number"
                  variant="outlined"
                  class="mb-2"
                  prepend-inner-icon="mdi-timer-outline"
                  @update:model-value="quantityFin"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="newPreparation.slices_ton"
                  label="طن الشرائح"
                  type="number"
                  variant="outlined"
                  class="mb-2"
                  prepend-inner-icon="mdi-weight-kilogram"
                  @update:model-value="quantityFin"
                ></v-text-field>
              </v-col>

              <v-col cols="12">
                <div class="d-flex gap-4 mb-4 pa-4 rounded-lg bg-surface-variant bg-opacity-5 border border-dashed text-center">
                  <div class="flex-1">
                    <div class="text-caption text-muted mb-1">الكمية (كيلو)</div>
                    <div class="text-h5 font-weight-black color-primary">{{ newPreparation.quantity || 0 }}</div>
                  </div>
                  <v-divider vertical></v-divider>
                  <div class="flex-1">
                    <div class="text-caption text-muted mb-1">الكمية (سم)</div>
                    <div class="text-h5 font-weight-black color-secondary">{{ newPreparation.quantityGallon || 0 }}</div>
                  </div>
                </div>
              </v-col>

              <v-col cols="12">
                <v-text-field
                  v-model="newPreparation.note"
                  label="ملاحظات إضافية"
                  variant="outlined"
                  class="mb-2"
                  prepend-inner-icon="mdi-note-text-outline"
                ></v-text-field>
              </v-col>

              <v-col cols="12">
                <div class="text-subtitle-2 mb-2">الوردية</div>
                <v-radio-group v-model="timeOfDay" inline hide-details class="mt-0">
                  <v-radio label="الوردية الأولى" value="الاولي" color="primary"></v-radio>
                  <v-radio label="الوردية الثانية" value="الثانية" color="primary"></v-radio>
                </v-radio-group>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>

        <div class="pa-6 border-t d-flex justify-end gap-2 bg-surface-variant bg-opacity-5">
          <v-btn color="secondary" variant="text" @click="dialogAddPreparation = false" class="rounded-lg px-6">إلغاء</v-btn>
          <v-btn color="success" @click="addPreparation" class="rounded-lg px-8 font-weight-bold">حفظ التحضيرة</v-btn>
        </div>
      </v-card>
    </v-dialog>

    <v-dialog v-model="dialogError" max-width="400">
      <v-card class="pa-6 rounded-xl border elevation-4 text-center">
        <v-icon color="error" size="64" class="mb-4">mdi-alert-circle-outline</v-icon>
        <v-card-title class="justify-center font-weight-bold">خطأ في البيانات</v-card-title>
        <v-card-text>
          <div v-if="messageError.pp" class="mb-2">حقل PPM مطلوب</div>
          <div v-if="messageError.actual_time" class="mb-2">وقت التحضير مطلوب</div>
          <div v-if="messageError.slices_ton" class="mb-2">طن الشرائح مطلوب</div>
        </v-card-text>
        <v-btn color="primary" class="rounded-lg mt-4" @click="dialogError = false">فهمت</v-btn>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { onMounted, ref, watchEffect } from 'vue';
import { usemainStore } from '../../store/mainStore';
import axios from 'axios';

const store = usemainStore();
const newPreparation = ref({});
const dialogAddPreparation = ref(false);
const dialogError = ref(false);
const messageError = ref('');
const stations = ref([]);
const typePreparation = ref(null);
const timeOfDay = ref('');

watchEffect(() => {
  if (
    !isNaN(parseFloat(newPreparation.value.ppm)) &&
    !isNaN(parseFloat(newPreparation.value.cont_hours)) &&
    !isNaN(parseFloat(newPreparation.value.slices_ton))
  ) {
    newPreparation.value.quantity = (
      (newPreparation.value.slices_ton *
        newPreparation.value.cont_hours *
        newPreparation.value.ppm) /
      1000
    ).toFixed(2);
    newPreparation.value.quantityGallon = (newPreparation.value.quantity / 14.8).toFixed(2);
  }
});

onMounted(() => {
  axios
    .get(`Stations?current_user=${store.user.id}`)
    .then(res => {
      stations.value = res.data;
      if (stations.value.length > 0) {
        newPreparation.value.station_id = stations.value[0].id;
      }
    })
    .catch(() => {
      store.startSnack('حدث خطأ في تحميل المحطات', 'no', 'danger');
    });
  store.getTypePre('latestPreparationActual.user');
  
  const currentTime = new Date();
  const currentHour = currentTime.getHours();
  if (currentHour >= 8 && currentHour < 20) {
    timeOfDay.value = 'الاولي';
  } else {
    timeOfDay.value = 'الثانية';
  }
});

function shangePrep(x) {
  typePreparation.value = x;
  if (x.latest_preparation_actual != null) {
    newPreparation.value = { ...x.latest_preparation_actual };
  }
}

function addPreparation() {
  newPreparation.value.user_id = store.user.id;
  newPreparation.value.typePreparation_id = typePreparation.value?.id;
  newPreparation.value.shift = timeOfDay.value;

  axios
    .post(`Pre`, newPreparation.value)
    .then(() => {
      dialogAddPreparation.value = false;
      newPreparation.value = {};
      typePreparation.value = null;
      store.startSnack('تم إضافة التحضيرة بنجاح', 'no', 'success');
      location.reload();
    })
    .catch(e => {
      if (e.response?.data) {
        messageError.value = e.response.data;
        dialogError.value = true;
      }
      store.startSnack('حدث خطأ أثناء الإضافة', 'no', 'danger');
    });
}
function quantityFin() {
    // This is handled by watchEffect
}
</script>

<style scoped>
.color-primary { color: var(--primary) !important; }
.color-secondary { color: var(--secondary) !important; }
.text-muted { color: var(--text-muted) !important; }
.gap-4 { gap: 16px; }
.flex-1 { flex: 1; }
</style>
