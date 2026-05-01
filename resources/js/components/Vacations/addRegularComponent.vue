<template>
  <div class="d-inline-block">
    <v-dialog v-model="dialog" max-width="400">
      <template v-slot:activator="{ props }">
        <v-btn
          color="success"
          variant="tonal"
          prepend-icon="mdi-plus-circle-outline"
          v-bind="props"
          class="rounded-lg font-weight-bold"
        >
          إضافة رصيد
        </v-btn>
      </template>

      <v-card class="rounded-xl border overflow-hidden bg-surface">
        <div class="pa-6 border-b bg-surface-variant bg-opacity-5 d-flex align-center">
          <v-icon color="success" class="mr-3">mdi-calendar-edit</v-icon>
          <span class="text-h6 font-weight-bold">تعديل الرصيد الاعتيادي</span>
          <v-spacer></v-spacer>
          <v-btn icon="mdi-close" variant="text" size="small" @click="dialog = false"></v-btn>
        </div>

        <v-card-text class="pa-6">
          <v-alert
            type="warning"
            variant="tonal"
            density="compact"
            class="mb-6 rounded-lg text-caption"
            border="start"
          >
            تنبيه: سيتم استبدال الرصيد الحالي بالقيمة الجديدة التي ستقوم بإدخالها.
          </v-alert>

          <v-form ref="form" @submit.prevent="saveRegular">
            <v-text-field
              v-model="addRegular.balance"
              type="number"
              label="الرصيد الجديد (بالأيام)"
              variant="outlined"
              prepend-inner-icon="mdi-numeric"
              class="mb-2"
              :rules="[v => !!v || 'هذا الحقل مطلوب']"
            ></v-text-field>
          </v-form>
        </v-card-text>

        <div class="pa-6 border-t d-flex justify-end gap-2 bg-surface-variant bg-opacity-5">
          <v-btn color="secondary" variant="text" @click="dialog = false" class="rounded-lg px-6">إلغاء</v-btn>
          <v-btn color="success" @click="saveRegular" class="rounded-lg px-8 font-weight-bold">حفظ التعديل</v-btn>
        </div>
      </v-card>
    </v-dialog>
  </div>
</template>
<script setup>
import axios from "axios";
import { usemainStore } from "../../store/mainStore";
const store = usemainStore();
import { ref } from "vue";
const dialog = ref(false);
const addRegular = ref({});

function saveRegular() {
    axios
        .post(`regular`, addRegular.value)
        .then((res) => {
            store.getAbsences();
            addRegular.value = ref({});
            dialog.value = false;
        })
        .catch((e) => {
            // console.log(e);
        });
}

</script>
