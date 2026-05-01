<template>
  <div class="d-inline-block">
    <v-dialog v-model="dialog" max-width="450">
      <template v-slot:activator="{ props }">
        <v-btn
          color="primary"
          prepend-icon="mdi-plus"
          @click="setField()"
          v-bind="props"
          class="rounded-lg font-weight-bold elevation-1"
        >
          إضافة بدل راحة
        </v-btn>
      </template>

      <v-card class="rounded-xl border overflow-hidden bg-surface">
        <div class="pa-6 border-b bg-surface-variant bg-opacity-5 d-flex align-center">
          <v-icon color="primary" class="mr-3">mdi-clock-plus-outline</v-icon>
          <span class="text-h6 font-weight-bold">إضافة رصيد بدل راحة</span>
          <v-spacer></v-spacer>
          <v-btn icon="mdi-close" variant="text" size="small" @click="dialog = false"></v-btn>
        </div>

        <v-card-text class="pa-6">
          <v-form ref="form" @submit.prevent="saveRequest">
            <v-textarea
              v-model="addRequest.description"
              label="الوصف (مثلاً: عمل يوم جمعة)"
              variant="outlined"
              rows="2"
              auto-grow
              class="mb-4"
              prepend-inner-icon="mdi-text-subject"
              placeholder="اكتب وصفاً موجزاً لسبب بدل الراحة"
              :rules="[v => !!v || 'هذا الحقل مطلوب']"
            ></v-textarea>

            <v-text-field
              v-model="addRequest.date"
              label="التاريخ"
              type="date"
              variant="outlined"
              class="mb-4"
              prepend-inner-icon="mdi-calendar"
            ></v-text-field>
          </v-form>
        </v-card-text>

        <div class="pa-6 border-t d-flex justify-end gap-2 bg-surface-variant bg-opacity-5">
          <v-btn color="secondary" variant="text" @click="dialog = false" class="rounded-lg px-6">إلغاء</v-btn>
          <v-btn color="primary" @click="saveRequest" class="rounded-lg px-8 font-weight-bold">حفظ السجل</v-btn>
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
const dialog2 = ref(false);
const addRequest = ref({});
const restEixist = ref({});
function saveRequest() {
    if (!addRequest.value.description) {
        addRequest.value.description = ' بدل راحه '
    }
    axios
        .post(`restallowance`, addRequest.value)
        .then(() => {
            // console.log(res.data)
            store.getAbsences();
            addRequest.value = ref({});
            dialog.value = false;
        })
        .catch((e) => {
            // console.log(e);
            if (Array.isArray(e.response.data)) {
                restEixist.value = e.response.data;
            } else {
                restEixist.value = ' حدث خطا اعد المحاولة';
            }
            dialog2.value = true;
        });
}
function setField() {
    addRequest.value.date = store.formatDate(new Date());
}
const typeRequest = ref([
    "Regular",
    "Rest allowance",
    "Casual",
    "Umrah leave",
    "Recruitment",
    "Maternity leave",
    "Compensatory leave",
    "Other",
]);
// const typeRequest = ref({
//     "Regular": "Regular",
//     "Rest allowance": "Rest allowance",
//     "Casual": "Casual",
//     "Umrah leave": "Umrah leave",
//     "Recruitment": "Recruitment",
//     "Maternity leave": "Maternity leave",
//     "Compensatory leave": "Compensatory leave",
//     "Other": "Other",
// });

// اعتيادية	Regular
//  بدل راحة	Rest allowance
//  عارضة	Casual
//  اجازة عمرة	Umrah leave
//  تجنيد	Recruitment
//  اجازة وضع	Maternity leave
//  اجازة تعويضية	Compensatory leave
//  اخري	Other
// const location = ref(null);

</script>
